<?php

namespace App\Http\Controllers\API;

use App\Exports\PaymentReport;
use App\Http\Controllers\BkashController;
use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class PaymentController extends Controller
{
    protected $fetchingError = '';

    /**
     * Search
     * search a payment using trx_id in the database
     * this payment will come through a webhook of bKash
     *
     * @param String $reference
     * @return JsonResponse
     * */
    function search(Request $request, string $reference)
    {
        $action = "SEARCH PAYMENT";
        try {
            // Permission check
            if (!auth()->user()->can('payments.search')) {
                $this->lg($this->accessDenied . ', PERMISSION: payments.search', 'alert', $action, 403);
                return response()->json($this->accessDenied, 403);
            }

            $this->lg("Searching Payment using payment reference(Phone/TrxID): ". $reference, 'info', $action, 200);

            // Reference check
            if ($reference && is_string($reference)) { // must be string
                $queryPayment = Payment::query()->whereTrxIdOrSenderAccountNo($reference, $reference)
                    ->orderBy('id', 'desc')
                    ->api()
                    ->authorized()
                    ->with(array('merchant'=>function($query){
                        $query->select('id','name', 'account_no');
                    }))
                     ->whereDate('created_at', Carbon::today())
//                    ->limit(50)
                    ->get();
                if (count($queryPayment->toArray()) > 0) {
                    $this->lg('Payment found, payment : '. json_encode($queryPayment), 'info', $action, 200);
                    return response()->json($queryPayment, 200);
                } else {
                    $this->lg('Payment ID is invalid or not found', 'warning', $action, 404);
                    return response()->json('Payment ID is invalid or not found', 404);
                }
            } else {
                $this->lg('Payment information is not valid to check', 'warning', $action, 422);
                return response()->json('Payment information is not valid to check', 422);
            }
        } catch (\Exception $e) {
            // Crash Reporting
            $this->lg($e, 'error', $action, 500);
            return response()->json($this->experDifficulties, 500);
        }
    }

    /**
     * Fetched missed transaction
     * Get transaction details from bKash PGW Search transaction API
     *
     * @param Request $request
     * @param string $reference
     * @return JsonResponse
     * */
    function missed(Request $request, string $reference)
    {
        $action = "MISSED PAYMENT";
        try {
            // Permission check
            if (!auth()->user()->can('payments.missed')) {
                $this->lg($this->accessDenied . ', PERMISSION: payments.missed', 'alert', $action, 403);
                return response()->json($this->accessDenied, 403);
            }


            if ($reference && is_string($reference)) { // must be string

                $this->lg("Fetching Payment using payment trx ID: ". $reference, 'info', $action, 200);

                // Check payment internally
                $isRecordedInSystem = Payment::query()->whereTrxId($reference)->authorized()->first(); //--- include date check
                if ($isRecordedInSystem) {
                    $this->lg('Already exists in system, try searching payment', 'warning', $action, 422);
                    return response()->json("Already exists in system, try searching payment", 422);
                }

                if(!empty(auth()->user()->merchant_id)) {
                    $merchant = Merchant::query()->whereId(auth()->user()->merchant_id)->first(); // need to update this

                    // Not exists in system, fetch from server
                    $fetchPayment = $this->fetchPayment($reference, $merchant);
                    if ($fetchPayment) {
                        $this->lg('Payment found, payment: '. json_encode($fetchPayment), 'info', $action, 200);
                        return response()->json($fetchPayment, 200);
                    } else {
                        $this->lg($this->fetchingError, 'warning', $action, 404);
                        return response()->json($this->fetchingError, 404);
                    }
                } else {
                    $this->lg('User of merchant can use this feature!', 'warning', $action, 422);
                    return response()->json('User of merchant can use this feature!', 422);
                }
            } else {
                $this->lg('Transaction ID is invalid or not found', 'warning', $action, 422);
                return response()->json('Transaction ID is invalid or not found', 422);
            }
        } catch (\Exception $e) {
            // Crash Reporting
            $this->lg($e, 'error', $action, 500);
            return response()->json($this->experDifficulties, 500);
        }
    }

    /**
     * All payments
     * Get all payments list
     *
     * @param Request $request
     * @return JsonResponse
     * */
    function allPayments(Request $request)
    {
        $action = "ALL PAYMENTS";
        try {
            // Permission check
            if ( !$request->user()->hasAnyPermission(['payments.list','payments.view']) ) {
                $this->lg($this->accessDenied . ', PERMISSIONS: payments.list, payments.view', 'alert', $action, 403);
                return response()->json($this->accessDenied, 403);
            }
            // Columns
            /*$selectedColumns = [
                'id',
                'trx_id',
                'sender_account_no',
                'amount',
                'currency',
                'transaction_datetime',
                'transactionReference',
                'merchant_ref',
                'merchant_id'
            ];*/

            $payments = Payment::query()
                ->join('merchants', 'payments.merchant_id', 'merchants.id');

            // If user searched for trx_id or sender_account_no
            if (!empty(request()->get("search"))) {
                $search = strip_tags(request()->get("search"));
//                $payments = $payments->whereTrxIdOrSenderAccountNo($search, $search);
                $payments = $payments->where(function($q) use($search) {
                    return $q->where('payments.trx_id', $search)
                        ->orWhere('payments.sender_account_no', $search)
                        ->orWhere('merchants.account_no', $search);
                });
            }

            $queryPayments = $payments
                // ->with('merchant_details')
                ->with(array('merchant_details'=>function($query){
                    $query->select('merchants.id','merchants.name', 'merchants.account_no');
                }))
//                ->with('reference_added_by')
                ->with(array('reference_added_by' => function($query){
                    $query->select('users.id','users.name', 'users.username');
                }))
                ->api()
                ->authorized()
                ->orderBy('payments.id', 'desc')
                ->paginate();

            if (!$queryPayments->isEmpty()) {
                $this->lg('Payment found, payment: '. json_encode($queryPayments), 'info', $action, 200);
                return response()->json($queryPayments, 200);
            } else {
                $this->lg('Payment list is not available now', 'warning', $action, 404);
                return response()->json('Payment list is not available now', 404);
            }
        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500);
            return response()->json($this->experDifficulties, 500);
        }
    }

    /**
     * Download Excel of Payments
     * Search by a date range or download whole payment list
     *
     * @param Request $request
     * @return mixed
     *
     * @throws \Illuminate\Validation\ValidationException
     * */
    function downloadReport(Request $request)
    {
        $action = "DOWNLOAD REPORT";
        // Check validation
        $this->validate($request, [
            'start' => 'required',
            'end' => 'required'
        ]);

        // Validation passed
        $start = $request->get('start');
        $end = $request->get('end');

        try {
            // Permission check
            if (!auth()->user()->can('payments.download')) {
                $this->lg($this->accessDenied . ', PERMISSION: payments.download', 'alert', $action, 403);
                return response()->json($this->accessDenied, 403);
            }


            $start_date = Carbon::parse($start);
            $end_date = Carbon::parse($end);

            $isThreeMonthsOld = $start_date->toDate() > Carbon::now()->subMonths(3)->toDate();
            $isNotInFuture = $end_date->toDate() <= Carbon::now()->toDate();

            if ($isThreeMonthsOld and $isNotInFuture) {
                // Fetch Payment
                $payments = Payment::query()
                    ->whereBetween('transaction_datetime',
                        [$start_date->toDateString(), $end_date->toDateString()])
                    ->with(array('merchant_details'=>function($query){
                        $query->select('merchants.id','merchants.name', 'merchants.account_no');
                    }))
                    ->api()
                    ->authorized()->get();

                if ($payments) {
                    $this->lg('Report downloaded successfully', 'info', $action, 200);
                    return Excel::download(new PaymentReport($payments), 'all_payments.xlsx');
                } else {
                    $this->lg('No data available to download', 'warning', $action, 404);
                    return response()->json("No data available to download", 422);
                }
            } else {
                $this->lg('Date range is out of allowed range', 'warning', $action, 404);
                return response()->json("Date range is out of allowed range", 422);
            }

        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500);
            return response()->json($this->experDifficulties, 500);
        }

//         return Excel::download(new SampleReportExport(), 'invoices.xlsx');
    }

    /**
     * Fetch A Payment
     * Fetch a payment using Transaction ID from bKash PGW
     *
     * @param string $reference
     * @param $merchant
     * @return mixed
     * */
    public function fetchPayment($reference, $merchant)
    {
        $action = "FETCH PAYMENT";
        try {
            // $model ...
            if ($merchant) {
                $bkash = new BkashController($merchant);
                $resp = $bkash->searchTransaction($reference);

                if (is_array($resp) && isset($resp['trxID'])) { // Payment information fetched

                    // Got the payment, Insert in DB
                    $payment = new Payment();
                    $payment->sender_account_no = isset($resp['customerMsisdn']) ? substr($resp['customerMsisdn'], -11) : '';
                    $payment->receiver_account_no = isset($resp['organizationShortCode']) ? substr($resp['organizationShortCode'], -11) : '';
                    $payment->amount = isset($resp['amount']) ? (float)$resp['amount'] : '';
                    $payment->trx_id = isset($resp['trxID']) ? $resp['trxID'] : '';
                    $payment->merchant_id = $merchant->id;
                    $payment->currency = isset($resp['currency']) ? $resp['currency'] : '';
                    $payment->transaction_datetime = isset($resp['completedTime']) ?
                        Carbon::createFromFormat('Y-m-d H:i:s',
                            str_replace('T', ' ', str_replace(":000 GMT+0600", "", $resp['completedTime']))
                        )->setTimezone('Asia/Dhaka')->toDateTimeString() : '';
                    $payment->transactionType = isset($resp['transactionType']) ? $resp['transactionType'] : '';
                    $payment->transactionReference = isset($resp['transactionReference']) ? $resp['transactionReference'] : '';
                    $payment->save();
                    if ($payment) {
                        $this->lg($payment, 'info', $action, 200);
                        return Payment::query()->api()->with(array('merchant'=>function($query){
                            $query->select('id','name', 'account_no');
                        }))->find($payment->id); // fetching successful
                    } else {
                        $this->lg("Payment insertion error", 'warning', $action, 422, 'internal');
                        $this->fetchingError = "Payment insertion error";
                    }
                } else {
                    $this->lg("This payment is not available in bKash system, " . $bkash->getLastError(),
                        'warning', $action, 422, 'internal');
                    $this->fetchingError = "This payment is not available in bKash system, " . $bkash->getLastError();
                }
            } else {
                $this->lg("Merchant info not found", 'warning', $action, 422, 'internal');
                $this->fetchingError = "Merchant info not found";
            }
        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500);
            $this->fetchingError = $this->experDifficulties;
        }
        return false;
    }


    /**
     * Update Payment
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * */
    public function update(Request $request, $id)
    {
        $action = "UPDATE PAYMENT";
        $this->validate($request, [
            'reference' => 'required|string'
        ]);

        try {
            // Permission check
            if (!auth()->user()->can('payments.update')) {
                $this->lg($this->accessDenied . ', PERMISSION: payments.update', 'alert', $action, 403);
                return response()->json($this->accessDenied, 403);
            }

            $payment = Payment::query()->find($id);
            if ($payment) {
                if (empty($payment->merchant_ref)) {
                    // Checking merchant user to update the tag
                    if(auth()->user()->merchant_id !== $payment->merchant_id) {
                        $this->lg("Have to be an user of merchant to update any information", 'alert', $action, 422);
                        return response()->json("Have to be an user of merchant to update any information", 422);
                    }

                    $payment->merchant_ref = strip_tags($request->get('reference'));
                    $payment->used_by = $request->user()->id;
                    $payment->save();
                    if ($payment) {
                        $this->lg('Updated successfully', 'info', $action, 200);
                        return response()->json("Updated successfully", 200);
                    } else {
                        $this->lg("Cannot update reference, try again", 'warning', $action, 422);
                        return response()->json("Cannot update reference, try again", 422);
                    }
                } else {
                    $this->lg("Reference is used for this payment", 'warning', $action, 422);
                    return response()->json("Reference is used for this payment", 422);
                }
            } else {
                $this->lg("Payment not found", 'warning', $action, 404);
                return response()->json("Payment not found", 404);
            }
        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500);
            return response()->json($this->experDifficulties, 500);
        }
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WebHookController extends Controller
{
    public function addWebHookPayment(Request $request){
        $action = "ADD WEBHOOK PAYMENT";
        $validate = Validator::make($request->all(), [
            'Message' => 'required',
            'handshake' => 'required|in:bkash2020Webh'
        ]);
        if($validate->fails()) {
            $this->lg($validate->errors()->first(), 'warning', $action, 422, 'webhook');
            exit;
        }

        try {
            $this->lg("New webhook payment", 'info', $action, 200, 'webhook');

            if ($request->get('Message')) {
                $resp = json_decode($request->get('Message'), true);

                if (isset($resp['creditShortCode']) && !empty($resp['creditShortCode'])) {
                    $merchant = Merchant::query()->where('account_no', substr($resp['creditShortCode'], -11))
                        ->where('status', 'ACTIVE')->first();
                    if ($merchant) {

                        $oldPayment = Payment::query()->where('trx_id', $resp['trxID'])->first();
                        if (!$oldPayment) {

                            $payment = new Payment();
                            $payment->sender_account_no = isset($resp['debitMSISDN']) ? substr($resp['debitMSISDN'], -11) : '';
                            $payment->receiver_account_no = isset($resp['creditShortCode']) ? substr($resp['creditShortCode'], -11) : '';
                            $payment->amount = isset($resp['amount']) ? (float) $resp['amount'] : '';
                            $payment->trx_id = isset($resp['trxID']) ? $resp['trxID'] : '';
                            $payment->merchant_id = $merchant->id;
                            $payment->currency = isset($resp['currency']) ? $resp['currency'] : '';
                            $payment->transaction_datetime = isset($resp['dateTime']) ?
                                Carbon::createFromFormat(
                                    'YmdHis',
                                    $resp['dateTime']
                                )->setTimezone('Asia/Dhaka')->toDateTimeString() : '';
                            $payment->transactionType = isset($resp['transactionType']) ? $resp['transactionType'] : '';
                            $payment->transactionReference = isset($resp['transactionReference']) ? $resp['transactionReference'] : '';
                            $payment->creditOrganizationName = isset($resp['creditOrganizationName']) ? $resp['creditOrganizationName'] : '';
                            $payment->save();
                            if ($payment) {
                                $this->lg("Payment added successfully", 'info', $action, 201);
                                return "OK";
                            }
                        } else {
                            $this->lg("Webhook: Payment already exists", 'alert', $action, 422);
                            Log::error("Webhook: Payment already exists");
                        }

                        // dd($resp);
                    } else {
                        // nothing to do
                        $this->lg("Webhook: Merchant not found with provided account number", 'warning', $action, 422);
                    }
                } else {
                    // nothing to do
                    $this->lg("Webhook: Response format is not correct", 'warning', $action, 422);
                }
            }
        } catch (\Exception $e) {
            $this->lg($e, 'error', $action, 500);
        }
    }
}

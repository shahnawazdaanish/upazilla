<?php

namespace App\Exports;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PaymentReport implements FromCollection, WithMapping, WithHeadings
{
    protected $payments;
    public function __construct(Collection $payments)
    {
        $this->payments = $payments;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->payments;
    }

    /**
     * @var Payment $payment
     */
    public function map($payment): array
    {
        return [
            $payment->id,
            $payment->trx_id,
            $payment->amount . ' '. $payment->currency,
            $payment->sender_account_no,
            $payment->merchant_details->account_no,
            Date::dateTimeToExcel($payment->transaction_datetime),
            $payment->transactionReference,
            $payment->merchant_ref,
            $payment->merchant->name,
        ];
    }
    public function headings(): array
    {
        return [
            '#',
            'Transaction ID',
            'Amount',
            'Customer Account',
            'Merchant Short Code',
            'Transaction Time',
            'Transaction Reference',
            'Merchant Added Reference',
            'Merchant Name'
        ];
    }
}

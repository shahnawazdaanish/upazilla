<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'transaction_datetime'];

    public function scopeAuthorized($query) {
        $merchant_id = auth()->user()->merchant_id;
        // echo $merchant_id;
        if(!empty($merchant_id)) {
            return $query->where('merchant_id', $merchant_id);
        }
        return $query;
    }
    public function scopeApi($query) {
        return $query->select([
            'payments.id',
            'payments.trx_id',
            'payments.amount',
            'payments.currency',
            'payments.sender_account_no',
            'payments.transaction_datetime',
            'payments.transactionReference',
            'payments.transactionType',
            'payments.merchant_ref',
            'payments.merchant_id']);
    }
    public function merchant() {
        return $this->belongsTo(Merchant::class, 'merchant_id');
    }
    public function merchant_details() {
        return $this->belongsTo(Merchant::class, 'merchant_id')->select(['id', 'name']);
    }
    public function reference_added_by(){
        return $this->belongsTo(User::class, 'payments.used_by', 'users.id');
    }
    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->format('d-M-Y H:i:s');
    }

    protected function serializeDate(\DateTimeInterface $date) {
        return Carbon::parse($date->getTimestamp(), $date->getTimezone())->format('Y-m-d H:i:s');
    }
}

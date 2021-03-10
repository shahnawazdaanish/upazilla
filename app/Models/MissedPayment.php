<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MissedPayment extends Model
{
    use SoftDeletes;

    protected $table = 'missed_payments';

    protected function serializeDate(\DateTimeInterface $date) {
        return Carbon::parse($date->getTimestamp(), $date->getTimezone())->format('Y-m-d H:i:s');
    }

    public function merchant(){
        return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }
    public function scopeAuthorized($query) {
        $merchant_id = auth()->user()->merchant_id;
        if(!empty($merchant_id)) {
            return $query->where('id', $merchant_id);
        }
        return $query;
    }
}

<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Merchant extends Model
{
    use SoftDeletes;

    protected $hidden = ['app_secret', 'bkash_password'];

    protected function serializeDate(\DateTimeInterface $date) {
        return Carbon::parse($date->getTimestamp(), $date->getTimezone())->format('Y-m-d H:i:s');
    }

    public function scopeAuthorized($query) {
        echo 'hi'.$merchant_id = auth()->user()->merchant_id;
        if(!empty($merchant_id)) {
            return $query->where('id', $merchant_id);
        }
        return $query;
    }

    public function users() {
        $this->hasMany(User::class, 'merchant_id');
    }
}

<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->store_id = 1;
            $product->user_id = 1;
            $product->pid = (string) Str::uuid();
            $product->unit()->associate($product->unit);
        });
    }

    public function getIncrementing()
    {
        return false;
    }

    public function getKeyType()
    {
        return 'string';
    }

    public function unit(){
        return $this->belongsTo(Measurement::class, 'unit', 'id');
    }
    protected function serializeDate(\DateTimeInterface $date) {
        return Carbon::parse($date->getTimestamp(), $date->getTimezone())->format('Y-m-d H:i:s');
    }
}

<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected function serializeDate(\DateTimeInterface $date) {
        return Carbon::parse($date->getTimestamp(), $date->getTimezone())->format('Y-m-d H:i:s');
    }
}

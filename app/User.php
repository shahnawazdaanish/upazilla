<?php

namespace App;

use App\Models\Merchant;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens,Notifiable, HasRoles;

    protected $guard_name = 'user';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected function serializeDate(\DateTimeInterface $date) {
        return Carbon::parse($date->getTimestamp(), $date->getTimezone())->format('Y-m-d H:i:s');
    }



    function merchant() {
        return $this->belongsTo(Merchant::class, 'merchant_id', 'id');
    }

    public function scopeApi($query) {
        return $query->select([
            'id',
            'name',
            'email',
            'username',
            'created_at',
            'updated_at',
            'merchant_id',
            'status'
        ]);
    }

    public function scopeAuthorized($query) {
        $merchant_id = auth()->user()->merchant_id;
        if(!empty($merchant_id)) {
            return $query->where('merchant_id', $merchant_id);
        }
        return $query;
    }
}

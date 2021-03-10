<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasApiTokens,Notifiable, HasRoles;

    protected $table = "admin_users";

    protected $guard_name = 'admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'username', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        // 'email_verified_at' => 'datetime',
    ];

    protected function serializeDate(\DateTimeInterface $date) {
        return Carbon::parse($date->getTimestamp(), $date->getTimezone())->format('Y-m-d H:i:s');
    }

    public function scopeApi($query) {
        return $query->select([
            'id',
            'name',
            'username',
            'created_at',
            'updated_at',
            'status'
        ]);
    }

    public function store(){
        return $this->belongsTo(Store::class);
    }
}

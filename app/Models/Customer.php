<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Notifications\ResetPassword;

class Customer extends Authenticatable implements CanResetPassword
{
    use HasApiTokens;
    use Notifiable;
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'gender',
        'avatar',
        'provider',
        'provider_id',
        'email_verified_at',
        'verification_code',
        'verification_code_expires_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'provider_id',
        'verification_code',
        'verification_code_expires_at',
        'created_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function scopeVerified($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function getAvatarAttribute($value)
    {
        return $value ? url('storage/' . $value) : null;
    }
}

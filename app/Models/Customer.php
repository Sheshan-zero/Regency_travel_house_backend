<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Notifications\CustomerResetPasswordNotification;

class Customer extends Authenticatable
{

    use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = ['full_name', 'email', 'password', 'phone', 'address', 'country_of_residence', 'nic', 'date_of_birth', 'loyalty_points', 'per_for_news', 'per_for_loyalty', 'per_for_privacy'];

    protected $hidden = ['password'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    public function loyaltyHistory()
    {
        return $this->hasMany(Loyalty::class);
    }

    public function loyalty()
    {
        return $this->hasOne(Loyalty::class);
    }
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomerResetPasswordNotification($token));
    }
}

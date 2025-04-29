<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loyalty extends Model
{
    protected $fillable = ['customer_id', 'points_earned', 'points_redeemed', 'membership_tier', 'last_updated','donate'];

    protected $table = 'loyalties';

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function isExpired(): bool
    {
        return now()->diffInYears($this->last_updated) >= 2;
    }

    public function scopeNotExpired($query)
    {
        return $query->where('last_updated', '>', now()->subYears(2));
    }


}


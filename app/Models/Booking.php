<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = ['customer_id', 'package_id', 'booking_date', 'travel_date', 'number_of_travelers', 'total_price', 'status', 'quote_id', 'payment_reference', 'payment_verified'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}

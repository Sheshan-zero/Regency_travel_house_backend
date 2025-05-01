<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $fillable = ['destination_id', 'title','special_package', 'country','description','activities','include','exclude', 'price_per_person', 'duration_days', 'start_date', 'end_date', 'available_slots', 'image_url', 'is_featured','category','footprint'];

    public function destination()
    {
        return $this->belongsTo(Destination::class);
    }

    public function itineraries()
    {
        return $this->hasMany(Itinerary::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

}

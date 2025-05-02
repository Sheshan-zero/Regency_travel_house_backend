<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Itinerary extends Model
{
    protected $fillable = [
        'package_id', 'day_number', 'title', 'description', 'location',
        'latitude', 'longitude', 'zoom_level', 'image_url'
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}


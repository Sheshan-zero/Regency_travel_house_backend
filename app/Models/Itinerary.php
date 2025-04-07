<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Itinerary extends Model
{
    protected $fillable = ['package_id', 'day_number', 'title', 'description', 'location'];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}


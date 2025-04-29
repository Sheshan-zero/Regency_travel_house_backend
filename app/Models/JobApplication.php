<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = ['full_name', 'email', 'phone', 'position_applied', 'cv_path','cover_letter'];

}

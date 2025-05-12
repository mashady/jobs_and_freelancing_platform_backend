<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    public function freelancer()
    {
        return $this->belongsTo(FreelancerProfile::class);
    }
}

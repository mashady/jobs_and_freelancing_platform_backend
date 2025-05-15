<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkExperience extends Model
{
     protected $fillable = [
    'freelancer_id',
    'company_name',
    'position',
    'description',
    'start_date',
    'end_date',
];
    public function freelancer()
    {

        return $this->belongsTo(FreelancerProfile::class, 'freelancer_id');
    }
}

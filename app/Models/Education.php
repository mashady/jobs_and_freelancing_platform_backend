<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    protected $table = 'educations';
     protected $fillable = [
    'freelancer_id',
    'description',
    'institution',
    'degree',
    'field_of_study',
    'start_date',
    'end_date'
];
    public function freelancer()
    {
        return $this->belongsTo(FreelancerProfile::class, 'freelancer_id');
    }
}

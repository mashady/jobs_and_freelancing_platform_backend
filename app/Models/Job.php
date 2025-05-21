<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    protected $fillable = [
        'employer_id',
        'category_id',
        'position_name',
        'location',
        'offered_salary',
        'job_description',
        'experience_years',
        'status',
        'type',
        'job_responsibility'
    ];
    public function employer()
    {
        return $this->belongsTo(EmployerProfile::class, 'employer_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

   /*  public function responsibilities()
    {
        return $this->hasMany(JobResponsibility::class);
    } */

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'job_skills');
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

}

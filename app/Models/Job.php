<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    public function employer()
    {
        return $this->belongsTo(EmployerProfile::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function responsibilities()
    {
        return $this->hasMany(JobResponsibility::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'job_skills');
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
   public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function freelancers()
    {
        return $this->belongsToMany(FreelancerProfile::class, 'freelancer_skills')
            ->withPivot('proficiency_level', 'years_experience');
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class, 'project_skills');
    }

    public function jobs()
    {
        return $this->belongsToMany(Job::class, 'job_skills');
    }
}

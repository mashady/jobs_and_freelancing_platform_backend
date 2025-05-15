<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FreelancerProfile extends Model
{
protected $fillable = [
    'user_id',
    'city',
    'address',
    'email',
    'bio',
    'gender',
    'birth_date',
    'job_title',
    'min_hourly_rate',
    'max_hourly_rate',
    'category_id',
    'english_level',
    'payment_method',
    'resume'
];
     public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function freelancerSkills()
    {
        return $this->belongsToMany(Skill::class, 'freelancer_skills');
    }
    public function workExperiences()
    {
        return $this->hasMany(WorkExperience::class, 'freelancer_id');
    }

    public function educations()
    {
        return $this->hasMany(Education::class, 'freelancer_id');
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'freelancer_skills', 'freelancer_id', 'skill_id');
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'description', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function freelancers()
    {
        return $this->hasMany(FreelancerProfile::class, 'freelancer_id');
    }

    public function employers()
    {
        return $this->hasMany(EmployerProfile::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function skills()
    {
        return $this->hasMany(Skill::class);
    }
}

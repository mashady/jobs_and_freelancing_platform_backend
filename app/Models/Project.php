<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'employer_id',
        'category_id',
        'title',
        'description',
        'budget_min',
        'budget_max',
        'duration',
        'english_level',
        'project_language',
        'general_level',
        'status',
        'deadline',
        'project_type'
    ];

    public function employer()
    {
        return $this->belongsTo(EmployerProfile::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function attachments()
    {
        return $this->hasMany(ProjectAttachment::class);
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'project_skills');
    }

    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}

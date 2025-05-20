<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = [
        'job_id',
        'user_id',
        'cover_letter',
        'resume_path',
        'status'
    ];
    public function job()
    {
        return $this->belongsTo(Job::class);
    }
    public function comments()
{
    return $this->hasMany(JobComment::class);
}
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

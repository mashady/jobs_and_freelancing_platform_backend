<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function freelancer()
    {
        return $this->belongsTo(FreelancerProfile::class);
    }

    public function employer()
    {
        return $this->belongsTo(EmployerProfile::class);
    }

    public function proposal()
    {
        return $this->belongsTo(Proposal::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}

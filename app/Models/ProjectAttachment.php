<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectAttachment extends Model
{
     protected $fillable = [
        'project_id',
        'file_name',
        'file_path',
        'file_size',
        'file_type'
    ];
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employer_id' => $this->employer_id,
            'category_id' => $this->category_id,
            'title' => $this->title,
            'description' => $this->description,
            'budget_min' => $this->budget_min,
            'budget_max' => $this->budget_max,
            'duration' => $this->duration,
            'english_level' => $this->english_level,
            'project_language' => $this->project_language,
            'general_level' => $this->general_level,
            'status' => $this->status,
            'deadline' => $this->deadline,
            'project_type' => $this->project_type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
    
        ];
    }
}

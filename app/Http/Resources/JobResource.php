<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobResource extends JsonResource
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
            'position_name' => $this->position_name,
            'location' => $this->location,
            'offered_salary' => $this->offered_salary,
            'job_description' => $this->job_description,
            'experience_years' => $this->experience_years,
            'status' => $this->status,
            'type' => $this->type,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

    }
}

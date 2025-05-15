<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FreelancerProfileResource extends JsonResource
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
            'user_id' => $this->user_id,
            'city' => $this->city,
            'address' => $this->address,
            'email' => $this->email,
            'bio' => $this->bio,
            'gender' => $this->gender,
            'birth_date' => $this->birth_date,
            'job_title' => $this->job_title,
            'min_hourly_rate' => $this->min_hourly_rate,
            'max_hourly_rate' => $this->max_hourly_rate,
            'category_id' => $this->category_id,
            'english_level' => $this->english_level,
            'payment_method' => $this->payment_method,
            'resume' => $this->resume,
            'skills' => $this->skills->pluck('name'),
            'work_experiences' => $this->workExperiences,
            'educations' => $this->educations,
        ];
    }
}

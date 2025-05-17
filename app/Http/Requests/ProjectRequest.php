<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employer_id' => 'required|exists:employer_profiles,id',
            'category_id' => 'required|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget_min' => 'required|numeric|min:0',
            'budget_max' => 'required|numeric|gt:budget_min',
            'duration' => 'required|integer|min:1',
            'english_level' => 'required|in:beginner,intermediate,fluent,native',
            'project_language' => 'required|string|max:255',
            'general_level' => 'required|in:entry,intermediate,expert',
            'status' => 'sometimes|in:open,in-progress,completed,cancelled',
            'deadline' => 'required|date|after:today',
            'project_type' => 'sometimes|in:fixed,hourly',
        ];
    }
}

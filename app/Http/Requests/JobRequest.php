<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobRequest extends FormRequest
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
            'category_id' => 'nullable|exists:categories,id',
            'position_name' => 'required|string|max:255',
            'location' => 'required|string|max:255',
            'offered_salary' => 'required|numeric|min:0',
            'job_description' => 'required|string',
            'experience_years' => 'required|integer|min:0',
            'status' => 'required|in:open,closed',
            'type' => 'required|in:fulltime,parttime,contract',
        ];
    }

}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFreelancerProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    /* public function authorize(): bool
    {
        return false;
    } */

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'city' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'email' => ['required', 'email', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'gender' => ['nullable', 'in:male,female,other'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'job_title' => ['required', 'string', 'max:255'],
            'min_hourly_rate' => ['required', 'numeric', 'min:0'],
            'max_hourly_rate' => ['required', 'numeric', 'gte:min_hourly_rate'],
            'category_id' => ['required', 'exists:categories,id'],
            'english_level' => ['required', 'in:beginner,intermediate,advanced,fluent,native'],
            'payment_method' => ['required', 'in:paypal,bank_transfer,crypto,other'],
            'resume' => ['nullable', 'url', 'max:2048'], // 2MB max
            'work_experiences' => ['required', 'array'],
            'work_experiences.*.company_name' => ['required', 'string', 'max:255'],
            'work_experiences.*.position' => ['required', 'string', 'max:255'],
            'work_experiences.*.description' => ['nullable', 'string', 'max:1000'],
            'work_experiences.*.start_date' => ['required', 'date', 'before_or_equal:today'],
            'work_experiences.*.end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'work_experiences.*.currently_working' => ['required', 'boolean'],
            'educations' => ['required', 'array'],
            'educations.*.institution' => ['required', 'string', 'max:255'],
            'educations.*.degree' => ['required', 'string', 'max:255'],
            'educations.*.field_of_study' => ['nullable', 'string', 'max:1000'],
            'educations.*.start_date' => ['required', 'date', 'before_or_equal:today'],
            'educations.*.end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'skills' => ['required', 'array'],
            'skills.*' => ['string', 'max:255'],
        ];
    }
}

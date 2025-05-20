<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobCommentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'job_id' => 'required|exists:jobs,id',
            'content' => 'required|string|max:500',
        ];
    }
}

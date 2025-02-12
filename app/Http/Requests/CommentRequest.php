<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }
    public function rules(): array
    {
        return [
            'content' => 'required|string|max:1000',
        ];
    }
    public function messages()
    {
        return [
            'content.required' => 'The comment content field is required.',
        ];
    }
}
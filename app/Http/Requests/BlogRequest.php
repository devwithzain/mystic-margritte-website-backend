<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BlogRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:258',
            'description' => 'required|string',
            'short_description' => 'required|string',
            'category' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
        ];
    }
}
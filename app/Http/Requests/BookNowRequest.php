<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookNowRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'services' => 'required|string|max:255',
            'healingTopics' => 'required|string|max:255',
            'preferredTime' => 'required|string|max:255',
            'cityAndState' => 'required|string|max:255',
            'specialMessage' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'The name field is required.',
            'lastName.required' => 'The lastName number field is required.',
            'services.required' => 'The services number field is required.',
            'healingTopics.required' => 'The healingTopics number field is required.',
            'preferredTime.required' => 'The preferredTime number field is required.',
            'cityAndState.required' => 'The city number field is required.',
            'specialMessage.required' => 'The Special Message field is required.',
        ];
    }
}
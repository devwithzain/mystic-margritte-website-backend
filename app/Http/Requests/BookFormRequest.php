<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookFormRequest extends FormRequest
{
    public function rules()
    {
        return [
            'birth_date' => 'required|date',
            'birth_time' => 'required|string',
            'birth_place' => 'required|string|max:255',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'country' => 'required|string|max:255',
            'street_address' => 'required|string|max:255',
            'town_city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip' => 'required|string|max:20',
            'notes' => 'nullable|string',
            'time_slot_id' => 'required|exists:time_slots,id',
            'meeting_link' => 'nullable|url|max:255',
            'timezone' => 'required|string|max:255',
        ];
    }

    public function messages()
    {
        return [
            'birth_date.required' => 'The birth date field is required.',
            'birth_time.required' => 'The birth time field is required.',
            'birth_place.required' => 'The birth place field is required.',
            'first_name.required' => 'The first name field is required.',
            'last_name.required' => 'The last name field is required.',
            'phone.required' => 'The phone field is required.',
            'email.required' => 'The email field is required.',
            'country.required' => 'The country field is required.',
            'street_address.required' => 'The street address field is required.',
            'town_city.required' => 'The town/city field is required.',
            'state.required' => 'The state field is required.',
            'zip.required' => 'The zip code field is required.',
            'time_slot_id.required' => 'The time slot field is required.',
            'timezone.required' => 'The timezone field is required.',
        ];
    }
}
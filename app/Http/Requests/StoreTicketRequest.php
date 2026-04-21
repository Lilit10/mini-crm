<?php

namespace App\Http\Requests;

use App\Rules\UniqueTicketPerDayForEmail;
use App\Rules\UniqueTicketPerDayForPhone;
use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone_e164' => [
                'required',
                'string',
                'regex:/^\\+[1-9]\\d{1,14}$/',
                app(UniqueTicketPerDayForPhone::class),
            ],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                app(UniqueTicketPerDayForEmail::class),
            ],
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:10000'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone_e164.regex' => 'The phone number must be in E.164 format (e.g. +15551234567).',
            'email.email' => 'The email must be a valid email address.',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

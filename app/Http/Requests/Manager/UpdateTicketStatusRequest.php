<?php

namespace App\Http\Requests\Manager;

use App\Enums\TicketStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', 'string', Rule::in(array_map(fn (TicketStatus $s) => $s->value, TicketStatus::cases()))],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}

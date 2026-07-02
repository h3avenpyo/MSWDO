<?php

namespace App\Http\Requests\SocialCase;

use Illuminate\Foundation\Http\FormRequest;

class ClientSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['nullable', 'string', 'max:255'],
            'client_id' => ['nullable', 'integer', 'exists:mswdo_social_case.clients,id'],
            'birthdate' => ['nullable', 'date'],
        ];
    }
}

<?php

namespace App\Http\Requests\SocialCase;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSocialCaseStudyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['Open', 'In Progress', 'Closed'])],
            'summary' => ['nullable', 'string', 'max:2000'],
            'interview_date' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }
}

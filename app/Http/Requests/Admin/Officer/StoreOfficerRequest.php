<?php

namespace App\Http\Requests\Admin\Officer;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Wcpu\User as WcpuUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOfficerRequest extends FormRequest
{
    public function messages(): array
    {
        return [
            'email_verification_code.required' => \App\Http\Controllers\Admin\Auth\OfficerEmailVerificationController::REQUIRED_MESSAGE,
            'email_verification_code.digits' => \App\Http\Controllers\Admin\Auth\OfficerEmailVerificationController::REQUIRED_MESSAGE,
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) {
                if (in_array($this->input('role'), ['bcpc_admin', 'swd_admin'], true)) {
                    return;
                }
                $exists = User::whereRaw('LOWER(name) = ?', [strtolower($value)])->exists();
                if ($exists) {
                    $fail('An officer with this name already exists.');
                }
            }],
            'email' => ['required', 'email', 'max:255', 'unique:users,email',
                ... (in_array($this->input('role'), ['bcpc_admin', 'swd_admin'], true)
                    ? [Rule::unique(WcpuUser::class, 'email')]
                    : []),
            ],
            'password' => ['required', 'confirmed', 'min:8'],
            'email_verification_code' => ['required', 'digits:6'],
            'role' => ['required', Rule::in([...array_column(UserRole::cases(), 'value'), 'bcpc_admin', 'swd_admin'])],
            'phone' => ['nullable', 'string', 'max:20', new \App\Rules\ContactNumber(null, 20)],
            'status' => ['nullable', 'in:active,inactive'],
            'signature_position' => ['nullable', 'in:osca_head,mswdo_officer,mswdo_staff'],
            'signature_image' => ['nullable', 'image', 'max:2048'],
        ];
    }
}

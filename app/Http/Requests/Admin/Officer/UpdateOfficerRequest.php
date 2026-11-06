<?php

namespace App\Http\Requests\Admin\Officer;

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Wcpu\User as WcpuUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOfficerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $officerId = $this->route('id');

        return [
            'username' => ['nullable', 'string', 'min:3', 'max:255', 'regex:/\A[A-Za-z0-9_.-]+\z/',
                Rule::unique(User::class, 'username')->ignore($officerId),
                Rule::unique(WcpuUser::class, 'username'),
                Rule::unique(User::class, 'email')->ignore($officerId),
                Rule::unique(WcpuUser::class, 'email'),
            ],
            'name' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) use ($officerId) {
                $exists = User::whereRaw('LOWER(name) = ?', [strtolower($value)])
                    ->where('id', '!=', $officerId)
                    ->exists();
                if ($exists) {
                    $fail('An officer with this name already exists.');
                }
            }],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($officerId)],
            'role' => ['required', Rule::enum(UserRole::class)],
            'phone' => ['nullable', 'string', 'max:20', new \App\Rules\ContactNumber(null, 20)],
            'status' => ['nullable', 'in:active,inactive'],
            'signature_position' => ['nullable', 'in:osca_head,mswdo_officer,mswdo_staff'],
            'signature_image' => ['nullable', 'image', 'max:2048'],
        ];
    }
}

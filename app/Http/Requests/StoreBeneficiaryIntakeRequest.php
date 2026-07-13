<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBeneficiaryIntakeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_client_beneficiary' => $this->boolean('is_client_beneficiary'),
        ]);
    }

    public function rules(): array
    {
        return [
            // Processing Information
            'client_id' => ['nullable', 'integer', 'exists:mswdo_social_case.clients,id'],
            'control_number' => ['required', 'string', 'unique:mswdo_social_case.beneficiary_intakes,control_number'],
            'date_processed' => ['required', 'date'],
            'encoder' => ['required', 'string'],
            
            // Client Information
            'client_last_name' => ['required', 'string', 'max:255'],
            'client_first_name' => ['required', 'string', 'max:255'],
            'client_middle_name' => ['nullable', 'string', 'max:255'],
            'client_birthday' => ['required', 'date'],
            'client_age' => ['required', 'integer', 'min:0'],
            'client_sex' => ['required', 'in:Male,Female'],
            'client_civil_status' => ['required', 'string'],
            'client_address' => ['required', 'string'],
            'client_barangay' => ['required', 'string'],
            'client_contact_number' => ['required', 'string', 'max:20'],
            'client_occupation' => ['nullable', 'string', 'max:255'],
            'client_monthly_income' => ['nullable', 'numeric', 'min:0'],
            
            // Beneficiary Information
            'is_client_beneficiary' => ['boolean'],
            'beneficiary_last_name' => ['required_if:is_client_beneficiary,false', 'nullable', 'string', 'max:255'],
            'beneficiary_first_name' => ['required_if:is_client_beneficiary,false', 'nullable', 'string', 'max:255'],
            'beneficiary_middle_name' => ['nullable', 'string', 'max:255'],
            'beneficiary_birthday' => ['required_if:is_client_beneficiary,false', 'nullable', 'date'],
            'beneficiary_age' => ['required_if:is_client_beneficiary,false', 'nullable', 'integer', 'min:0'],
            'beneficiary_sex' => ['required_if:is_client_beneficiary,false', 'nullable', 'in:Male,Female'],
            'beneficiary_barangay' => ['required_if:is_client_beneficiary,false', 'nullable', 'string'],
            'beneficiary_relationship' => ['required_if:is_client_beneficiary,false', 'nullable', 'string'],
            
            // Medical Condition
            'medical_conditions' => ['nullable', 'array'],
            'medical_condition_other' => ['required_if:medical_conditions.*,Other', 'nullable', 'string', 'max:255'],
            
            // Service Provided
            'service_provided' => ['required', 'string'],
            
            // Purpose
            'purpose' => ['required', 'string'],
            'purpose_other' => ['required_if:purpose,Others', 'nullable', 'string', 'max:255'],
            
            // Submitted To
            'submitted_to' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'beneficiary_last_name.required_if' => 'Beneficiary last name is required when client is not the beneficiary.',
            'beneficiary_first_name.required_if' => 'Beneficiary first name is required when client is not the beneficiary.',
            'beneficiary_birthday.required_if' => 'Beneficiary birthday is required when client is not the beneficiary.',
            'beneficiary_age.required_if' => 'Beneficiary age is required when client is not the beneficiary.',
            'beneficiary_sex.required_if' => 'Beneficiary sex is required when client is not the beneficiary.',
            'beneficiary_barangay.required_if' => 'Beneficiary barangay is required when client is not the beneficiary.',
            'beneficiary_relationship.required_if' => 'Beneficiary relationship is required when client is not the beneficiary.',
            'medical_condition_other.required_if' => 'Please specify the other medical condition.',
            'purpose_other.required_if' => 'Please specify the other purpose.',
        ];
    }
}

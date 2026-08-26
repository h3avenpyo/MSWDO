<?php

namespace App\Http\Requests\SocialCase;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreBeneficiaryIntakeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $benBday = $this->normalizeDate($this->beneficiary_birthday);
        $repBday = $this->normalizeDate($this->rep_birthday);

        $this->merge([
            'has_representative' => $this->boolean('has_representative'),
            'is_client_beneficiary' => $this->boolean('is_client_beneficiary'),
            'beneficiary_birthday' => $benBday,
            'beneficiary_age' => $this->beneficiary_age ?? (!empty($benBday) ? Carbon::parse($benBday)->age : null),
            'beneficiary_city' => $this->beneficiary_city ?? 'Silang',
            'beneficiary_province' => $this->beneficiary_province ?? 'Cavite',
            'beneficiary_region' => $this->beneficiary_region ?? 'Region IV-A',
            'rep_birthday' => $repBday,
            'rep_age' => $this->rep_age ?? (!empty($repBday) ? Carbon::parse($repBday)->age : null),
            'date_processed' => $this->normalizeDate($this->date_processed),
        ]);
    }

    private function normalizeDate(?string $dateStr): ?string
    {
        if (empty($dateStr)) {
            return null;
        }
        if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $dateStr)) {
            try {
                return Carbon::createFromFormat('m/d/Y', $dateStr)->format('Y-m-d');
            } catch (\Throwable $e) {
                return $dateStr;
            }
        }
        return $dateStr;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['nullable', 'integer', 'exists:clients,id'],
            'control_number' => ['required', 'string', 'max:50', 'unique:beneficiary_intakes,control_number'],
            'client_type' => ['required', 'in:New,Returning'],
            'date_processed' => ['nullable', 'date'],
            'time_start' => ['nullable', 'string', 'max:20'],
            'time_end' => ['nullable', 'string', 'max:20'],
            'encoder' => ['nullable', 'integer', 'exists:users,id'],
            'is_client_beneficiary' => ['boolean'],

            // Beneficiary Information (Required)
            'beneficiary_last_name' => ['required', 'string', 'max:100'],
            'beneficiary_first_name' => ['required', 'string', 'max:100'],
            'beneficiary_middle_name' => ['nullable', 'string', 'max:100'],
            'beneficiary_extension_name' => ['nullable', 'string', 'max:20'],
            'beneficiary_street_address' => ['required', 'string', 'max:255'],
            'beneficiary_barangay' => ['required', 'string', 'max:100'],
            'beneficiary_city' => ['required', 'string', 'max:100'],
            'beneficiary_province' => ['required', 'string', 'max:100'],
            'beneficiary_region' => ['required', 'string', 'max:100'],
            'beneficiary_contact_number' => ['required', 'string', 'max:50'],
            'beneficiary_birthday' => ['required', 'date'],
            'beneficiary_age' => ['required', 'integer', 'min:0', 'max:150'],
            'beneficiary_sex' => ['required', 'in:Male,Female'],
            'beneficiary_civil_status' => ['required', 'string', 'max:50'],
            'beneficiary_occupation' => ['nullable', 'string', 'max:150'],
            'beneficiary_monthly_salary' => ['nullable', 'numeric', 'min:0'],
            'beneficiary_category' => ['nullable', 'string', 'max:100'],
            'beneficiary_category_other' => ['nullable', 'string', 'max:150'],
            'beneficiary_categories' => ['nullable', 'array'],

            // Representative Information (Optional)
            'has_representative' => ['boolean'],
            'rep_last_name' => ['nullable', 'required_if:has_representative,1,true', 'string', 'max:100'],
            'rep_first_name' => ['nullable', 'required_if:has_representative,1,true', 'string', 'max:100'],
            'rep_middle_name' => ['nullable', 'string', 'max:100'],
            'rep_extension_name' => ['nullable', 'string', 'max:20'],
            'rep_street_address' => ['nullable', 'required_if:has_representative,1,true', 'string', 'max:255'],
            'rep_barangay' => ['nullable', 'required_if:has_representative,1,true', 'string', 'max:100'],
            'rep_city' => ['nullable', 'string', 'max:100'],
            'rep_province' => ['nullable', 'string', 'max:100'],
            'rep_region' => ['nullable', 'string', 'max:100'],
            'rep_contact_number' => ['nullable', 'required_if:has_representative,1,true', 'string', 'max:50'],
            'rep_birthday' => ['nullable', 'required_if:has_representative,1,true', 'date'],
            'rep_age' => ['nullable', 'required_if:has_representative,1,true', 'integer', 'min:0', 'max:150'],
            'rep_sex' => ['nullable', 'required_if:has_representative,1,true', 'in:Male,Female'],
            'rep_civil_status' => ['nullable', 'required_if:has_representative,1,true', 'string', 'max:50'],
            'rep_occupation' => ['nullable', 'string', 'max:150'],
            'rep_monthly_salary' => ['nullable', 'numeric', 'min:0'],
            'rep_relationship' => ['nullable', 'required_if:has_representative,1,true', 'string', 'max:100'],

            // DSWD Section & Assessment
            'family_composition' => ['nullable', 'array'],
            'social_worker_assessment' => ['nullable', 'string'],
            'recommended_assistance_type' => ['nullable', 'string', 'max:150'],
            'assistance_purpose' => ['nullable', 'string', 'max:255'],
            'recommended_amount' => ['nullable', 'numeric', 'min:0'],
            'interviewed_by' => ['nullable', 'string', 'max:150'],
            'reviewed_by' => ['nullable', 'string', 'max:150'],

            'service_provided' => ['nullable', 'string', 'max:255'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'purpose_other' => ['nullable', 'string', 'max:255'],
            'submitted_to' => ['nullable', 'string', 'max:255'],
            'medical_conditions' => ['nullable', 'array'],
            'medical_condition_other' => ['nullable', 'string', 'max:255'],
        ];
    }
}

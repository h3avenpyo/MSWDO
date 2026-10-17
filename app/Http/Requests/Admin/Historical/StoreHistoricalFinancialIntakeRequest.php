<?php

namespace App\Http\Requests\Admin\Historical;

use App\Models\SocialCase\BeneficiaryIntake;
use App\Services\Financial\FinancialControlNumberService;
use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreHistoricalFinancialIntakeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $benBday = $this->normalizeDate($this->beneficiary_birthday);
        $repBday = $this->normalizeDate($this->rep_birthday);
        $procDate = $this->normalizeDate($this->date_processed);
        $claimDate = $this->normalizeDate($this->claiming_date);

        $benSalary = $this->beneficiary_monthly_salary !== null 
            ? str_replace(',', '', (string)$this->beneficiary_monthly_salary) 
            : null;

        $repSalary = $this->rep_monthly_salary !== null 
            ? str_replace(',', '', (string)$this->rep_monthly_salary) 
            : null;

        $normalizePhone = function ($phone) {
            if ($phone === null || trim((string)$phone) === '') {
                return null;
            }
            $clean = preg_replace('/[\s\-\(\)\.]/', '', (string)$phone);
            if (preg_match('/^(\+63|63)(9\d{9})$/', $clean, $matches)) {
                return '0' . $matches[2];
            }
            return $clean;
        };

        $benPhone = $normalizePhone($this->beneficiary_contact_number);
        $repPhone = $normalizePhone($this->rep_contact_number);

        $assignedControlNumber = null;
        if (!empty($this->control_number) && !BeneficiaryIntake::where('control_number', $this->control_number)->exists()) {
            $assignedControlNumber = $this->control_number;
        } else {
            $assignedControlNumber = FinancialControlNumberService::generate($procDate ? Carbon::parse($procDate) : null);
        }

        $mergeData = [
            'client_type' => $this->client_type ?: 'New',
            'control_number' => $assignedControlNumber,
            'has_representative' => $this->boolean('has_representative'),
            'is_client_beneficiary' => $this->boolean('is_client_beneficiary', true),
            'beneficiary_birthday' => $benBday,
            'beneficiary_age' => $this->beneficiary_age ?? (!empty($benBday) ? Carbon::parse($benBday)->age : null),
            'beneficiary_city' => $this->beneficiary_city ?? 'Silang',
            'beneficiary_province' => $this->beneficiary_province ?? 'Cavite',
            'beneficiary_region' => $this->beneficiary_region ?? 'Region IV-A',
            'rep_birthday' => $repBday,
            'rep_age' => $this->rep_age ?? (!empty($repBday) ? Carbon::parse($repBday)->age : null),
            'date_processed' => $procDate,
            'claiming_date' => $claimDate,
            'claim_status' => $this->claim_status ?: 'Claimed',
        ];

        if ($this->has('beneficiary_contact_number')) {
            $mergeData['beneficiary_contact_number'] = $benPhone;
        }
        if ($this->has('rep_contact_number')) {
            $mergeData['rep_contact_number'] = $repPhone;
        }

        if ($this->has('beneficiary_monthly_salary')) {
            $mergeData['beneficiary_monthly_salary'] = ($benSalary === '' || $benSalary === null) ? null : $benSalary;
        }
        if ($this->has('rep_monthly_salary')) {
            $mergeData['rep_monthly_salary'] = ($repSalary === '' || $repSalary === null) ? null : $repSalary;
        }

        $familyComposition = $this->family_composition;
        if (is_array($familyComposition)) {
            foreach ($familyComposition as &$famMember) {
                if (isset($famMember['salary']) && $famMember['salary'] !== null) {
                    $cleanSalary = str_replace(',', '', (string)$famMember['salary']);
                    $famMember['salary'] = ($cleanSalary === '') ? null : $cleanSalary;
                }
                foreach (['name', 'relationship', 'occupation'] as $txtField) {
                    if (isset($famMember[$txtField])) {
                        $trimmed = trim((string)$famMember[$txtField]);
                        $famMember[$txtField] = ($trimmed === '') ? null : $trimmed;
                    }
                }
            }
            unset($famMember);
            $mergeData['family_composition'] = $familyComposition;
        }

        if ($this->has('beneficiary_occupation')) {
            $benOcc = trim((string)$this->beneficiary_occupation);
            $mergeData['beneficiary_occupation'] = ($benOcc === '') ? null : $benOcc;
        }
        if ($this->has('rep_occupation')) {
            $repOcc = trim((string)$this->rep_occupation);
            $mergeData['rep_occupation'] = ($repOcc === '') ? null : $repOcc;
        }

        $this->merge($mergeData);
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
            'date_processed' => ['required', 'date', 'before_or_equal:today'],
            'time_start' => ['nullable', 'string', 'max:20'],
            'time_end' => ['nullable', 'string', 'max:20'],
            'encoder' => ['nullable', 'integer', 'exists:users,id'],
            'is_client_beneficiary' => ['boolean'],

            // Financial Assistance Amount & Claim Status
            'recommended_amount' => ['required', 'numeric', 'min:0', 'max:9999999.99'],
            'claim_status' => ['nullable', 'string', 'in:Claimed,Pending'],
            'claiming_date' => ['nullable', 'date', 'before_or_equal:today'],

            // Beneficiary Information (Required)
            'beneficiary_last_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^(?![\s\.\'\x{2019}\-]+$)[\p{L}\p{M}\s\.\'\x{2019}\-]+$/u',
            ],
            'beneficiary_first_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^(?![\s\.\'\x{2019}\-]+$)[\p{L}\p{M}\s\.\'\x{2019}\-]+$/u',
            ],
            'beneficiary_middle_name' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^(?![\s\.\'\x{2019}\-]+$)[\p{L}\p{M}\s\.\'\x{2019}\-]+$/u',
            ],
            'beneficiary_extension_name' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^(?![\s\.\'\x{2019}\-]+$)[\p{L}\p{M}\s\.\'\x{2019}\-]+$/u',
            ],
            'beneficiary_street_address' => ['required', 'string', 'max:255'],
            'beneficiary_barangay' => ['required', 'string', 'max:100'],
            'beneficiary_city' => ['required', 'string', 'max:100'],
            'beneficiary_province' => ['required', 'string', 'max:100'],
            'beneficiary_region' => ['required', 'string', 'max:100'],
            'beneficiary_contact_number' => ['required', 'string', 'regex:/^[0-9]{11}$/', new \App\Rules\ContactNumber(11)],
            'beneficiary_birthday' => ['required', 'date'],
            'beneficiary_age' => ['required', 'integer', 'min:0', 'max:150'],
            'beneficiary_sex' => ['required', 'in:Male,Female'],
            'beneficiary_civil_status' => ['required', 'string', 'max:50'],
            'beneficiary_occupation' => [
                'nullable',
                'string',
                'max:150',
                'regex:/^(?!.*[0-9])(?=.*[\p{L}])[\p{L}\p{M}\s\.\'\x{2019}\-\/\&\(\)\,]+$/u',
            ],
            'beneficiary_monthly_salary' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'beneficiary_category' => ['nullable', 'string', 'max:100'],
            'beneficiary_category_other' => ['nullable', 'string', 'max:150'],
            'beneficiary_categories' => ['nullable', 'array'],

            // Representative Information (Optional)
            'has_representative' => ['boolean'],
            'rep_last_name' => [
                'nullable',
                'required_if:has_representative,1,true',
                'string',
                'max:100',
                'regex:/^(?![\s\.\'\x{2019}\-]+$)[\p{L}\p{M}\s\.\'\x{2019}\-]+$/u',
            ],
            'rep_first_name' => [
                'nullable',
                'required_if:has_representative,1,true',
                'string',
                'max:100',
                'regex:/^(?![\s\.\'\x{2019}\-]+$)[\p{L}\p{M}\s\.\'\x{2019}\-]+$/u',
            ],
            'rep_middle_name' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^(?![\s\.\'\x{2019}\-]+$)[\p{L}\p{M}\s\.\'\x{2019}\-]+$/u',
            ],
            'rep_extension_name' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^(?![\s\.\'\x{2019}\-]+$)[\p{L}\p{M}\s\.\'\x{2019}\-]+$/u',
            ],
            'rep_street_address' => ['nullable', 'required_if:has_representative,1,true', 'string', 'max:255'],
            'rep_barangay' => ['nullable', 'required_if:has_representative,1,true', 'string', 'max:100'],
            'rep_city' => ['nullable', 'string', 'max:100'],
            'rep_province' => ['nullable', 'string', 'max:100'],
            'rep_region' => ['nullable', 'string', 'max:100'],
<<<<<<< Updated upstream
            'rep_contact_number' => ['nullable', 'required_if:has_representative,1,true', 'string', 'regex:/^(09\d{9}|\+639\d{9}|639\d{9})$/'],
            'rep_birthday' => ['nullable', 'required_if:has_representative,1,true', 'date', 'before_or_equal:today'],
            'rep_age' => ['nullable', 'required_if:has_representative,1,true', 'integer', 'min:18', 'max:125'],
=======
            'rep_contact_number' => ['nullable', 'required_if:has_representative,1,true', 'string', 'regex:/^[0-9]{11}$/', new \App\Rules\ContactNumber(11)],
            'rep_birthday' => ['nullable', 'required_if:has_representative,1,true', 'date'],
            'rep_age' => ['nullable', 'required_if:has_representative,1,true', 'integer', 'min:0', 'max:150'],
>>>>>>> Stashed changes
            'rep_sex' => ['nullable', 'required_if:has_representative,1,true', 'in:Male,Female'],
            'rep_civil_status' => ['nullable', 'required_if:has_representative,1,true', 'string', 'max:50'],
            'rep_occupation' => [
                'nullable',
                'string',
                'max:150',
                'regex:/^(?!.*[0-9])(?=.*[\p{L}])[\p{L}\p{M}\s\.\'\x{2019}\-\/\&\(\)\,]+$/u',
            ],
            'rep_monthly_salary' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'rep_relationship' => ['nullable', 'required_if:has_representative,1,true', 'string', 'max:100'],

            // DSWD Section & Assessment
            'family_composition' => ['nullable', 'array'],
            'family_composition.*.name' => [
                'nullable',
                'string',
                'max:150',
                'regex:/^(?!.*[0-9])(?=.*[\p{L}])[\p{L}\p{M}\s\.\'\x{2019}\-]+$/u',
            ],
            'family_composition.*.relationship' => [
                'nullable',
                'string',
                'max:100',
                'regex:/^(?!.*[0-9])(?=.*[\p{L}])[\p{L}\p{M}\s\.\'\x{2019}\-\/\(\)\,]+$/u',
            ],
            'family_composition.*.age' => ['nullable', 'integer', 'min:0', 'max:125'],
            'family_composition.*.occupation' => [
                'nullable',
                'string',
                'max:150',
                'regex:/^(?!.*[0-9])(?=.*[\p{L}])[\p{L}\p{M}\s\.\'\x{2019}\-\/\&\(\)\,]+$/u',
            ],
            'family_composition.*.salary' => ['nullable', 'numeric', 'min:0', 'max:9999999.99'],
            'social_worker_assessment' => ['nullable', 'string'],
            'recommended_assistance_type' => ['nullable', 'string', 'max:150'],
            'assistance_purpose' => ['nullable', 'string', 'max:255'],
            'interviewed_by' => [
                'nullable',
                'string',
                'max:150',
                'regex:/^(?![\s\.\'\x{2019}\-]+$)[\p{L}\p{M}\s\.\'\x{2019}\-]+$/u',
            ],
            'reviewed_by' => [
                'nullable',
                'string',
                'max:150',
                'regex:/^(?![\s\.\'\x{2019}\-]+$)[\p{L}\p{M}\s\.\'\x{2019}\-]+$/u',
            ],

            'service_provided' => ['nullable', 'string', 'max:255'],
            'purpose' => ['nullable', 'string', 'max:255'],
            'purpose_other' => ['nullable', 'string', 'max:255'],
            'submitted_to' => ['nullable', 'string', 'max:255'],
            'medical_conditions' => ['nullable', 'array'],
            'medical_condition_other' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'date_processed.required' => 'The Original Intake Date is required for historical records.',
            'date_processed.before_or_equal' => 'The Original Intake Date cannot be in the future.',
            'recommended_amount.required' => 'The Financial Assistance Amount provided is required.',
            'recommended_amount.numeric' => 'The Financial Assistance Amount must be a valid number.',
            'recommended_amount.min' => 'The Financial Assistance Amount cannot be negative.',
            'recommended_amount.max' => 'The Financial Assistance Amount exceeds the maximum allowed limit.',
            'claiming_date.before_or_equal' => 'The Claiming Date cannot be in the future.',
            'beneficiary_first_name.required' => 'Unang pangalan (First name) is required.',
            'beneficiary_first_name.regex' => 'Ang unang pangalan ay maaari lamang maglaman ng mga titik, espasyo, gitling (-), kudlit (\'), at tuldok (.). Hindi pinapayagan ang purong numero.',
            'beneficiary_last_name.required' => 'Apelyido (Last name) is required.',
            'beneficiary_last_name.regex' => 'Ang apelyido ay maaari lamang maglaman ng mga titik, espasyo, gitling (-), kudlit (\'), at tuldok (.). Hindi pinapayagan ang purong numero.',
            'beneficiary_middle_name.regex' => 'Ang gitnang pangalan ay maaari lamang maglaman ng mga titik, espasyo, gitling (-), kudlit (\'), at tuldok (.).',
            'beneficiary_extension_name.regex' => 'Ang extension name ay maaari lamang maglaman ng mga titik, tuldok (.), o gitling.',
            'rep_first_name.required_if' => 'Unang pangalan ng kinatawan is required when representative is enabled.',
            'rep_first_name.regex' => 'Ang unang pangalan ng kinatawan ay maaari lamang maglaman ng mga titik, espasyo, gitling (-), kudlit (\'), at tuldok (.). Hindi pinapayagan ang purong numero.',
            'rep_last_name.required_if' => 'Apelyido ng kinatawan is required when representative is enabled.',
            'rep_last_name.regex' => 'Ang apelyido ng kinatawan ay maaari lamang maglaman ng mga titik, espasyo, gitling (-), kudlit (\'), at tuldok (.). Hindi pinapayagan ang purong numero.',
            'rep_middle_name.regex' => 'Ang gitnang pangalan ng kinatawan ay maaari lamang maglaman ng mga titik, espasyo, gitling (-), kudlit (\'), at tuldok (.).',
            'beneficiary_contact_number.required' => 'Numero ng telepono (Contact number) is required.',
            'beneficiary_contact_number.regex' => 'Mangyaring maglagay ng wastong Philippine mobile number (halimbawa: 09171234567 o +639171234567).',
            'rep_contact_number.required_if' => 'Representative contact number is required when representative is enabled.',
            'rep_contact_number.regex' => 'Mangyaring maglagay ng wastong Philippine mobile number para sa kinatawan (halimbawa: 09171234567 o +639171234567).',
            'beneficiary_birthday.required' => 'Kapanganakan (Date of birth) is required.',
            'beneficiary_birthday.before_or_equal' => 'Ang petsa ng kapanganakan ay hindi maaaring sa hinaharap (future date).',
            'rep_birthday.before_or_equal' => 'Ang petsa ng kapanganakan ng kinatawan ay hindi maaaring sa hinaharap (future date).',
            'beneficiary_age.min' => 'Ang edad ng benepisyaryo ay hindi maaaring mas mababa sa 0.',
            'beneficiary_age.max' => 'Ang edad ng benepisyaryo ay hindi maaaring lumagpas sa 125.',
            'rep_age.min' => 'Ang kinatawan ay dapat nasa hustong gulang (18 taong gulang pataas).',
            'rep_age.max' => 'Ang edad ng kinatawan ay hindi maaaring lumagpas sa 125.',
            'beneficiary_monthly_salary.numeric' => 'Ang buwanang sahod ay dapat wastong numero o halaga.',
            'beneficiary_monthly_salary.min' => 'Ang buwanang sahod ay hindi maaaring negatibo.',
            'beneficiary_monthly_salary.max' => 'Ang buwanang sahod ay lumagpas sa pinakamataas na limitasyon.',
            'rep_monthly_salary.numeric' => 'Ang buwanang sahod ng kinatawan ay dapat wastong numero o halaga.',
            'rep_monthly_salary.min' => 'Ang buwanang sahod ng kinatawan ay hindi maaaring negatibo.',
            'rep_monthly_salary.max' => 'Ang buwanang sahod ng kinatawan ay lumagpas sa pinakamataas na limitasyon.',
            'control_number.unique' => 'This Control Number has already been used in the system.',
            'beneficiary_occupation.regex' => 'Ang trabaho ng benepisyaryo ay dapat naglalaman ng mga wastong titik o deskripsyon lamang, at hindi pinapayagan ang mga numero.',
            'rep_occupation.regex' => 'Ang trabaho ng kinatawan ay dapat naglalaman ng mga wastong titik o deskripsyon lamang, at hindi pinapayagan ang mga numero.',
            'family_composition.*.name.regex' => 'Ang pangalan sa komposisyon ng pamilya ay maaari lamang maglaman ng mga titik, espasyo, gitling (-), kudlit (\'), at tuldok (.). Hindi pinapayagan ang purong numero.',
            'family_composition.*.relationship.regex' => 'Ang relasyon sa komposisyon ng pamilya ay dapat teksto lamang (hal. Ama, Ina, Asawa, Anak) at hindi maaaring maglaman ng numero.',
            'family_composition.*.occupation.regex' => 'Ang trabaho sa komposisyon ng pamilya ay dapat naglalaman ng mga wastong titik o deskripsyon lamang, at hindi pinapayagan ang mga numero.',
            'family_composition.*.age.min' => 'Ang edad ng miyembro ng pamilya ay hindi maaaring mas mababa sa 0.',
            'family_composition.*.age.max' => 'Ang edad ng miyembro ng pamilya ay hindi maaaring lumagpas sa 125.',
            'family_composition.*.salary.numeric' => 'Ang sahod ng miyembro ng pamilya ay dapat wastong numero o halaga.',
            'family_composition.*.salary.min' => 'Ang sahod ng miyembro ng pamilya ay hindi maaaring negatibo.',
        ];
    }
}

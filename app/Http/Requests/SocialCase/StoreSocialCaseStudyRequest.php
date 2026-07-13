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
            'date_processed' => ['nullable', 'date'],
            'client_last_name' => ['required', 'string', 'max:255'],
            'client_first_name' => ['required', 'string', 'max:255'],
            'client_middle_name' => ['nullable', 'string', 'max:255'],
            'client_age' => ['required', 'integer', 'min:0'],
            'client_sex' => ['required', 'in:Male,Female'],
            'client_barangay' => ['required', 'string', 'max:255'],
            'beneficiary_last_name' => ['nullable', 'string', 'max:255'],
            'beneficiary_first_name' => ['nullable', 'string', 'max:255'],
            'beneficiary_middle_name' => ['nullable', 'string', 'max:255'],
            'beneficiary_age' => ['nullable', 'integer', 'min:0'],
            'beneficiary_birthday' => ['nullable', 'date'],
            'beneficiary_sex' => ['nullable', 'in:Male,Female'],
            'beneficiary_barangay' => ['nullable', 'string', 'max:255'],
            'medical_conditions' => ['nullable', 'array'],
            'medical_conditions.*' => ['string'],
            'additional_requirements' => ['nullable', 'string'],
            'requirements_complete' => ['nullable', 'boolean'],
            'interview_date' => ['nullable', 'date'],
            'interview_reason' => ['nullable', 'string'],
            'interview_situation' => ['nullable', 'string'],
            'interview_household' => ['nullable', 'string'],
            'monthly_income' => ['nullable', 'numeric'],
            'monthly_expenses' => ['nullable', 'numeric'],
            'family_illnesses' => ['nullable', 'string'],
            'previous_assistance' => ['nullable', 'in:Yes,No'],
            'interview_notes' => ['nullable', 'string'],
            'interview_complete' => ['nullable', 'boolean'],
            'social_worker_assessment' => ['nullable', 'string'],
            'recommendation' => ['nullable', 'in:Approved,Needs Additional Info,Not Qualified'],
            'recommended_amount' => ['nullable', 'numeric'],
            'supervisor_notes' => ['nullable', 'string'],
            'evaluation_complete' => ['nullable', 'boolean'],
            'service_provided' => ['required', 'in:SOCIAL CASE STUDY REPORT,GENERAL INTAKE,CERTIFICATION'],
            'purpose' => ['required', 'in:FINANCIAL ASSISTANCE,MEDICAL ASSISTANCE,BURIAL ASSISTANCE,BIRTH CORRECTION,PHILHEALTH INDIGENCY,MERALCO INDIGENCY,PUBLIC ATTORNEY\'S OFFICE CERTIFICATION,BALIK PROBINSYA,FIRE INCIDENT,CHED SCHOLARSHIP,NATURAL DISASTER,DRUG REHABILITATION'],
            'submitted_to' => ['required', 'in:OFFICE OF THE PRESIDENT,OFFICE OF THE VICE PRESIDENT,DSWD - REGIONAL OFFICE,DSWD - CENTRAL OFFICE,DOH,PCSO,PROVINCIAL DEPARTMENT OF HEALTH OFFICE,OFFICE OF THE SENATE,PARTYLIST,OFFICE OF THE CONGRESSMAN,SANGUNIANG BAYAN COUNCILOR,OFFICE OF THE VICE MAYOR,PHILHEALTH,NOT APPLICABLE,SATELLITE OFFICE,INSTITUTION'],
            'encoded_by' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['Open', 'In Progress', 'Closed'])],
            'summary' => ['nullable', 'string', 'max:2000'],
            'report_generated' => ['nullable', 'boolean'],
            'assistance_released' => ['nullable', 'boolean'],
            'assistance_amount' => ['nullable', 'numeric'],
            'assistance_date' => ['nullable', 'date'],
        ];
    }
}

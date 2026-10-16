<?php

namespace App\Http\Requests\SocialCase;

use Illuminate\Foundation\Http\FormRequest;

abstract class CaseStepRequest extends FormRequest
{
    public function authorize(): bool { return true; }
}

class RequirementsStepRequest extends CaseStepRequest { public function rules(): array { return [
    'date_processed'=>['required','date'], 'client_last_name'=>['required','string','max:255'], 'client_first_name'=>['required','string','max:255'], 'client_middle_name'=>['nullable','string','max:255'], 'client_age'=>['required','integer','min:0'], 'client_sex'=>['required','in:Male,Female'], 'client_barangay'=>['required','string','max:255'], 'beneficiary_last_name'=>['nullable','string','max:255'], 'beneficiary_first_name'=>['nullable','string','max:255'], 'beneficiary_middle_name'=>['nullable','string','max:255'], 'beneficiary_age'=>['nullable','integer','min:0'], 'beneficiary_birthday'=>['nullable','date'], 'beneficiary_sex'=>['nullable','in:Male,Female'], 'beneficiary_barangay'=>['nullable','string','max:255'], 'medical_conditions'=>['nullable','array'], 'medical_conditions.*'=>['string'], 'additional_requirements'=>['nullable','string'], 'requirements_complete'=>['nullable','boolean'],
]; } }
class InterviewStepRequest extends CaseStepRequest { public function rules(): array { return ['interview_date'=>['required','date'],'interview_reason'=>['required','string'],'interview_situation'=>['required','string'],'interview_notes'=>['nullable','string'],'interview_complete'=>['accepted']]; } }
class FamilyStepRequest extends CaseStepRequest { public function rules(): array { return [
    'interview_household'=>['nullable','string'], 'monthly_income'=>['nullable','numeric'], 'monthly_expenses'=>['nullable','numeric'], 'family_illnesses'=>['nullable','string'], 'previous_assistance'=>['required','in:Yes,No'],
    'family_members'=>['required','array','min:1'], 'family_members.*.full_name'=>['required','string','max:255'], 'family_members.*.relationship'=>['required','string','max:100'], 'family_members.*.age'=>['nullable','integer','min:0','max:150'], 'family_members.*.sex'=>['nullable','in:Male,Female'], 'family_members.*.occupation'=>['nullable','string','max:255'], 'family_members.*.monthly_income'=>['nullable','numeric','min:0'], 'family_members.*.is_dependent'=>['nullable','boolean'], 'family_members.*.notes'=>['nullable','string','max:2000'],
]; } }
class AssessmentStepRequest extends CaseStepRequest { public function rules(): array { return ['social_worker_assessment'=>['required','string'],'recommendation'=>['required','in:Approved,Needs Additional Info,Not Qualified'],'recommended_amount'=>['nullable','numeric']]; } }
class ReportStepRequest extends CaseStepRequest { public function rules(): array { return []; } }
class ExportStepRequest extends CaseStepRequest { public function rules(): array { return []; } }
class ReleaseReportStepRequest extends CaseStepRequest { public function rules(): array { return ['encoded_by'=>['required','string','max:255']]; } }
class AssistanceStepRequest extends CaseStepRequest { public function rules(): array { return ['assistance_amount'=>['required','numeric','min:0'],'assistance_date'=>['required','date'],'assistance_released'=>['accepted']]; } }
class CloseStepRequest extends CaseStepRequest { public function rules(): array { return ['status'=>['required','in:Closed']]; } }

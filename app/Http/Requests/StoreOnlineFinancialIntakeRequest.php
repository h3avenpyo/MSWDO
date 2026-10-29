<?php

namespace App\Http\Requests;

use App\Http\Requests\SocialCase\StoreBeneficiaryIntakeRequest;
use App\Models\Financial\OnlineFinancialIntake;

class StoreOnlineFinancialIntakeRequest extends StoreBeneficiaryIntakeRequest
{
    public function rules(): array
    {
        $rules = parent::rules();
        $rules['documents'] = ['nullable', 'array:'.implode(',', array_keys(OnlineFinancialIntake::DOCUMENT_LABELS))];
        foreach (OnlineFinancialIntake::DOCUMENT_LABELS as $key => $label) {
            $rules['documents.'.$key] = ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'];
        }

        return $rules;
    }
}

<?php

namespace App\Services\Wcpu;

use App\Models\Wcpu\CaseModel;
use App\Rules\ContactNumber;
use Illuminate\Http\Request;

class AdditionalCasePeople
{
    public static function updateFromRequest(Request $request, CaseModel $case): void
    {
        $user = $request->user();
        if ($user?->position !== 'Desk Officer' && !$user?->isBcpcAdmin() && !$user?->isVawcAdmin()) {
            return;
        }
        $groups = [
            'additional_victims' => ['victim_name', 'victim_age', 'victim_gender', 'victim_contact', 'victim_address', 'guardian', 'guardian_contact', 'guardian_address', 'relationship_to_victim'],
            'additional_perpetrators' => ['perpetrator_name', 'abuser_age', 'abuser_address', 'abuse_relationship', 'guardian_abuser_name', 'guardian_abuser_contact', 'guardian_abuser_address', 'relationship_to_abuser'],
        ];
        $rules = [];
        foreach ($groups as $group => $fields) {
            if (!$request->has($group)) {
                continue;
            }
            $rules[$group] = ['array'];
            $rules[$group . '.*'] = ['array:' . implode(',', $fields)];
            foreach ($fields as $field) {
                $rule = ['nullable', 'string', 'max:2000'];
                if (in_array($field, ['victim_name', 'perpetrator_name', 'guardian', 'guardian_abuser_name'], true)) {
                    $rule = [$field === 'victim_name' || $field === 'perpetrator_name' ? 'required' : 'nullable', 'string', 'max:255', "regex:/^[\p{L}\p{M} '’-]+$/u"];
                } elseif ($field === 'victim_age' || $field === 'abuser_age') {
                    $rule = ['required', 'integer', $field === 'victim_age' ? 'min:0' : 'min:1'];
                    if ($field === 'abuser_age') $rule[] = 'max:120';
                } elseif ($field === 'victim_gender') {
                    $rule = ['required', 'in:Female,Male,LGBTQIA+,Prefer not to say'];
                } elseif (str_ends_with($field, '_contact')) {
                    $rule = ['nullable', 'string', 'max:20', new ContactNumber(11)];
                } elseif (in_array($field, ['victim_address', 'abuser_address', 'abuse_relationship'], true)) {
                    $rule = ['required', 'string', 'max:2000'];
                }
                $rules[$group . '.*.' . $field] = $rule;
            }
        }
        if (!$rules) return;
        $validated = $request->validate($rules);
        $data = $case->bcpc_data ?? [];
        foreach ($groups as $group => $fields) {
            if (array_key_exists($group, $validated)) {
                $data[$group] = array_values($validated[$group]);
            }
        }
        $case->bcpc_data = $data;
    }
}

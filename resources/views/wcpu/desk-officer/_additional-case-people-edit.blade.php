@if(auth()->user()?->position === 'Desk Officer' || auth()->user()?->isBcpcAdmin() || auth()->user()?->isVawcAdmin())
@foreach([
    'additional_victims' => ['Victim', ['victim_name' => 'Full Name', 'victim_age' => 'Age', 'victim_gender' => 'Gender', 'victim_contact' => 'Contact Number', 'victim_address' => 'Address', 'guardian' => 'Guardian Name', 'guardian_contact' => 'Guardian Contact', 'guardian_address' => 'Guardian Address', 'relationship_to_victim' => 'Relationship to Victim']],
    'additional_perpetrators' => ['Perpetrator', ['perpetrator_name' => 'Perpetrator Name', 'abuser_age' => 'Perpetrator Age', 'abuse_relationship' => 'Relationship to Victim', 'abuser_address' => 'Perpetrator Address', 'guardian_abuser_name' => 'Guardian Name', 'guardian_abuser_contact' => 'Guardian Contact', 'guardian_abuser_address' => 'Guardian Address', 'relationship_to_abuser' => 'Relationship to Perpetrator']],
] as $extraGroup => [$extraLabel, $extraFields])
    @foreach(array_values($case->bcpc_data[$extraGroup] ?? []) as $extraIndex => $extraPerson)
        @php
            $extraRows = $extraGroup === 'additional_victims'
                ? [['victim_name', 'victim_age', 'victim_gender'], ['victim_contact', 'victim_address']]
                : [['perpetrator_name', 'abuser_age', 'abuse_relationship'], ['abuser_address']];
            $extraGuardianRows = $extraGroup === 'additional_victims'
                ? [['guardian', 'guardian_contact'], ['guardian_address', 'relationship_to_victim']]
                : [['guardian_abuser_name', 'guardian_abuser_contact'], ['guardian_abuser_address', 'relationship_to_abuser']];
            $extraSections = [
                [$extraLabel . ' Information ' . ($extraIndex + 2), $extraRows],
                [($extraGroup === 'additional_victims' ? 'Guardian Information ' : 'Guardian of Perpetrator Information ') . ($extraIndex + 2), $extraGuardianRows],
            ];
        @endphp
        @foreach($extraSections as [$extraHeading, $extraSectionRows])
        <fieldset class="mb-6 min-w-0" style="border:0;padding:0;" @if($isReadOnly ?? false) disabled @endif>
            <legend class="w-full text-lg font-semibold text-gray-800 pb-2 border-b-2 border-gray-200 mb-4">{{ $extraHeading }}</legend>
            @foreach($extraSectionRows as $extraRow)
            <div class="grid grid-cols-1 {{ count($extraRow) === 3 || ($extraGroup === 'additional_perpetrators' && $extraRow === ['abuser_address']) ? 'md:grid-cols-3' : 'md:grid-cols-2' }} gap-4 mb-4">
                @foreach($extraRow as $extraField)
                    @php
                        $extraFieldLabel = $extraFields[$extraField];
                        $extraInputName = $extraGroup . '[' . $extraIndex . '][' . $extraField . ']';
                        $extraValue = old($extraGroup . '.' . $extraIndex . '.' . $extraField, $extraPerson[$extraField] ?? '');
                        $extraRequired = in_array($extraField, ['victim_name', 'victim_age', 'victim_gender', 'victim_address', 'perpetrator_name', 'abuser_age', 'abuser_address', 'abuse_relationship'], true);
                    @endphp
                    <label class="block text-sm font-medium text-gray-700">{{ $extraFieldLabel }} @if($extraRequired)<span class="text-red-500">*</span>@endif
                        @if($extraField === 'victim_gender')
                            <select name="{{ $extraInputName }}" required class="mt-2 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm">
                                <option value="">Select Gender</option>
                                @foreach(['Female', 'Male', 'LGBTQIA+', 'Prefer not to say'] as $extraGender)<option value="{{ $extraGender }}" @selected($extraValue === $extraGender)>{{ $extraGender }}</option>@endforeach
                            </select>
                        @elseif(in_array($extraField, ['abuse_relationship', 'relationship_to_victim', 'relationship_to_abuser'], true))
                            @php
                                $extraRelationshipOptions = $extraField === 'abuse_relationship'
                                    ? ['spouse', 'partner', 'parent', 'sibling', 'child', 'relative', 'friend', 'neighbor', 'stranger', 'other']
                                    : ['Parent', 'Legal Guardian', 'Grandparent', 'Other Relative', 'Other'];
                                if ($extraValue !== '' && !in_array($extraValue, $extraRelationshipOptions, true)) $extraRelationshipOptions[] = $extraValue;
                            @endphp
                            <select name="{{ $extraInputName }}" @if($extraRequired) required @endif class="mt-2 block w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm">
                                <option value="">Select Relationship</option>
                                @foreach($extraRelationshipOptions as $extraRelationship)<option value="{{ $extraRelationship }}" @selected($extraValue === $extraRelationship)>{{ ucfirst($extraRelationship) }}</option>@endforeach
                            </select>
                        @else
                            <input name="{{ $extraInputName }}" value="{{ $extraValue }}" type="{{ str_ends_with($extraField, '_age') ? 'number' : (str_ends_with($extraField, '_contact') ? 'tel' : 'text') }}" @if($extraRequired) required @endif
                                @if(str_ends_with($extraField, '_age')) min="{{ $extraField === 'victim_age' ? 0 : 1 }}" @if($extraField === 'abuser_age') max="120" @endif @endif
                                @if(str_ends_with($extraField, '_contact')) maxlength="11" pattern="[0-9]{11}" inputmode="numeric" @endif
                                class="mt-2 block w-full min-w-0 rounded-lg border border-gray-300 px-4 py-2.5 text-sm">
                        @endif
                    </label>
                @endforeach
            </div>
            @endforeach
        </fieldset>
        @endforeach
    @endforeach
@endforeach
@endif

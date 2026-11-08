@if(auth()->user()?->position === 'Desk Officer' || auth()->user()?->isBcpcAdmin() || auth()->user()?->isVawcAdmin())
@foreach(['additional_victims' => 'Client', 'additional_perpetrators' => 'Perpetrator'] as $group => $role)
    @php
        $printPeople = array_values($referral->case?->bcpc_data[$group] ?? []);
        if ($group === 'additional_victims') {
            array_unshift($printPeople, [
                'victim_name' => $clientName, 'victim_age' => $clientAge,
                'victim_gender' => $clientSex, 'victim_address' => $clientAddress,
                'victim_contact' => $displayValue($referral->client_contact, $referral->case?->victim_contact),
                'guardian' => $guardianName, 'guardian_contact' => $guardianContact,
                'guardian_address' => $guardianAddress,
                'relationship_to_victim' => $referral->case?->relationship_to_victim,
            ]);
        }
    @endphp
    @foreach($printPeople as $index => $person)
        @php
            $isVictim = $group === 'additional_victims';
            $rows = $isVictim
                ? [['Name of Client ' . ($index + 1) => 'victim_name'], ['Age' => 'victim_age', 'Sex' => 'victim_gender', 'Address' => 'victim_address']]
                : [['Name of Perpetrator ' . ($index + 2) => 'perpetrator_name'], ['Age' => 'abuser_age', 'Relationship to Victim' => 'abuse_relationship', 'Address' => 'abuser_address']];
            $guardianKeys = $isVictim
                ? ['guardian', 'guardian_contact', 'guardian_address', 'relationship_to_victim']
                : ['guardian_abuser_name', 'guardian_abuser_contact', 'guardian_abuser_address', 'relationship_to_abuser'];
            if (collect($guardianKeys)->contains(fn ($key) => filled($person[$key] ?? null))) {
                $rows[] = ['Name of Family/Guardian' => $guardianKeys[0], 'Contact No.' => $guardianKeys[1]];
                $rows[] = ['Address' => $guardianKeys[2], 'Relationship' => $guardianKeys[3]];
            }
            if ($isVictim && filled($person['victim_contact'] ?? null)) $rows[] = ['Client Contact No.' => 'victim_contact'];
        @endphp
        <div style="margin-top:6px;page-break-inside:avoid;">
            @foreach($rows as $fields)
                <div class="row" style="table-layout:fixed;">
                    @foreach($fields as $label => $key)
                        <div class="col col-{{ count($fields) === 3 ? '33' : (count($fields) === 2 ? '50' : '100') }}">
                            <span class="label">{{ $label }}:</span>
                            <span class="value {{ count($fields) === 1 && !(auth()->user()?->isBcpcAdmin() || auth()->user()?->isVawcAdmin()) ? 'value-full' : '' }}"
                                @if(auth()->user()?->isBcpcAdmin() || auth()->user()?->isVawcAdmin())
                                    style="min-width:0;width:{{ $label === 'Age' ? 12 : ($label === 'Sex' ? 22 : (str_starts_with($label, 'Name') ? 55 : ($label === 'Address' ? 45 : 32))) }}mm;max-width:100%;overflow-wrap:anywhere;"
                                @elseif(count($fields) > 1)
                                    style="min-width:0;width:{{ count($fields) === 3 ? 45 : 50 }}%;overflow-wrap:anywhere;"
                                @endif>{{ str_starts_with($label, 'Name') ? censorLastName($person[$key] ?? '') : ($person[$key] ?? '') }}</span>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    @endforeach
@endforeach
@endif

@foreach(['additional_victims' => 'Client', 'additional_perpetrators' => 'Perpetrator'] as $group => $role)
    @foreach(array_values($referral->case?->bcpc_data[$group] ?? []) as $index => $person)
        @php
            $isVictim = $group === 'additional_victims';
            $fields = $isVictim
                ? ['Name' => 'victim_name', 'Age' => 'victim_age', 'Sex' => 'victim_gender', 'Contact' => 'victim_contact', 'Address' => 'victim_address']
                : ['Name' => 'perpetrator_name', 'Age' => 'abuser_age', 'Relationship to Victim' => 'abuse_relationship', 'Address' => 'abuser_address'];
            $guardianFields = $isVictim
                ? ['Name' => 'guardian', 'Contact' => 'guardian_contact', 'Address' => 'guardian_address', 'Relationship to Victim' => 'relationship_to_victim']
                : ['Name' => 'guardian_abuser_name', 'Contact' => 'guardian_abuser_contact', 'Address' => 'guardian_abuser_address', 'Relationship to Perpetrator' => 'relationship_to_abuser'];
            $sections = [$role . ' Information ' . ($index + 2) => $fields];
            if (collect($guardianFields)->contains(fn ($key) => filled($person[$key] ?? null))) {
                $sections[($isVictim ? 'Guardian' : 'Guardian of Perpetrator') . ' Information ' . ($index + 2)] = $guardianFields;
            }
        @endphp
        @foreach($sections as $heading => $sectionFields)
            @if($archivedPeople ?? false)
                <section class="overflow-hidden {{ ($archivedSwd ?? false) ? 'rounded-lg' : 'rounded-xl' }} border border-gray-200 bg-white">
                    <h3 class="border-b border-gray-200 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800">{{ $heading }}</h3>
                    <dl class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2">
                        @foreach($sectionFields as $label => $key)
                            <div class="min-w-0 {{ ($archivedSwd ?? false) ? '' : 'rounded-lg border border-gray-100 bg-gray-50 p-3' }} {{ in_array($label, ['Name', 'Address']) ? 'sm:col-span-2' : '' }}">
                                <dt class="text-xs text-gray-500 {{ ($archivedSwd ?? false) ? 'mb-1' : 'font-medium uppercase tracking-wide' }}">{{ $label }}</dt>
                                <dd class="{{ ($archivedSwd ?? false) ? '' : 'mt-1' }} whitespace-pre-wrap break-words text-sm text-gray-900">{{ isset($person[$key]) && (string) $person[$key] !== '' ? $person[$key] : 'N/A' }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </section>
            @else
            <div>
                <h4 class="text-sm font-semibold text-gray-800 uppercase tracking-wide mb-3 flex items-center gap-2">
                    <i class="fas {{ str_starts_with($heading, 'Guardian') ? 'fa-user-shield' : ($isVictim ? 'fa-user' : 'fa-user-slash') }} text-purple-600" aria-hidden="true"></i>
                    {{ $heading }}
                </h4>
                <div class="bg-gray-50 rounded-lg p-4 grid grid-cols-2 sm:grid-cols-3 gap-4">
                    @foreach($sectionFields as $label => $key)
                        <div class="min-w-0 {{ $label === 'Address' ? 'col-span-2 sm:col-span-3' : '' }}">
                            <p class="text-xs text-gray-500">{{ $label }}</p>
                            <p class="text-sm font-medium text-gray-800 break-words">{{ isset($person[$key]) && (string) $person[$key] !== '' ? $person[$key] : 'N/A' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        @endforeach
    @endforeach
@endforeach

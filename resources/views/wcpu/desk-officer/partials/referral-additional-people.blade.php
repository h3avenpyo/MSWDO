@if(auth()->user()?->position === 'Desk Officer')
@foreach(['additional_victims' => 'Client', 'additional_perpetrators' => 'Perpetrator'] as $group => $role)
    @foreach(array_values($referral->case?->bcpc_data[$group] ?? []) as $index => $person)
        @php
            $isExtraVictim = $group === 'additional_victims';
            $personFields = $isExtraVictim
                ? ['Name of Client' => 'victim_name', 'Age' => 'victim_age', 'Sex' => 'victim_gender', 'Contact Number' => 'victim_contact', 'Address' => 'victim_address']
                : ['Name of Perpetrator' => 'perpetrator_name', 'Age' => 'abuser_age', 'Relationship to Victim' => 'abuse_relationship', 'Address' => 'abuser_address'];
            $guardianFields = $isExtraVictim
                ? ['Guardian Name' => 'guardian', 'Contact Number' => 'guardian_contact', 'Address' => 'guardian_address', 'Relationship to Victim' => 'relationship_to_victim']
                : ['Guardian Name' => 'guardian_abuser_name', 'Contact Number' => 'guardian_abuser_contact', 'Address' => 'guardian_abuser_address', 'Relationship to Perpetrator' => 'relationship_to_abuser'];
            $sections = [$role . ' Information ' . ($index + 2) => $personFields];
            if (collect($guardianFields)->contains(fn ($key) => filled($person[$key] ?? null))) {
                $sections[($isExtraVictim ? 'Guardian' : 'Guardian of Perpetrator') . ' Information ' . ($index + 2)] = $guardianFields;
            }
        @endphp
        @foreach($sections as $heading => $fields)
            @if($isArchived ?? false)
                <section class="overflow-hidden rounded-xl border border-gray-200 bg-white">
                    <h3 class="border-b border-gray-200 bg-gray-50 px-4 py-3 text-sm font-semibold text-gray-800">{{ $heading }}</h3>
                    <dl class="grid grid-cols-1 gap-3 p-4 sm:grid-cols-2">
                        @foreach($fields as $label => $key)
                            <div class="min-w-0 rounded-lg border border-gray-100 bg-gray-50 p-3 {{ str_contains($label, 'Name') || $label === 'Address' ? 'sm:col-span-2' : '' }}">
                                <dt class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ $label }}</dt>
                                <dd class="mt-1 break-words text-sm text-gray-900">{{ filled($person[$key] ?? null) ? (str_contains($label, 'Name') ? censorLastName($person[$key]) : $person[$key]) : 'N/A' }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </section>
            @elseif($additionalReferralEdit ?? false)
                <div class="mb-3">
                    <h3 class="form-label font-medium text-gray-700">{{ $heading }}</h3>
                    <div class="row">
                        @foreach($fields as $label => $key)
                            <div class="{{ str_contains($label, 'Name') || $label === 'Address' ? 'col-12' : 'col-md-6' }} mb-3">
                                <label class="form-label">{{ $label }}</label>
                                <input type="text" class="form-control" value="{{ $person[$key] ?? '' }}" readonly>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <section class="referral-detail-section">
                    <h3>{{ $heading }}</h3>
                    <div class="referral-detail-grid">
                        @foreach($fields as $label => $key)
                            <div class="referral-detail-field {{ str_contains($label, 'Name') || $label === 'Address' ? 'referral-detail-wide' : '' }}">
                                <label class="form-label font-medium text-gray-700">{{ $label }}</label>
                                <input type="text" class="form-control" value="{{ str_contains($label, 'Name') ? censorLastName($person[$key] ?? '') : ($person[$key] ?? '') }}" readonly>
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif
        @endforeach
    @endforeach
@endforeach
@endif

@if(auth()->user()?->position === 'Desk Officer' || auth()->user()?->isBcpcAdmin() || auth()->user()?->isVawcAdmin())
@foreach(['additional_victims' => 'Client/Victim', 'additional_perpetrators' => 'Perpetrator'] as $referralPeopleKey => $referralPersonLabel)
    @foreach(array_values($case->bcpc_data[$referralPeopleKey] ?? []) as $referralPersonIndex => $referralPerson)
        @php
            $referralIsVictim = $referralPeopleKey === 'additional_victims';
            $referralFields = $referralIsVictim
                ? ['victim_name' => 'Name', 'victim_age' => 'Age', 'victim_gender' => 'Sex', 'victim_address' => 'Address', 'victim_contact' => 'Contact']
                : ['perpetrator_name' => 'Name', 'abuser_age' => 'Age', 'abuse_relationship' => 'Relationship to Victim', 'abuser_address' => 'Address'];
            $referralGuardianFields = $referralIsVictim
                ? ['guardian' => 'Name', 'guardian_contact' => 'Contact', 'guardian_address' => 'Address', 'relationship_to_victim' => 'Relationship to Victim']
                : ['guardian_abuser_name' => 'Name', 'guardian_abuser_contact' => 'Contact', 'guardian_abuser_address' => 'Address', 'relationship_to_abuser' => 'Relationship to Perpetrator'];
        @endphp
        <section class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                <h3 class="text-gray-800 font-semibold flex flex-wrap items-center gap-2">
                    <i class="fas {{ $referralIsVictim ? 'fa-user' : 'fa-user-slash' }} text-red-600" aria-hidden="true"></i>
                    {{ $referralPersonLabel }} Information {{ $referralPersonIndex + 2 }}
                    <span class="text-xs font-normal text-gray-500">(Auto-filled from case)</span>
                </h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($referralFields as $referralField => $referralFieldLabel)
                        <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-blue-500 {{ str_ends_with($referralField, '_address') ? 'sm:col-span-2' : '' }}">
                            <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">{{ $referralFieldLabel }}</p>
                            <p class="font-semibold text-gray-800" style="overflow-wrap:anywhere;">{{ $referralPerson[$referralField] ?? 'N/A' }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
        @if(collect(array_keys($referralGuardianFields))->contains(fn ($field) => isset($referralPerson[$field]) && (string) $referralPerson[$field] !== ''))
            <section class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-gray-800 font-semibold flex flex-wrap items-center gap-2"><i class="fas fa-user-shield text-red-600" aria-hidden="true"></i> {{ $referralIsVictim ? 'Guardian' : 'Guardian of Perpetrator' }} Information {{ $referralPersonIndex + 2 }} <span class="text-xs font-normal text-gray-500">(Optional)</span></h3>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    @foreach($referralGuardianFields as $referralField => $referralFieldLabel)
                        <label class="block text-sm font-medium text-gray-700">{{ $referralFieldLabel }}
                            <input type="text" value="{{ $referralPerson[$referralField] ?? 'N/A' }}" readonly class="mt-2 w-full min-w-0 px-4 py-2.5 border border-gray-300 rounded-lg bg-gray-50 text-gray-700">
                        </label>
                    @endforeach
                </div>
            </section>
        @endif
    @endforeach
@endforeach
@endif

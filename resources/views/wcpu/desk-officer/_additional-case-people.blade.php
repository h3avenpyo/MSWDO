@if(auth()->user()?->position === 'Desk Officer' || auth()->user()?->isBcpcAdmin() || auth()->user()?->isVawcAdmin())
@if(!($additionalPeopleFullPage ?? false) && (auth()->user()?->position === 'Desk Officer' || auth()->user()?->isBcpcAdmin() || auth()->user()?->isVawcAdmin()))
@once
<style>
    .desk-extra-people-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; grid-column:1 / -1; margin-bottom:16px; }
    .desk-extra-person-card { min-width:0; border:1px solid #e1e5ea; border-radius:8px; overflow:hidden; background:#fff; }
    .desk-extra-person-card:last-child:nth-child(odd) { grid-column:1 / -1; }
    .desk-extra-person-heading { display:flex; align-items:center; gap:8px; padding:12px 16px; margin:0; border-bottom:1px solid #e1e5ea; background:#f8f9fb; font-size:14px; font-weight:500; line-height:1.3; color:#001b36; }
    .desk-extra-person-heading i { font-size:14px; flex-shrink:0; }
    .desk-extra-person-row { display:flex; gap:8px; padding:10px 16px; border-bottom:1px solid #f0f1f4; font-size:14px; line-height:1.5; }
    .desk-extra-person-row:last-child { border-bottom:0; }
    .desk-extra-person-label { flex:0 0 132px; color:#64748b; }
    .desk-extra-person-value { flex:1; min-width:0; overflow-wrap:anywhere; color:#001b36; }
    @media (max-width:47.9375rem) {
        .desk-extra-people-grid { grid-template-columns:minmax(0,1fr); }
        .desk-extra-person-row { flex-direction:column; gap:4px; }
        .desk-extra-person-label { flex:auto; }
    }
</style>
@endonce
@php
    $deskExtraVictims = array_values($case->bcpc_data['additional_victims'] ?? []);
    $deskExtraPerpetrators = array_values($case->bcpc_data['additional_perpetrators'] ?? []);
@endphp
@for($deskPersonIndex = 0; $deskPersonIndex < max(count($deskExtraVictims), count($deskExtraPerpetrators)); $deskPersonIndex++)
    @php
        $deskPersonCards = [];
        $deskGuardianCards = [];
        $adminPersonCards = [];
        foreach (['Victim' => $deskExtraVictims, 'Perpetrator' => $deskExtraPerpetrators] as $deskRole => $deskPeople) {
            if (!isset($deskPeople[$deskPersonIndex])) continue;
            $deskPerson = $deskPeople[$deskPersonIndex];
            $deskIsVictim = $deskRole === 'Victim';
            $deskFields = $deskIsVictim
                ? ['victim_name' => 'Name', 'victim_age' => 'Age', 'victim_gender' => 'Gender', 'victim_contact' => 'Contact', 'victim_email' => 'Email', 'victim_address' => 'Address']
                : ['perpetrator_name' => 'Name', 'abuser_age' => 'Age', 'abuse_relationship' => 'Relationship', 'abuser_address' => 'Address of the Perpetrator'];
            $deskPersonCards[] = ['title' => $deskRole . ' Information ' . ($deskPersonIndex + 2), 'icon' => $deskIsVictim ? 'fa-user' : 'fa-user-slash', 'color' => '#dc143c', 'fields' => $deskFields, 'person' => $deskPerson];
            $adminPersonCards[] = end($deskPersonCards);
            $deskGuardianFields = $deskIsVictim
                ? ['guardian' => 'Name', 'guardian_contact' => 'Contact', 'guardian_address' => 'Address', 'relationship_to_victim' => 'Relationship']
                : ['guardian_abuser_name' => 'Name', 'guardian_abuser_contact' => 'Contact', 'guardian_abuser_address' => 'Address', 'relationship_to_abuser' => 'Relationship'];
            if (collect(array_keys($deskGuardianFields))->contains(fn ($field) => isset($deskPerson[$field]) && (string) $deskPerson[$field] !== '')) {
                $deskGuardianCards[] = ['title' => 'Guardian of ' . $deskRole . ' ' . ($deskPersonIndex + 2), 'icon' => $deskIsVictim ? 'fa-user-friends' : 'fa-user-tie', 'color' => '#f06400', 'fields' => $deskGuardianFields, 'person' => $deskPerson];
                $adminGuardianCard = end($deskGuardianCards);
                if ($deskIsVictim) {
                    $adminGuardianCard['title'] = 'Guardian Information ' . ($deskPersonIndex + 2);
                    $adminGuardianCard['icon'] = 'fa-user-shield';
                    $adminGuardianCard['color'] = '#dc143c';
                }
                $adminPersonCards[] = $adminGuardianCard;
            }
        }
    @endphp
    <div class="desk-extra-people-grid">
        @foreach(auth()->user()?->position === 'Desk Officer' ? array_merge($deskPersonCards, $deskGuardianCards) : $adminPersonCards as $deskCard)
            <section class="desk-extra-person-card">
                <h4 class="desk-extra-person-heading"><i class="fas {{ $deskCard['icon'] }}" style="color:{{ $deskCard['color'] }};" aria-hidden="true"></i>{{ $deskCard['title'] }}</h4>
                @foreach($deskCard['fields'] as $deskField => $deskLabel)
                    <div class="desk-extra-person-row">
                        <span class="desk-extra-person-label">{{ $deskLabel }}:</span>
                        <span class="desk-extra-person-value">{{ isset($deskCard['person'][$deskField]) && (string) $deskCard['person'][$deskField] !== '' ? $deskCard['person'][$deskField] : 'N/A' }}</span>
                    </div>
                @endforeach
            </section>
        @endforeach
    </div>
@endfor
@else
@once
<style>
    .additional-case-person .additional-person-mobile-icon { display:none; }
    @if(auth()->user()?->position === 'Desk Officer' && !($additionalPeopleFullPage ?? false))
    .additional-case-person .additional-person-icon {
        width:16px !important;
        height:16px !important;
        min-width:16px;
        background:transparent !important;
        color:var(--person-mobile-icon-color) !important;
        border-radius:0;
        font-size:14px;
    }
    .additional-case-person .additional-person-desktop-icon { display:none; }
    .additional-case-person .additional-person-mobile-icon { display:inline-block; }
    @endif
    @media (max-width: 47.9375rem) {
        .additional-case-person > .additional-person-header { padding:12px 16px !important; gap:10px !important; }
        .additional-case-person .additional-person-icon { width:16px !important; height:16px !important; min-width:16px; background:transparent !important; color:var(--person-mobile-icon-color) !important; border-radius:0; font-size:14px; }
        .additional-case-person .additional-person-desktop-icon { display:none; }
        .additional-case-person .additional-person-mobile-icon { display:inline-block; }
        .additional-case-person .additional-person-header h3 { min-width:0; margin:0; font-size:14px !important; line-height:1.4 !important; }
        .additional-case-person > .additional-person-body { padding:12px 16px !important; }
        .additional-case-person .additional-person-fields { row-gap:4px !important; }
        .additional-case-person .additional-person-fields > .detail-row { padding-top:6px !important; padding-bottom:6px !important; }
        .additional-admin-case-person .additional-person-fields { grid-template-columns:minmax(0, 1fr) !important; }
        .additional-admin-case-person .additional-person-fields > .detail-row { flex-direction:column; align-items:flex-start !important; gap:4px !important; min-width:0; }
        .additional-admin-case-person .additional-person-fields > .detail-row > span { max-width:100%; white-space:normal; overflow-wrap:anywhere; }
    }
</style>
@endonce
@foreach(['additional_victims' => 'Victim', 'additional_perpetrators' => 'Perpetrator'] as $peopleKey => $personLabel)
    @foreach(array_values($case->bcpc_data[$peopleKey] ?? []) as $personIndex => $additionalPerson)
        @php
            $isAdditionalVictim = $peopleKey === 'additional_victims';
            $personFields = $isAdditionalVictim
                ? ['victim_name' => 'Full Name', 'victim_age' => 'Age', 'victim_gender' => 'Gender', 'victim_contact' => 'Contact', 'victim_address' => 'Address']
                : ['perpetrator_name' => 'Full Name', 'abuser_age' => 'Age', 'abuse_relationship' => 'Relationship to Victim', 'abuser_address' => 'Address of the Perpetrator'];
            $guardianFields = $isAdditionalVictim
                ? ['guardian' => 'Guardian Name', 'guardian_contact' => 'Contact', 'guardian_address' => 'Address', 'relationship_to_victim' => 'Relationship']
                : ['guardian_abuser_name' => 'Guardian Name', 'guardian_abuser_contact' => 'Contact', 'guardian_abuser_address' => 'Address', 'relationship_to_abuser' => 'Relationship'];
            $personCards = [[
                'title' => $personLabel . ' Information ' . ($personIndex + 2),
                'fields' => $personFields,
                'icon' => $isAdditionalVictim ? 'fa-user-shield' : 'fa-user-slash',
                'mobile-icon' => $isAdditionalVictim ? 'fa-user' : 'fa-user-slash',
                'mobile-color' => '#dc2626',
                'background' => $isAdditionalVictim ? '#dbeafe' : '#fee2e2',
                'color' => $isAdditionalVictim ? '#2563eb' : '#dc2626',
            ]];
            if (collect(array_keys($guardianFields))->contains(fn ($field) => isset($additionalPerson[$field]) && (string) $additionalPerson[$field] !== '')) {
                $personCards[] = [
                    'title' => 'Guardian of ' . $personLabel . ' Information ' . ($personIndex + 2),
                    'fields' => $guardianFields,
                    'icon' => $isAdditionalVictim ? 'fa-user-friends' : 'fa-user-tie',
                    'mobile-icon' => $isAdditionalVictim ? 'fa-user-friends' : 'fa-user-tie',
                    'mobile-color' => $isAdditionalVictim ? '#9333ea' : '#ea580c',
                    'background' => $isAdditionalVictim ? '#f3e8ff' : '#ffedd5',
                    'color' => $isAdditionalVictim ? '#9333ea' : '#ea580c',
                ];
            }
        @endphp
        @foreach($personCards as $personCard)
            <section class="additional-case-person {{ auth()->user()?->isBcpcAdmin() || auth()->user()?->isVawcAdmin() ? 'additional-admin-case-person' : '' }} bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6 md:col-span-2">
                <div class="additional-person-header px-6 py-4 border-b border-gray-100 flex items-center gap-3" @if((auth()->user()?->isBcpcAdmin() || auth()->user()?->isVawcAdmin()) && !($additionalPeopleFullPage ?? false)) style="background:#f8fafc !important;color:#111827 !important;" @endif>
                    <div class="additional-person-icon w-10 h-10 rounded-lg flex items-center justify-center" style="background:{{ $personCard['background'] }};color:{{ $personCard['color'] }};--person-mobile-icon-color:{{ $personCard['mobile-color'] }};flex-shrink:0;">
                        <i class="additional-person-desktop-icon fas {{ $personCard['icon'] }}" aria-hidden="true"></i>
                        <i class="additional-person-mobile-icon fas {{ $personCard['mobile-icon'] }}" aria-hidden="true"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-800" @if((auth()->user()?->isBcpcAdmin() || auth()->user()?->isVawcAdmin()) && !($additionalPeopleFullPage ?? false)) style="color:#111827 !important;" @endif>{{ $personCard['title'] }}</h3>
                </div>
                <div class="additional-person-body p-6">
                    <div class="additional-person-fields grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-8">
                        @foreach($personCard['fields'] as $personField => $fieldLabel)
                            <div class="detail-row flex items-baseline gap-2 py-2 border-b border-gray-50 {{ str_contains($personField, 'address') || str_starts_with($personField, 'relationship_to_') ? 'sm:col-span-2' : '' }}" style="justify-content:flex-start !important;flex-wrap:wrap;gap:8px;">
                                <span class="text-sm text-gray-500" style="flex-shrink:0;">{{ $fieldLabel }}</span>
                                <span class="text-sm font-medium text-gray-800" style="min-width:0;overflow-wrap:anywhere;text-align:left;">{{ $additionalPerson[$personField] ?? 'N/A' }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endforeach
    @endforeach
@endforeach
@endif
@endif

@if(request()->routeIs('desk-officer.cases.index', 'admin-barangay-cases', 'admin.barangay-cases.*', '*referrals*', '*bcpc-case-monitoring*', '*quarterly-reports*', '*archived-barangay-cases*', 'admin.users.archived', 'admin.users.index'))
<link rel="stylesheet" href="{{ asset('css/record-table-columns.css') }}">
@endif

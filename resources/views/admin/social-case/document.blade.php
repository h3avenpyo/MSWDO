@extends('admin.social-case.layout')
@section('title', 'Document - Social Case Study')

@section('content')
<div class="main" style="padding: 0; max-width: none;">
    <div id="documentContent"></div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/social-case.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        lucide.createIcons();
        const caseId = '{{ $caseId ?? '' }}';
        const agency = '{{ $agency ?? '' }}';
        if (caseId && agency) {
            loadDocument(caseId, agency);
        }
    });
</script>
@endpush

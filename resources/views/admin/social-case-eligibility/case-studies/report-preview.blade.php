<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>{{ $report->title }} · {{ $report->case_number }}</title>
</head>
<body class="bg-light"><main class="container py-5" style="max-width:900px">
    <div class="d-flex justify-content-between align-items-start mb-4"><div><h1 class="h3 mb-1">{{ $report->title }}</h1><p class="text-muted mb-0">{{ $report->case_number }} · Draft generated {{ optional($report->generated_at)->format('M d, Y h:i A') }}</p></div><a class="btn btn-outline-secondary" href="{{ route('admin.social-case-studies.step.show', [$socialCaseStudy, 'print_export']) }}">Continue workflow</a></div>
    <div class="d-flex gap-2 mb-3">
        <a class="btn btn-outline-primary" target="_blank" href="{{ route('admin.social-case-studies.reports.pdf', $socialCaseStudy) }}">Print</a>
        <a class="btn btn-primary" href="{{ route('admin.social-case-studies.reports.download', $socialCaseStudy) }}">Download PDF</a>
        <a class="btn btn-outline-success" href="{{ route('admin.social-case-studies.reports.word', $socialCaseStudy) }}">Download Word (.DOC)</a>
    </div>
    <article class="card shadow-sm"><div class="card-body p-4 p-md-5">
        <h2 class="h5">Case information</h2>
        <dl class="row"><dt class="col-sm-4">Client</dt><dd class="col-sm-8">{{ collect($report->snapshot['client'] ?? [])->filter()->implode(' ') }}</dd><dt class="col-sm-4">Purpose</dt><dd class="col-sm-8">{{ data_get($report->snapshot, 'case.purpose', '—') }}</dd><dt class="col-sm-4">Beneficiary</dt><dd class="col-sm-8">{{ collect($report->snapshot['beneficiary'] ?? [])->only(['beneficiary_first_name', 'beneficiary_middle_name', 'beneficiary_last_name'])->filter()->implode(' ') }}</dd></dl>
        <h2 class="h5 mt-4">Social worker assessment</h2><p>{{ data_get($report->snapshot, 'assessment.social_worker_assessment', '—') }}</p>
        <h2 class="h5 mt-4">Family composition</h2><ul class="mb-0">@forelse($report->snapshot['family'] ?? [] as $member)<li>{{ $member['full_name'] ?? 'Unnamed' }} — {{ $member['relationship'] ?? 'Relationship not recorded' }}</li>@empty<li>No family members recorded.</li>@endforelse</ul>
    </div></article>
</main></body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Social Case Study</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #F8FAFC; color: #111827; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .container { max-width: 720px; margin: 2rem auto; }
        .card { border-radius: 18px; border: 1px solid #E5E7EB; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="mb-1">Edit Social Case Study</h4>
                    <p class="text-muted mb-0">Client: {{ $socialCaseStudy->client->full_name }}</p>
                </div>
                <a href="/admin/social-case-eligibility/cases" class="btn btn-outline-secondary btn-sm">Back</a>
            </div>
            <form method="POST" action="{{ route('admin.social-case-studies.update', $socialCaseStudy) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Case Status</label>
                    <select name="status" class="form-select" required>
                        <option value="Open" {{ $socialCaseStudy->status === 'Open' ? 'selected' : '' }}>Open</option>
                        <option value="In Progress" {{ $socialCaseStudy->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="Closed" {{ $socialCaseStudy->status === 'Closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Interview Date</label>
                    <input type="date" name="interview_date" value="{{ $socialCaseStudy->interview_date?->format('Y-m-d') }}" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Summary</label>
                    <textarea name="summary" rows="6" class="form-control">{{ $socialCaseStudy->summary }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update Case Study</button>
            </form>
        </div>
    </div>
</body>
</html>

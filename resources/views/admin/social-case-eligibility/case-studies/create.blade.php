<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Social Case Study</title>
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
                    <h4 class="mb-1">Create Social Case Study</h4>
                    <p class="text-muted mb-0">Client: {{ $client->full_name }}</p>
                </div>
                <a href="/admin/social-case-eligibility/cases" class="btn btn-outline-secondary btn-sm">Back</a>
            </div>
            <form method="POST" action="{{ route('admin.social-case-studies.store', $client) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Case Status</label>
                    <select name="status" class="form-select" required>
                        <option value="Open">Open</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Closed">Closed</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Interview Date</label>
                    <input type="date" name="interview_date" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Summary</label>
                    <textarea name="summary" rows="6" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Create Case Study</button>
            </form>
        </div>
    </div>
</body>
</html>

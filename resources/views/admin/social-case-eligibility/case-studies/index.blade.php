<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Case Studies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #F8FAFC; color: #111827; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .container { max-width: 1080px; margin: 0 auto; padding: 2rem 1rem; }
        .card { border-radius: 18px; border: 1px solid #E5E7EB; }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Social Case Studies</h4>
                <p class="text-muted mb-0">Manage social case studies created after eligibility validation.</p>
            </div>
            <a href="/admin/social-case-eligibility" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-2"></i>Eligibility Dashboard</a>
        </div>
        <div class="card p-4">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Case Number</th>
                            <th>Client</th>
                            <th>Status</th>
                            <th>Interview Date</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($studies as $study)
                            <tr>
                                <td>{{ $study->id }}</td>
                                <td>{{ $study->case_number }}</td>
                                <td>{{ $study->client->full_name }}</td>
                                <td>{{ $study->status }}</td>
                                <td>{{ optional($study->interview_date)->format('M d, Y') ?? '-' }}</td>
                                <td>{{ $study->created_at->format('M d, Y') }}</td>
                                <td>
                                    <a href="/admin/social-case-eligibility/cases/{{ $study->id }}/edit" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <form method="POST" action="/admin/social-case-eligibility/cases/{{ $study->id }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $studies->links() }}</div>
        </div>
    </div>
</body>
</html>

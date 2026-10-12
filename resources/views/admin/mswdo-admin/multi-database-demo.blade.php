<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MSWDO Multi-Database Demo</title>
    @vite(['resources/css/admin-compat.css'])
</head>
<body class="bg-light">
<div class="container py-5">
    <h1 class="mb-4">MSWDO Multi-Database Setup</h1>
    <p class="text-muted">Authentication remains in the admin database while each module uses its own connection.</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Create a dummy record</h5>
            <form method="POST" action="{{ route('admin.multi-database.store') }}" class="row g-3">
                @csrf
                <div class="col-md-4">
                    <label class="form-label">Module</label>
                    <select name="module" class="form-select" required>
                        <option value="admin">Admin</option>
                        <option value="financial">Financial</option>
                        <option value="senior">Senior</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Title / Name</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary w-100">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-2"><div class="card text-center p-3"><h3>{{ $adminUsers }}</h3><small>Admin Users</small></div></div>
        <div class="col-md-2"><div class="card text-center p-3"><h3>{{ $adminProfiles }}</h3><small>Admin Profiles</small></div></div>
        <div class="col-md-2"><div class="card text-center p-3"><h3>{{ $financialApplications }}</h3><small>Financial Apps</small></div></div>
        <div class="col-md-2"><div class="card text-center p-3"><h3>{{ $seniorRecords }}</h3><small>Senior Records</small></div></div>
    </div>
</div>
</body>
</html>

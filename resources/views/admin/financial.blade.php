<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Assistance Module</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="h4 mb-3">Financial Assistance Module</h1>
            <p class="text-muted">Welcome, {{ session('admin_user_name') ?? 'Officer' }}.</p>
            <p>You have access to financial assistance features.</p>
            <a href="/admin/logout" class="btn btn-outline-danger">Logout</a>
        </div>
    </div>
</div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Password Reset | iSERVE SILANG</title>
    <link rel="icon" href="{{ asset('images/iserve_logo.jpg') }}" type="image/jpeg">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Montserrat', sans-serif; background-image: url('{{ asset('images/vawc-collage.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat; background-attachment: fixed; position: relative; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        body::before { content: ''; position: fixed; inset: 0; background: linear-gradient(135deg, rgba(196, 30, 58, 0.88) 0%, rgba(153, 23, 46, 0.88) 100%); z-index: -1; }
        .container { background: white; padding: 2.5rem; border-radius: 16px; box-shadow: 0 20px 60px rgba(196, 30, 58, 0.2); max-width: 480px; width: 100%; }
        .logo-container { text-align: center; margin-bottom: 2rem; }
        .logo { width: 80px; height: 80px; border-radius: 50%; margin-bottom: 1rem; border: 3px solid #C41E3A; object-fit: cover; }
        h2 { color: #C41E3A; margin-bottom: 0.5rem; font-size: 1.75rem; font-weight: 700; text-align: center; }
        .subtitle { color: #666; text-align: center; margin-bottom: 2rem; font-size: 0.9rem; }
        .form-group { margin-bottom: 1.25rem; }
        label { display: block; margin-bottom: 0.5rem; color: #333; font-weight: 600; font-size: 0.9rem; }
        input, select {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            font-family: 'Montserrat', sans-serif;
            transition: all 0.3s;
            background-color: #fff;
            color: #333;
        }
        select {
            margin-bottom: 1rem;
            cursor: pointer;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23666666'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1.25em 1.25em;
            padding-right: 2.5rem;
        }
        input:focus, select:focus {
            outline: none;
            border-color: #C41E3A;
            box-shadow: 0 0 0 3px rgba(196, 30, 58, 0.15);
        }
        button {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #C41E3A 0%, #99172E 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(196, 30, 58, 0.35);
        }
        .alert { padding: 1rem; border-radius: 8px; margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.75rem; }
        .alert-error { background: #ffebee; color: #c62828; border-left: 4px solid #f44336; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <img src="{{ asset('images/iserve_logo.jpg') }}" alt="iServe Logo" class="logo">
            <h2>Access Your Account</h2>
            <p class="subtitle">Enter the required details to continue.</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('password.reset.access.verify', ['id' => $resetRequest->id]) }}">
            @csrf
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="{{ old('full_name') }}" required autofocus>
            </div>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <label for="department">Department</label>
                <select id="department" name="department" required>
                    <option value="">Select department</option>
                    <option value="VAWC" @selected(old('department') === 'VAWC')>SWD</option>
                    <option value="BCPC" @selected(old('department') === 'BCPC')>BCPC</option>
                    <option value="BCPC/VAWC" @selected(old('department') === 'BCPC/VAWC')>BCPC/SWD</option>
                </select>
            </div>
            <button type="submit">
                <i class="fas fa-sign-in-alt"></i>
                Continue
            </button>
        </form>
    </div>
</body>
</html>

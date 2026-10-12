<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password - MSWDO Silang</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('IserveIcon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700,800" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            font-weight: 400;
            color: #1F2937;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            background: linear-gradient(160deg, #F0F7FF 0%, #E3EEFA 55%, #DCE9F7 100%);
        }

        .bg-blur {
            position: fixed;
            z-index: 0;
            border-radius: 50%;
            filter: blur(90px);
            opacity: .45;
            pointer-events: none;
        }

        .bg-blur--one {
            width: 420px;
            height: 420px;
            top: -80px;
            left: -80px;
            background: radial-gradient(circle, #BFDBFE 0%, transparent 70%);
        }

        .bg-blur--two {
            width: 480px;
            height: 480px;
            bottom: -120px;
            right: -80px;
            background: radial-gradient(circle, #93C5FD 0%, transparent 70%);
        }

        .page-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
        }

        .auth-card {
            background: #FFFFFF;
            border-radius: 24px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, .12);
            overflow: hidden;
            padding: 2.5rem 2rem;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            color: #64748B;
            font-size: .8438rem;
            font-weight: 500;
            text-decoration: none;
            transition: color .2s ease;
            margin-bottom: 1.5rem;
        }

        .back-link:hover {
            color: #1D4ED8;
        }

        .login-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .login-title {
            margin: 0 0 .5rem;
            font-size: 1.75rem;
            font-weight: 700;
            color: #0F172A;
        }

        .login-subtitle {
            margin: 0;
            font-size: .9375rem;
            color: #64748B;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-size: .7813rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: .5rem;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 1.05rem;
            top: 50%;
            transform: translateY(-50%);
            width: 1.15rem;
            height: 1.15rem;
            color: #94A3B8;
        }

        .form-input {
            width: 100%;
            height: 56px;
            padding: .9rem 1rem .9rem 3.1rem;
            font-family: 'Poppins', sans-serif;
            font-size: .9375rem;
            color: #1F2937;
            border: 1px solid #E2E8F0;
            border-radius: 14px;
            background: #F8FAFC;
            outline: none;
            transition: border-color .2s ease, background-color .2s ease, box-shadow .2s ease;
        }

        .form-input:focus {
            border-color: #1D4ED8;
            background: #FFFFFF;
            box-shadow: 0 0 0 4px rgba(29, 78, 216, .12);
        }

        .form-input:focus~.input-icon {
            color: #1D4ED8;
        }

        .submit-button {
            width: 100%;
            height: 56px;
            border: none;
            border-radius: 14px;
            font-family: 'Poppins', sans-serif;
            font-size: .9375rem;
            font-weight: 600;
            letter-spacing: .03em;
            text-transform: uppercase;
            color: #FFFFFF;
            background: linear-gradient(135deg, #1E3A8A 0%, #1D4ED8 100%);
            box-shadow: 0 10px 20px rgba(29, 78, 216, .25);
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
            margin-top: 1rem;
        }

        .submit-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 28px rgba(29, 78, 216, .32);
            filter: brightness(1.06);
        }

        .submit-button:active {
            transform: translateY(0);
        }

        .form-error {
            background: #FEF2F2;
            border: 1px solid #FECACA;
            color: #B91C1C;
            border-radius: 12px;
            padding: .75rem 1rem;
            margin-bottom: 1.25rem;
            font-size: .8438rem;
        }

        .form-success {
            background: #F0FDF4;
            border: 1px solid #BBF7D0;
            color: #15803D;
            border-radius: 12px;
            padding: .75rem 1rem;
            margin-bottom: 1.25rem;
            font-size: .8438rem;
        }

        .form-info {
            background: #EFF6FF;
            border: 1px solid #BFDBFE;
            color: #1E3A8A;
            border-radius: 12px;
            padding: .75rem 1rem;
            margin-bottom: 1.25rem;
            font-size: .8438rem;
        }
    </style>
</head>

<body>
    <div class="bg-blur bg-blur--one" aria-hidden="true"></div>
    <div class="bg-blur bg-blur--two" aria-hidden="true"></div>

    <div class="page-wrapper">
        <div class="auth-card">
            <a href="{{ route('admin.login.form') }}" class="back-link">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5"
                    stroke="currentColor" width="16" height="16">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                </svg>
                Back to Login
            </a>

            <div class="login-header">
                <h1 class="login-title">Forgot Password?</h1>
                <p class="login-subtitle">Enter your email address to request a password reset. Once approved by admin, you'll receive an email with the reset link.</p>
            </div>

            @if ($errors->any())
                <div class="form-error" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.password.send-link') }}">
                @csrf

                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-wrap">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                        </svg>
                        <input type="email" id="email" name="email" required autocomplete="email"
                            class="form-input" placeholder="admin@mswdo.gov.ph" value="{{ old('email') }}">
                    </div>
                </div>

                <button type="submit" class="submit-button">
                    Submit Request
                </button>
            </form>
        </div>
    </div>

    <script>
        @if(session('error'))
            Swal.fire({
                title: 'Error',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonColor: '#DC2626',
                confirmButtonText: 'OK',
                background: '#ffffff',
                customClass: { popup: 'rounded-4 shadow-lg' }
            });
        @endif

        @if(session('success'))
            Swal.fire({
                title: 'Success',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: '#16A34A',
                confirmButtonText: 'OK',
                background: '#ffffff',
                customClass: { popup: 'rounded-4 shadow-lg' }
            });
        @endif

        @if(session('info'))
            Swal.fire({
                title: 'Information',
                text: '{{ session('info') }}',
                icon: 'info',
                confirmButtonColor: '#1D4ED8',
                confirmButtonText: 'OK',
                background: '#ffffff',
                customClass: { popup: 'rounded-4 shadow-lg' }
            });
        @endif
    </script>
</body>
</html>

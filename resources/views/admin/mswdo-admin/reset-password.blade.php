<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password - MSWDO Silang</title>
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

        .password-toggle {
            position: absolute;
            right: .4rem;
            top: 50%;
            transform: translateY(-50%);
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: none;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            color: #94A3B8;
            transition: color .2s ease, background-color .2s ease;
        }

        .password-toggle:hover {
            color: #1D4ED8;
            background: #EFF6FF;
        }

        .password-toggle svg {
            width: 1.15rem;
            height: 1.15rem;
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
                <h1 class="login-title">Reset Password</h1>
                <p class="login-subtitle">Enter your new password below.</p>
            </div>

            @if ($errors->any())
                <div class="form-error" role="alert">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('admin.password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="form-group">
                    <label for="password" class="form-label">New Password</label>
                    <div class="input-wrap">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                        <input type="password" id="password" name="password" required
                            autocomplete="new-password" class="form-input"
                            placeholder="Enter new password" minlength="8">
                        <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <div class="input-wrap">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            autocomplete="new-password" class="form-input"
                            placeholder="Confirm new password" minlength="8">
                        <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation', this)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="submit-button">
                    Reset Password
                </button>
            </form>
        </div>
    </div>

    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const svg = button.querySelector('svg');

            if (input.type === 'password') {
                input.type = 'text';
                svg.innerHTML = `
                    <path d="M3.98 8.223a10.477 10.477 0 0 0-.639 2.277 1 1 0 0 0 0 .696 10.75 10.75 0 0 0 5.98 7.114" />
                    <path d="M14.598 17.995a10.75 10.75 0 0 0 5.382-5.382 1 1 0 0 0 0-.696 10.75 10.75 0 0 0-5.382-5.382" />
                    <path d="m9.172 9.172 5.656 5.656" />
                `;
            } else {
                input.type = 'password';
                svg.innerHTML = `
                    <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" />
                    <circle cx="12" cy="12" r="3" />
                `;
            }
        }
    </script>

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

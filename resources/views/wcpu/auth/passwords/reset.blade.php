<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | iSERVE</title>
    <link rel="icon" href="{{ asset('images/iserve_logo.jpg') }}" type="image/jpeg">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Montserrat', sans-serif;
            background-image: url('{{ asset('images/vawc-collage.png') }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(196, 30, 58, 0.9);
            z-index: -1;
        }
        .container {
            background: white;
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(196, 30, 58, 0.3);
            max-width: 450px;
            width: 100%;
        }
        .logo-container {
            text-align: center;
            margin-bottom: 2rem;
        }
        .logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 1rem;
            border: 3px solid #C41E3A;
        }
        h2 { 
            color: #C41E3A;
            margin-bottom: 0.5rem;
            font-size: 1.75rem;
            font-weight: 700;
            text-align: center;
        }
        .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 2rem;
            font-size: 0.9rem;
        }
        .form-group { 
            margin-bottom: 1.5rem; 
        }
        label { 
            display: block; 
            margin-bottom: 0.5rem; 
            color: #333; 
            font-weight: 600;
            font-size: 0.9rem;
        }
        .input-wrapper {
            position: relative;
        }
        input {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            font-family: 'Montserrat', sans-serif;
            transition: all 0.3s;
        }
        input[type="password"] {
            padding-right: 3rem;
        }
        input:focus {
            outline: none;
            border-color: #C41E3A;
            box-shadow: 0 0 0 3px rgba(196, 30, 58, 0.1);
        }
        .toggle-password {
            position: absolute;
            top: 0;
            right: 0;
            width: 3rem;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            cursor: pointer;
            transition: color 0.3s;
            background: transparent;
            border: none;
            padding: 0;
            margin: 0;
            z-index: 1;
        }
        .toggle-password:hover {
            color: #C41E3A;
        }
        button:not(.toggle-password) {
            width: 100%;
            padding: 1rem;
            background: #C41E3A;
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
            margin-top: 0.5rem;
        }
        button:not(.toggle-password):hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(196, 30, 58, 0.3);
        }
        button:not(.toggle-password):active {
            transform: translateY(0);
        }
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }
        .alert-error {
            background: #ffebee;
            color: #c62828;
            border-left: 4px solid #f44336;
        }
        .password-requirements {
            background: #fdf0f2;
            border-left: 4px solid #C41E3A;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-size: 0.85rem;
        }
        .password-requirements h4 {
            color: #C41E3A;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        .password-requirements ul {
            margin-left: 1.25rem;
            color: #555;
        }
        .password-requirements li {
            margin-bottom: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <img src="{{ asset('images/iserve_logo.jpg') }}" alt="iSERVE Logo" class="logo">
            <h2>Reset Password</h2>
            <p class="subtitle">Women and Child Protection Unit</p>
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

        <div class="password-requirements">
            <h4><i class="fas fa-info-circle"></i> Password Requirements:</h4>
            <ul>
                <li>Minimum 8 characters</li>
                <li>Both password fields must match</li>
            </ul>
        </div>

        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ request('email') ?? old('email') }}">
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" value="{{ request('email') ?? old('email') }}" required autofocus disabled readonly style="background-color: #f5f5f5; cursor: not-allowed;">
            </div>
            
            <div class="form-group">
                <label for="password"><i class="fas fa-lock"></i> New Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password" name="password" required minlength="8" placeholder="Enter your new password">
                    <button type="button" class="toggle-password" onclick="togglePassword('password', this)" aria-label="Show password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password_confirmation"><i class="fas fa-lock"></i> Confirm Password</label>
                <div class="input-wrapper">
                    <input type="password" id="password_confirmation" name="password_confirmation" required minlength="8" placeholder="Re-enter your new password">
                    <button type="button" class="toggle-password" onclick="togglePassword('password_confirmation', this)" aria-label="Show password">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>
            
            <button type="submit">
                <i class="fas fa-check-circle"></i>
                Reset Password
            </button>
        </form>
    </div>
    
    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                button.setAttribute('aria-label', 'Hide password');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                button.setAttribute('aria-label', 'Show password');
            }
        }
        
        // Real-time password match validation
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('password_confirmation');
        
        confirmPassword.addEventListener('input', function() {
            if (password.value && confirmPassword.value) {
                if (password.value !== confirmPassword.value) {
                    confirmPassword.style.borderColor = '#f44336';
                } else {
                    confirmPassword.style.borderColor = '#4caf50';
                }
            }
        });
    </script>
</body>
</html>

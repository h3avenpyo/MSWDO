<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="description" content="WCPU Support System Login - Secure access for administrators and desk officers">
  <title>Login | iSERVE SILANG</title>
  <link rel="icon" href="{{ asset('images/iserve_logo.jpg') }}" type="image/jpeg">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Raleway:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="font-['Montserrat','Raleway',sans-serif]">

  <section class="relative flex items-center justify-center min-h-screen bg-cover bg-left bg-no-repeat" 
           style="background-image: url('{{ asset('images/vawc-collage.png') }}')">
    <div class="absolute inset-0 bg-black/70"></div>
    <div class="absolute top-0 right-0 bottom-0 w-1/2 bg-red-900 clip-path-polygon hidden sm:block"></div>

    <!--Left side form-->
    <div class="relative z-10 w-full sm:w-11/12 max-w-5xl mx-auto flex flex-col sm:flex-row bg-white/95 rounded-2xl shadow-2xl overflow-hidden">
    <div class="flex-1 flex flex-col justify-center items-center text-center p-6 sm:p-10">
        <div class="flex flex-col items-center">
            <img src="{{ asset('images/iserve_logo.jpg') }}"
            alt="iServe Logo"
            class="w-24 h-24 sm:w-28 sm:h-28 rounded-full object-cover mb-6">
        </div>
        <h2 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-4">
            Welcome to the BCPC and SWD Portal
        </h2>
        <p class="text-gray-700 mb-6 max-w-md">
            Access the secure portal to manage BCPC and SWD cases, coordinate child protection,
            and deliver social welfare services to children, families, and communities.
        </p>
        <ul class="space-y-3 text-gray-700 max-w-md w-full text-left mb-7">
            <li class="flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-2"></i> Secure case management system
            </li>
            <li class="flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-2"></i> Resource allocation and tracking
            </li>
            <li class="flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-2"></i> Comprehensive reporting tools
            </li>
            <li class="flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-2"></i> Support coordination platform
            </li>
        </ul>
    </div>


      <!-- Right Side Form -->
       <div class="flex-1 bg-white p-6 sm:p-10 flex flex-col justify-center">
        <div class="text-center mb-2">
          <h1 class="text-2xl sm:text-3xl font-bold text-red-700 mt-10">Sign In</h1>
        </div>

        <form id="login-form" method="POST" action="{{ route('login') }}" class="space-y-4 max-w-sm mx-auto w-full">
          @csrf
          <input type="hidden" name="user_type" id="user_type" value="admin">

          <div>
            <label for="login" class="block mb-1 font-medium text-gray-800">Email or Username</label>
            <div class="relative">
              <i class="fas fa-envelope absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
              <input type="text" id="login" name="login" value="{{ old('login') }}" 
                     class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none"
                     placeholder="Enter your email or username" required autofocus>
            </div>
          </div>

        <div>
            <label for="password" class="block mb-1 font-medium text-gray-800">Password</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input type="password" id="password" name="password"
                class="w-full pl-10 pr-14 py-3 border border-gray-300 rounded-md 
                  focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none"
                placeholder="Enter your password" required aria-describedby="password-constraints">

            <button type="button" id="toggle-password"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <i class="far fa-eye"></i>
            </button>
        </div>

        <div id="password-constraints" class="sr-only">
            Password must be at least 8 characters long
        </div>
     </div>

          @error('login')
            @php
              $isLocked = session('locked_until') && now()->timestamp < session('locked_until');
            @endphp
            <div class="p-3 {{ $isLocked ? 'bg-red-50 border-red-500 text-red-700' : 'bg-yellow-50 border-yellow-500 text-yellow-700' }} border-l-4 rounded-md">
              <div class="flex items-start">
                <i class="fas fa-exclamation-triangle mt-0.5 mr-3"></i>
                <span class="text-sm" @if($isLocked) id="lockout-message" data-locked-until="{{ session('locked_until') }}" @endif>{{ $message }}</span>
              </div>
            </div>
          @enderror
          @error('email')
            <div class="p-3 bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 rounded-md">
              <div class="flex items-start">
                <i class="fas fa-exclamation-triangle mt-0.5 mr-3"></i>
                <span class="text-sm">{{ $message }}</span>
              </div>
            </div>
          @enderror

        <div class="text-right -mt-4">
            <a href="#" id="forgot-password-link" 
                class="text-sm text-red-600 hover:text-red-900">
                Forgot password?
            </a>
        </div>

          <button type="submit" 
                  class="w-full py-3 bg-red-700 hover:bg-red-800 text-white font-semibold rounded-md shadow-md transition transform hover:-translate-y-1">
            Login
          </button>

          <div class="text-center">
            <p class="text-gray-600 text-sm">
              Having trouble? 
              <a href="https://mail.google.com/mail/?view=cm&fs=1&to=wcpu.silang4118@gmail.com&su=Support%20Request%20-%20Login%20Assistance" 
                 target="_blank" 
                 class="text-red-700 font-semibold hover:underline">Contact Support</a>
            </p>
          </div>
        </form>
      </div>
    </div>
  </section>

  <!-- Forgot Password Modal -->
  <div id="forgot-password-modal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center backdrop-blur-sm">
    <div class="bg-white rounded-lg shadow-xl w-11/12 max-w-lg animate-fade-in">
      <div class="flex justify-between items-center p-4 text-black bg-purple-50 border-b">
        <h2 class="text-xl font-semibold text-red-700">Password Reset Request</h2>
        <span class="close-modal cursor-pointer text-2xl hover:text-purple-700 transition">&times;</span>
      </div>
      <div class="p-6">
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4">
          <div class="flex items-start">
            <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-3"></i>
            <div class="text-sm text-gray-700">
              <p class="font-semibold mb-1">For Admins:</p>
              <p>Enter your email address to receive a password reset link.</p>
              <p class="font-semibold mt-3 mb-1">For Desk Officers:</p>
              <p>Enter your username to request a password reset. An admin will be notified to assist you.</p>
            </div>
          </div>
        </div>
        <form id="reset-password-form" class="space-y-4">
          @csrf
          <div>
            <label for="reset-identifier" class="block mb-2 font-medium text-gray-800">Email or Username</label>
            <div class="relative">
              <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
              <input type="text" id="reset-identifier" name="reset-identifier"
                     class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-md focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none"
                     placeholder="Enter your email or username" required>
            </div>
          </div>
          <div id="reset-message" class="hidden"></div>
          <button type="submit" 
                  class="w-full py-3 bg-red-700 hover:bg-red-800 text-white font-semibold rounded-md shadow-md transition">
            <i class="fas fa-paper-plane mr-2"></i>Submit Request
          </button>
        </form>
      </div>
    </div>
  </div>

  <style>
    .clip-path-polygon {
      clip-path: polygon(20% 0, 100% 0, 100% 100%, 0% 100%);
    }
    @keyframes fade-in {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
      animation: fade-in 0.3s ease forwards;
    }
    .sr-only {
      position: absolute;
      width: 1px;
      height: 1px;
      padding: 0;
      margin: -1px;
      overflow: hidden;
      clip: rect(0,0,0,0);
      white-space: nowrap;
      border: 0;
    }
    /* Spinner animation */
    @keyframes spin {
      from {
        transform: rotate(0deg);
      }
      to {
        transform: rotate(360deg);
      }
    }
    .fa-spin {
      animation: spin 1s linear infinite;
    }
    /* Button disabled state */
    button:disabled {
      opacity: 0.7;
      cursor: not-allowed;
    }
  </style>

  <dialog id="login-message-dialog" aria-labelledby="login-message-title" class="m-auto rounded-xl border-0 p-6 shadow-xl w-full max-w-md backdrop:bg-black/50">
    <h2 id="login-message-title" class="text-xl font-semibold mb-4">{{ session('password_reset_success') ? 'Password Reset Successful' : 'Notice' }}</h2>
    <p id="login-message-text" class="text-gray-700 mb-6"></p>
    <button type="button" id="login-message-ok" class="w-full py-3 rounded-md bg-red-700 text-white font-semibold">OK</button>
  </dialog>
  <div id="inactivity-login-message" hidden>{{ session('inactivity_message', (request()->boolean('inactive') && !session('status')) ? \App\Http\Middleware\EnforceInactivityTimeout::MESSAGE : '') }}</div>
  <div id="password-reset-success-message" hidden>{{ session('password_reset_success') }}</div>
  <script src="{{ asset('js/login.js') }}"></script>
  <script>
    // Auto-show forgot password modal if account is permanently locked
    @if(session('show_forgot_password'))
      document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('forgot-password-modal');
        if (modal) {
          modal.style.display = 'flex';
          document.body.style.overflow = 'hidden';
        }
      });
    @endif
  </script>
</body>
</html>

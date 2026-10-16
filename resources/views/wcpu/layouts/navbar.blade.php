<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'My Website')</title>
    @php $hasManifest = file_exists(public_path('build/manifest.json')); @endphp
    @if($hasManifest)
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <script defer src="{{ asset('js/app.js') }}"></script>
    @endif
    <link rel="icon" href="{{ asset('images/iserve_logo.jpg') }}" type="image/jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>

</head>
<body class="font-sans bg-black text-white" id="root">
<header id="navbar" class="fixed top-0 left-0 w-full z-50 bg-transparent text-white transition-colors duration-300 ease-in-out">
  <div class="max-w-7xl mx-auto w-full flex items-center justify-between px-6 lg:px-10 py-4">
    
    <!-- Desktop Header -->
    <div class="hidden lg:flex items-center justify-between w-full max-w-6xl">
      
      <!-- Logo -->
      <div class="flex items-center gap-2 text-2xl font-bold transition-colors duration-300">
        <img src="{{ asset('images/wcpu.png') }}" alt="SWD Logo" class="h-10 w-auto">
        <span class="transition-colors duration-350"><a href="#home">WCPU</a></span>
      </div>

      <!-- Desktop Navigation -->
      <nav class="flex items-center space-x-10 text-lg font-semibold transition-colors duration-300">
        <a href="#law" class="transition-colors duration-300 hover:text-orange-400">Legal Framework</a>
        <a href="#contact" class="transition-colors duration-300 hover:text-orange-400">Contact</a>
        <a href="#about" class="transition-colors duration-300 hover:text-orange-400">About</a>
        <a href="#help" class="bg-orange-500 hover:bg-orange-400 px-4 py-1 rounded text-white">Get Help</a>
      </nav>
    </div>

    <!-- Mobile Header -->
    <div class="flex justify-between w-full lg:hidden">
      <!-- Mobile Logo -->
      <div class="flex items-center gap-2 text-xl font-bold transition-colors duration-300">
        <img src="{{ asset('images/wcpu.png') }}" alt="SWD Logo" class="h-10 w-auto">
        <span class="transition-colors duration-350"><a href="#home">WCPU</a></span>
    </div>

      <!-- Hamburger Button -->
      <button id="mobile-menu-button" class="transition-colors duration-300" aria-expanded="false">
        <svg id="hamburger-icon" class="h-6 w-6 text-white transition-colors duration-300" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
    </div>
  </div>

  <!-- Mobile Menu -->
  <div id="mobile-menu"
    class="fixed top-16 left-0 right-0 z-40 hidden flex flex-col items-center text-center gap-4 text-base font-semibold text-black px-6 py-6 
           bg-white/80 backdrop-blur-lg shadow-lg rounded-b-xl transform transition-all duration-300 ease-in-out 
           origin-top opacity-0 scale-y-95 lg:hidden">
    <a href="#law" class="hover:text-orange-400 transition">Legal Framework</a>
    <a href="#contact" class="block py-2">Contact</a>
    <a href="#about" class="block py-2">About</a>
    <a href="#help" class="block w-full max-w-xs bg-orange-600 hover:bg-orange-700 px-4 py-2 rounded text-white text-center">
      Get Help
    </a>
  </div>
</header>




    <!-- Page Content -->
    <main class="min-h-screen">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-purple-900 text-white">
        <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
              <div class="justify-center items-center">
                <div>
                <h3 class="text-lg font-semibold mb-4 text text-center">In partnership with:</h3>
                </div>
                  <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 justify-items-center py-5">
                    <img src="{{ asset('images/cvsu.png') }}" 
                      alt="CVSU Logo" 
                      class="w-16 sm:w-20 md:w-24 lg:w-28 h-auto">
                    <img src="{{ asset('images/dswd.png') }}" 
                      alt="DSWD Logo" 
                      class="w-16 sm:w-20 md:w-24 lg:w-28 h-auto">
                    <img src="{{ asset('images/silang.png') }}" 
                      alt="Silang Logo" 
                      class="w-16 sm:w-20 md:w-24 lg:w-28 h-auto">
                    <img src="{{ asset('images/wcpu.png') }}" 
                      alt="WCPU Logo" 
                      class="w-20 sm:w-20 md:w-24 lg:w-28 h-auto">
                  </div>

              </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4 text text-center">Quick Links</h3>
                    <ul class="space-y-2 text-center">
                        <li><a href="#law" class="text-gray-200 hover:text-white te">Legal Framework</a></li>
                        <li><a href="#contact" class="text-gray-200 hover:text-white">Contact</a></li>
                        <li><a href="#about" class="text-gray-200 hover:text-white">About</a></li>
                        <li><a href="#help" class="text-gray-200 hover:text-white">Get Help</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-center">Contact Us</h3>
                    <address class="not-italic text-gray-200 text-center">
                        Biga II, Silang, Cavite<br>
                        
                        Email: wcpusilang@gmail.com<br>
                    </address>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4 text-center">Social Media Links</h3>
                    <div class="flex justify-center">
                      <a href="https://www.facebook.com/share/17LRbJRaFd/" target="_blank" class="text-white-600 hover:text-blue-800 text-3xl">
                      <i class="fab fa-facebook"></i>
                      </a>
                  </div>
                </div>
            </div>
            <div class="mt-8 pt-8 border-t border-[#C2BFBC]">
                <p class="text-center text-gray-200 text-sm">&copy; 2025 Women and Child Protection Unit. All rights reserved.</p>
            </div>
        </div>
    </footer>

<!-- JavaScript for toggle -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const menuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    menuButton.addEventListener('click', function () {
        const expanded = menuButton.getAttribute('aria-expanded') === 'true';
        menuButton.setAttribute('aria-expanded', !expanded);
        mobileMenu.classList.toggle('hidden');

        // Optional: for smoother transitions
        setTimeout(() => {
            mobileMenu.classList.toggle('opacity-0');
            mobileMenu.classList.toggle('scale-y-95');
        }, 10);
    });
});




  document.addEventListener("DOMContentLoaded", () => {
    const navbar = document.getElementById("navbar");
    let lastScrollTop = 0;

    window.addEventListener("scroll", () => {
      const scrollTop = window.scrollY;

      if (scrollTop > 10) {
        navbar.classList.remove("bg-transparent");
        navbar.classList.add("bg-white/30", "backdrop-blur-md", "shadow-md");
      } else {
        navbar.classList.add("bg-transparent");
        navbar.classList.remove("bg-white/30", "backdrop-blur-md", "shadow-md");
      }

      // Optional: hide on scroll down, show on scroll up
      if (scrollTop > lastScrollTop && scrollTop > 20) {
        navbar.classList.add("-translate-y-full");
      } else {
        navbar.classList.remove("-translate-y-full");
      }

      lastScrollTop = scrollTop;
    });
  });


  
   document.addEventListener("DOMContentLoaded", () => {
    const navbar = document.getElementById("navbar");
    const hamburgerIcon = document.getElementById("hamburger-icon");

    // Select all color-relevant elements inside the navbar
    const links = navbar.querySelectorAll("a, span, svg");

    function setNavbarState(isScrolled) {
      if (isScrolled) {
        navbar.classList.remove("bg-transparent");
        navbar.classList.add("bg-white/70", "backdrop-blur-md", "shadow-md", "text-black");

        links.forEach(el => {
          el.classList.remove("text-white");
          el.classList.add("text-black");
        });

        hamburgerIcon?.classList.remove("text-white");
        hamburgerIcon?.classList.add("text-black");
      } else {
        navbar.classList.remove("bg-white/70", "backdrop-blur-md", "shadow-md", "text-black");
        navbar.classList.add("bg-transparent", "text-white");

        links.forEach(el => {
          el.classList.remove("text-black");
          el.classList.add("text-white");
        });

        hamburgerIcon?.classList.remove("text-black");
        hamburgerIcon?.classList.add("text-white");
      }
    }

    // Listen for scroll
    window.addEventListener("scroll", () => {
      const scrollY = window.scrollY;
      setNavbarState(scrollY > 600);
    });
  });
</script>

<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

@include('components.inactivity-timeout')
</body>
</html>

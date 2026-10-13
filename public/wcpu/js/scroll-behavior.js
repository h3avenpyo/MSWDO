document.addEventListener('DOMContentLoaded', function() {
    const header = document.querySelector('.top-header');
    if (!header) return; // Exit if no header found
    
    let lastScroll = 0;
    const scrollThreshold = 50; // Pixels to scroll before hiding
    let ticking = false;
    let isMobile = window.innerWidth < 992; // Check if mobile view
    
    // Function to handle header and burger menu visibility
    function updateHeader() {
        const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
        const burgerMenu = document.querySelector('.mobile-menu-toggle');
        
        // Reset at the top of the page
        if (currentScroll <= 0) {
            header.classList.remove('header-hide');
            header.classList.add('header-show');
            if (burgerMenu) {
                burgerMenu.classList.remove('header-hide');
                burgerMenu.classList.add('header-show');
            }
            lastScroll = 0;
            ticking = false;
            return;
        }
        
        // Only apply the effect on mobile or when in mobile view
        if (isMobile || window.innerWidth < 992) {
            // Scrolling down
            if (currentScroll > lastScroll && currentScroll > scrollThreshold) {
                header.classList.add('header-hide');
                header.classList.remove('header-show');
                if (burgerMenu) {
                    burgerMenu.classList.add('header-hide');
                    burgerMenu.classList.remove('header-show');
                }
            } 
            // Scrolling up
            else if (currentScroll < lastScroll - 5) { // Small threshold to prevent jitter
                header.classList.remove('header-hide');
                header.classList.add('header-show');
                if (burgerMenu) {
                    burgerMenu.classList.remove('header-hide');
                    burgerMenu.classList.add('header-show');
                }
            }
        } else {
            // Always show header and burger menu on desktop
            header.classList.remove('header-hide');
            header.classList.add('header-show');
            if (burgerMenu) {
                burgerMenu.classList.remove('header-hide');
                burgerMenu.classList.add('header-show');
            }
        }
        
        lastScroll = currentScroll <= 0 ? 0 : currentScroll;
        ticking = false;
    }
    
    // Throttle scroll events for better performance
    function handleScroll() {
        if (!ticking) {
            window.requestAnimationFrame(updateHeader);
            ticking = true;
        }
    }
    
    // Add scroll event listener
    window.addEventListener('scroll', handleScroll, { passive: true });
    
    // Update mobile state on resize
    function checkMobile() {
        isMobile = window.innerWidth < 992;
        // Reset header state on resize
        if (!isMobile) {
            header.classList.remove('header-hide');
            header.classList.remove('header-show');
        }
    }
    
    // Listen for window resize
    window.addEventListener('resize', checkMobile);
    
    // Initial check
    checkMobile();
});

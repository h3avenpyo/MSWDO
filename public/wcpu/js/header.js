document.addEventListener('DOMContentLoaded', function() {
    // Initialize header auto-hide on scroll
    initHeaderAutoHide();
    
    // Re-initialize when the window is resized
    window.addEventListener('resize', initHeaderAutoHide);
    
    // Re-initialize when the sidebar is toggled
    const sidebarToggle = document.querySelector('.toggle-sidebar');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            setTimeout(initHeaderAutoHide, 350); // Wait for sidebar animation
        });
    }
});

function initHeaderAutoHide() {
    const header = document.querySelector('.top-header');
    if (!header) return;

    let lastScrollY = window.scrollY;
    let ticking = false;
    const threshold = 8; // Minimum scroll delta to trigger hide/show
    const headerHeight = header.offsetHeight;
    
    // Set initial padding on main content to account for header
    const mainContent = document.querySelector('.main-content');
    if (mainContent) {
        mainContent.style.paddingTop = `${headerHeight + 20}px`;
    }

    const onScroll = () => {
        const currentY = window.scrollY;
        const diff = currentY - lastScrollY;
        
        // Skip if we're already processing a scroll event
        if (ticking) return;
        
        // Use requestAnimationFrame for performance
        window.requestAnimationFrame(() => {
            // Only update if we've scrolled more than the threshold
            if (Math.abs(diff) > threshold) {
                const isScrollingDown = diff > 0 && currentY > headerHeight;
                const isNearTop = currentY <= headerHeight;
                const isMobile = window.innerWidth <= 1366; // Match your mobile breakpoint
                
                if (isScrollingDown && isMobile) {
                    // Scrolling down on mobile
                    header.style.transform = 'translateY(-100%)';
                    header.style.boxShadow = 'none';
                    // Also hide the mobile menu toggle
                    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
                    if (mobileMenuToggle) {
                        mobileMenuToggle.classList.add('hide');
                    }
                } else if ((!isScrollingDown || isNearTop) && isMobile) {
                    // Scrolling up or at top on mobile
                    header.style.transform = 'translateY(0)';
                    header.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.08)';
                    // Show the mobile menu toggle
                    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
                    if (mobileMenuToggle) {
                        mobileMenuToggle.classList.remove('hide');
                    }
                } else if (!isMobile) {
                    // At the top or on desktop
                    header.style.transform = 'translateY(0)';
                    header.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.08)';
                }
                
                lastScrollY = currentY;
            }
            ticking = false;
        });
        
        ticking = true;
    };

    // Add scroll event listener
    window.addEventListener('scroll', onScroll, { passive: true });
    
    // Initial check in case we load the page with scroll position
    onScroll();
    
    // Clean up function to remove event listener if needed
    return () => {
        window.removeEventListener('scroll', onScroll, { passive: true });
    };
}

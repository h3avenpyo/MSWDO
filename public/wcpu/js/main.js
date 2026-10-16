document.addEventListener('DOMContentLoaded', function() {
    // Skip initialization if no sidebar is present
    if (!document.getElementById('sidebar')) return;
    
    // Elements
    const sidebar = document.getElementById('sidebar');
    const toggleSidebarBtn = document.querySelector('.toggle-sidebar');
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const sidebarOverlay = document.querySelector('.sidebar-overlay');
    const body = document.body;
    
    // Check if we're on mobile/tablet (extended to include larger iPads)
    const isMobileView = () => window.innerWidth < 1367;
    
    // Initialize sidebar state
    const initSidebar = () => {
        if (!isMobileView()) {
            const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                const icon = toggleSidebarBtn?.querySelector('i');
                if (icon) {
                    icon.style.transform = 'rotate(180deg)';
                    icon.style.transition = 'transform 0.3s ease';
                }
            } else {
                sidebar.classList.remove('collapsed');
            }
        } else {
            // On mobile, always start with sidebar hidden
            sidebar.classList.remove('collapsed', 'visible');
            body.classList.remove('sidebar-open');
            if (sidebarOverlay) {
                sidebarOverlay.style.display = 'none';
                sidebarOverlay.style.opacity = '0';
            }
            document.body.style.overflow = '';
        }
    };
    
    // Toggle sidebar state
    const toggleSidebarState = (e) => {
        if (e) e.preventDefault();
        
        if (isMobileView()) {
            const isOpening = !sidebar.classList.contains('visible');
            
            if (isOpening) {
                // Opening the sidebar
                sidebar.style.transform = 'translateX(0)';
                body.style.overflow = 'hidden';
                
                // Show overlay with fade effect
                if (sidebarOverlay) {
                    sidebarOverlay.style.display = 'block';
                    setTimeout(() => {
                        sidebarOverlay.style.opacity = '1';
                    }, 10);
                }
                
                // Update menu icon to close (X)
                if (mobileMenuToggle) {
                    const icon = mobileMenuToggle.querySelector('i');
                    if (icon) {
                        icon.className = 'fas fa-times';
                    }
                }
                
                // Add visible class after transition starts
                setTimeout(() => {
                    sidebar.classList.add('visible');
                }, 10);
                
            } else {
                // Closing the sidebar
                sidebar.style.transform = 'translateX(-100%)';
                body.style.overflow = '';
                
                // Hide overlay with fade effect
                if (sidebarOverlay) {
                    sidebarOverlay.style.opacity = '0';
                    setTimeout(() => {
                        if (sidebarOverlay) {
                            sidebarOverlay.style.display = 'none';
                        }
                    }, 300);
                }
                
                // Update menu icon to hamburger
                if (mobileMenuToggle) {
                    const icon = mobileMenuToggle.querySelector('i');
                    if (icon) {
                        icon.className = 'fas fa-bars';
                    }
                }
                
                // Remove visible class after transition
                setTimeout(() => {
                    sidebar.classList.remove('visible');
                }, 300);
            }
            
        } else {
            // On desktop, toggle collapsed state with smooth transition
            const isCollapsed = !sidebar.classList.contains('collapsed');
            
            // Add transition class for smooth animation
            sidebar.style.transition = 'width 0.3s ease, transform 0.3s ease';
            
            if (isCollapsed) {
                sidebar.classList.add('collapsed');
                localStorage.setItem('sidebarCollapsed', 'true');
            } else {
                sidebar.classList.remove('collapsed');
                localStorage.setItem('sidebarCollapsed', 'false');
            }
            
            // Update toggle button icon with animation
            const icon = toggleSidebarBtn?.querySelector('i');
            if (icon) {
                icon.style.transition = 'transform 0.3s ease';
                icon.style.transform = isCollapsed ? 'rotate(180deg)' : 'rotate(0)';
            }
        }
    };
    
    // Handle click outside the sidebar to close it on mobile
    const handleClickOutside = (e) => {
        if (!isMobileView()) return;
        
        const isClickInsideSidebar = sidebar.contains(e.target);
        const isClickOnToggle = mobileMenuToggle?.contains(e.target);
        
        if (!isClickInsideSidebar && !isClickOnToggle && sidebar.classList.contains('visible')) {
            toggleSidebarState();
        }
    };
    
    // Handle window resize
    const handleResize = () => {
        if (isMobileView()) {
            // On mobile, ensure sidebar is hidden by default
            if (!sidebar.classList.contains('visible')) {
                sidebar.style.transform = 'translateX(-100%)';
            }
        } else {
            // On desktop, reset any mobile-specific styles
            sidebar.style.transform = '';
            body.style.overflow = '';
            if (sidebarOverlay) {
                sidebarOverlay.style.display = 'none';
            }
        }
        initSidebar();
    };
    
    // Initialize event listeners
    const initEventListeners = () => {
        // Toggle sidebar on desktop
        if (toggleSidebarBtn) {
            toggleSidebarBtn.addEventListener('click', toggleSidebarState);
        }
        
        // Mobile menu toggle
        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', toggleSidebarState);
        }
        
        // Close sidebar when clicking overlay on mobile
        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', toggleSidebarState);
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', handleClickOutside);
        
        // Handle window resize with debounce
        let resizeTimer;
        const handleWindowResize = () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(handleResize, 250);
        };
        
        window.addEventListener('resize', handleWindowResize);
        
        // Handle touch events for swipe to close on mobile
        let touchStartX = 0;
        let touchEndX = 0;
        
        document.addEventListener('touchstart', (e) => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        
        document.addEventListener('touchend', (e) => {
            if (!isMobileView()) return;
            
            touchEndX = e.changedTouches[0].screenX;
            const touchDiff = touchStartX - touchEndX;
            
            // Swipe right to open (only from left edge)
            if (touchStartX < 50 && touchDiff < -50) {
                if (!sidebar.classList.contains('visible')) {
                    toggleSidebarState();
                }
            }
            
            // Swipe left to close
            if (touchDiff > 50 && sidebar.classList.contains('visible')) {
                toggleSidebarState();
            }
        }, { passive: true });
    };
    
    // Initialize everything
    const init = () => {
        initSidebar();
        initEventListeners();
        
        // Initialize tooltips if tooltip elements exist
        if (document.querySelector('[data-bs-toggle="tooltip"]')) {
            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }

        // Initialize mobile header hide/reveal on scroll
        initMobileHeaderAutoHide();
    };
    
    // Start the application
    init();
    
    // Clean up event listeners when the page is unloaded
    window.addEventListener('beforeunload', () => {
        if (toggleSidebarBtn) toggleSidebarBtn.removeEventListener('click', toggleSidebarState);
        if (mobileMenuToggle) mobileMenuToggle.removeEventListener('click', toggleSidebarState);
        if (sidebarOverlay) sidebarOverlay.removeEventListener('click', toggleSidebarState);
        document.removeEventListener('click', handleClickOutside);
        window.removeEventListener('resize', handleResize);
    });
    
    // Expose functions for debugging if needed
    window.toggleSidebar = toggleSidebarState;
    
    
    // Initialize tooltips based on screen size
    function initTooltips() {
        if (window.innerWidth > 767) {
            const menuItems = document.querySelectorAll('.menu-item');
            menuItems.forEach(item => {
                const tooltip = item.getAttribute('data-tooltip');
                if (tooltip && !item.hasAttribute('title')) {
                    item.setAttribute('title', tooltip);
                }
            });
        } else {
            // Remove tooltips on mobile
            document.querySelectorAll('.menu-item[title]').forEach(item => {
                item.removeAttribute('title');
            });
        }
    }
    
    // Initialize tooltips on load and window resize
    initTooltips();
    window.addEventListener('resize', initTooltips);

    // Hide/reveal sticky header on mobile while scrolling
    function initMobileHeaderAutoHide() {
        const headers = document.querySelectorAll('.top-header');
        if (!headers.length) return;

        let lastScrollY = window.scrollY;
        let ticking = false;
        const threshold = 8; // minimal delta to react

        const onScroll = () => {
            // Do not hide header when mobile sidebar is open
            const sidebarOpen = sidebar.classList.contains('visible');
            if (sidebarOpen) {
                document.body.classList.remove('header-hidden');
                lastScrollY = window.scrollY;
                ticking = false;
                return;
            }

            const currentY = window.scrollY;
            const diff = currentY - lastScrollY;

            if (Math.abs(diff) > threshold) {
                if (diff > 0 && currentY > 40) {
                    // scrolling down
                    document.body.classList.add('header-hidden');
                } else {
                    // scrolling up or near top
                    document.body.classList.remove('header-hidden');
                }
                lastScrollY = currentY;
            }
            ticking = false;
        };

        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(onScroll);
                ticking = true;
            }
        }, { passive: true });
    }
});
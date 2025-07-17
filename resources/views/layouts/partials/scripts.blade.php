<script>
    window.authUserId = @auth'{{ auth()->id() }}'@else null @endauth;
    window.csrfToken = '{{ csrf_token() }}';
        // DOM Content Loaded
        document.addEventListener('DOMContentLoaded', function() {

            // User Dropdown Menu functionality
            const userMenuButton = document.getElementById('user-menu-button');
            const userMenu = document.getElementById('user-menu');

            if (userMenuButton && userMenu) {
                // Toggle user dropdown
                userMenuButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const isExpanded = userMenuButton.getAttribute('aria-expanded') === 'true';

                    if (isExpanded) {
                        closeUserMenu();
                    } else {
                        openUserMenu();
                    }
                });

                // Close user menu when clicking outside
                document.addEventListener('click', function(e) {
                    if (!userMenuButton.contains(e.target) && !userMenu.contains(e.target)) {
                        closeUserMenu();
                    }
                });

                // Close user menu on Escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        closeUserMenu();
                    }
                });

                function openUserMenu() {
                    userMenu.classList.remove('hidden');
                    userMenuButton.setAttribute('aria-expanded', 'true');

                    // Focus first menu item
                    const firstMenuItem = userMenu.querySelector('a, button');
                    if (firstMenuItem) {
                        firstMenuItem.focus();
                    }
                }

                function closeUserMenu() {
                    userMenu.classList.add('hidden');
                    userMenuButton.setAttribute('aria-expanded', 'false');
                }
            }

            // Mobile Menu functionality
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            const hamburgerIcon = document.getElementById('hamburger-icon');
            const closeIcon = document.getElementById('close-icon');

            if (mobileMenuButton && mobileMenu) {
                // Toggle mobile menu
                mobileMenuButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const isExpanded = mobileMenuButton.getAttribute('aria-expanded') === 'true';

                    if (isExpanded) {
                        closeMobileMenu();
                    } else {
                        openMobileMenu();
                    }
                });

                // Close mobile menu when clicking on a link
                const mobileMenuLinks = mobileMenu.querySelectorAll('a');
                mobileMenuLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        closeMobileMenu();
                    });
                });

                // Close mobile menu on window resize to desktop size
                window.addEventListener('resize', function() {
                    if (window.innerWidth >= 768) { // md breakpoint
                        closeMobileMenu();
                    }
                });

                function openMobileMenu() {
                    mobileMenu.classList.remove('hidden');
                    mobileMenuButton.setAttribute('aria-expanded', 'true');
                    hamburgerIcon.classList.add('hidden');
                    closeIcon.classList.remove('hidden');

                    // Add slide down animation
                    mobileMenu.style.maxHeight = '0px';
                    mobileMenu.style.overflow = 'hidden';
                    mobileMenu.style.transition = 'max-height 0.3s ease-out';

                    // Force reflow
                    mobileMenu.offsetHeight;

                    // Animate to full height
                    mobileMenu.style.maxHeight = mobileMenu.scrollHeight + 'px';

                    // Clean up after animation
                    setTimeout(() => {
                        mobileMenu.style.maxHeight = '';
                        mobileMenu.style.overflow = '';
                        mobileMenu.style.transition = '';
                    }, 300);
                }

                function closeMobileMenu() {
                    // Add slide up animation
                    mobileMenu.style.maxHeight = mobileMenu.scrollHeight + 'px';
                    mobileMenu.style.overflow = 'hidden';
                    mobileMenu.style.transition = 'max-height 0.3s ease-in';

                    // Force reflow
                    mobileMenu.offsetHeight;

                    // Animate to closed
                    mobileMenu.style.maxHeight = '0px';

                    setTimeout(() => {
                        mobileMenu.classList.add('hidden');
                        mobileMenuButton.setAttribute('aria-expanded', 'false');
                        hamburgerIcon.classList.remove('hidden');
                        closeIcon.classList.add('hidden');

                        // Clean up styles
                        mobileMenu.style.maxHeight = '';
                        mobileMenu.style.overflow = '';
                        mobileMenu.style.transition = '';
                    }, 300);
                }
            }


            // Alert dismissible functionality
            const alertCloseButtons = document.querySelectorAll('.alert-close');
            alertCloseButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const alert = this.closest('.alert-dismissible');
                    if (alert) {
                        alert.style.opacity = '0';
                        alert.style.transform = 'translateY(-10px)';
                        alert.style.transition = 'all 0.3s ease-out';

                        setTimeout(() => {
                            alert.remove();
                        }, 300);
                    }
                });
            });

            // Auto-hide success messages after 5 seconds
            const successAlerts = document.querySelectorAll('.bg-green-100');
            successAlerts.forEach(alert => {
                setTimeout(() => {
                    const closeButton = alert.querySelector('.alert-close');
                    if (closeButton) {
                        closeButton.click();
                    }
                }, 5000);
            });

            // Smooth scroll for anchor links
            const anchorLinks = document.querySelectorAll('a[href^="#"]');
            anchorLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href !== '#') {
                        const target = document.querySelector(href);
                        if (target) {
                            e.preventDefault();
                            target.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    }
                });
            });

            // Form validation enhancement
            const forms = document.querySelectorAll('form');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitButton = form.querySelector('button[type="submit"]');
                    if (submitButton && !submitButton.disabled) {
                        submitButton.disabled = true;
                        submitButton.innerHTML = submitButton.innerHTML.includes('Yuklanmoqda')
                            ? submitButton.innerHTML
                            : 'Yuklanmoqda...';

                        // Re-enable button after 5 seconds to prevent permanent disable
                        setTimeout(() => {
                            submitButton.disabled = false;
                            submitButton.innerHTML = submitButton.innerHTML.replace('Yuklanmoqda...', submitButton.dataset.originalText || 'Yuborish');
                        }, 5000);
                    }
                });
            });

            // Add loading states for navigation links
            const navLinks = document.querySelectorAll('nav a');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Don't add loading state for external links or anchors
                    if (this.hostname !== window.location.hostname || this.getAttribute('href').startsWith('#')) {
                        return;
                    }

                    // Add loading state
                    this.style.opacity = '0.7';
                    this.style.pointerEvents = 'none';

                    // Remove loading state after navigation (fallback)
                    setTimeout(() => {
                        this.style.opacity = '';
                        this.style.pointerEvents = '';
                    }, 3000);
                });
            });
        });

        // Utility function for showing temporary notifications
        function showNotification(message, type = 'info', duration = 3000) {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg text-white transform transition-all duration-300 translate-x-full ${
                type === 'success' ? 'bg-green-500' :
                type === 'error' ? 'bg-red-500' :
                type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500'
            }`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <span>${message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </div>
            `;

            document.body.appendChild(notification);

            // Slide in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);

            // Auto remove
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 300);
            }, duration);
        }

        // Global error handler for AJAX requests
        window.addEventListener('unhandledrejection', function(e) {
            console.error('Unhandled promise rejection:', e.reason);
            showNotification('Xatolik yuz berdi. Iltimos, qaytadan urinib ko\'ring.', 'error');
        });
    </script>
@stack('scripts')

/**
 * Header Animations & Interactions
 * Handles scroll-based animations, ripple effects, and mobile menu
 */

(function () {
    'use strict';

    // ========================================================================
    // CONFIGURATION
    // ========================================================================

    const config = {
        scrollThreshold: 50,          // Pixels scrolled before navbar changes
        rippleDuration: 600,           // Ripple effect duration in ms
        debounceDelay: 10,             // Scroll event debounce delay
        animationClass: 'scrolled',    // Class added when scrolled
        activeClass: 'active',         // Active state class
        rippleClass: 'ripple'          // Ripple animation class
    };

    // ========================================================================
    // UTILITIES
    // ========================================================================

    /**
     * Debounce function for performance optimization
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * Request Animation Frame wrapper for smooth animations
     */
    function rafThrottle(callback) {
        let requestId = null;
        let lastArgs;

        const later = () => {
            requestId = null;
            callback(...lastArgs);
        };

        return function (...args) {
            lastArgs = args;
            if (requestId === null) {
                requestId = requestAnimationFrame(later);
            }
        };
    }

    // ========================================================================
    // SCROLL-BASED NAVBAR ANIMATIONS
    // ========================================================================

    /**
     * Handle navbar scroll animations
     */
    function handleNavbarScroll() {
        const navbar = document.querySelector('.main-navbar');
        if (!navbar) return;

        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop > config.scrollThreshold) {
            navbar.classList.add(config.animationClass);
        } else {
            navbar.classList.remove(config.animationClass);
        }
    }

    /**
     * Initialize scroll listener with performance optimization
     */
    function initScrollAnimations() {
        // Use RAF throttle for better performance
        const optimizedScrollHandler = rafThrottle(handleNavbarScroll);

        window.addEventListener('scroll', optimizedScrollHandler, { passive: true });

        // Check initial state
        handleNavbarScroll();
    }

    // ========================================================================
    // RIPPLE EFFECT ON BUTTONS
    // ========================================================================

    /**
     * Create ripple effect on button click
     */
    function createRipple(event) {
        const button = event.currentTarget;

        // Remove any existing ripple class
        button.classList.remove(config.rippleClass);

        // Force reflow to restart animation
        void button.offsetWidth;

        // Add ripple class
        button.classList.add(config.rippleClass);

        // Remove ripple class after animation
        setTimeout(() => {
            button.classList.remove(config.rippleClass);
        }, config.rippleDuration);
    }

    /**
     * Initialize ripple effects on all nav buttons
     */
    function initRippleEffect() {
        const buttons = document.querySelectorAll('.nav-btn, .nav-btn-login, .nav-btn-admin');

        buttons.forEach(button => {
            button.addEventListener('click', createRipple);
        });
    }

    // ========================================================================
    // MOBILE MENU TOGGLE
    // ========================================================================

    /**
     * Toggle mobile menu with smooth animation
     */
    function toggleMobileMenu() {
        const menuToggle = document.getElementById('mobileMenuToggle');
        const navbarMenu = document.getElementById('navbarMenu');

        if (!menuToggle || !navbarMenu) return;

        menuToggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

            // Toggle active classes
            this.classList.toggle(config.activeClass);
            navbarMenu.classList.toggle(config.activeClass);

            // Update ARIA attributes for accessibility
            const isExpanded = navbarMenu.classList.contains(config.activeClass);
            this.setAttribute('aria-expanded', isExpanded);
            navbarMenu.setAttribute('aria-hidden', !isExpanded);
        });

        // Close menu when clicking outside
        document.addEventListener('click', function (e) {
            const isClickInsideMenu = navbarMenu.contains(e.target);
            const isClickOnToggle = menuToggle.contains(e.target);

            if (!isClickInsideMenu && !isClickOnToggle && navbarMenu.classList.contains(config.activeClass)) {
                menuToggle.classList.remove(config.activeClass);
                navbarMenu.classList.remove(config.activeClass);
                menuToggle.setAttribute('aria-expanded', 'false');
                navbarMenu.setAttribute('aria-hidden', 'true');
            }
        });

        // Close menu on escape key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && navbarMenu.classList.contains(config.activeClass)) {
                menuToggle.classList.remove(config.activeClass);
                navbarMenu.classList.remove(config.activeClass);
                menuToggle.setAttribute('aria-expanded', 'false');
                navbarMenu.setAttribute('aria-hidden', 'true');
            }
        });
    }

    // ========================================================================
    // ACTIVE LINK HIGHLIGHTING
    // ========================================================================

    /**
     * Highlight active navigation link based on current page
     */
    function highlightActiveLink() {
        const currentPath = window.location.pathname;
        const navLinks = document.querySelectorAll('.navbar-menu a');

        navLinks.forEach(link => {
            const linkPath = new URL(link.href).pathname;

            if (linkPath === currentPath ||
                (currentPath.includes(linkPath) && linkPath !== '/')) {
                link.classList.add(config.activeClass);
            } else {
                link.classList.remove(config.activeClass);
            }
        });
    }

    // ========================================================================
    // SMOOTH SCROLL FOR ANCHOR LINKS
    // ========================================================================

    /**
     * Add smooth scroll behavior to anchor links
     */
    function initSmoothScroll() {
        const anchorLinks = document.querySelectorAll('a[href^="#"]');

        anchorLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                const targetId = this.getAttribute('href');

                // Skip if it's just "#"
                if (targetId === '#') return;

                const targetElement = document.querySelector(targetId);

                if (targetElement) {
                    e.preventDefault();

                    // Close mobile menu if open
                    const navbarMenu = document.getElementById('navbarMenu');
                    const menuToggle = document.getElementById('mobileMenuToggle');

                    if (navbarMenu && navbarMenu.classList.contains(config.activeClass)) {
                        navbarMenu.classList.remove(config.activeClass);
                        if (menuToggle) {
                            menuToggle.classList.remove(config.activeClass);
                        }
                    }

                    // Smooth scroll to target
                    const headerOffset = 80; // Account for fixed navbar
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            });
        });
    }

    // ========================================================================
    // SEARCH ICON FUNCTIONALITY
    // ========================================================================

    /**
     * Handle search icon click to focus search input
     */
    function initSearchToggle() {
        const searchToggle = document.getElementById('searchToggle');

        if (searchToggle) {
            searchToggle.addEventListener('click', function () {
                const heroSearchInput = document.querySelector('.hero-search-input');

                if (heroSearchInput) {
                    // Scroll to hero section smoothly
                    const heroSection = document.querySelector('.hero-section');
                    if (heroSection) {
                        heroSection.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }

                    // Focus on search input after a short delay
                    setTimeout(() => {
                        heroSearchInput.focus();
                    }, 500);
                }
            });
        }
    }

    // ========================================================================
    // INTERSECTION OBSERVER FOR ANIMATIONS
    // ========================================================================

    /**
     * Observe elements and animate them when they come into view
     */
    function initIntersectionObserver() {
        // Check if IntersectionObserver is supported
        if (!('IntersectionObserver' in window)) return;

        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);

        // Observe elements with data-animate attribute
        const animatedElements = document.querySelectorAll('[data-animate]');
        animatedElements.forEach(el => observer.observe(el));
    }

    // ========================================================================
    // INITIALIZATION
    // ========================================================================

    /**
     * Initialize all header animations and interactions
     */
    function init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
            return;
        }

        // Initialize all components
        initScrollAnimations();
        initRippleEffect();
        toggleMobileMenu();
        highlightActiveLink();
        initSmoothScroll();
        initSearchToggle();
        initIntersectionObserver();

        console.log('✨ Header animations initialized');
    }

    // Start initialization
    init();

})();

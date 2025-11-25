/**
 * Footer Animations & Interactions
 * Handles scroll-to-top button, newsletter form, and footer reveal animations
 */

(function () {
    'use strict';

    // ========================================================================
    // CONFIGURATION
    // ========================================================================

    const config = {
        scrollThreshold: 300,        // Pixels to scroll before showing scroll-to-top button
        scrollDuration: 800,          // Smooth scroll duration in ms
        visibleClass: 'visible',      // Class for visible elements
        animateClass: 'animate-in',   // Class for animated elements
        debounceDelay: 15             // Scroll event debounce delay
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
    // SCROLL TO TOP BUTTON
    // ========================================================================

    /**
     * Create and initialize scroll-to-top button
     */
    function initScrollToTop() {
        // Check if button already exists
        let scrollBtn = document.querySelector('.scroll-to-top');

        // Create button if it doesn't exist
        if (!scrollBtn) {
            scrollBtn = document.createElement('button');
            scrollBtn.className = 'scroll-to-top';
            scrollBtn.innerHTML = '<i class="bi bi-arrow-up"></i>';
            scrollBtn.setAttribute('aria-label', 'Scroll to top');
            scrollBtn.setAttribute('title', 'Back to top');
            document.body.appendChild(scrollBtn);
        }

        // Handle scroll visibility
        const handleScroll = rafThrottle(() => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;

            if (scrollTop > config.scrollThreshold) {
                scrollBtn.classList.add(config.visibleClass);
            } else {
                scrollBtn.classList.remove(config.visibleClass);
            }
        });

        // Add scroll listener
        window.addEventListener('scroll', handleScroll, { passive: true });

        // Handle click to scroll to top
        scrollBtn.addEventListener('click', function (e) {
            e.preventDefault();

            // Smooth scroll to top
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });

        // Initial check
        handleScroll();
    }

    // ========================================================================
    // FOOTER REVEAL ANIMATION
    // ========================================================================

    /**
     * Animate footer sections when they come into view
     */
    function initFooterReveal() {
        // Check if IntersectionObserver is supported
        if (!('IntersectionObserver' in window)) return;

        const footer = document.querySelector('footer');
        if (!footer) return;

        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add(config.animateClass);
                    // Optionally stop observing after animation
                    // observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe footer
        observer.observe(footer);
    }

    // ========================================================================
    // STAGGERED ANIMATION FOR FOOTER SECTIONS
    // ========================================================================

    /**
     * Add staggered animation to footer sections
     */
    function staggerFooterSections() {
        // Check if IntersectionObserver is supported
        if (!('IntersectionObserver' in window)) return;

        const footerSections = document.querySelectorAll('footer .col-lg-4, footer .col-6, footer .col-lg-2');
        if (!footerSections.length) return;

        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, index) => {
                if (entry.isIntersecting) {
                    // Add slight delay based on index
                    setTimeout(() => {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }, index * 100);

                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Set initial state and observe
        footerSections.forEach((section, index) => {
            section.style.opacity = '0';
            section.style.transform = 'translateY(30px)';
            section.style.transition = 'all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1)';
            observer.observe(section);
        });
    }

    // ========================================================================
    // NEWSLETTER FORM HANDLING
    // ========================================================================

    /**
     * Handle newsletter form submission
     */
    function handleNewsletterSubmit() {
        const forms = document.querySelectorAll('footer form');

        forms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

                const emailInput = this.querySelector('input[type="email"]');
                const submitBtn = this.querySelector('button[type="submit"]');

                if (!emailInput || !submitBtn) return;

                const email = emailInput.value.trim();

                // Basic email validation
                if (!isValidEmail(email)) {
                    showFormMessage(form, 'Please enter a valid email address.', 'error');
                    return;
                }

                // Disable button and show loading state
                submitBtn.disabled = true;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Subscribing...';

                // Simulate API call (replace with actual API call)
                setTimeout(() => {
                    // Success
                    showFormMessage(form, 'Thank you for subscribing!', 'success');
                    emailInput.value = '';

                    // Reset button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 1500);

                // In a real implementation, you would make an API call here:
                // fetch('/api/newsletter', {
                //     method: 'POST',
                //     headers: { 'Content-Type': 'application/json' },
                //     body: JSON.stringify({ email })
                // })
                // .then(response => response.json())
                // .then(data => {
                //     showFormMessage(form, 'Thank you for subscribing!', 'success');
                //     emailInput.value = '';
                // })
                // .catch(error => {
                //     showFormMessage(form, 'An error occurred. Please try again.', 'error');
                // })
                // .finally(() => {
                //     submitBtn.disabled = false;
                //     submitBtn.innerHTML = originalText;
                // });
            });
        });
    }

    /**
     * Validate email format
     */
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    /**
     * Show form message (success or error)
     */
    function showFormMessage(form, message, type) {
        // Remove any existing message
        const existingMessage = form.querySelector('.form-message');
        if (existingMessage) {
            existingMessage.remove();
        }

        // Create message element
        const messageEl = document.createElement('div');
        messageEl.className = `form-message form-message-${type}`;
        messageEl.textContent = message;
        messageEl.style.cssText = `
            margin-top: 0.75rem;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            animation: slideIn 0.3s ease;
            ${type === 'success'
                ? 'background: rgba(16, 185, 129, 0.2); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);'
                : 'background: rgba(239, 68, 68, 0.2); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3);'
            }
        `;

        // Insert message after form
        form.appendChild(messageEl);

        // Auto-remove after 5 seconds
        setTimeout(() => {
            messageEl.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => messageEl.remove(), 300);
        }, 5000);
    }

    // ========================================================================
    // SOCIAL MEDIA ICON ANIMATIONS
    // ========================================================================

    /**
     * Add hover animations to social media icons
     */
    function initSocialIcons() {
        const socialIcons = document.querySelectorAll('.social-icon');

        socialIcons.forEach(icon => {
            icon.addEventListener('mouseenter', function () {
                this.style.transform = 'rotateY(360deg) scale(1.1)';
            });

            icon.addEventListener('mouseleave', function () {
                this.style.transform = 'rotateY(0deg) scale(1)';
            });
        });
    }

    // ========================================================================
    // PARALLAX SCROLL EFFECT FOR FOOTER BACKGROUND
    // ========================================================================

    /**
     * Add subtle parallax effect to footer background
     */
    function initFooterParallax() {
        const footer = document.querySelector('footer');
        if (!footer) return;

        const handleScroll = rafThrottle(() => {
            const footerTop = footer.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;

            if (footerTop < windowHeight) {
                const scrolled = windowHeight - footerTop;
                const parallaxSpeed = 0.3;
                const yPos = -(scrolled * parallaxSpeed);

                // Apply transform to pseudo-elements via CSS custom property
                footer.style.setProperty('--parallax-offset', `${yPos}px`);
            }
        });

        window.addEventListener('scroll', handleScroll, { passive: true });
    }

    // ========================================================================
    // LINK HOVER SOUND EFFECT (OPTIONAL)
    // ========================================================================

    /**
     * Add subtle sound effect on link hover (optional, requires audio file)
     */
    function initLinkSoundEffects() {
        // This is optional and requires audio files
        // Uncomment if you want to add sound effects

        /*
        const hoverSound = new Audio('/assets/sounds/hover.mp3');
        hoverSound.volume = 0.2;
        
        const footerLinks = document.querySelectorAll('footer a');
        
        footerLinks.forEach(link => {
            link.addEventListener('mouseenter', () => {
                hoverSound.currentTime = 0;
                hoverSound.play().catch(e => {});
            });
        });
        */
    }

    // ========================================================================
    // INITIALIZATION
    // ========================================================================

    /**
     * Initialize all footer animations and interactions
     */
    function init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
            return;
        }

        // Initialize all components
        initScrollToTop();
        initFooterReveal();
        staggerFooterSections();
        handleNewsletterSubmit();
        initSocialIcons();
        initFooterParallax();
        // initLinkSoundEffects(); // Optional
    }

    // Start initialization
    init();

})();

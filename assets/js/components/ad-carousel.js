/**
 * Advertisement Carousel
 * Handles automatic rotation and manual navigation of ad carousel
 */

(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        autoPlayInterval: 5000, // 5 seconds per ad
        transitionDuration: 600, // Match CSS transition
        pauseOnHover: true
    };

    class AdCarousel {
        constructor(container) {
            this.container = container;
            this.slides = container.querySelectorAll('.ad-slide');
            this.dots = container.querySelectorAll('.ad-dot');
            this.prevBtn = container.querySelector('.ad-prev');
            this.nextBtn = container.querySelector('.ad-next');
            this.currentSlide = 0;
            this.totalSlides = this.slides.length;
            this.autoPlayTimer = null;
            this.isPlaying = true;

            this.init();
        }

        init() {
            if (this.totalSlides === 0) {
                console.warn('No ad slides found');
                return;
            }

            // Set up event listeners
            this.setupEventListeners();

            // Start autoplay
            this.startAutoPlay();

            // Track ad impressions (optional analytics)
            this.trackImpression(0);
        }

        setupEventListeners() {
            // Navigation buttons
            if (this.prevBtn) {
                this.prevBtn.addEventListener('click', () => this.previousSlide());
            }

            if (this.nextBtn) {
                this.nextBtn.addEventListener('click', () => this.nextSlide());
            }

            // Dot indicators
            this.dots.forEach((dot, index) => {
                dot.addEventListener('click', () => this.goToSlide(index));
            });

            // Pause on hover
            if (CONFIG.pauseOnHover) {
                this.container.addEventListener('mouseenter', () => this.pauseAutoPlay());
                this.container.addEventListener('mouseleave', () => this.resumeAutoPlay());
            }

            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (this.isInViewport(this.container)) {
                    if (e.key === 'ArrowLeft') {
                        this.previousSlide();
                    } else if (e.key === 'ArrowRight') {
                        this.nextSlide();
                    }
                }
            });

            // Pause when tab is hidden
            document.addEventListener('visibilitychange', () => {
                if (document.hidden) {
                    this.pauseAutoPlay();
                } else {
                    this.resumeAutoPlay();
                }
            });

            // Track clicks for analytics
            this.slides.forEach((slide, index) => {
                const link = slide.querySelector('.ad-link');
                if (link) {
                    link.addEventListener('click', () => this.trackClick(index));
                }
            });
        }

        nextSlide() {
            this.goToSlide((this.currentSlide + 1) % this.totalSlides);
        }

        previousSlide() {
            this.goToSlide((this.currentSlide - 1 + this.totalSlides) % this.totalSlides);
        }

        goToSlide(index) {
            if (index === this.currentSlide) return;

            // Remove active class from current slide and dot
            this.slides[this.currentSlide].classList.remove('active');
            this.slides[this.currentSlide].classList.add('fade-out');
            this.dots[this.currentSlide].classList.remove('active');

            // Update current slide
            this.currentSlide = index;

            // Add active class to new slide and dot
            setTimeout(() => {
                this.slides.forEach(slide => slide.classList.remove('fade-out'));
                this.slides[this.currentSlide].classList.add('active');
                this.dots[this.currentSlide].classList.add('active');
            }, CONFIG.transitionDuration / 2);

            // Track impression
            this.trackImpression(index);

            // Reset autoplay
            if (this.isPlaying) {
                this.startAutoPlay();
            }
        }

        startAutoPlay() {
            this.stopAutoPlay();
            this.autoPlayTimer = setInterval(() => {
                this.nextSlide();
            }, CONFIG.autoPlayInterval);
        }

        stopAutoPlay() {
            if (this.autoPlayTimer) {
                clearInterval(this.autoPlayTimer);
                this.autoPlayTimer = null;
            }
        }

        pauseAutoPlay() {
            this.isPlaying = false;
            this.stopAutoPlay();
        }

        resumeAutoPlay() {
            this.isPlaying = true;
            this.startAutoPlay();
        }

        isInViewport(element) {
            const rect = element.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        }

        trackImpression(index) {
            // Track ad impressions for analytics
            const adUrl = this.slides[index].querySelector('.ad-link')?.href;
            const adAlt = this.slides[index].querySelector('.ad-image')?.alt;

            console.log('Ad Impression:', {
                index: index,
                url: adUrl,
                alt: adAlt,
                timestamp: new Date().toISOString()
            });

            // Send to analytics service (e.g., Google Analytics)
            if (typeof gtag !== 'undefined') {
                gtag('event', 'ad_impression', {
                    'ad_position': index + 1,
                    'ad_name': adAlt
                });
            }
        }

        trackClick(index) {
            // Track ad clicks for analytics
            const adUrl = this.slides[index].querySelector('.ad-link')?.href;
            const adAlt = this.slides[index].querySelector('.ad-image')?.alt;

            console.log('Ad Click:', {
                index: index,
                url: adUrl,
                alt: adAlt,
                timestamp: new Date().toISOString()
            });

            // Send to analytics service
            if (typeof gtag !== 'undefined') {
                gtag('event', 'ad_click', {
                    'ad_position': index + 1,
                    'ad_name': adAlt,
                    'ad_url': adUrl
                });
            }
        }

        destroy() {
            this.stopAutoPlay();
            // Remove event listeners if needed
        }
    }

    // Initialize carousel when DOM is ready
    function initAdCarousel() {
        const carouselContainer = document.querySelector('.ad-carousel-container');
        
        if (carouselContainer) {
            new AdCarousel(carouselContainer);
        }
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAdCarousel);
    } else {
        initAdCarousel();
    }

    // Expose globally for manual initialization if needed
    window.AdCarousel = AdCarousel;

})();

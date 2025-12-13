/**
 * Advertisement Banner Carousel
 * Handles full-width promotional banner rotation
 */

(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        autoPlayInterval: 5000, // 5 seconds per banner
        transitionDuration: 800, // Match CSS transition
        pauseOnHover: true
    };

    class BannerCarousel {
        constructor(container) {
            this.container = container;
            this.slides = container.querySelectorAll('.banner-slide');
            this.dots = container.querySelectorAll('.banner-dot');
            this.prevBtn = container.querySelector('.banner-prev');
            this.nextBtn = container.querySelector('.banner-next');
            this.currentSlide = 0;
            this.totalSlides = this.slides.length;
            this.autoPlayTimer = null;
            this.isPlaying = true;

            this.init();
        }

        init() {
            if (this.totalSlides === 0) {
                console.warn('No banner slides found');
                return;
            }

            // Set up event listeners
            this.setupEventListeners();

            // Start autoplay
            this.startAutoPlay();

            // Track banner impressions
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

            // Touch/swipe support for mobile
            this.setupTouchEvents();

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
                const link = slide.querySelector('.banner-link');
                if (link) {
                    link.addEventListener('click', () => this.trackClick(index));
                }
            });
        }

        setupTouchEvents() {
            let touchStartX = 0;
            let touchEndX = 0;

            this.container.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            }, { passive: true });

            this.container.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                this.handleSwipe(touchStartX, touchEndX);
            }, { passive: true });
        }

        handleSwipe(startX, endX) {
            const swipeThreshold = 50;
            const diff = startX - endX;

            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    // Swipe left - next slide
                    this.nextSlide();
                } else {
                    // Swipe right - previous slide
                    this.previousSlide();
                }
            }
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
            this.dots[this.currentSlide].classList.remove('active');

            // Update current slide
            this.currentSlide = index;

            // Add active class to new slide and dot
            this.slides[this.currentSlide].classList.add('active');
            this.dots[this.currentSlide].classList.add('active');

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

        trackImpression(index) {
            // Track banner impressions for analytics
            const bannerUrl = this.slides[index].querySelector('.banner-link')?.href;
            const bannerAlt = this.slides[index].querySelector('.banner-image')?.alt;

            console.log('Banner Impression:', {
                index: index,
                url: bannerUrl,
                alt: bannerAlt,
                timestamp: new Date().toISOString()
            });

            // Send to analytics service (e.g., Google Analytics)
            if (typeof gtag !== 'undefined') {
                gtag('event', 'banner_impression', {
                    'banner_position': index + 1,
                    'banner_name': bannerAlt
                });
            }
        }

        trackClick(index) {
            // Track banner clicks for analytics
            const bannerUrl = this.slides[index].querySelector('.banner-link')?.href;
            const bannerAlt = this.slides[index].querySelector('.banner-image')?.alt;

            console.log('Banner Click:', {
                index: index,
                url: bannerUrl,
                alt: bannerAlt,
                timestamp: new Date().toISOString()
            });

            // Send to analytics service
            if (typeof gtag !== 'undefined') {
                gtag('event', 'banner_click', {
                    'banner_position': index + 1,
                    'banner_name': bannerAlt,
                    'banner_url': bannerUrl
                });
            }
        }

        destroy() {
            this.stopAutoPlay();
            // Remove event listeners if needed
        }
    }

    // Initialize banner carousel when DOM is ready
    function initBannerCarousel() {
        const bannerContainer = document.querySelector('.ad-banner-carousel');
        
        if (bannerContainer) {
            new BannerCarousel(bannerContainer);
        }
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBannerCarousel);
    } else {
        initBannerCarousel();
    }

    // Expose globally for manual initialization if needed
    window.BannerCarousel = BannerCarousel;

})();

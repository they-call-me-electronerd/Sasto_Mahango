/**
 * Landing Advertisement Carousel
 * Professional auto-scrolling carousel with smooth transitions
 */

class LandingAdCarousel {
    constructor() {
        this.track = document.querySelector('.landing-ad-track');
        this.slides = document.querySelectorAll('.landing-ad-slide');
        this.prevBtn = document.querySelector('.landing-ad-prev');
        this.nextBtn = document.querySelector('.landing-ad-next');
        this.indicators = document.querySelectorAll('.landing-ad-indicator');
        
        this.currentIndex = 0;
        this.totalSlides = this.slides.length;
        this.autoScrollInterval = null;
        this.autoScrollDelay = 5000; // 5 seconds per slide
        this.isTransitioning = false;
        
        if (this.track && this.slides.length > 0) {
            this.init();
        }
    }
    
    init() {
        // Add event listeners
        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', () => this.prev());
        }
        
        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', () => this.next());
        }
        
        // Indicator click events
        this.indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => this.goToSlide(index));
        });
        
        // Pause on hover
        const container = document.querySelector('.landing-ad-container');
        if (container) {
            container.addEventListener('mouseenter', () => this.pauseAutoScroll());
            container.addEventListener('mouseleave', () => this.startAutoScroll());
        }
        
        // Start auto-scroll
        this.startAutoScroll();
        
        // Add hover effects to navigation buttons
        this.addButtonHoverEffects();
    }
    
    goToSlide(index) {
        if (this.isTransitioning || index === this.currentIndex) return;
        
        this.isTransitioning = true;
        this.currentIndex = index;
        
        // Update track position
        const offset = -100 * this.currentIndex;
        this.track.style.transform = `translateX(${offset}%)`;
        
        // Update indicators
        this.updateIndicators();
        
        // Reset progress bars
        this.resetProgressBars();
        this.startProgressBar(this.currentIndex);
        
        setTimeout(() => {
            this.isTransitioning = false;
        }, 800);
    }
    
    next() {
        const nextIndex = (this.currentIndex + 1) % this.totalSlides;
        this.goToSlide(nextIndex);
        this.restartAutoScroll();
    }
    
    prev() {
        const prevIndex = (this.currentIndex - 1 + this.totalSlides) % this.totalSlides;
        this.goToSlide(prevIndex);
        this.restartAutoScroll();
    }
    
    updateIndicators() {
        this.indicators.forEach((indicator, index) => {
            if (index === this.currentIndex) {
                indicator.classList.add('active');
                indicator.style.background = '#333';
            } else {
                indicator.classList.remove('active');
                indicator.style.background = 'rgba(0, 0, 0, 0.3)';
            }
        });
    }
    
    startProgressBar(index) {
        const indicator = this.indicators[index];
        if (indicator) {
            const progressBar = indicator.querySelector('.progress-bar');
            if (progressBar) {
                // Reset and animate
                progressBar.style.transition = 'none';
                progressBar.style.width = '0%';
                
                setTimeout(() => {
                    progressBar.style.transition = `width ${this.autoScrollDelay}ms linear`;
                    progressBar.style.width = '100%';
                }, 10);
            }
        }
    }
    
    resetProgressBars() {
        this.indicators.forEach(indicator => {
            const progressBar = indicator.querySelector('.progress-bar');
            if (progressBar) {
                progressBar.style.transition = 'none';
                progressBar.style.width = '0%';
            }
        });
    }
    
    startAutoScroll() {
        this.stopAutoScroll();
        this.startProgressBar(this.currentIndex);
        
        this.autoScrollInterval = setInterval(() => {
            this.next();
        }, this.autoScrollDelay);
    }
    
    stopAutoScroll() {
        if (this.autoScrollInterval) {
            clearInterval(this.autoScrollInterval);
            this.autoScrollInterval = null;
        }
        this.resetProgressBars();
    }
    
    pauseAutoScroll() {
        this.stopAutoScroll();
    }
    
    restartAutoScroll() {
        this.startAutoScroll();
    }
    
    addButtonHoverEffects() {
        const buttons = [this.prevBtn, this.nextBtn];
        
        buttons.forEach(btn => {
            if (btn) {
                btn.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-50%) scale(1.1)';
                    this.style.background = 'rgba(255, 255, 255, 1)';
                    this.style.boxShadow = '0 8px 30px rgba(0, 0, 0, 0.25)';
                });
                
                btn.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(-50%) scale(1)';
                    this.style.background = 'rgba(255, 255, 255, 0.95)';
                    this.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.15)';
                });
            }
        });
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    new LandingAdCarousel();
});

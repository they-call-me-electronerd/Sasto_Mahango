/**
 * Category Carousel Component
 * Handles horizontal scrolling carousel for product categories
 */

class CategoryCarousel {
    constructor() {
        this.track = document.querySelector('.categories-carousel-track');
        this.prevBtn = document.querySelector('.category-prev');
        this.nextBtn = document.querySelector('.category-next');
        this.itemWidth = 280 + 24; // Item width + gap
        this.currentPosition = 0;
        
        if (this.track && this.prevBtn && this.nextBtn) {
            this.init();
        }
    }
    
    init() {
        // Add event listeners
        this.prevBtn.addEventListener('click', () => this.scrollPrev());
        this.nextBtn.addEventListener('click', () => this.scrollNext());
        
        // Update button states
        this.updateButtons();
        
        // Handle window resize
        window.addEventListener('resize', () => this.updateButtons());
        
        // Touch/swipe support for mobile
        this.addTouchSupport();
    }
    
    scrollNext() {
        const maxScroll = -(this.track.scrollWidth - this.track.parentElement.offsetWidth);
        const visibleItems = Math.floor(this.track.parentElement.offsetWidth / this.itemWidth);
        const scrollAmount = visibleItems * this.itemWidth;
        
        this.currentPosition = Math.max(maxScroll, this.currentPosition - scrollAmount);
        this.track.style.transform = `translateX(${this.currentPosition}px)`;
        this.updateButtons();
    }
    
    scrollPrev() {
        const visibleItems = Math.floor(this.track.parentElement.offsetWidth / this.itemWidth);
        const scrollAmount = visibleItems * this.itemWidth;
        
        this.currentPosition = Math.min(0, this.currentPosition + scrollAmount);
        this.track.style.transform = `translateX(${this.currentPosition}px)`;
        this.updateButtons();
    }
    
    updateButtons() {
        const maxScroll = -(this.track.scrollWidth - this.track.parentElement.offsetWidth);
        
        // Disable prev button at start
        if (this.currentPosition >= 0) {
            this.prevBtn.disabled = true;
            this.prevBtn.style.opacity = '0.3';
        } else {
            this.prevBtn.disabled = false;
            this.prevBtn.style.opacity = '1';
        }
        
        // Disable next button at end
        if (this.currentPosition <= maxScroll) {
            this.nextBtn.disabled = true;
            this.nextBtn.style.opacity = '0.3';
        } else {
            this.nextBtn.disabled = false;
            this.nextBtn.style.opacity = '1';
        }
    }
    
    addTouchSupport() {
        let startX = 0;
        let currentX = 0;
        let isDragging = false;
        let startPosition = 0;
        
        this.track.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            startPosition = this.currentPosition;
            isDragging = true;
            this.track.style.transition = 'none';
        });
        
        this.track.addEventListener('touchmove', (e) => {
            if (!isDragging) return;
            
            currentX = e.touches[0].clientX;
            const diff = currentX - startX;
            const maxScroll = -(this.track.scrollWidth - this.track.parentElement.offsetWidth);
            
            let newPosition = startPosition + diff;
            newPosition = Math.max(maxScroll, Math.min(0, newPosition));
            
            this.track.style.transform = `translateX(${newPosition}px)`;
        });
        
        this.track.addEventListener('touchend', (e) => {
            if (!isDragging) return;
            
            isDragging = false;
            this.track.style.transition = 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
            
            const diff = currentX - startX;
            const maxScroll = -(this.track.scrollWidth - this.track.parentElement.offsetWidth);
            
            this.currentPosition = startPosition + diff;
            this.currentPosition = Math.max(maxScroll, Math.min(0, this.currentPosition));
            
            this.track.style.transform = `translateX(${this.currentPosition}px)`;
            this.updateButtons();
        });
        
        // Mouse drag support for desktop
        this.track.addEventListener('mousedown', (e) => {
            startX = e.clientX;
            startPosition = this.currentPosition;
            isDragging = true;
            this.track.style.transition = 'none';
            this.track.style.cursor = 'grabbing';
            e.preventDefault();
        });
        
        document.addEventListener('mousemove', (e) => {
            if (!isDragging) return;
            
            currentX = e.clientX;
            const diff = currentX - startX;
            const maxScroll = -(this.track.scrollWidth - this.track.parentElement.offsetWidth);
            
            let newPosition = startPosition + diff;
            newPosition = Math.max(maxScroll, Math.min(0, newPosition));
            
            this.track.style.transform = `translateX(${newPosition}px)`;
        });
        
        document.addEventListener('mouseup', (e) => {
            if (!isDragging) return;
            
            isDragging = false;
            this.track.style.transition = 'transform 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
            this.track.style.cursor = 'grab';
            
            const diff = currentX - startX;
            const maxScroll = -(this.track.scrollWidth - this.track.parentElement.offsetWidth);
            
            this.currentPosition = startPosition + diff;
            this.currentPosition = Math.max(maxScroll, Math.min(0, this.currentPosition));
            
            this.track.style.transform = `translateX(${this.currentPosition}px)`;
            this.updateButtons();
        });
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new CategoryCarousel();
});

/**
 * Categories Page Interactive Effects
 * Enhanced animations and interactivity for category cards
 */

document.addEventListener('DOMContentLoaded', function () {
    // View Toggle Functionality
    const viewButtons = document.querySelectorAll('.view-btn');
    const categoriesGrid = document.getElementById('categoriesGrid');

    viewButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const view = this.dataset.view;

            // Update active state
            viewButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            // Toggle grid view
            if (view === 'list') {
                categoriesGrid.classList.add('list-view');
            } else {
                categoriesGrid.classList.remove('list-view');
            }

            // Save preference
            localStorage.setItem('categoriesView', view);
        });
    });

    // Restore saved view preference
    const savedView = localStorage.getItem('categoriesView');
    if (savedView === 'list') {
        document.querySelector('[data-view="list"]')?.click();
    }

    // Interactive Mouse Glow Effect for Cards
    const categoryCards = document.querySelectorAll('.category-card');

    categoryCards.forEach(card => {
        card.addEventListener('mousemove', function (e) {
            const rect = card.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;

            card.style.setProperty('--mouse-x', `${x}%`);
            card.style.setProperty('--mouse-y', `${y}%`);
        });

        card.addEventListener('mouseleave', function () {
            card.style.setProperty('--mouse-x', '50%');
            card.style.setProperty('--mouse-y', '50%');
        });
    });

    // Staggered Animation on Scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 50);
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Apply initial state and observe
    categoryCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        observer.observe(card);
    });

    // Enhanced Category Card Interaction
    categoryCards.forEach(card => {
        // Add ripple effect on click
        card.addEventListener('click', function (e) {
            const ripple = document.createElement('span');
            ripple.className = 'card-ripple';
            const rect = card.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';

            card.appendChild(ripple);

            setTimeout(() => ripple.remove(), 600);
        });

        // Add tilt effect on mouse move
        card.addEventListener('mousemove', function (e) {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = (y - centerY) / 20;
            const rotateY = (centerX - x) / 20;

            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-5px)`;
        });

        card.addEventListener('mouseleave', function () {
            card.style.transform = '';
        });
    });

    // Smooth count animation for category items
    const animateCount = (element, start, end, duration) => {
        const range = end - start;
        const increment = range / (duration / 16);
        let current = start;

        const timer = setInterval(() => {
            current += increment;
            if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                current = end;
                clearInterval(timer);
            }
            element.textContent = Math.floor(current);
        }, 16);
    };

    // Animate counts when cards come into view
    categoryCards.forEach(card => {
        const countElement = card.querySelector('.category-count');
        if (countElement) {
            const countText = countElement.textContent;
            const count = parseInt(countText.match(/\d+/)?.[0] || 0);

            const countObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const numSpan = document.createElement('span');
                        numSpan.textContent = '0';
                        const originalHTML = countElement.innerHTML;
                        countElement.innerHTML = originalHTML.replace(/\d+/, numSpan.outerHTML);
                        const newNumSpan = countElement.querySelector('span');
                        
                        setTimeout(() => {
                            animateCount(newNumSpan, 0, count, 1000);
                        }, 200);
                        
                        countObserver.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.5 });

            countObserver.observe(card);
        }
    });

    // Add keyboard navigation
    categoryCards.forEach((card, index) => {
        card.setAttribute('tabindex', '0');
        
        card.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                card.click();
            } else if (e.key === 'ArrowRight' && categoryCards[index + 1]) {
                categoryCards[index + 1].focus();
            } else if (e.key === 'ArrowLeft' && categoryCards[index - 1]) {
                categoryCards[index - 1].focus();
            }
        });
    });

    // Performance optimization: Debounce hover effects
    const debounce = (func, wait) => {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    };

    // Log analytics (if analytics is available)
    if (window.gtag) {
        categoryCards.forEach(card => {
            card.addEventListener('click', function () {
                const categoryName = card.querySelector('.category-name')?.textContent;
                gtag('event', 'view_category', {
                    category_name: categoryName,
                    event_category: 'engagement',
                    event_label: categoryName
                });
            });
        });
    }
});

// Add ripple effect styles dynamically
const style = document.createElement('style');
style.textContent = `
    .card-ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(74, 222, 128, 0.3);
        transform: scale(0);
        animation: ripple-animation 0.6s ease-out;
        pointer-events: none;
        z-index: 10;
    }

    @keyframes ripple-animation {
        to {
            transform: scale(2);
            opacity: 0;
        }
    }

    [data-theme="dark"] .card-ripple {
        background: rgba(74, 222, 128, 0.2);
    }
`;
document.head.appendChild(style);

/**
 * Contributor Dashboard Interactions
 * Enhanced UI animations and interactions
 */

// Smooth scroll function for internal links
function smoothScroll(event, targetId) {
    event.preventDefault();
    const target = document.getElementById(targetId);
    if (target) {
        target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
        // Add highlight effect
        target.style.transition = 'all 0.3s ease';
        target.style.transform = 'scale(1.01)';
        setTimeout(() => {
            target.style.transform = 'scale(1)';
        }, 300);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    
    // Auto-dismiss flash messages after 5 seconds
    const flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach(message => {
        setTimeout(() => {
            message.style.transition = 'all 0.5s ease';
            message.style.opacity = '0';
            message.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                message.remove();
            }, 500);
        }, 5000);
    });
    
    // Animate stats on scroll
    const observerOptions = {
        threshold: 0.2,
        rootMargin: '0px 0px -50px 0px'
    };

    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                
                // Animate the counter
                const valueElement = entry.target.querySelector('.stat-value');
                if (valueElement) {
                    animateValue(valueElement);
                }
            }
        });
    }, observerOptions);

    // Observe all stat cards
    document.querySelectorAll('.stat-card').forEach(card => {
        statsObserver.observe(card);
    });

    // Counter animation
    function animateValue(element) {
        const target = parseInt(element.textContent);
        const duration = 1000;
        const increment = target / (duration / 16);
        let current = 0;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 16);
    }

    // Notification interactions
    const notificationItems = document.querySelectorAll('.notification-item');
    notificationItems.forEach(item => {
        item.addEventListener('click', function() {
            // Mark as read
            this.classList.remove('unread');
            
            // Add subtle click animation
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = '';
            }, 100);
        });
    });

    // Activity item hover enhancement
    const activityItems = document.querySelectorAll('.activity-item');
    activityItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        });
    });

    // Action card click analytics (optional - can track user interactions)
    const actionCards = document.querySelectorAll('.action-card');
    actionCards.forEach(card => {
        card.addEventListener('click', function(e) {
            const actionName = this.querySelector('h3').textContent;
            console.log(`Action clicked: ${actionName}`);
            
            // Add ripple effect
            createRipple(e, this);
        });
    });

    // Ripple effect function
    function createRipple(event, element) {
        const ripple = document.createElement('span');
        const rect = element.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;

        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple');

        const existingRipple = element.querySelector('.ripple');
        if (existingRipple) {
            existingRipple.remove();
        }

        element.appendChild(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600);
    }

    // Auto-refresh stats indicator
    let lastUpdate = new Date();
    function updateTimeAgo() {
        const timeElements = document.querySelectorAll('.notification-time');
        timeElements.forEach(element => {
            // This is just a demo - in production, you'd calculate actual time differences
            const text = element.textContent.trim();
            if (text.includes('Just now')) {
                const minutesAgo = Math.floor((new Date() - lastUpdate) / 60000);
                if (minutesAgo > 0) {
                    element.innerHTML = `<i class="bi bi-clock"></i> ${minutesAgo} min ago`;
                }
            }
        });
    }

    // Update time every minute
    setInterval(updateTimeAgo, 60000);

    // Smooth scroll to sections
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });

    // Add loading states for action cards
    const addLoadingState = (element) => {
        element.style.opacity = '0.6';
        element.style.pointerEvents = 'none';
        
        setTimeout(() => {
            element.style.opacity = '1';
            element.style.pointerEvents = 'auto';
        }, 300);
    };

    // Keyboard accessibility improvements
    document.querySelectorAll('.action-card, .notification-item').forEach(item => {
        item.setAttribute('tabindex', '0');
        item.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    });

    // Console greeting
    console.log('%c🎉 Welcome to Mulyasuchi Dashboard!', 'color: #f97316; font-size: 16px; font-weight: bold;');
    console.log('%cContributor Portal v1.0', 'color: #6b7280; font-size: 12px;');
});

// Add ripple CSS dynamically if not exists
if (!document.querySelector('#ripple-styles')) {
    const style = document.createElement('style');
    style.id = 'ripple-styles';
    style.textContent = `
        .action-card {
            position: relative;
            overflow: hidden;
        }
        
        .ripple {
            position: absolute;
            border-radius: 50%;
            background: rgba(249, 115, 22, 0.3);
            transform: scale(0);
            animation: ripple-animation 0.6s ease-out;
            pointer-events: none;
        }
        
        @keyframes ripple-animation {
            to {
                transform: scale(4);
                opacity: 0;
            }
        }
    `;
    document.head.appendChild(style);
}

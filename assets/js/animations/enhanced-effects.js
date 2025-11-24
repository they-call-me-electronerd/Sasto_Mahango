/**
 * Enhanced Scroll Animations & Fluid Effects
 * Mulyasuchi - Professional Interactions
 */

// =================================== 
// SMOOTH SCROLL REVEAL
// ===================================

document.addEventListener('DOMContentLoaded', function() {
    
    // Intersection Observer for Scroll Reveals
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('scroll-reveal');
                // Optionally unobserve after animation
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Observe all sections
    document.querySelectorAll('section').forEach(section => {
        observer.observe(section);
    });

    // ===================================
    // PARALLAX EFFECT ON SCROLL
    // ===================================
    
    let ticking = false;
    
    window.addEventListener('scroll', function() {
        if (!ticking) {
            window.requestAnimationFrame(function() {
                const scrolled = window.pageYOffset;
                
                // Parallax for hero illustration
                const heroIllustration = document.querySelector('.hero-illustration');
                if (heroIllustration) {
                    heroIllustration.style.transform = `translateY(${scrolled * 0.3}px)`;
                }
                
                // Parallax for decorative elements
                const circles = document.querySelectorAll('.illustration-circle');
                circles.forEach(circle => {
                    circle.style.transform = `translateY(${scrolled * 0.2}px)`;
                });
                
                ticking = false;
            });
            ticking = true;
        }
    });

    // ===================================
    // SMOOTH CATEGORY CARD ANIMATIONS
    // ===================================
    
    const cardObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
                cardObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1
    });

    document.querySelectorAll('.category-card-animate').forEach(card => {
        card.style.animationPlayState = 'paused';
        cardObserver.observe(card);
    });

    // ===================================
    // ENHANCED HOVER EFFECTS
    // ===================================
    
    // Add glow effect on card hover
    document.querySelectorAll('.category-card-inner').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.classList.add('hover-glow');
        });
        
        card.addEventListener('mouseleave', function() {
            this.classList.remove('hover-glow');
        });
    });

    // ===================================
    // RIPPLE EFFECT HANDLER
    // ===================================
    
    function createRipple(event) {
        const button = event.currentTarget;
        const ripple = document.createElement('span');
        const diameter = Math.max(button.clientWidth, button.clientHeight);
        const radius = diameter / 2;

        ripple.style.width = ripple.style.height = `${diameter}px`;
        ripple.style.left = `${event.clientX - button.offsetLeft - radius}px`;
        ripple.style.top = `${event.clientY - button.offsetTop - radius}px`;
        ripple.classList.add('ripple');

        const existingRipple = button.querySelector('.ripple');
        if (existingRipple) {
            existingRipple.remove();
        }

        button.appendChild(ripple);
    }

    document.querySelectorAll('.ripple-effect').forEach(button => {
        button.addEventListener('click', createRipple);
    });

    // ===================================
    // SMOOTH SCROLL TO ANCHORS
    // ===================================
    
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && href !== '#!') {
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

    // ===================================
    // FLOATING ANIMATION CONTROL
    // ===================================
    
    // Pause floating animations when out of viewport for performance
    const floatingObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
            } else {
                entry.target.style.animationPlayState = 'paused';
            }
        });
    });

    document.querySelectorAll('.floating').forEach(element => {
        floatingObserver.observe(element);
    });

    // ===================================
    // PERFORMANCE: Reduce Motion
    // ===================================
    
    // Respect user's reduced motion preference
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        document.querySelectorAll('*').forEach(element => {
            element.style.animation = 'none';
            element.style.transition = 'none';
        });
    }

    console.log('✨ Enhanced fluid animations loaded!');
});

/**
 * LYNVAII Hotel Booking System — Client App JS
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Sticky Navbar
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(10, 10, 10, 0.98)';
                navbar.style.boxShadow  = '0 4px 12px rgba(0,0,0,0.5)';
            } else {
                navbar.style.background = 'rgba(10, 10, 10, 0.95)';
                navbar.style.boxShadow  = 'none';
            }
        }, { passive: true });
    }

    // 2. Dropdown behavior (Click outside to close)
    document.addEventListener('click', (e) => {
        const userDropdown = document.querySelector('.navbar-user');
        if (userDropdown && !userDropdown.contains(e.target)) {
            const dropdown = userDropdown.querySelector('.navbar-dropdown');
            if (dropdown) dropdown.classList.remove('show');
        }
    });

    // 3. Close mobile menu when a nav link is clicked
    const navMenu = document.getElementById('navMenu');
    if (navMenu) {
        navMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('show');
            });
        });
    }

    // 4. Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const targetId = this.getAttribute('href');
            if (targetId === '#') return;

            const targetElement = document.querySelector(targetId);
            if (targetElement) {
                e.preventDefault();
                targetElement.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    // 5. Animate elements on scroll (Fade In Up)
    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animationPlayState = 'running';
                obs.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.animate-fadeInUp').forEach(el => {
        el.style.animationPlayState = 'paused';
        observer.observe(el);
    });

    // 6. Navbar search — redirect to destinasi with query
    const navSearch = document.getElementById('navSearch');
    if (navSearch) {
        navSearch.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && navSearch.value.trim()) {
                e.preventDefault();
                const base = document.querySelector('meta[name="base-url"]')?.content || '';
                window.location.href = base + '?page=destinasi&search=' + encodeURIComponent(navSearch.value.trim());
            }
        });
    }

    // 7. Auto-hide flash messages with slide out animation
    const flash = document.getElementById('flashMessage');
    if (flash) {
        setTimeout(() => {
            flash.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            flash.style.opacity = '0';
            flash.style.transform = 'translateX(20px)';
            setTimeout(() => flash.remove(), 500);
        }, 5000);
    }
});

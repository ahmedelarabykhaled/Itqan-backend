import './bootstrap';

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/**
 * Reveals elements as they scroll into view, honouring the per-element delay
 * declared through the `data-reveal-delay` attribute.
 */
function initScrollReveal() {
    const targets = document.querySelectorAll('[data-reveal]');

    if (targets.length === 0) {
        return;
    }

    if (prefersReducedMotion || ! ('IntersectionObserver' in window)) {
        targets.forEach((target) => target.classList.add('is-revealed'));

        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (! entry.isIntersecting) {
                    return;
                }

                const delay = entry.target.dataset.revealDelay;

                if (delay) {
                    entry.target.style.setProperty('--reveal-delay', `${delay}ms`);
                }

                entry.target.classList.add('is-revealed');
                observer.unobserve(entry.target);
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -8% 0px' },
    );

    targets.forEach((target) => observer.observe(target));
}

/**
 * Counts a number up from zero once its element becomes visible.
 */
function initCounters() {
    const counters = document.querySelectorAll('[data-counter]');

    if (counters.length === 0) {
        return;
    }

    const render = (element, value) => {
        const decimals = Number(element.dataset.counterDecimals ?? 0);
        const prefix = element.dataset.counterPrefix ?? '';
        const suffix = element.dataset.counterSuffix ?? '';

        element.textContent = `${prefix}${value.toFixed(decimals)}${suffix}`;
    };

    if (prefersReducedMotion || ! ('IntersectionObserver' in window)) {
        counters.forEach((counter) => render(counter, Number(counter.dataset.counter)));

        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (! entry.isIntersecting) {
                    return;
                }

                const element = entry.target;
                const target = Number(element.dataset.counter);
                const duration = Number(element.dataset.counterDuration ?? 1600);
                const start = performance.now();

                const step = (now) => {
                    const progress = Math.min((now - start) / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);

                    render(element, target * eased);

                    if (progress < 1) {
                        requestAnimationFrame(step);
                    }
                };

                requestAnimationFrame(step);
                observer.unobserve(element);
            });
        },
        { threshold: 0.4 },
    );

    counters.forEach((counter) => {
        render(counter, 0);
        observer.observe(counter);
    });
}

/**
 * Adds a solid background to the header once the page is scrolled.
 */
function initNavScrollState() {
    const header = document.querySelector('[data-nav]');

    if (! header) {
        return;
    }

    const update = () => header.classList.toggle('is-scrolled', window.scrollY > 12);

    update();
    window.addEventListener('scroll', update, { passive: true });
}

function initMobileMenu() {
    const menuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');

    if (! menuToggle || ! mobileMenu) {
        return;
    }

    menuToggle.addEventListener('click', () => {
        const isOpen = ! mobileMenu.classList.contains('hidden');

        mobileMenu.classList.toggle('hidden', isOpen);
        menuToggle.setAttribute('aria-expanded', String(! isOpen));
    });

    mobileMenu.querySelectorAll('a[href^="#"]').forEach((link) => {
        link.addEventListener('click', () => {
            mobileMenu.classList.add('hidden');
            menuToggle.setAttribute('aria-expanded', 'false');
        });
    });
}

function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
        anchor.addEventListener('click', (event) => {
            const href = anchor.getAttribute('href');

            if (! href || href === '#') {
                return;
            }

            const target = document.querySelector(href);

            if (! target) {
                return;
            }

            event.preventDefault();
            target.scrollIntoView({
                behavior: prefersReducedMotion ? 'auto' : 'smooth',
                block: 'start',
            });
        });
    });
}

/**
 * Tracks the pointer over gradient cards so their glow follows the cursor.
 */
function initCardGlow() {
    if (prefersReducedMotion) {
        return;
    }

    document.querySelectorAll('.landing-gradient-card').forEach((card) => {
        card.addEventListener('pointermove', (event) => {
            const bounds = card.getBoundingClientRect();

            card.style.setProperty('--x', `${((event.clientX - bounds.left) / bounds.width) * 100}%`);
            card.style.setProperty('--y', `${((event.clientY - bounds.top) / bounds.height) * 100}%`);
        });
    });
}

function closeModal(modal) {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
}

function initModals() {
    document.querySelectorAll('[data-modal-open]').forEach((trigger) => {
        trigger.addEventListener('click', () => {
            const modal = document.getElementById(trigger.getAttribute('data-modal-open'));

            if (! modal) {
                return;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        });
    });

    document.querySelectorAll('[data-modal-close]').forEach((closeButton) => {
        closeButton.addEventListener('click', () => {
            const modal = closeButton.closest('[role="dialog"]');

            if (modal) {
                closeModal(modal);
            }
        });
    });

    document.querySelectorAll('[role="dialog"]').forEach((modal) => {
        modal.addEventListener('click', (event) => {
            if (event.target === modal) {
                closeModal(modal);
            }
        });
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape') {
            return;
        }

        document.querySelectorAll('[role="dialog"]:not(.hidden)').forEach(closeModal);
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initScrollReveal();
    initCounters();
    initNavScrollState();
    initMobileMenu();
    initSmoothScroll();
    initCardGlow();
    initModals();
});

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-feature-carousel]').forEach(carousel => {
        const slides = Array.from(carousel.querySelectorAll('[data-feature-slide]'));
        if (slides.length < 2) return;
        const dots = Array.from(carousel.querySelectorAll('[data-carousel-go]'));
        const toggle = carousel.querySelector('[data-carousel-toggle]');
        const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        let index = 0;
        let playing = !reducedMotion.matches;
        let hovered = false;
        let timer;
        let touch;

        function schedule() {
            clearTimeout(timer);
            if (playing && !hovered && !document.hidden) timer = setTimeout(() => show(index + 1), 8000);
        }

        function updateToggle() {
            toggle.setAttribute('aria-label', playing ? 'Pause slideshow' : 'Play slideshow');
            toggle.querySelector('[data-carousel-play-icon]').textContent = playing ? 'Ⅱ' : '▶';
        }

        function pause() {
            playing = false;
            updateToggle();
            schedule();
        }

        function show(next, manual = false) {
            index = (next + slides.length) % slides.length;
            slides.forEach((slide, position) => {
                const active = position === index;
                slide.hidden = !active;
                slide.inert = !active;
                slide.setAttribute('aria-hidden', String(!active));
            });
            dots.forEach((dot, position) => {
                if (position === index) dot.setAttribute('aria-current', 'true');
                else dot.removeAttribute('aria-current');
            });
            carousel.querySelector('[data-carousel-count]').textContent = `${index + 1} / ${slides.length}`;
            carousel.dataset.slide = String(index);
            if (manual) {
                pause();
                carousel.querySelector('[data-carousel-status]').textContent = slides[index].getAttribute('aria-label');
            }
            schedule();
        }

        carousel.querySelector('[data-carousel-previous]').addEventListener('click', () => show(index - 1, true));
        carousel.querySelector('[data-carousel-next]').addEventListener('click', () => show(index + 1, true));
        dots.forEach(dot => dot.addEventListener('click', () => show(Number(dot.dataset.carouselGo), true)));
        toggle.addEventListener('click', () => {
            playing = !playing;
            updateToggle();
            schedule();
        });
        carousel.addEventListener('focusin', event => { if (!toggle.contains(event.target)) pause(); });
        carousel.addEventListener('mouseenter', () => { hovered = true; schedule(); });
        carousel.addEventListener('mouseleave', () => { hovered = false; schedule(); });
        carousel.addEventListener('keydown', event => {
            const positions = { ArrowLeft: index - 1, ArrowRight: index + 1, Home: 0, End: slides.length - 1 };
            if (!(event.key in positions)) return;
            event.preventDefault();
            show(positions[event.key], true);
        });
        carousel.addEventListener('pointerdown', event => {
            if (event.pointerType !== 'touch' || event.target.closest('a, button')) return;
            touch = { x: event.clientX, y: event.clientY };
        });
        carousel.addEventListener('pointerup', event => {
            if (!touch) return;
            const x = event.clientX - touch.x;
            const y = event.clientY - touch.y;
            touch = null;
            if (Math.abs(x) > 50 && Math.abs(x) > Math.abs(y)) show(index + (x < 0 ? 1 : -1), true);
        });
        carousel.addEventListener('pointercancel', () => { touch = null; });
        reducedMotion.addEventListener('change', () => { if (reducedMotion.matches) pause(); });
        document.addEventListener('visibilitychange', schedule);
        carousel.classList.add('feature-carousel-ready');
        carousel.querySelector('[data-carousel-controls]').hidden = false;
        updateToggle();
        show(0);
    });
});

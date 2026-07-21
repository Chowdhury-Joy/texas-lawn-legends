// Alpine is provided by Livewire (loaded site-wide via @livewireScripts in the
// layout), so we do NOT import/start a second Alpine instance here — doing so
// would cause "multiple instances of Alpine" conflicts on Livewire pages.
//
// Register any custom Alpine plugins/directives on the livewire:init hook:
// document.addEventListener('livewire:init', () => { /* Alpine.plugin(...) */ });

// Subtle scroll-reveal: add `.is-visible` to any [data-reveal]/[data-stagger]
// element as it individually enters the viewport — each element is observed
// on its own, so staggered cards only start their fade once they themselves
// are visible, not as soon as the (possibly much taller) section they live
// in first pokes into view. Re-scans after Livewire navigations/updates.
document.addEventListener('DOMContentLoaded', () => {
    const revealSelector = '[data-reveal], [data-stagger]';
    const reveals = document.querySelectorAll(revealSelector);
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reduce || ! ('IntersectionObserver' in window)) {
        reveals.forEach((el) => el.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    reveals.forEach((el) => observer.observe(el));

    // Re-scan after Livewire swaps content (estimator/portal steps, etc.).
    document.addEventListener('livewire:navigated', () => {
        document.querySelectorAll(`${revealSelector}:not(.is-visible)`).forEach((el) => observer.observe(el));
    });
});

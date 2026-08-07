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
// in first pokes into view. Handles dynamically added content via MutationObserver.
document.addEventListener('DOMContentLoaded', () => {
    const revealSelector = '[data-reveal], [data-stagger]';
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reduce || ! ('IntersectionObserver' in window)) {
        document.querySelectorAll(revealSelector).forEach((el) => el.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

    const observeElements = (root = document) => {
        const elements = root.querySelectorAll ? root.querySelectorAll(`${revealSelector}:not(.is-visible)`) : [];
        elements.forEach((el) => observer.observe(el));
        if (root.matches && root.matches(`${revealSelector}:not(.is-visible)`)) {
            observer.observe(root);
        }
    };

    observeElements();

    // Re-scan whenever DOM mutations add new content (Livewire steps, Alpine state changes, etc.)
    const mutationObserver = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (node.nodeType === 1) {
                    observeElements(node);
                }
            });
        });
    });

    mutationObserver.observe(document.body, { childList: true, subtree: true });

    // Re-scan after Livewire swaps content
    document.addEventListener('livewire:navigated', () => observeElements());
    document.addEventListener('livewire:initialized', () => observeElements());
});


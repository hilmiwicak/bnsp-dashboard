// Dashboard sidebar drawer (mobile / tablet only).
const sidebar = document.querySelector('[data-sidebar]');
const backdrop = document.querySelector('[data-sidebar-backdrop]');
const toggle = document.querySelector('[data-sidebar-toggle]');

if (sidebar && backdrop && toggle) {
    const setOpen = (open) => {
        sidebar.classList.toggle('-translate-x-full', !open);
        backdrop.classList.toggle('hidden', !open);
    };

    toggle.addEventListener('click', () => {
        setOpen(sidebar.classList.contains('-translate-x-full'));
    });

    backdrop.addEventListener('click', () => setOpen(false));
}

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

// Salin nilai field sekali-tampil (kredensial tenant) ke clipboard.
document.querySelectorAll('[data-copy]').forEach((button) => {
    button.addEventListener('click', async () => {
        const field = document.getElementById(button.dataset.copy);
        if (!field) return;

        const value = 'value' in field ? field.value : field.textContent.trim();

        try {
            await navigator.clipboard.writeText(value);
        } catch {
            // Fallback untuk browser tanpa Clipboard API (mis. non-HTTPS).
            const helper = document.createElement('textarea');
            helper.value = value;
            helper.setAttribute('readonly', '');
            helper.style.position = 'fixed';
            helper.style.opacity = '0';
            document.body.appendChild(helper);
            helper.select();
            document.execCommand('copy');
            helper.remove();
        }

        const label = button.querySelector('[data-copy-label]');
        if (!label) return;

        const original = label.textContent;
        label.textContent = 'Tersalin';
        setTimeout(() => {
            label.textContent = original;
        }, 1500);
    });
});

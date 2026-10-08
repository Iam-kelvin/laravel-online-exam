(() => {
    const key = 'crazyexam-theme';
    const system = window.matchMedia('(prefers-color-scheme: dark)');
    let preference;
    try { preference = localStorage.getItem(key); } catch (_) {}

    function apply(theme) {
        document.documentElement.dataset.theme = theme;
        document.querySelectorAll('[data-theme-toggle]').forEach(button => {
            button.textContent = theme === 'dark' ? 'Light mode' : 'Dark mode';
            button.setAttribute('aria-pressed', String(theme === 'dark'));
            button.setAttribute('aria-label', `Switch to ${theme === 'dark' ? 'light' : 'dark'} mode`);
        });
    }

    apply(preference === 'dark' || preference === 'light' ? preference : system.matches ? 'dark' : 'light');
    document.addEventListener('DOMContentLoaded', () => {
        apply(document.documentElement.dataset.theme);
        document.querySelectorAll('[data-theme-toggle]').forEach(button => button.addEventListener('click', () => {
            preference = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
            try { localStorage.setItem(key, preference); } catch (_) {}
            apply(preference);
        }));
    });
    system.addEventListener('change', () => { if (preference !== 'light' && preference !== 'dark') apply(system.matches ? 'dark' : 'light'); });
    window.addEventListener('storage', event => {
        if (event.key === key) {
            preference = event.newValue;
            apply(preference === 'dark' || preference === 'light' ? preference : system.matches ? 'dark' : 'light');
        }
    });
})();

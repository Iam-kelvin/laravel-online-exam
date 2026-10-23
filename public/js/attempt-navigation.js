document.addEventListener('DOMContentLoaded', () => {
    const element = document.getElementById('attemptNavigation');
    if (!element) return;
    const config = JSON.parse(element.textContent);
    const key = 'crazyexam-attempt-completed-' + config.attemptId;
    function wasCompleted() {
        try { return sessionStorage.getItem(key) === 'true'; } catch (_) { return false; }
    }
    if (config.completed) {
        try { sessionStorage.setItem(key, 'true'); } catch (_) {}
        return;
    }
    function leaveCompletedAttempt() {
        if (!wasCompleted()) return false;
        document.querySelector('[data-active-attempt]')?.setAttribute('inert', '');
        window.location.replace(config.setupUrl);
        return true;
    }
    if (leaveCompletedAttempt()) return;
    window.addEventListener('pageshow', event => {
        if (leaveCompletedAttempt()) return;
        // A restored browser snapshot must recheck the server's deadline and status.
        if (event.persisted) window.location.reload();
    });
    document.querySelector('[data-finalize-attempt]')?.addEventListener('submit', () => {
        window.history.replaceState(null, '', config.setupUrl);
    });
});

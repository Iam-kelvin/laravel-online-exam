document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-password-toggle]').forEach(function (input) {
        var wrapper = input.closest('.password-input');
        var button = wrapper ? wrapper.querySelector('[data-password-toggle-button]') : null;

        if (! button || input.dataset.passwordToggleReady === 'true') {
            return;
        }

        input.dataset.passwordToggleReady = 'true';

        button.addEventListener('click', function () {
            var isVisible = input.type === 'text';

            input.type = isVisible ? 'password' : 'text';
            button.textContent = isVisible ? 'Show' : 'Hide';
            button.setAttribute('aria-label', isVisible ? 'Show password' : 'Hide password');
            button.setAttribute('aria-pressed', isVisible ? 'false' : 'true');
        });
    });
});

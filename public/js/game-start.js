document.addEventListener('DOMContentLoaded', () => {
    const duration = document.getElementById('gameDuration');
    if (!duration) return;
    const less = document.getElementById('gameDurationLess');
    const more = document.getElementById('gameDurationMore');
    const update = () => {
        less.disabled = Number(duration.value) <= 15;
        more.disabled = Number(duration.value) >= 180;
    };
    less.addEventListener('click', () => { duration.value = Math.max(15, Number(duration.value) - 15); update(); });
    more.addEventListener('click', () => { duration.value = Math.min(180, Number(duration.value) + 15); update(); });
    duration.addEventListener('change', update);
    update();
});

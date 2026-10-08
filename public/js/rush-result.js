document.addEventListener('DOMContentLoaded', () => {
    const element = document.getElementById('rushResultConfig');
    const button = document.getElementById('rushResultSound');
    if (!element || !button) return;
    const config = JSON.parse(element.textContent);
    const audio = new window.CrazyExamAudio();
    const status = document.getElementById('rushAudioStatus');
    const key = 'crazyexam-rush-result-' + config.attemptId;
    let played = false;
    try { played = sessionStorage.getItem(key) === 'true'; } catch (_) {}
    audio.onChange = playing => {
        button.textContent = playing ? 'Pause reaction' : 'Play reaction';
        button.setAttribute('aria-pressed', String(playing));
    };
    async function play() {
        if (await audio.playClip(config.sound)) {
            status.textContent = '';
            try { sessionStorage.setItem(key, 'true'); } catch (_) {}
        }
    }
    button.addEventListener('click', () => {
        if (audio.clip && !audio.clip.paused) { audio.stop(); return; }
        audio.setMuted(false);
        play();
    });
    if (audio.muted) status.textContent = 'Sound is muted';
    else if (!played) play();
    window.addEventListener('pagehide', () => audio.stop());
});

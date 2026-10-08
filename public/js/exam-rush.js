document.addEventListener('DOMContentLoaded', () => {
    const configElement = document.getElementById('rushConfig');
    if (!configElement) return;
    const config = JSON.parse(configElement.textContent);
    const cards = Array.from(document.querySelectorAll('.rush-question'));
    const finishForm = document.getElementById('rushFinishForm');
    const finishButton = document.getElementById('rushFinishButton');
    const soundToggle = document.getElementById('rushSoundToggle');
    const notice = document.getElementById('rushNotice');
    const started = performance.now();
    let index = 0;
    let streak = Number(document.getElementById('rushStreak').textContent);
    let ending = false;
    let pending = null;
    let advanceTimeout;
    const audio = new window.CrazyExamAudio();
    const introKey = 'crazyexam-rush-intro-' + config.attemptId;
    let introPending = !!config.introSound && !audio.muted;
    try { if (sessionStorage.getItem(introKey) === 'true') introPending = false; } catch (_) {}

    function updateSoundToggle() {
        soundToggle.textContent = audio.muted ? 'Sound off' : 'Sound on';
        soundToggle.setAttribute('aria-pressed', String(audio.muted));
        soundToggle.setAttribute('aria-label', audio.muted ? 'Unmute sound effects' : 'Mute sound effects');
    }

    async function playFeedback(correct) {
        if (audio.muted) return;
        if (!correct && config.wrongSounds.length) {
            const url = config.wrongSounds[Math.floor(Math.random() * config.wrongSounds.length)];
            if (!await audio.playClip(url)) audio.tone(false);
        } else {
            audio.tone(correct);
        }
    }

    async function playIntro() {
        if (!introPending || audio.muted) return;
        if (await audio.playClip(config.introSound)) {
            introPending = false;
            try { sessionStorage.setItem(introKey, 'true'); } catch (_) {}
        }
    }

    soundToggle.addEventListener('click', () => {
        audio.setMuted(!audio.muted);
        updateSoundToggle();
        if (!audio.muted) playIntro();
    });
    updateSoundToggle();
    if (introPending) {
        playIntro();
        // Mobile browsers may need the first tap before allowing page-entry audio.
        document.addEventListener('pointerdown', () => { if (introPending) playIntro(); }, { once: true });
    }

    const shell = document.querySelector('.rush-shell');
    function fitShell() {
        shell.style.setProperty('--rush-offset', Math.ceil(shell.getBoundingClientRect().top + 12) + 'px');
    }
    fitShell();
    window.addEventListener('resize', fitShell);
    window.visualViewport?.addEventListener('resize', fitShell);
    if (window.ResizeObserver) new ResizeObserver(fitShell).observe(document.querySelector('.app-topbar'));
    window.addEventListener('pagehide', () => audio.stop());

    function showQuestion(focus = false) {
        cards.forEach((card, cardIndex) => { card.hidden = cardIndex !== index; });
        if (!cards[index]) {
            finishExam();
        } else if (focus) {
            cards[index].querySelector('.rush-question-title').focus({ preventScroll: true });
        }
    }

    async function finishExam() {
        if (ending) return;
        ending = true;
        clearTimeout(advanceTimeout);
        finishButton.disabled = true;
        finishButton.textContent = 'Saving your result…';
        cards.forEach(card => card.querySelectorAll('button').forEach(button => { button.disabled = true; }));
        // Let an answer already being saved settle before finalizing its score.
        if (pending) await pending;
        audio.stop();
        finishForm.submit();
    }

    finishForm.addEventListener('submit', event => {
        event.preventDefault();
        finishExam();
    });

    function advance(result) {
        if (ending) return;
        clearTimeout(advanceTimeout);
        if (result.finished) {
            ending = true;
            window.location.assign(result.review_url);
            return;
        }
        index += 1;
        showQuestion(true);
    }

    async function markAnswer(card, button) {
        const buttons = Array.from(card.querySelectorAll('.rush-option'));
        buttons.forEach(option => { option.disabled = true; });
        const feedback = card.querySelector('.rush-feedback');
        feedback.textContent = 'Checking…';
        notice.hidden = true;
        const controller = new AbortController();
        const requestTimeout = setTimeout(() => controller.abort(), 10000);
        try {
            const response = await fetch(card.dataset.answerUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ answer: button.dataset.answer }),
                signal: controller.signal,
            });
            const result = await response.json();
            if (response.status === 410 && result.review_url) {
                ending = true;
                window.location.assign(result.review_url);
                return;
            }
            if (!response.ok) throw new Error(result.message || 'Could not save your answer.');
            if (ending) return;

            buttons.forEach(option => {
                if (option.dataset.answer === result.correct_answer) option.classList.add('is-correct');
                if (option.dataset.answer === result.selected_answer && !result.is_correct) option.classList.add('is-wrong');
            });
            const correctButton = buttons.find(option => option.dataset.answer === result.correct_answer);
            const correctLabel = Array.from(correctButton.children).map(part => part.textContent.trim()).join('. ');
            feedback.textContent = result.is_correct ? '✓ Correct! Keep going.' : `✕ Wrong. Correct answer: ${correctLabel}`;
            feedback.classList.add(result.is_correct ? 'text-success' : 'text-danger');
            document.getElementById('rushAnswered').textContent = result.answered;
            document.getElementById('rushCorrect').textContent = result.score;
            streak = result.is_correct ? streak + 1 : 0;
            document.getElementById('rushStreak').textContent = streak;
            playFeedback(result.is_correct);
            const next = card.querySelector('.rush-next');
            next.hidden = false;
            next.textContent = result.finished ? 'See your result →' : 'Next question →';
            next.onclick = () => advance(result);
            next.focus({ preventScroll: true });
            advanceTimeout = setTimeout(() => advance(result), result.is_correct ? 650 : 1200);
        } catch (_) {
            if (ending) return;
            feedback.textContent = '';
            notice.textContent = 'Could not confirm your answer. Check your connection and tap again to retry; your first saved answer still counts.';
            notice.hidden = false;
            buttons.forEach(option => { option.disabled = false; });
        } finally {
            clearTimeout(requestTimeout);
        }
    }

    cards.forEach(card => card.querySelectorAll('.rush-option').forEach(button => {
        button.addEventListener('click', () => {
            if (pending || ending) return;
            audio.unlock();
            pending = markAnswer(card, button).finally(() => { pending = null; });
        });
    }));

    function tick() {
        const remaining = Math.max(0, Math.ceil(config.remainingSeconds - (performance.now() - started) / 1000));
        document.getElementById('timer').textContent = `${String(Math.floor(remaining / 60)).padStart(2, '0')}:${String(remaining % 60).padStart(2, '0')}`;
        document.getElementById('rushTimerBox').classList.toggle('timer-urgent', remaining <= 10);
        if (remaining <= 0) finishExam();
    }
    showQuestion();
    tick();
    const interval = setInterval(() => { if (ending) clearInterval(interval); else tick(); }, 200);
    document.addEventListener('visibilitychange', () => { if (!document.hidden && !ending) tick(); });
});

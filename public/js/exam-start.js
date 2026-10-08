document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('examStartForm');
    if (!form) return;
    const subjects = JSON.parse(document.getElementById('subjectData').textContent);
    const checks = Array.from(form.querySelectorAll('[name="subject_ids[]"]'));
    const count = document.getElementById('questionCount');
    const duration = document.getElementById('durationValue');
    const unit = document.getElementById('durationUnit');
    const rushDuration = document.getElementById('rushDuration');
    const bankType = document.getElementById('bankType');
    const picker = document.getElementById('subjectPicker');
    const chips = document.getElementById('selectedSubjects');
    const notice = document.getElementById('examStartNotice');
    const room = document.querySelector('.exam-room');
    const tiles = document.getElementById('subjectTiles');
    const compact = window.matchMedia('(max-width: 600px)');
    const isRush = () => form.querySelector('[name="mode"]:checked').value === 'rush';

    const selectedSubjects = () => subjects.filter(subject => checks.some(input => input.checked && Number(input.value) === subject.id));
    const subjectPriority = name => {
        const terms = ['english', 'math', 'biology', 'chemistry', 'physics', 'history', 'lagos', 'pop', 'bible', 'quran'];
        const match = terms.findIndex(term => name.toLocaleLowerCase().includes(term));
        return match < 0 ? terms.length : match;
    };

    function renderTiles(available) {
        tiles.replaceChildren();
        const visible = [...available].sort((a, b) => subjectPriority(a.name) - subjectPriority(b.name));
        const selected = selectedSubjects();
        const selectedInCategory = selected.filter(subject => subject.type === bankType.value);
        const focused = compact.matches && selectedInCategory.length;
        const displaySubjects = focused ? selectedInCategory.slice(0, 4) : visible.slice(0, compact.matches ? 4 : 6);
        tiles.classList.toggle('room-subject-tiles-focused', Boolean(focused && displaySubjects.length === 1));
        displaySubjects.forEach(subject => {
            const check = checks.find(input => Number(input.value) === subject.id);
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'room-subject-tile';
            button.dataset.subject = subject.id;
            button.title = subject.name;
            button.setAttribute('aria-pressed', String(check.checked));
            const monogram = document.createElement('span');
            monogram.className = 'room-subject-monogram';
            monogram.setAttribute('aria-hidden', 'true');
            monogram.textContent = subject.name.split(/\s+/).map(word => word[0]).slice(0, 2).join('').toUpperCase();
            const name = document.createElement('span');
            name.className = 'room-subject-name';
            name.textContent = subject.name;
            button.append(monogram, name);
            if (check.checked) {
                const tick = document.createElement('span');
                tick.className = 'room-subject-tick';
                tick.textContent = '✓';
                tick.setAttribute('aria-hidden', 'true');
                button.append(tick);
            }
            button.addEventListener('click', () => {
                const wasChecked = check.checked;
                if (isRush()) checks.forEach(input => { input.checked = false; });
                check.checked = !wasChecked;
                renderSubjects();
                tiles.querySelector('[data-subject="' + subject.id + '"]')?.focus({ preventScroll: true });
            });
            tiles.append(button);
        });
        if (!available.length) {
            const message = document.createElement('p');
            message.className = 'room-help';
            message.textContent = 'No subjects in this category yet.';
            tiles.append(message);
        }
    }

    function updateSummary() {
        const selected = selectedSubjects();
        const rush = isRush();
        const seconds = rush ? Number(rushDuration.value) : Math.round(Number(duration.value) * (unit.value === 'minutes' ? 60 : 1));
        const validTime = Number.isFinite(seconds) && seconds >= 1;
        const timeValue = validTime ? !rush && seconds % 60 === 0 ? seconds / 60 : seconds : '—';
        const timeUnit = validTime ? !rush && seconds % 60 === 0 ? 'min' : 'sec' : '';
        const durationSummary = document.getElementById('summaryDurationValue');
        const suffix = document.createElement('span');
        suffix.textContent = timeUnit;
        durationSummary.replaceChildren(document.createTextNode(String(timeValue)), suffix);
        const names = selected.length > 2 ? selected.length + ' subjects selected' : selected.map(subject => subject.name).join(' + ');
        document.getElementById('sessionSubjects').textContent = names || 'Choose what you want to practise.';
        document.getElementById('sessionTitle').textContent = rush ? 'Race the clock.' : 'A fresh start.';
        document.getElementById('modeCaption').textContent = rush
            ? 'Race the clock. How many can you answer in ' + seconds + ' seconds?'
            : 'A focused session, at your pace.';
        document.getElementById('summaryQuestionLabel').textContent = rush ? 'Subjects' : 'Questions';
        document.getElementById('summaryQuestionValue').textContent = rush ? selected.length || '—' : Number(count.value) || '—';
        document.getElementById('mobileSessionSubject').textContent = names || 'Pick a subject';
        document.getElementById('mobileSessionDetails').textContent = (rush ? 'Rush' : (Number(count.value) || '—') + ' questions') + ' · ' + timeValue + ' ' + timeUnit;
        document.getElementById('sessionRuleOne').textContent = rush ? 'Instant feedback on every answer' : 'Fresh questions every session';
        document.getElementById('sessionRuleTwo').textContent = rush ? 'Your first answer is the one that counts' : 'Review your answers when you finish';
        const status = document.getElementById('sessionStatusText');
        const nextStatus = selected.length ? 'Ready to start' : 'Choose a subject';
        if (status.textContent !== nextStatus) status.textContent = nextStatus;
        document.getElementById('startExamHint').textContent = selected.length ? 'Ready when you are.' : 'Choose a subject to get started.';
        document.getElementById('startExamButton').disabled = !selected.length;
        room.dataset.ready = String(selected.length > 0);
        document.querySelectorAll('[data-question-count]').forEach(button => {
            button.setAttribute('aria-pressed', String(Number(button.dataset.questionCount) === Number(count.value)));
        });
    }

    function updateSuggestion() {
        const seconds = Math.min(86400, Math.max(1, Number(count.value) || 1) * 30);
        document.getElementById('suggestedTime').textContent = seconds % 60 ? seconds + ' seconds' : seconds / 60 + ' minutes';
        updateSummary();
        return seconds;
    }

    function updatePicker() {
        const selected = checks.filter(input => input.checked).map(input => Number(input.value));
        picker.replaceChildren(new Option('Browse all subjects', ''));
        const available = subjects.filter(subject => subject.type === bankType.value);
        available.forEach(subject => {
            const option = new Option(subject.name, subject.id);
            option.disabled = !isRush() && selected.includes(subject.id);
            picker.add(option);
        });
        if (!available.length) picker.add(new Option('No subjects found', '', false, false));
        renderTiles(available);
        document.querySelectorAll('[data-bank-type]').forEach(button => {
            button.setAttribute('aria-pressed', String(button.dataset.bankType === bankType.value));
        });
    }

    function renderSubjects() {
        chips.replaceChildren();
        chips.hidden = isRush() || (compact.matches && selectedSubjects().length <= 4);
        checks.filter(input => input.checked).forEach(input => {
            const subject = subjects.find(item => item.id === Number(input.value));
            if (!subject) return;
            const button = document.createElement('button');
            button.type = 'button';
            button.className = 'subject-chip';
            button.dataset.removeSubject = subject.id;
            button.setAttribute('aria-label', 'Remove ' + subject.name);
            button.append(document.createTextNode(subject.name + ' '));
            const cross = document.createElement('span');
            cross.textContent = '×';
            cross.setAttribute('aria-hidden', 'true');
            button.append(cross);
            button.addEventListener('click', () => {
                input.checked = false;
                renderSubjects();
                picker.focus();
            });
            chips.append(button);
        });
        updatePicker();
        updateSummary();
    }

    function updateStepper() {
        document.getElementById('decreaseRushDuration').disabled = Number(rushDuration.value) <= 15;
        document.getElementById('increaseRushDuration').disabled = Number(rushDuration.value) >= 180;
        updateSummary();
    }

    function updateMode() {
        const rush = isRush();
        document.getElementById('questionCountGroup').hidden = rush;
        document.getElementById('standardDurationGroup').hidden = rush;
        document.getElementById('rushDurationGroup').hidden = !rush;
        count.disabled = rush;
        count.required = !rush;
        duration.disabled = rush;
        duration.required = !rush;
        unit.disabled = rush;
        rushDuration.disabled = !rush;
        chips.hidden = rush;
        document.getElementById('challengeSettingsTitle').textContent = rush ? 'Set the clock' : 'Set the pace';
        document.getElementById('practiceQuickPicks').hidden = rush;
        document.getElementById('timeSuggestion').hidden = rush;
        room.dataset.mode = rush ? 'rush' : 'standard';
        document.querySelectorAll('[data-mode-icon]').forEach(icon => { icon.hidden = icon.dataset.modeIcon !== room.dataset.mode; });
        document.getElementById('subjectHint').textContent = rush ? 'One subject. All your focus.' : 'Choose one, or mix a few together.';
        document.getElementById('startExamLabel').textContent = rush ? 'Start Rush' : 'Start practice';
        if (rush) checks.filter(input => input.checked).slice(1).forEach(input => { input.checked = false; });
        const first = subjects.find(subject => checks.some(input => input.checked && Number(input.value) === subject.id));
        if (rush && first) bankType.value = first.type;
        renderSubjects();
        updateStepper();
    }

    picker.addEventListener('change', () => {
        const id = Number(picker.value);
        if (!id) return;
        if (isRush()) checks.forEach(input => { input.checked = false; });
        const check = checks.find(input => Number(input.value) === id);
        if (check) check.checked = true;
        const description = document.getElementById('subjectDescription');
        description.textContent = subjects.find(subject => subject.id === id)?.description || '';
        description.hidden = !description.textContent;
        renderSubjects();
    });
    document.querySelectorAll('[data-bank-type]').forEach(button => button.addEventListener('click', () => {
        if (bankType.value === button.dataset.bankType) return;
        bankType.value = button.dataset.bankType;
        document.getElementById('subjectDescription').hidden = true;
        if (isRush()) checks.forEach(input => { input.checked = false; });
        renderSubjects();
    }));
    form.querySelectorAll('[name="mode"]').forEach(input => input.addEventListener('change', updateMode));
    checks.forEach(input => input.addEventListener('change', updateMode));
    count.addEventListener('input', updateSuggestion);
    duration.addEventListener('input', updateSummary);
    unit.addEventListener('change', updateSummary);
    compact.addEventListener('change', renderSubjects);
    document.querySelectorAll('[data-question-count]').forEach(button => button.addEventListener('click', () => {
        count.value = button.dataset.questionCount;
        updateSuggestion();
    }));
    [-1, 1].forEach(direction => document.getElementById(direction < 0 ? 'decreaseQuestionCount' : 'increaseQuestionCount').addEventListener('click', () => {
        count.value = String(Math.max(1, Math.min(10000, (Number(count.value) || 1) + direction)));
        updateSuggestion();
    }));
    document.getElementById('useSuggestedTime').addEventListener('click', () => {
        const seconds = updateSuggestion();
        unit.value = seconds % 60 === 0 ? 'minutes' : 'seconds';
        duration.value = unit.value === 'minutes' ? seconds / 60 : seconds;
        updateSummary();
    });
    [-1, 1].forEach(direction => document.getElementById(direction < 0 ? 'decreaseRushDuration' : 'increaseRushDuration').addEventListener('click', () => {
        rushDuration.value = String(Math.max(15, Math.min(180, Number(rushDuration.value) + direction * 15)));
        updateStepper();
    }));
    rushDuration.addEventListener('change', updateStepper);
    form.addEventListener('submit', event => {
        const selectedCount = checks.filter(input => input.checked).length;
        const seconds = isRush() ? Number(rushDuration.value) : Math.round(Number(duration.value) * (unit.value === 'minutes' ? 60 : 1));
        const messages = [];
        if (!selectedCount) messages.push('Choose a subject to start.');
        if (isRush() && selectedCount !== 1) messages.push('Rush uses one subject.');
        if (isRush() && (seconds < 15 || seconds > 180 || seconds % 15)) messages.push('Choose 15 to 180 seconds in 15-second steps.');
        if (!Number.isFinite(seconds) || seconds < 1 || seconds > 86400) messages.push('Choose a valid duration.');
        if (messages.length) {
            event.preventDefault();
            notice.textContent = messages.join(' ');
            notice.classList.remove('d-none');
            notice.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    });
    updateSuggestion();
    updateMode();
});

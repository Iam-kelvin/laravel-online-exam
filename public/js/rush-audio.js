window.CrazyExamAudio = class {
    constructor() {
        this.context = null;
        this.clip = null;
        this.voices = new Set();
        this.muted = false;
        try { this.muted = localStorage.getItem('crazyexam-rush-muted') === 'true'; } catch (_) {}
    }

    unlock() {
        if (this.muted) return;
        try {
            const Context = window.AudioContext || window.webkitAudioContext;
            if (!this.context && Context) this.context = new Context();
            if (this.context?.state === 'suspended') this.context.resume().catch(() => {});
        } catch (_) {}
    }

    setMuted(muted) {
        this.muted = muted;
        try { localStorage.setItem('crazyexam-rush-muted', String(muted)); } catch (_) {}
        if (muted) this.stop();
        else this.unlock();
    }

    stop() {
        if (this.clip) this.clip.pause();
        this.clip = null;
        this.voices.forEach(voice => { try { voice.stop(); } catch (_) {} });
        this.voices.clear();
        this.onChange?.(false);
    }

    async playClip(url) {
        if (this.muted || !url) return false;
        this.stop();
        const clip = new Audio(url);
        clip.volume = 0.55;
        this.clip = clip;
        clip.addEventListener('ended', () => {
            if (this.clip === clip) { this.clip = null; this.onChange?.(false); }
        });
        try {
            await clip.play();
            if (this.clip !== clip) return false;
            this.onChange?.(true);
            return true;
        } catch (_) {
            if (this.clip === clip) { this.clip = null; this.onChange?.(false); }
            return false;
        }
    }

    tone(correct) {
        if (this.muted || !this.context || this.context.state !== 'running') return;
        this.stop();
        const now = this.context.currentTime;
        const frequencies = correct ? [523.25, 783.99] : [220, 146.83];
        frequencies.forEach((frequency, step) => {
            const oscillator = this.context.createOscillator();
            const gain = this.context.createGain();
            const start = now + step * 0.11;
            oscillator.type = correct ? 'sine' : 'triangle';
            oscillator.frequency.value = frequency;
            gain.gain.setValueAtTime(0, start);
            gain.gain.linearRampToValueAtTime(0.12, start + 0.015);
            gain.gain.exponentialRampToValueAtTime(0.001, start + 0.2);
            oscillator.connect(gain);
            gain.connect(this.context.destination);
            this.voices.add(oscillator);
            oscillator.start(start);
            oscillator.stop(start + 0.21);
            oscillator.onended = () => { this.voices.delete(oscillator); oscillator.disconnect(); gain.disconnect(); };
        });
    }
};

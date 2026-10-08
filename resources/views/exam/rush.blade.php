@extends('layouts.ap')

@section('content')
    @php
        $answered = $attempt->questions->whereNotNull('selected_answer')->count();
        $correct = $attempt->questions->where('is_correct', true)->count();
        $remainingQuestions = $attempt->questions->whereNull('selected_answer');
        $streak = $attempt->questions->whereNotNull('selected_answer')->reverse()->takeWhile(fn ($question) => $question->is_correct)->count();
    @endphp
    <div class="rush-shell">
        <header class="rush-toolbar">
            <div class="rush-heading">
                <h1>&#9889; Rush exam</h1>
                <p>{{ $attempt->examName() }}</p>
            </div>
            <div class="rush-controls">
                <button id="rushSoundToggle" type="button" class="btn btn-sm btn-outline-secondary" aria-pressed="false">Sound on</button>
                <div id="rushTimerBox" class="exam-timer" role="timer" aria-label="Time remaining"><span id="timer">--:--</span></div>
            </div>
        </header>
        <div class="rush-stats">
            <div><span id="rushAnswered">{{ $answered }}</span><small>Answered</small></div>
            <div><span id="rushCorrect">{{ $correct }}</span><small>Correct</small></div>
            <div><span id="rushStreak">{{ $streak }}</span><small>Streak</small></div>
        </div>
        <div id="rushNotice" class="alert alert-danger small mb-0" role="alert" hidden></div>
        <noscript><div class="alert alert-warning">Enable JavaScript to play Rush.</div></noscript>
        @foreach($remainingQuestions as $question)
            <section class="card rush-question" data-question-id="{{ $question->id }}" data-answer-url="{{ route('exam.answer', [$attempt, $question]) }}" hidden>
                <div class="card-body">
                    <p class="rush-question-number">Question {{ $question->position }}</p>
                    <h2 class="rush-question-title" tabindex="-1">{{ $question->question_text }}</h2>
                    <div class="rush-options">
                        @foreach(['option_a' => 'A', 'option_b' => 'B', 'option_c' => 'C', 'option_d' => 'D'] as $option => $letter)
                            <button type="button" class="rush-option" data-answer="{{ $option }}">
                                <span class="rush-option-letter">{{ $letter }}</span><span>{{ $question->{$option} }}</span>
                            </button>
                        @endforeach
                    </div>
                    <p class="rush-feedback" role="status" aria-live="polite"></p>
                    <button type="button" class="rush-next btn btn-sm btn-primary" hidden>Next question &rarr;</button>
                </div>
            </section>
        @endforeach
        <footer class="rush-footer">
            <form id="rushFinishForm" method="POST" action="{{ route('exam.submit', $attempt) }}">
                @csrf
                <button id="rushFinishButton" type="submit" class="btn btn-outline-secondary">Finish Rush</button>
            </form>
        </footer>
    </div>
@endsection

@push('scripts')
    <script id="rushConfig" type="application/json">{!! json_encode([
        'attemptId' => $attempt->id,
        'remainingSeconds' => max(0, $attempt->ends_at->getTimestamp() - microtime(true)),
        'wrongSounds' => $wrongSounds,
        'introSound' => $introSound,
        'reviewUrl' => route('exam.review', $attempt),
    ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
    <script src="{{ asset('js/rush-audio.js') }}" defer></script>
    <script src="{{ asset('js/exam-rush.js') }}" defer></script>
@endpush

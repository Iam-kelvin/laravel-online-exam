@extends('layouts.ap')

@section('content')
    @php
        $mode = in_array(old('mode', $selectedMode), ['standard', 'rush']) ? old('mode', $selectedMode) : 'standard';
        $selectedIds = collect((array) old('subject_ids', $selectedSubjectIds))->map(fn ($id) => (int) $id);
        $selectedSubjects = $subjects->whereIn('id', $selectedIds);
        $currentBank = old('bank_type', $selectedSubjects->first()?->bank_type ?? 'academic');
        $standardDuration = $selectedMode === 'rush' ? 300 : $selectedDuration;
        $durationUnit = old('duration_unit', $standardDuration % 60 !== 0 ? 'seconds' : 'minutes');
        $durationValue = old('duration_value', $durationUnit === 'minutes' ? $standardDuration / 60 : $standardDuration);
        $rushDuration = $mode === 'rush' ? max(15, min(180, (int) round(old('duration_seconds', $selectedDuration) / 15) * 15)) : 60;
        $subjectData = $subjects->map(fn ($subject) => [
            'id' => $subject->id, 'name' => $subject->name,
            'type' => $subject->bank_type, 'description' => $subject->description,
        ])->values();
    @endphp
    <div class="exam-setup exam-room" data-mode="{{ $mode }}">
        <a href="{{ route('home') }}" class="room-back">@include('partials.exam-icon', ['icon' => 'back']) Dashboard <span>/ New session</span></a>
        <header class="room-page-heading">
            <div>
                <p class="room-eyebrow">Practice room</p>
                <h1>What are we practising?</h1>
                <p>Pick your subjects. Make the challenge yours.</p>
            </div>
            <span class="room-brand-mark" aria-hidden="true">@include('partials.exam-icon', ['icon' => 'spark'])</span>
        </header>
        @if($comboSource)
            <div class="alert alert-info small" role="alert">Shared subjects and settings loaded. Your questions will be fresh.</div>
        @endif

        <form action="{{ route('exam.store') }}" method="POST" id="examStartForm" class="room-form">
            @csrf
            <div class="room-mode-row">
                <fieldset class="room-mode-switch">
                    <legend class="sr-only">Choose your exam mode</legend>
                    <label class="room-mode">
                        <input type="radio" name="mode" value="standard" {{ $mode === 'standard' ? 'checked' : '' }}>
                        @include('partials.exam-icon', ['icon' => 'book'])
                        <span class="room-mode-copy"><span>Practice</span><small>Review when done</small></span>
                    </label>
                    <label class="room-mode room-mode-rush">
                        <input type="radio" name="mode" value="rush" {{ $mode === 'rush' ? 'checked' : '' }}>
                        @include('partials.exam-icon', ['icon' => 'bolt'])
                        <span class="room-mode-copy"><span>Rush</span><small>Race the clock</small></span>
                    </label>
                </fieldset>
                <span id="modeCaption" class="room-mode-caption">{{ $mode === 'rush' ? "Race the clock. How many can you answer in {$rushDuration} seconds?" : 'A focused session, at your pace.' }}</span>
            </div>
            <div id="examStartNotice" class="alert alert-danger d-none small room-notice" role="alert"></div>
            <div class="room-workbench">
                <div class="room-builder">
                    <section class="room-section">
                        <div class="room-section-heading">
                            <span class="room-step" aria-hidden="true">01</span>
                            <h2>Pick your subjects</h2>
                        </div>
                        <div class="room-bank-tabs" role="group" aria-label="Subject category">
                            <button type="button" data-bank-type="academic" aria-pressed="{{ $currentBank === 'academic' ? 'true' : 'false' }}">Academic</button>
                            <button type="button" data-bank-type="challenge" aria-pressed="{{ $currentBank === 'challenge' ? 'true' : 'false' }}">Street, culture &amp; faith</button>
                        </div>
                        <input type="hidden" id="bankType" name="bank_type" value="{{ $currentBank }}">
                        <div id="subjectTiles" class="room-subject-tiles" role="group" aria-label="Subject shortcuts"></div>
                        <label class="sr-only" for="subjectPicker">Browse all subjects</label>
                        <select id="subjectPicker" class="room-input room-subject-picker" aria-describedby="subjectHint">
                            <option value="">Browse all subjects</option>
                            @foreach($subjects->where('bank_type', $currentBank) as $subject)
                                <option value="{{ $subject->id }}" {{ $mode === 'rush' && $selectedIds->contains($subject->id) ? 'selected' : '' }}>{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        <p id="subjectHint" class="room-help">{{ $mode === 'rush' ? 'One subject. All your focus.' : 'Choose one, or mix a few together.' }}</p>
                        <div id="selectedSubjects" class="selected-subjects room-picked" aria-label="Selected subjects" aria-live="polite" @if($mode === 'rush') hidden @endif>
                            @foreach($selectedSubjects as $subject)
                                <button type="button" class="subject-chip" data-remove-subject="{{ $subject->id }}" aria-label="Remove {{ $subject->name }}">{{ $subject->name }} <span aria-hidden="true">&times;</span></button>
                            @endforeach
                        </div>
                        <p id="subjectDescription" class="room-help" hidden></p>
                        @if($subjects->isEmpty())
                            <p class="room-help">No active subjects are available yet.</p>
                        @endif
                        @foreach($subjects as $subject)
                            <input type="checkbox" hidden name="subject_ids[]" value="{{ $subject->id }}" {{ $selectedIds->contains($subject->id) ? 'checked' : '' }}>
                        @endforeach
                        <noscript><p class="room-help">Enable JavaScript to choose subjects and duration.</p></noscript>
                    </section>

                    <section class="room-section room-pace-section">
                        <div class="room-section-heading">
                            <span class="room-step" aria-hidden="true">02</span>
                            <h2 id="challengeSettingsTitle">{{ $mode === 'rush' ? 'Set the clock' : 'Set the pace' }}</h2>
                        </div>
                        <div class="room-settings-fields">
                            <div id="questionCountGroup" @if($mode === 'rush') hidden @endif>
                                <label class="room-field-label" for="questionCount">Questions</label>
                                <div class="room-count-control">
                                    <button type="button" id="decreaseQuestionCount" aria-label="Decrease question count">&minus;</button>
                                    <input id="questionCount" name="question_count" type="number" min="1" max="10000" step="1"
                                        value="{{ old('question_count', $selectedQuestionCount) }}" {{ $mode === 'standard' ? 'required' : 'disabled' }}>
                                    <button type="button" id="increaseQuestionCount" aria-label="Increase question count">+</button>
                                </div>
                            </div>
                            <div id="standardDurationGroup" @if($mode === 'rush') hidden @endif>
                                <label class="room-field-label" for="durationValue">Duration</label>
                                <div class="room-duration-input">
                                    <input id="durationValue" name="duration_value" type="number" min="0.01" max="86400" step="any"
                                        value="{{ $durationValue }}" {{ $mode === 'standard' ? 'required' : 'disabled' }}>
                                    <select id="durationUnit" name="duration_unit" aria-label="Duration unit" @if($mode === 'rush') disabled @endif>
                                        <option value="seconds" {{ $durationUnit === 'seconds' ? 'selected' : '' }}>Seconds</option>
                                        <option value="minutes" {{ $durationUnit === 'minutes' ? 'selected' : '' }}>Minutes</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="room-pace-tools">
                        <div id="practiceQuickPicks" class="room-quick-picks" @if($mode === 'rush') hidden @endif>
                            <span>Quick picks</span>
                            @foreach([10, 20, 50, 100] as $number)
                                <button type="button" data-question-count="{{ $number }}" aria-pressed="false">{{ $number }}</button>
                            @endforeach
                        </div>
                        <div id="timeSuggestion" class="room-time-suggestion" @if($mode === 'rush') hidden @endif>
                            @include('partials.exam-icon', ['icon' => 'clock'])
                            <span>Suggested: <strong id="suggestedTime">5 minutes</strong></span>
                            <button id="useSuggestedTime" type="button">Apply</button>
                        </div>
                        </div>
                        <div id="rushDurationGroup" @if($mode !== 'rush') hidden @endif>
                            <label class="room-field-label sr-only" for="rushDuration">Rush duration</label>
                            <div class="room-rush-clock">
                                <div class="rush-duration-stepper room-rush-stepper">
                                    <button id="decreaseRushDuration" type="button" aria-label="Decrease Rush duration by 15 seconds">&minus;</button>
                                    <select id="rushDuration" name="duration_seconds" aria-label="Rush duration" @if($mode !== 'rush') disabled @endif>
                                        @foreach(range(15, 180, 15) as $seconds)
                                            <option value="{{ $seconds }}" {{ $rushDuration === $seconds ? 'selected' : '' }}>{{ $seconds }} seconds</option>
                                        @endforeach
                                    </select>
                                    <button id="increaseRushDuration" type="button" aria-label="Increase Rush duration by 15 seconds">+</button>
                                </div>
                                <span class="room-rush-limit">15-second steps &middot; 3-minute max</span>
                            </div>
                        </div>
                    </section>
                </div>

                <aside class="room-preview" aria-label="Your session">
                    <div class="room-preview-content">
                        <div class="room-preview-topline">
                            <span class="room-eyebrow">Your session</span>
                            <span class="room-session-status"><span aria-hidden="true"></span><span id="sessionStatusText" role="status">Choose a subject</span></span>
                        </div>
                        <div class="room-preview-symbol">
                            <span data-mode-icon="standard" @if($mode === 'rush') hidden @endif>@include('partials.exam-icon', ['icon' => 'book'])</span>
                            <span data-mode-icon="rush" @if($mode !== 'rush') hidden @endif>@include('partials.exam-icon', ['icon' => 'bolt'])</span>
                        </div>
                        <h2 id="sessionTitle">{{ $mode === 'rush' ? 'Race the clock.' : 'A fresh start.' }}</h2>
                        <p id="sessionSubjects" class="room-preview-subjects">Choose what you want to practise.</p>
                        <dl class="room-session-metrics">
                            <div><dt id="summaryQuestionLabel">Questions</dt><dd id="summaryQuestionValue">10</dd></div>
                            <div><dt>Duration</dt><dd id="summaryDurationValue">5<span>min</span></dd></div>
                        </dl>
                        <div class="room-session-rules">
                            <p>@include('partials.exam-icon', ['icon' => 'check'])<span id="sessionRuleOne">Fresh questions every session</span></p>
                            <p>@include('partials.exam-icon', ['icon' => 'check'])<span id="sessionRuleTwo">Review your answers when you finish</span></p>
                        </div>
                    </div>
                    <div class="room-preview-footer">
                        <div class="room-mobile-summary"><strong id="mobileSessionSubject">Pick a subject</strong><span id="mobileSessionDetails">10 questions · 5 min</span></div>
                        <button id="startExamButton" type="submit" class="room-start-button" @if($subjects->isEmpty()) disabled @endif>
                            <span id="startExamLabel">{{ $mode === 'rush' ? 'Start Rush' : 'Start practice' }}</span>
                            @include('partials.exam-icon', ['icon' => 'arrow'])
                        </button>
                        <p id="startExamHint" class="room-start-hint">Choose a subject to get started.</p>
                    </div>
                </aside>
            </div>
        </form>
        <p class="room-bottom-note">Your score is saved when you finish.</p>
    </div>
@endsection

@push('styles')
    <link href="{{ asset('css/exam-setup.css') }}" rel="stylesheet">
@endpush

@push('scripts')
    <script id="subjectData" type="application/json">{!! json_encode($subjectData, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
    <script src="{{ asset('js/exam-start.js') }}" defer></script>
@endpush

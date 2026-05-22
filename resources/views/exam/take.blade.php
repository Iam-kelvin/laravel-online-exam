@extends('layouts.ap')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Exam</h1>
            <p class="text-muted mb-0">
                {{ $attempt->subjects->pluck('name')->join(', ') }} &middot; {{ $attempt->question_count }} questions
            </p>
        </div>
        <div class="exam-timer mt-3 mt-md-0">
            <span id="timer">--:--</span>
        </div>
    </div>

    <form id="exam-form" action="{{ route('exam.submit', $attempt) }}" method="POST">
        @csrf

        @foreach ($attempt->questions as $question)
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h2 class="h5">Question {{ $question->position }}</h2>
                        @if($question->subject)
                            <span class="badge badge-light">{{ $question->subject->name }}</span>
                        @endif
                    </div>

                    <p class="mb-3">{{ $question->question_text }}</p>

                    @foreach (['option_a', 'option_b', 'option_c', 'option_d'] as $option)
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" id="{{ $option }}_{{ $question->id }}"
                                name="answers[{{ $question->id }}]" value="{{ $option }}">
                            <label class="form-check-label" for="{{ $option }}_{{ $question->id }}">
                                {{ $question->{$option} }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <button type="submit" class="btn btn-primary">Submit Exam</button>
    </form>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var endsAt = new Date(@json($endsAt)).getTime();
            var timer = document.getElementById('timer');
            var form = document.getElementById('exam-form');
            var submitted = false;

            function formatTime(seconds) {
                var minutes = Math.floor(seconds / 60);
                var remainingSeconds = seconds % 60;

                return String(minutes).padStart(2, '0') + ':' + String(remainingSeconds).padStart(2, '0');
            }

            function submitExam() {
                if (submitted) {
                    return;
                }

                submitted = true;
                form.submit();
            }

            function tick() {
                var remaining = Math.max(0, Math.floor((endsAt - Date.now()) / 1000));
                timer.textContent = formatTime(remaining);

                if (remaining <= 0) {
                    submitExam();
                }
            }

            tick();
            setInterval(tick, 1000);
        });
    </script>
@endpush

@extends('layouts.ap')

@section('content')
    @php
        $optionLabels = [
            'option_a' => 'A',
            'option_b' => 'B',
            'option_c' => 'C',
            'option_d' => 'D',
        ];

        $scorePercent = $attempt->question_count > 0
            ? round(($attempt->score / $attempt->question_count) * 100)
            : 0;
    @endphp

    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Exam Review</h1>
            <p class="text-muted mb-0">
                {{ $attempt->subjects->pluck('name')->join(', ') }} &middot;
                submitted {{ $attempt->submitted_at->format('M j, Y g:i A') }}
            </p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('exam.results') }}" class="btn btn-outline-secondary">Back to Results</a>
            <a href="{{ $attempt->shortLink() }}" class="btn btn-success" target="_blank" rel="noopener">Share Card</a>
            <a href="{{ route('exam.start') }}" class="btn btn-primary">Take Another Exam</a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body d-flex flex-wrap align-items-center justify-content-between">
            <div>
                <p class="text-muted mb-1">Score</p>
                <h2 class="h4 mb-0">{{ $attempt->score }} / {{ $attempt->question_count }}</h2>
            </div>
            <div class="display-4 text-primary">{{ $scorePercent }}%</div>
        </div>
    </div>

    @foreach ($attempt->questions as $question)
        @php
            $status = $question->is_correct ? 'Correct' : ($question->selected_answer ? 'Wrong' : 'Unanswered');
            $statusClass = $question->is_correct ? 'success' : ($question->selected_answer ? 'danger' : 'warning');
        @endphp

        <div class="card mb-3 border-{{ $statusClass }}">
            <div class="card-body">
                <div class="d-flex flex-wrap justify-content-between mb-2">
                    <div>
                        <h2 class="h5 mb-1">Question {{ $question->position }}</h2>
                        @if($question->subject)
                            <span class="badge badge-light">{{ $question->subject->name }}</span>
                        @endif
                    </div>
                    <span class="badge badge-{{ $statusClass }} align-self-start">
                        {{ $status }}
                    </span>
                </div>

                <p class="mb-3">{{ $question->question_text }}</p>

                <div class="list-group mb-3">
                    @foreach ($optionLabels as $optionKey => $optionLabel)
                        @php
                            $isSelected = $question->selected_answer === $optionKey;
                            $isCorrectOption = $question->correct_answer === $optionKey;
                            $optionClass = $isCorrectOption ? 'list-group-item-success' : '';

                            if ($isSelected && ! $isCorrectOption) {
                                $optionClass = 'list-group-item-danger';
                            }
                        @endphp

                        <div class="list-group-item {{ $optionClass }}">
                            <div class="d-flex flex-wrap align-items-center justify-content-between">
                                <span>
                                    <strong>{{ $optionLabel }}.</strong>
                                    {{ $question->{$optionKey} }}
                                </span>
                                <span>
                                    @if($isSelected)
                                        <span class="badge badge-secondary">Your answer</span>
                                    @endif
                                    @if($isCorrectOption)
                                        <span class="badge badge-success">Correct answer</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if(! $question->selected_answer)
                    <p class="text-muted mb-0">You did not answer this question.</p>
                @endif
            </div>
        </div>
    @endforeach
@endsection

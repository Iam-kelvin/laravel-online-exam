@extends('layouts.ap')

@section('content')
    <div class="container">
        <h2>Quiz</h2>
        <h4>Duration: <span id="timer">{{ $duration }}</span> seconds</h4>

        @push('scripts')
            <script>
                var duration = {{ $duration }};
                var timer = document.getElementById('timer');

                function countdown() {
                    if (duration > 0) {
                        timer.textContent = duration--;
                        setTimeout(countdown, 1000);
                    } else {
                        // Submit the form automatically when the timer reaches 0
                        document.getElementById('exam-form').submit();
                    }
                }

                document.addEventListener('DOMContentLoaded', countdown);
            </script>
        @endpush

        <form id="exam-form" action="{{ route('exam.submit') }}" method="POST">
            @csrf
            @php $questionNumber = 1; @endphp
            @foreach ($questions as $question)
                <div class="mb-3">
                    <h5>Question {{ $questionNumber }}:</h5>
                    <p>{{ $question->question }}</p>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]"
                            value="option_a">
                        <label class="form-check-label" for="option_a_{{ $question->id }}">{{ $question->option_a }}</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]"
                            value="option_b">
                        <label class="form-check-label" for="option_b_{{ $question->id }}">{{ $question->option_b }}</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]"
                            value="option_c">
                        <label class="form-check-label" for="option_c_{{ $question->id }}">{{ $question->option_c }}</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]"
                            value="option_d">
                        <label class="form-check-label" for="option_d_{{ $question->id }}">{{ $question->option_d }}</label>
                    </div>
                </div>
            @php $questionNumber++; @endphp
            @endforeach
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
@endsection

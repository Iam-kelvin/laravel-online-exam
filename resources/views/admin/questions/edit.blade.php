@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Question</h2>
        <form action="{{ route('questions.update', $question->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="question">Question:</label>
                <input type="text" class="form-control" id="question" name="question" value="{{ $question->question }}"
                    required>
            </div>
            <div class="form-group">
                <label for="option_a">Option A:</label>
                <input type="text" class="form-control" id="option_a" name="option_a" value="{{ $question->option_a }}"
                    required>
            </div>
            <div class="form-group">
                <label for="option_b">Option B:</label>
                <input type="text" class="form-control" id="option_b" name="option_b" value="{{ $question->option_b }}"
                    required>
            </div>
            <div class="form-group">
                <label for="option_c">Option C:</label>
                <input type="text" class="form-control" id="option_c" name="option_c" value="{{ $question->option_c }}"
                    required>
            </div>
            <div class="form-group">
                <label for="option_d">Option D:</label>
                <input type="text" class="form-control" id="option_d" name="option_d" value="{{ $question->option_d }}"
                    required>
            </div>
            <div class="form-group">
                <label for="answer">Answer:</label>
                <input type="text" class="form-control" id="answer" name="answer" value="{{ $question->answer }}"
                    required>
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Add New Question</h2>
        <form action="{{ route('questions.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="question">Question:</label>
                <input type="text" class="form-control" id="question" name="question" required>
            </div>
            <div class="form-group">
                <label for="option_a">Option A:</label>
                <input type="text" class="form-control" id="option_a" name="option_a" required>
            </div>
            <div class="form-group">
                <label for="option_b">Option B:</label>
                <input type="text" class="form-control" id="option_b" name="option_b" required>
            </div>
            <div class="form-group">
                <label for="option_c">Option C:</label>
                <input type="text" class="form-control" id="option_c" name="option_c" required>
            </div>
            <div class="form-group">
                <label for="option_d">Option D:</label>
                <input type="text" class="form-control" id="option_d" name="option_d" required>
            </div>
            <div class="form-group">
                <label for="answer">Answer:</label>
                <input type="text" class="form-control" id="answer" name="answer" required>
            </div>
            <div class="form-group">
                <label for="duration">Duration (in seconds):</label>
                <input type="number" class="form-control" id="duration" name="duration" required>
            </div>            
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
@endsection

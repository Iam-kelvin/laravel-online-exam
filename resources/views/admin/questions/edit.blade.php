@extends('layouts.ap')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Edit Question</h1>
            <p class="text-muted mb-0">Update this banked question.</p>
        </div>
        <a href="{{ route('questions.index') }}" class="btn btn-outline-secondary">Question Bank</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('questions.update', $question) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="subject_id">Subject</label>
                    <select class="form-control" id="subject_id" name="subject_id" required>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}"
                                {{ (int) old('subject_id', $question->subject_id) === $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="question">Question</label>
                    <textarea class="form-control" id="question" name="question" rows="3" required>{{ old('question', $question->question) }}</textarea>
                </div>

                <div class="row">
                    @foreach (['option_a' => 'Option A', 'option_b' => 'Option B', 'option_c' => 'Option C', 'option_d' => 'Option D'] as $field => $label)
                        <div class="form-group col-md-6">
                            <label for="{{ $field }}">{{ $label }}</label>
                            <input type="text" class="form-control" id="{{ $field }}" name="{{ $field }}"
                                value="{{ old($field, $question->{$field}) }}" required>
                        </div>
                    @endforeach
                </div>

                <div class="form-group">
                    <label for="answer">Correct Answer</label>
                    <select class="form-control" id="answer" name="answer" required>
                        @foreach (['option_a' => 'Option A', 'option_b' => 'Option B', 'option_c' => 'Option C', 'option_d' => 'Option D'] as $value => $label)
                            <option value="{{ $value }}" {{ old('answer', $question->answer) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Update Question</button>
            </form>
        </div>
    </div>
@endsection

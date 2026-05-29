@extends('layouts.ap')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Add Question</h1>
            <p class="text-muted mb-0">Questions are saved into subject banks.</p>
        </div>
        <a href="{{ route('questions.index') }}" class="btn btn-outline-secondary">Question Bank</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('questions.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="subject_id">Subject</label>
                    <select class="form-control" id="subject_id" name="subject_id" required>
                        <option value="">Choose subject</option>
                        @foreach ($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ (int) old('subject_id', request('subject_id')) === $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="question">Question</label>
                    <textarea class="form-control" id="question" name="question" rows="3" required>{{ old('question') }}</textarea>
                </div>

                <div class="row">
                    @foreach (['option_a' => 'Option A', 'option_b' => 'Option B', 'option_c' => 'Option C', 'option_d' => 'Option D'] as $field => $label)
                        <div class="form-group col-md-6">
                            <label for="{{ $field }}">{{ $label }}</label>
                            <input type="text" class="form-control" id="{{ $field }}" name="{{ $field }}"
                                value="{{ old($field) }}" required>
                        </div>
                    @endforeach
                </div>

                <div class="form-group">
                    <label for="answer">Correct Answer</label>
                    <select class="form-control" id="answer" name="answer" required>
                        <option value="">Choose answer</option>
                        @foreach (['option_a' => 'Option A', 'option_b' => 'Option B', 'option_c' => 'Option C', 'option_d' => 'Option D'] as $value => $label)
                            <option value="{{ $value }}" {{ old('answer') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Save Question</button>
            </form>
        </div>
    </div>
@endsection

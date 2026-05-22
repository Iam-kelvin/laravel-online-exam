@extends('layouts.ap')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Edit Duration</h1>
            <p class="text-muted mb-0">{{ $examPreset->label }}</p>
        </div>
        <a href="{{ route('exam-presets.index') }}" class="btn btn-outline-secondary">Durations</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('exam-presets.update', $examPreset) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="active" value="0">

                <div class="form-group">
                    <label for="label">Label</label>
                    <input type="text" class="form-control" id="label" name="label"
                        value="{{ old('label', $examPreset->label) }}" required>
                </div>

                <div class="form-group">
                    <label for="question_count">Questions</label>
                    <input type="number" class="form-control" id="question_count" name="question_count"
                        value="{{ old('question_count', $examPreset->question_count) }}" min="1" required>
                </div>

                <div class="form-group">
                    <label for="duration_minutes">Minutes</label>
                    <input type="number" class="form-control" id="duration_minutes" name="duration_minutes"
                        value="{{ old('duration_minutes', intdiv($examPreset->duration_seconds, 60)) }}" min="1" required>
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="active" name="active" value="1"
                        {{ old('active', $examPreset->active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="active">Active</label>
                </div>

                <button type="submit" class="btn btn-primary">Update Preset</button>
            </form>
        </div>
    </div>
@endsection

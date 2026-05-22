@extends('layouts.ap')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Start Exam</h1>
            <p class="text-muted mb-0">Choose one or more subject banks.</p>
        </div>
    </div>

    <form action="{{ route('exam.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-lg-7 mb-4">
                <div class="selection-card">
                    <h2 class="h5 mb-3">Subjects</h2>

                    @forelse ($subjects as $subject)
                        <label class="d-flex align-items-center justify-content-between border rounded p-3 mb-2">
                            <span>
                                <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}"
                                    {{ in_array($subject->id, old('subject_ids', [])) ? 'checked' : '' }}>
                                <span class="ml-2 font-weight-bold">{{ $subject->name }}</span>
                            </span>
                            <span class="badge badge-secondary">{{ $subject->questions_count }}</span>
                        </label>
                    @empty
                        <div class="alert alert-warning mb-0">No active subjects are available.</div>
                    @endforelse
                </div>
            </div>

            <div class="col-lg-5 mb-4">
                <div class="selection-card">
                    <h2 class="h5 mb-3">Question Count & Time</h2>

                    @forelse ($presets as $preset)
                        <label class="d-flex align-items-center justify-content-between border rounded p-3 mb-2">
                            <span>
                                <input type="radio" name="exam_preset_id" value="{{ $preset->id }}"
                                    {{ (int) old('exam_preset_id') === $preset->id ? 'checked' : '' }}>
                                <span class="ml-2 font-weight-bold">{{ $preset->label }}</span>
                            </span>
                            <span class="text-muted">{{ intdiv($preset->duration_seconds, 60) }} mins</span>
                        </label>
                    @empty
                        <div class="alert alert-warning mb-0">No active duration presets are available.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary" @if($subjects->isEmpty() || $presets->isEmpty()) disabled @endif>
            Start Exam
        </button>
    </form>
@endsection

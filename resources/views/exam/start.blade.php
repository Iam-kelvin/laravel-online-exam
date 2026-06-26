@extends('layouts.ap')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Start Exam</h1>
            <p class="text-muted mb-0">Choose one or more exam banks.</p>
        </div>
    </div>

    @if($comboSource)
        <div class="alert alert-info" role="alert">
            Shared combo loaded. You will get fresh questions from the same exam banks.
        </div>
    @endif

    <form action="{{ route('exam.store') }}" method="POST" id="examStartForm" novalidate>
        @csrf

        <div id="examStartNotice" class="alert alert-danger d-none" role="alert"></div>

        <div class="row">
            <div class="col-lg-7 mb-4">
                <div class="selection-card @error('subject_ids') border-danger @enderror">
                    <h2 class="h5 mb-3">Exam Banks</h2>
                    @error('subject_ids')
                        <div class="text-danger mb-3">{{ $message }}</div>
                    @enderror

                    @if($subjects->isEmpty())
                        <div class="alert alert-warning mb-0">No active exam banks are available.</div>
                    @else
                        @foreach (\App\Models\Subject::TYPES as $type => $label)
                            @php
                                $group = $subjectGroups->get($type, collect());
                            @endphp

                            @if($group->isNotEmpty())
                                <div class="bank-section mb-3">
                                    <div class="bank-section-title">{{ $label }}</div>

                                    @foreach ($group as $subject)
                                        <label class="d-flex align-items-center justify-content-between border rounded p-3 mb-2">
                                            <span>
                                                <input type="checkbox" name="subject_ids[]" value="{{ $subject->id }}"
                                                    {{ in_array($subject->id, old('subject_ids', $selectedSubjectIds ?? [])) ? 'checked' : '' }}>
                                                <span class="ml-2 font-weight-bold">{{ $subject->name }}</span>
                                            @if($subject->description)
                                                <span class="d-block small text-muted ml-4">{{ $subject->description }}</span>
                                            @endif
                                        </span>
                                        @can('manage-questions')
                                            <span class="badge badge-secondary">{{ $subject->questions_count }}</span>
                                        @endcan
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>

            <div class="col-lg-5 mb-4">
                <div class="selection-card @error('exam_preset_id') border-danger @enderror">
                    <h2 class="h5 mb-3">Question Count & Time</h2>
                    @error('exam_preset_id')
                        <div class="text-danger mb-3">{{ $message }}</div>
                    @enderror

                    @forelse ($presets as $preset)
                        <label class="d-flex align-items-center justify-content-between border rounded p-3 mb-2">
                            <span>
                                <input type="radio" name="exam_preset_id" value="{{ $preset->id }}"
                                    {{ (int) old('exam_preset_id', $selectedPresetId ?? 0) === $preset->id ? 'checked' : '' }}>
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var form = document.getElementById('examStartForm');
            var notice = document.getElementById('examStartNotice');

            if (! form || ! notice) {
                return;
            }

            form.addEventListener('submit', function (event) {
                var hasSubject = form.querySelector('input[name="subject_ids[]"]:checked');
                var hasPreset = form.querySelector('input[name="exam_preset_id"]:checked');
                var messages = [];

                if (! hasSubject) {
                    messages.push('Choose at least one subject.');
                }

                if (! hasPreset) {
                    messages.push('Choose a question count and time.');
                }

                if (messages.length === 0) {
                    return;
                }

                event.preventDefault();
                notice.textContent = messages.join(' ');
                notice.classList.remove('d-none');
                notice.scrollIntoView({ behavior: 'smooth', block: 'center' });
            });
        });
    </script>
@endpush

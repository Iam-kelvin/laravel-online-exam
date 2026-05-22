@extends('layouts.ap')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Durations</h1>
            <p class="text-muted mb-0">Question count and time presets.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">Add Preset</div>
                <div class="card-body">
                    <form action="{{ route('exam-presets.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="active" value="0">

                        <div class="form-group">
                            <label for="label">Label</label>
                            <input type="text" class="form-control" id="label" name="label" value="{{ old('label') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="question_count">Questions</label>
                            <input type="number" class="form-control" id="question_count" name="question_count"
                                value="{{ old('question_count') }}" min="1" required>
                        </div>

                        <div class="form-group">
                            <label for="duration_minutes">Minutes</label>
                            <input type="number" class="form-control" id="duration_minutes" name="duration_minutes"
                                value="{{ old('duration_minutes') }}" min="1" required>
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="active" name="active" value="1" checked>
                            <label class="form-check-label" for="active">Active</label>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Preset</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Label</th>
                                <th>Questions</th>
                                <th>Minutes</th>
                                <th>Status</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($presets as $preset)
                                <tr>
                                    <td>{{ $preset->label }}</td>
                                    <td>{{ $preset->question_count }}</td>
                                    <td>{{ intdiv($preset->duration_seconds, 60) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $preset->active ? 'success' : 'secondary' }}">
                                            {{ $preset->active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('exam-presets.edit', $preset) }}" class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('exam-presets.destroy', $preset) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this preset?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No presets yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@extends('layouts.ap')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Edit Exam Bank</h1>
            <p class="text-muted mb-0">{{ $subject->name }}</p>
        </div>
        <a href="{{ route('subjects.index') }}" class="btn btn-outline-secondary">Exam Banks</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('subjects.update', $subject) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="active" value="0">

                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name"
                        value="{{ old('name', $subject->name) }}" required>
                </div>

                <div class="form-group">
                    <label for="bank_type">Type</label>
                    <select class="form-control" id="bank_type" name="bank_type" required>
                        @foreach(\App\Models\Subject::TYPES as $value => $label)
                            <option value="{{ $value }}" {{ old('bank_type', $subject->bank_type) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Short Description</label>
                    <input type="text" class="form-control" id="description" name="description"
                        value="{{ old('description', $subject->description) }}" maxlength="255">
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="active" name="active" value="1"
                        {{ old('active', $subject->active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="active">Active</label>
                </div>

                <button type="submit" class="btn btn-primary">Update Exam Bank</button>
            </form>
        </div>
    </div>
@endsection

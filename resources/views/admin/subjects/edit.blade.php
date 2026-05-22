@extends('layouts.ap')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Edit Subject</h1>
            <p class="text-muted mb-0">{{ $subject->name }}</p>
        </div>
        <a href="{{ route('subjects.index') }}" class="btn btn-outline-secondary">Subjects</a>
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

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="active" name="active" value="1"
                        {{ old('active', $subject->active) ? 'checked' : '' }}>
                    <label class="form-check-label" for="active">Active</label>
                </div>

                <button type="submit" class="btn btn-primary">Update Subject</button>
            </form>
        </div>
    </div>
@endsection

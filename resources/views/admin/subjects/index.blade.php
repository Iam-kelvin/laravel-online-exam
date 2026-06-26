@extends('layouts.ap')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Exam Banks</h1>
            <p class="text-muted mb-0">Academic exams and challenge quizzes used when building attempts.</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">Add Exam Bank</div>
                <div class="card-body">
                    <form action="{{ route('subjects.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="active" value="0">

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        </div>

                        <div class="form-group">
                            <label for="bank_type">Type</label>
                            <select class="form-control" id="bank_type" name="bank_type" required>
                                @foreach(\App\Models\Subject::TYPES as $value => $label)
                                    <option value="{{ $value }}" {{ old('bank_type', \App\Models\Subject::TYPE_ACADEMIC) === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="description">Short Description</label>
                            <input type="text" class="form-control" id="description" name="description"
                                value="{{ old('description') }}" maxlength="255">
                        </div>

                        <div class="form-check mb-3">
                            <input type="checkbox" class="form-check-input" id="active" name="active" value="1" checked>
                            <label class="form-check-label" for="active">Active</label>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Subject</button>
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
                                <th>Name</th>
                                <th>Type</th>
                                <th>Questions</th>
                                <th>Status</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($subjects as $subject)
                                <tr>
                                    <td>{{ $subject->name }}</td>
                                    <td>{{ $subject->type_label }}</td>
                                    <td>{{ $subject->questions_count }}</td>
                                    <td>
                                        <span class="badge badge-{{ $subject->active ? 'success' : 'secondary' }}">
                                            {{ $subject->active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('subjects.edit', $subject) }}" class="btn btn-sm btn-primary">Edit</a>
                                        <form action="{{ route('subjects.destroy', $subject) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                onclick="return confirm('Are you sure you want to delete this subject?')">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No exam banks yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

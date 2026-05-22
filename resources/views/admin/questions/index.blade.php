@extends('layouts.ap')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Question Bank</h1>
            <p class="text-muted mb-0">Questions grouped by subject.</p>
        </div>
        <a class="btn btn-primary mt-3 mt-md-0" href="{{ route('questions.create') }}">Add Question</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Subject</th>
                        <th>Question</th>
                        <th>Answer</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($questions as $question)
                        <tr>
                            <td>{{ $question->id }}</td>
                            <td>{{ optional($question->subject)->name ?? 'Unassigned' }}</td>
                            <td>{{ $question->question }}</td>
                            <td>{{ strtoupper(str_replace('option_', '', $question->answer)) }}</td>
                            <td class="text-right">
                                <a class="btn btn-sm btn-primary" href="{{ route('questions.edit', $question) }}">Edit</a>
                                <form action="{{ route('questions.destroy', $question) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this question?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No questions yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

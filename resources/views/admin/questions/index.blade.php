@extends('layouts.ap')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Question Bank</h1>
            <p class="text-muted mb-0">{{ $totalQuestions }} questions across {{ $subjects->count() }} subject banks.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a class="btn btn-outline-secondary" href="{{ url('/importExportView') }}">Import Questions</a>
            <a class="btn btn-primary" href="{{ route('questions.create') }}">Add Question</a>
        </div>
    </div>

    @if($subjects->isEmpty() && $unassignedQuestions->isEmpty())
        <div class="content-panel">
            <div class="empty-state">
                No subject banks yet. Add a subject or import questions with a subject column.
            </div>
        </div>
    @endif

    @foreach ($subjects as $subject)
        <section class="content-panel mb-4" id="subject-{{ $subject->id }}">
            <div class="panel-header">
                <div>
                    <h2 class="h5 mb-1">{{ $subject->name }}</h2>
                    <p class="text-muted mb-0">
                        {{ $subject->questions_count }} {{ \Illuminate\Support\Str::plural('question', $subject->questions_count) }}
                        &middot; {{ $subject->active ? 'Active' : 'Inactive' }}
                    </p>
                </div>
                <a class="btn btn-sm btn-outline-primary" href="{{ route('questions.create', ['subject_id' => $subject->id]) }}">
                    Add Question
                </a>
            </div>

            @if($subject->questions->isEmpty())
                <div class="empty-state">This subject bank is ready, but no questions have been added yet.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Question</th>
                                <th>Answer</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($subject->questions as $question)
                                <tr>
                                    <td>{{ $question->id }}</td>
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
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endforeach

    @if($unassignedQuestions->isNotEmpty())
        <section class="content-panel mb-4">
            <div class="panel-header">
                <div>
                    <h2 class="h5 mb-1">Unassigned</h2>
                    <p class="text-muted mb-0">Questions that need a subject before they can be used cleanly.</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Question</th>
                            <th>Answer</th>
                            <th class="text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($unassignedQuestions as $question)
                            <tr>
                                <td>{{ $question->id }}</td>
                                <td>{{ $question->question }}</td>
                                <td>{{ strtoupper(str_replace('option_', '', $question->answer)) }}</td>
                                <td class="text-right">
                                    <a class="btn btn-sm btn-primary" href="{{ route('questions.edit', $question) }}">Assign</a>
                                    <form action="{{ route('questions.destroy', $question) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete this question?')">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
@endsection

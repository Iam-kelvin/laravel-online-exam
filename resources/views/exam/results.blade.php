@extends('layouts.ap')

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-1">Exam Results</h1>
            <p class="text-muted mb-0">Your submitted attempts.</p>
        </div>
        <a href="{{ route('exam.start') }}" class="btn btn-primary mt-3 mt-md-0">Take Exam</a>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Attempt</th>
                        <th>Subjects</th>
                        <th>Score</th>
                        <th>Questions</th>
                        <th>Duration</th>
                        <th>Submitted</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($examAttempts as $attempt)
                        <tr>
                            <td>#{{ $attempt->id }}</td>
                            <td>{{ $attempt->subjects->pluck('name')->join(', ') }}</td>
                            <td>
                                @if($attempt->submitted_at)
                                    {{ $attempt->score }} / {{ $attempt->question_count }}
                                @else
                                    Pending
                                @endif
                            </td>
                            <td>{{ $attempt->question_count }}</td>
                            <td>{{ intdiv($attempt->duration_seconds, 60) }} mins</td>
                            <td>{{ optional($attempt->submitted_at)->format('M j, Y g:i A') ?? 'Not submitted' }}</td>
                            <td class="text-right">
                                @if($attempt->submitted_at)
                                    <a href="{{ route('exam.review', $attempt) }}" class="btn btn-sm btn-outline-primary">Review</a>
                                    <a href="{{ $attempt->shortLink() }}" class="btn btn-sm btn-success" target="_blank" rel="noopener">Share Card</a>
                                @else
                                    <a href="{{ route('exam.take', $attempt) }}" class="btn btn-sm btn-outline-secondary">Continue</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No exam attempts yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@extends('layouts.ap')

@section('content')
    @if (! Auth::user()->hasVerifiedEmail())
        <div class="verify-banner mb-4">
            <div>
                <strong>Confirm your email address</strong>
                <p class="mb-0">We sent a verification link to {{ Auth::user()->email }}. Open the link to finish setting up your account.</p>
            </div>
            <form method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" class="btn btn-success">Resend Email</button>
            </form>
        </div>
    @endif

    <div class="learner-hero mb-4">
        <div class="learner-hero-copy">
            <p class="text-uppercase font-weight-bold mb-2">Dashboard</p>
            <h1>Ready for your next practice run?</h1>
            <p>Choose one subject or mix several together. CrazyExam will balance the questions, keep the timer steady, and save your result when you finish.</p>
            <div class="dashboard-actions">
                @if($stats['in_progress_attempt_id'])
                    <a href="{{ route('exam.take', $stats['in_progress_attempt_id']) }}" class="btn btn-light">Resume Exam</a>
                @endif
                <a href="{{ Auth::user()->hasVerifiedEmail() ? route('exam.start') : route('verification.notice') }}" class="btn btn-success">Start New Exam</a>
                <a href="{{ route('exam.results') }}" class="btn btn-outline-light">View Results</a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="learner-stat">
                <span>Completed Exams</span>
                <strong>{{ $stats['completed_attempts'] }}</strong>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="learner-stat accent-green">
                <span>Average Score</span>
                <strong>{{ $stats['average_score'] !== null ? $stats['average_score'] . '%' : '-' }}</strong>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="learner-stat accent-gold">
                <span>Best Score</span>
                <strong>{{ $stats['best_score'] ?? '-' }}</strong>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-7 mb-4">
            <div class="content-panel h-100">
                <div class="panel-header">
                    <div>
                        <h2 class="h5 mb-1">Pick Your Practice</h2>
                        <p class="text-muted mb-0">These subject banks are available for your next exam.</p>
                    </div>
                    <a href="{{ Auth::user()->hasVerifiedEmail() ? route('exam.start') : route('verification.notice') }}" class="btn btn-sm btn-primary">Choose Subjects</a>
                </div>

                <div class="subject-grid">
                    @forelse ($subjects as $subject)
                        <div class="subject-tile learner-subject">
                            <span>{{ $subject->name }}</span>
                            <strong>{{ $subject->questions_count }}</strong>
                        </div>
                    @empty
                        <div class="empty-state">No subjects are available yet.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-5 mb-4">
            <div class="content-panel h-100">
                <div class="panel-header">
                    <div>
                        <h2 class="h5 mb-1">Available Exam Lengths</h2>
                        <p class="text-muted mb-0">Pick the format that matches your time.</p>
                    </div>
                </div>

                <div class="preset-list">
                    @forelse ($presets as $preset)
                        <div class="preset-row">
                            <span>{{ $preset->label }}</span>
                            <strong>{{ intdiv($preset->duration_seconds, 60) }} mins</strong>
                        </div>
                    @empty
                        <div class="empty-state">No exam lengths are available yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="content-panel">
        <div class="panel-header">
            <div>
                <h2 class="h5 mb-1">Recent Activity</h2>
                <p class="text-muted mb-0">Your latest exam attempts.</p>
            </div>
            <a href="{{ route('exam.results') }}" class="btn btn-sm btn-outline-secondary">All Results</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Subjects</th>
                        <th>Score</th>
                        <th>Questions</th>
                        <th>Status</th>
                        <th>Started</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentAttempts as $attempt)
                        <tr>
                            <td>{{ $attempt->subjects->pluck('name')->join(', ') }}</td>
                            <td>{{ $attempt->submitted_at ? $attempt->score . ' / ' . $attempt->question_count : '-' }}</td>
                            <td>{{ $attempt->question_count }}</td>
                            <td>
                                <span class="badge badge-{{ $attempt->submitted_at ? 'success' : 'warning' }}">
                                    {{ $attempt->submitted_at ? 'Submitted' : 'In progress' }}
                                </span>
                            </td>
                            <td>{{ $attempt->started_at->format('M j, g:i A') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Start your first exam when you are ready.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@extends('layouts.ap')

@section('content')
    <div class="dashboard-hero mb-4">
        <div>
            <p class="text-uppercase text-muted font-weight-bold mb-2">Admin</p>
            <h1 class="h2 mb-2">Platform Dashboard</h1>
            <p class="mb-0 text-muted">Track question banks, users, presets, and exam activity from one place.</p>
        </div>
        <div class="dashboard-actions">
            <a href="{{ route('questions.create') }}" class="btn btn-primary">Add Question</a>
            <a href="{{ route('subjects.index') }}" class="btn btn-outline-primary">Subjects</a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-xl-3 mb-4">
            <div class="metric-card">
                <span class="metric-label">Users</span>
                <strong>{{ $stats['users'] }}</strong>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-4">
            <div class="metric-card accent-green">
                <span class="metric-label">Questions</span>
                <strong>{{ $stats['questions'] }}</strong>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-4">
            <div class="metric-card accent-gold">
                <span class="metric-label">Attempts</span>
                <strong>{{ $stats['attempts'] }}</strong>
            </div>
        </div>
        <div class="col-sm-6 col-xl-3 mb-4">
            <div class="metric-card accent-coral">
                <span class="metric-label">Average Score</span>
                <strong>{{ $stats['average_score'] !== null ? $stats['average_score'] . '%' : '-' }}</strong>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="content-panel h-100">
                <div class="panel-header">
                    <div>
                        <h2 class="h5 mb-1">Coverage</h2>
                        <p class="text-muted mb-0">Current platform setup.</p>
                    </div>
                </div>
                <div class="preset-list">
                    <div class="preset-row"><span>Total Subjects</span><strong>{{ $stats['subjects'] }}</strong></div>
                    <div class="preset-row"><span>Active Subjects</span><strong>{{ $stats['active_subjects'] }}</strong></div>
                    <div class="preset-row"><span>Active Durations</span><strong>{{ $stats['active_presets'] }}</strong></div>
                    <div class="preset-row"><span>Completed Attempts</span><strong>{{ $stats['completed_attempts'] }}</strong></div>
                </div>
            </div>
        </div>

        <div class="col-lg-8 mb-4">
            <div class="content-panel h-100">
                <div class="panel-header">
                    <div>
                        <h2 class="h5 mb-1">Subject Banks</h2>
                        <p class="text-muted mb-0">Largest banks by question count.</p>
                    </div>
                    <a href="{{ route('subjects.index') }}" class="btn btn-sm btn-outline-secondary">Manage</a>
                </div>
                <div class="subject-grid">
                    @forelse ($subjects as $subject)
                        <div class="subject-tile">
                            <span>{{ $subject->name }}</span>
                            <strong>{{ $subject->questions_count }}</strong>
                        </div>
                    @empty
                        <div class="empty-state">No subjects yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="content-panel mb-4">
        <div class="panel-header">
            <div>
                <h2 class="h5 mb-1">Learner Insights</h2>
                <p class="text-muted mb-0">Location and academic profile spread across registered users.</p>
            </div>
        </div>

        <div class="row">
            @foreach ([
                'Countries' => $countryInsights,
                'Cities / Towns' => $cityInsights,
                'School Levels' => $levelInsights,
                'Classes / Years' => $classInsights,
            ] as $title => $items)
                <div class="col-md-6 col-xl-3 mb-3">
                    <h3 class="h6 text-muted mb-3">{{ $title }}</h3>
                    <div class="preset-list">
                        @forelse ($items as $item)
                            <div class="preset-row">
                                <span>{{ $item->label }}</span>
                                <strong>{{ $item->total }}</strong>
                            </div>
                        @empty
                            <div class="empty-state">No data yet.</div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="content-panel">
        <div class="panel-header">
            <div>
                <h2 class="h5 mb-1">Recent Attempts</h2>
                <p class="text-muted mb-0">Latest activity across all users.</p>
            </div>
            <a href="{{ route('exam-presets.index') }}" class="btn btn-sm btn-outline-secondary">Durations</a>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>User</th>
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
                            <td>{{ optional($attempt->user)->name ?? 'Deleted User' }}</td>
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
                            <td colspan="6" class="text-center text-muted py-4">No attempts yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

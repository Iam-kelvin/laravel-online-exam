@extends('layouts.ap')

@section('content')
    <div class="learner-hero mb-4">
        <div class="learner-hero-copy">
            <p class="text-uppercase font-weight-bold mb-2">Dashboard</p>
            <h1>Ready for your next practice run?</h1>
            <p>Choose one subject or mix several together. CrazyExam will balance the questions, keep the timer steady, and save your result when you finish.</p>
            <div class="dashboard-actions">
                @if($stats['in_progress_attempt_id'])
                    <a href="{{ route('exam.take', $stats['in_progress_attempt_id']) }}" class="btn btn-light">Resume Exam</a>
                @endif
                <a href="{{ route('exam.start') }}" class="btn btn-success">Start New Exam</a>
                <a href="{{ route('exam.results') }}" class="btn btn-outline-light">View Results</a>
            </div>
        </div>
    </div>

    <div class="content-panel fact-panel fact-panel-prominent mb-4">
        <div class="panel-header">
            <div>
                <h2 class="h5 mb-1">Quick Spark</h2>
                <p class="text-muted mb-0">Facts and quotes while you warm up.</p>
            </div>
        </div>

        @if($facts->isNotEmpty())
            <div id="factRotator" class="fact-rotator">
                <span class="fact-kind">{{ ucfirst($facts->first()->kind) }}</span>
                <div>
                    <h3 class="h5 mb-1">{{ $facts->first()->title }}</h3>
                    <p class="mb-0">{{ $facts->first()->body }}</p>
                </div>
            </div>
        @else
            <div class="empty-state">Fresh facts will show here soon.</div>
        @endif
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
                        <h2 class="h5 mb-1">Pick Your Lane</h2>
                        <p class="text-muted mb-0">Academic exams stay separate from fun challenge quizzes.</p>
                    </div>
                    <a href="{{ route('exam.start') }}" class="btn btn-sm btn-primary">Choose Banks</a>
                </div>

                @foreach(\App\Models\Subject::TYPES as $type => $label)
                    @php
                        $banks = $bankGroups->get($type, collect())->take(6);
                    @endphp

                    @if($banks->isNotEmpty())
                        <div class="bank-preview mb-3">
                            <div class="bank-section-title">{{ $label }}</div>
                            <div class="subject-grid">
                                @foreach ($banks as $subject)
                                    <div class="subject-tile learner-subject">
                                        <span>{{ $subject->name }}</span>
                                        @can('manage-questions')
                                            <strong>{{ $subject->questions_count }}</strong>
                                        @endcan
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach

                @if($bankGroups->isEmpty())
                    <div class="empty-state">No exam banks are available yet.</div>
                @endif
            </div>
        </div>

        <div class="col-lg-5 mb-4">
            <div class="content-panel mb-4">
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

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="content-panel h-100">
                <div class="panel-header">
                    <div>
                        <h2 class="h5 mb-1">This Week's Top Scorers</h2>
                        <p class="text-muted mb-0">Fresh competition for the current week.</p>
                    </div>
                </div>

                @include('partials.leaderboard', ['attempts' => $weeklyLeaders])
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="content-panel h-100">
                <div class="panel-header">
                    <div>
                        <h2 class="h5 mb-1">CrazyExam Hall of Fame</h2>
                        <p class="text-muted mb-0">The scores everyone is chasing.</p>
                    </div>
                </div>

                @include('partials.leaderboard', ['attempts' => $allTimeLeaders])
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var rotator = document.getElementById('factRotator');
            var facts = @json($factItems);

            if (! rotator || facts.length < 2) {
                return;
            }

            var index = 0;

            window.setInterval(function () {
                index = (index + 1) % facts.length;
                rotator.querySelector('.fact-kind').textContent = facts[index].kind;
                rotator.querySelector('h3').textContent = facts[index].title;
                rotator.querySelector('p').textContent = facts[index].body;
            }, 9000);
        });
    </script>
@endpush

@forelse($attempts as $index => $attempt)
    <div class="leaderboard-row">
        <div class="leaderboard-rank">{{ $index + 1 }}</div>
        <div class="leaderboard-player">
            <strong>{{ $attempt->user->publicName() }}</strong>
            <span>{{ $attempt->examName() }}</span>
        </div>
        <div class="leaderboard-score">
            <strong>{{ $attempt->scorePercent() }}%</strong>
            <span>{{ $attempt->timeUsedLabel() }}</span>
        </div>
    </div>
@empty
    <div class="empty-state">No submitted attempts yet.</div>
@endforelse

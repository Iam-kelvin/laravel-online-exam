<script id="attemptNavigation" type="application/json">{!! json_encode([
    'attemptId' => $attempt->id,
    'completed' => (bool) $attempt->submitted_at,
    'setupUrl' => $attempt->isGame()
        ? route('games.start', ['game' => $attempt->game_type, 'duration_seconds' => $attempt->duration_seconds])
        : route('exam.start', ['mode' => $attempt->mode, 'duration_seconds' => $attempt->duration_seconds]),
], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}</script>
<script src="{{ asset('js/attempt-navigation.js') }}" defer></script>

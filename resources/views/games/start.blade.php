@extends('layouts.ap')

@section('content')
    <div class="games-page game-setup">
        <a href="{{ route('games.index') }}" class="game-back">&larr; All games</a>
        <header class="games-heading">
            <p class="games-eyebrow">Beat the clock</p>
            <h1>{{ $details['name'] }}</h1>
            <p>{{ $details['description'] }}</p>
        </header>
        <div class="game-setup-panel">
            <div class="game-demo" aria-hidden="true">
                @if($game === 'word-scramble')
                    <span class="game-demo-label">Put the letters in order</span>
                    <span class="scramble-letters">R T E A K M</span>
                    <span class="game-demo-hint">Clue: A place to buy and sell.</span>
                @else
                    <img src="{{ asset('puzzles/rebus/puzzle-01.svg') }}" alt="" class="rebus-image">
                @endif
            </div>
            <form action="{{ route('games.store', $game) }}" method="POST" id="gameStartForm" class="game-setup-controls">
                @csrf
                <h2>Your clock. Your challenge.</h2>
                <label for="gameDuration">Duration</label>
                <div class="rush-duration-stepper game-duration">
                    <button type="button" id="gameDurationLess" aria-label="Decrease duration by 15 seconds">&minus;</button>
                    <select name="duration_seconds" id="gameDuration">
                        @foreach(range(15, 180, 15) as $seconds)
                            <option value="{{ $seconds }}" {{ (int) old('duration_seconds', $duration) === $seconds ? 'selected' : '' }}>{{ $seconds }} seconds</option>
                        @endforeach
                    </select>
                    <button type="button" id="gameDurationMore" aria-label="Increase duration by 15 seconds">+</button>
                </div>
                @error('duration_seconds')<p class="text-danger small">{{ $message }}</p>@enderror
                <p class="games-footnote">15-second steps · 3-minute max</p>
                <ul class="game-instructions">
                    <li>{{ $details['instruction'] }}</li>
                    <li>Get instant feedback. Skip if you’re stuck.</li>
                    <li>Every answer counts once. Your score is saved when you finish.</li>
                </ul>
                <button type="submit" class="btn btn-success game-start-button">Start {{ $details['name'] }} <span aria-hidden="true">&rarr;</span></button>
            </form>
        </div>
    </div>
@endsection

@push('styles')
    <link href="{{ asset('css/games.css') }}" rel="stylesheet">
@endpush
@push('scripts')
    <script src="{{ asset('js/game-start.js') }}" defer></script>
@endpush

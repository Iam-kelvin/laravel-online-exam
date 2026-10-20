@extends('layouts.ap')

@section('content')
    <div class="games-page">
        <header class="games-heading">
            <p class="games-eyebrow">A quick brain break</p>
            <h1>How fast can you solve it?</h1>
            <p>Pick a game, set the clock and see how far you can get.</p>
        </header>
        <div class="games-grid">
            @foreach($games as $game => $details)
                <a class="game-card game-card-{{ $game }}" href="{{ route('games.start', $game) }}">
                    <div class="game-card-art" aria-hidden="true">
                        @if($game === 'word-scramble')
                            <span class="game-letter">M</span><span class="game-letter">X</span><span class="game-letter">I</span>
                        @else
                            <span class="game-rebus-sample"><span>HEAD</span><span>HEELS</span></span>
                        @endif
                    </div>
                    <span class="games-eyebrow">15–180 seconds · Rush</span>
                    <h2>{{ $details['name'] }}</h2>
                    <p>{{ $details['description'] }}</p>
                    <span class="game-card-action">Let’s play <span aria-hidden="true">&rarr;</span></span>
                </a>
            @endforeach
        </div>
        <p class="games-footnote">Looking for subject quizzes? <a href="{{ route('exam.start') }}">Go to Practice &amp; Rush</a></p>
    </div>
@endsection

@push('styles')
    <link href="{{ asset('css/games.css') }}" rel="stylesheet">
@endpush

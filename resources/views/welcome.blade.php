<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lagos Rush — Think you know Lagos? | CrazyExam</title>
    <meta name="description" content="Think you know Lagos? Take Lagos Rush: a 60-second street knowledge challenge with instant marking, sound effects and a score to share.">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        body {
            background: #f4f7fb;
            color: #102a43;
        }

        .site-header {
            align-items: center;
            display: flex;
            justify-content: space-between;
            left: 0;
            padding: 1rem 5vw;
            position: absolute;
            right: 0;
            top: 0;
            z-index: 3;
        }

        .brand {
            color: #fff;
            font-size: 1.35rem;
            font-weight: 800;
        }

        .brand:hover,
        .nav-link-public:hover {
            color: #fff;
            text-decoration: none;
        }

        .brand strong {
            color: #7ee3b2;
        }

        .nav-actions {
            align-items: center;
            display: flex;
            gap: 0.75rem;
        }

        .nav-link-public {
            color: #fff;
            font-weight: 700;
        }

        .home-section {
            padding: 3rem 5vw;
        }

        .stat-strip {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            margin-top: -2.5rem;
            position: relative;
            z-index: 2;
        }

        .stat-card,
        .feature-card,
        .story-panel {
            background: #fff;
            border: 1px solid #d9e2ec;
            border-radius: 0.5rem;
        }

        .stat-card {
            padding: 1.25rem;
        }

        .stat-card span {
            color: #627d98;
            display: block;
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .stat-card strong {
            color: #0b8f65;
            display: block;
            font-size: 2rem;
            margin-top: 0.25rem;
        }

        .story-panel {
            display: grid;
            gap: 2rem;
            grid-template-columns: minmax(0, 1fr) minmax(280px, 420px);
            overflow: hidden;
        }

        .story-copy {
            padding: 2rem;
        }

        .story-copy h2 {
            font-size: clamp(1.7rem, 3vw, 2.6rem);
            font-weight: 800;
            letter-spacing: 0;
            margin-bottom: 1rem;
        }

        .story-copy p {
            color: #52606d;
            font-size: 1.05rem;
        }

        .story-image {
            background-color: #050505;
            background-image: url("{{ asset('images/crazyexam.jpg') }}?v={{ filemtime(public_path('images/crazyexam.jpg')) }}");
            background-position: center;
            background-repeat: no-repeat;
            background-size: contain;
            min-height: 320px;
        }

        .feature-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        }

        .feature-card {
            padding: 1.25rem;
        }

        .feature-card h3 {
            font-size: 1.1rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .feature-card p {
            color: #52606d;
            margin-bottom: 0;
        }

        .final-cta {
            background: #143d3a;
            border-radius: 0.5rem;
            color: #fff;
            padding: 2rem;
            text-align: center;
        }

        .final-cta p {
            color: #d9e2ec;
        }

        @media (max-width: 760px) {
            .site-header {
                align-items: flex-start;
                flex-direction: column;
                gap: 0.75rem;
            }

            .story-panel {
                grid-template-columns: 1fr;
            }
        }
    </style>
    @include('partials.theme-head')
    <link href="{{ asset('css/lagos-rush.css') }}" rel="stylesheet">
</head>
<body>
    <header class="site-header">
        <a href="{{ url('/') }}" class="brand"><strong>Crazy</strong>Exam</a>
        <nav class="nav-actions">
            @include('partials.theme-toggle')
            @auth
                <a href="{{ route('home') }}" class="nav-link-public">Dashboard</a>
                @can('manage-questions')
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-success">Admin</a>
                @endcan
            @else
                <a href="{{ route('login') }}" class="nav-link-public">Login</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn btn-success">Register</a>
                @endif
            @endauth
        </nav>
    </header>

    @include('partials.lagos-rush-hero', ['public' => true])

    <main>
        <section class="home-section">
            <div class="stat-strip">
                <div class="stat-card">
                    <span>More Subjects To Explore</span>
                    <strong>{{ $stats['subjects'] }}</strong>
                </div>
                <div class="stat-card">
                    <span>Practice Questions</span>
                    <strong>{{ $stats['questions'] }}</strong>
                </div>
                <div class="stat-card">
                    <span>Lagos Rush</span>
                    <strong>60 seconds</strong>
                </div>
            </div>
        </section>

        <section class="home-section pt-0">
            <div class="story-panel">
                <div class="story-copy">
                    <h2>From “I know Lagos” to “see my score.”</h2>
                    <p>Bus routes, market nicknames and the stories behind familiar places. Lagos Rush puts your street knowledge on the clock, one short question at a time.</p>
                    <p>Get your answer marked immediately, hear the reaction and keep moving. When the clock runs out, share your score and challenge your friends.</p>
                    <a href="{{ route('exam.lagos-rush') }}" class="btn btn-success">Take the Lagos challenge &rarr;</a>
                </div>
                <div class="story-image" aria-hidden="true"></div>
            </div>
        </section>

        <section class="home-section pt-0">
            <div class="feature-grid">
                <article class="feature-card">
                    <h3>How deep is your Lagos knowledge?</h3>
                    <p>Go beyond the obvious with questions about old Lagos, local markets and the stops you pass every day.</p>
                </article>
                <article class="feature-card">
                    <h3>Every answer gets a reaction</h3>
                    <p>Instant marking, live scores and meme sounds keep the Rush moving. Play with sound or keep it muted.</p>
                </article>
                <article class="feature-card">
                    <h3>Keep the challenge going</h3>
                    <p>Try Word Scramble, Picture Puzzles or academic practice when you’re ready for another challenge.</p>
                </article>
            </div>
        </section>

        <section class="home-section pt-0">
            <div class="final-cta">
                <h2 class="h3">Lagos knows you. Do you know Lagos?</h2>
                <p>Pick your time. Answer fast. Give your friends a score to beat.</p>
                <a href="{{ route('exam.lagos-rush') }}" class="btn btn-success">Play Lagos Rush</a>
            </div>
        </section>
    </main>
</body>
</html>

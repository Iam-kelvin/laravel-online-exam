<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CrazyExam</title>
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

        .hero {
            align-items: center;
            background-image: url("{{ asset('images/exx1.jpg') }}");
            background-position: center;
            background-size: cover;
            display: flex;
            min-height: 84vh;
            padding: 7rem 5vw 4rem;
            position: relative;
        }

        .hero::before {
            background: linear-gradient(90deg, rgba(10, 23, 42, 0.86), rgba(10, 23, 42, 0.48));
            content: "";
            inset: 0;
            position: absolute;
        }

        .hero-content {
            color: #fff;
            max-width: 760px;
            position: relative;
            z-index: 1;
        }

        .hero-kicker {
            color: #a7f3d0;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .hero h1 {
            font-size: clamp(2.5rem, 6vw, 5.25rem);
            font-weight: 800;
            letter-spacing: 0;
            line-height: 1;
            margin-bottom: 1rem;
        }

        .hero p {
            color: #d9e2ec;
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            max-width: 640px;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
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

            .hero {
                min-height: 88vh;
                padding-top: 9rem;
            }

            .story-panel {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <a href="{{ url('/') }}" class="brand"><strong>Crazy</strong>Exam</a>
        <nav class="nav-actions">
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

    <section class="hero">
        <div class="hero-content">
            <p class="hero-kicker">Practice with purpose</p>
            <h1>Build confidence before the real exam.</h1>
            <p>CrazyExam helps students turn scattered revision into timed, focused practice. Pick one subject, mix several together, and see exactly how each attempt went.</p>
            <div class="hero-actions">
                @auth
                    <a href="{{ route('exam.start') }}" class="btn btn-success btn-lg">Start Exam</a>
                    <a href="{{ route('home') }}" class="btn btn-outline-light btn-lg">Dashboard</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-success btn-lg">Get Started</a>
                    <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg">Login</a>
                @endauth
            </div>
        </div>
    </section>

    <main>
        <section class="home-section">
            <div class="stat-strip">
                <div class="stat-card">
                    <span>Subjects To Practice</span>
                    <strong>{{ $stats['subjects'] }}</strong>
                </div>
                <div class="stat-card">
                    <span>Practice Questions</span>
                    <strong>{{ $stats['questions'] }}</strong>
                </div>
                <div class="stat-card">
                    <span>Exam Lengths</span>
                    <strong>{{ $stats['presets'] }}</strong>
                </div>
            </div>
        </section>

        <section class="home-section pt-0">
            <div class="story-panel">
                <div class="story-copy">
                    <h2>Turn pressure into practice.</h2>
                    <p>The last few minutes of an exam can feel loud in your head. CrazyExam gives you a calmer way to train for that moment: choose what you want to practice, sit with a real countdown, and learn from the result.</p>
                    <p>Start small when you are warming up. Mix subjects when you want a tougher challenge. Each attempt helps you understand where you are strong and where your next revision session should go.</p>
                </div>
                <div class="story-image" aria-hidden="true"></div>
            </div>
        </section>

        <section class="home-section pt-0">
            <div class="feature-grid">
                <article class="feature-card">
                    <h3>Practice Your Way</h3>
                    <p>Focus on one subject or mix subjects when you want a broader challenge.</p>
                </article>
                <article class="feature-card">
                    <h3>Beat The Clock</h3>
                    <p>Use timed practice to get comfortable with exam pressure before the real day.</p>
                </article>
                <article class="feature-card">
                    <h3>Know Your Progress</h3>
                    <p>Review your attempts and use each result to decide what to practice next.</p>
                </article>
            </div>
        </section>

        <section class="home-section pt-0">
            <div class="final-cta">
                <h2 class="h3">Ready to practice smarter?</h2>
                <p>Start with a short exam, learn from the result, then come back sharper.</p>
                @auth
                    <a href="{{ route('exam.start') }}" class="btn btn-success">Start Exam</a>
                @else
                    <a href="{{ route('register') }}" class="btn btn-success">Create Account</a>
                @endauth
            </div>
        </section>
    </main>
</body>
</html>

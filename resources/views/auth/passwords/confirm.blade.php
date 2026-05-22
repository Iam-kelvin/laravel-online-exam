<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirm Password | CrazyExam</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-story">
            <div class="auth-story-content">
                <a href="{{ url('/') }}" class="auth-brand"><strong>Crazy</strong>Exam</a>
                <h1>One quick check.</h1>
                <p>Confirm your password before making a sensitive account change.</p>
            </div>
        </section>

        <section class="auth-panel">
            <div class="auth-card">
                <div class="auth-nav">
                    <a href="{{ route('home') }}">Dashboard</a>
                    <a href="{{ url('/') }}">Home</a>
                </div>

                <h2>Confirm password</h2>
                <p class="auth-helper mb-4">Enter your current password to continue.</p>

                <form method="POST" action="{{ route('password.confirm') }}">
                    @csrf

                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-input">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                name="password" required autocomplete="current-password" data-password-toggle>
                            <button type="button" class="password-toggle" data-password-toggle-button aria-label="Show password" aria-pressed="false">Show</button>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-success btn-block">Confirm Password</button>

                    @if (Route::has('password.request'))
                        <div class="text-center mt-3">
                            <a href="{{ route('password.request') }}">Forgot password?</a>
                        </div>
                    @endif
                </form>
            </div>
        </section>
    </main>
    <script src="{{ asset('js/password-toggle.js') }}" defer></script>
</body>
</html>

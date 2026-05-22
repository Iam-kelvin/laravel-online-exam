<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password | CrazyExam</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-story">
            <div class="auth-story-content">
                <a href="{{ url('/') }}" class="auth-brand"><strong>Crazy</strong>Exam</a>
                <h1>Get back in.</h1>
                <p>Request a secure reset link and return to your practice dashboard with a new password.</p>
            </div>
        </section>

        <section class="auth-panel">
            <div class="auth-card">
                <div class="auth-nav">
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ url('/') }}">Home</a>
                </div>

                <h2>Forgot password?</h2>
                <p class="auth-helper mb-4">Enter your account email and we will send a password reset link.</p>

                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-success btn-block">Send Reset Link</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>

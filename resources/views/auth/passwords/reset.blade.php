<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Reset Password | CrazyExam</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-story">
            <div class="auth-story-content">
                <a href="{{ url('/') }}" class="auth-brand"><strong>Crazy</strong>Exam</a>
                <h1>Set a fresh password.</h1>
                <p>Choose a new password, then continue your exam practice with the same account.</p>
            </div>
        </section>

        <section class="auth-panel">
            <div class="auth-card">
                <div class="auth-nav">
                    <a href="{{ route('login') }}">Login</a>
                    <a href="{{ url('/') }}">Home</a>
                </div>

                <h2>Reset password</h2>
                <p class="auth-helper mb-4">Use the reset link from your email to create a new password.</p>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf

                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                        @error('email')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password">New password</label>
                        <div class="password-input">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                                name="password" required autocomplete="new-password" data-password-toggle>
                            <button type="button" class="password-toggle" data-password-toggle-button aria-label="Show password" aria-pressed="false">Show</button>
                        </div>
                        @error('password')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password-confirm">Confirm new password</label>
                        <div class="password-input">
                            <input id="password-confirm" type="password" class="form-control"
                                name="password_confirmation" required autocomplete="new-password" data-password-toggle>
                            <button type="button" class="password-toggle" data-password-toggle-button aria-label="Show password" aria-pressed="false">Show</button>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success btn-block">Reset Password</button>
                </form>
            </div>
        </section>
    </main>
    <script src="{{ asset('js/password-toggle.js') }}" defer></script>
</body>
</html>

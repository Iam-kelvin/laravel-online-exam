<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verify Email | CrazyExam</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-story">
            <div class="auth-story-content">
                <a href="{{ url('/') }}" class="auth-brand"><strong>Crazy</strong>Exam</a>
                <h1>Check your inbox.</h1>
                <p>Use the verification link we sent to finish setting up your CrazyExam account.</p>
            </div>
        </section>

        <section class="auth-panel">
            <div class="auth-card">
                <div class="auth-nav">
                    <a href="{{ route('home') }}">Dashboard</a>
                    <a href="{{ route('account.edit') }}">Account settings</a>
                </div>

                <h2>Confirm your email</h2>
                <p class="auth-helper mb-4">We sent a verification link to {{ Auth::user()->email }}.</p>

                @if (session('resent'))
                    <div class="alert alert-success" role="alert">
                        A fresh verification link has been sent to your email address.
                    </div>
                @endif

                <form method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <button type="submit" class="btn btn-success btn-block">Resend Verification Link</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>

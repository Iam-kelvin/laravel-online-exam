<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | CrazyExam</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-story">
            <div class="auth-story-content">
                <a href="{{ url('/') }}" class="auth-brand"><strong>Crazy</strong>Exam</a>
                <h1>Start with a focused practice plan.</h1>
                <p>Create your profile, choose your subject mix, and use each result to see where to sharpen next.</p>
            </div>
        </section>

        <section class="auth-panel">
            <div class="auth-card">
                <div class="auth-nav">
                    <a href="{{ url('/') }}">Home</a>
                    <a href="{{ route('login') }}">Already registered?</a>
                </div>

                <h2>Create Account</h2>
                <p class="auth-helper mb-4">Tell us a little about you so your exam records stay organized.</p>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="auth-grid">
                        <div class="form-group full-width">
                            <label for="name">Full Name</label>
                            <input id="name" type="text" class="form-control @error('name') is-invalid @enderror"
                                name="name" value="{{ old('name') }}" required autocomplete="name">
                            @error('name')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="form-group full-width">
                            <label for="email">Email</label>
                            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                                name="email" value="{{ old('email') }}" required autocomplete="email">
                            @error('email')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        @foreach ([
                            'country' => 'Country',
                            'state' => 'State',
                            'county' => 'County',
                            'level' => 'School Level',
                            'grade' => 'Grade',
                            'school' => 'School',
                        ] as $field => $label)
                            <div class="form-group">
                                <label for="{{ $field }}">{{ $label }}</label>
                                <input id="{{ $field }}" type="text" class="form-control @error($field) is-invalid @enderror"
                                    name="{{ $field }}" value="{{ old($field) }}" required autocomplete="{{ $field }}">
                                @error($field)
                                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>
                        @endforeach

                        <div class="form-group">
                            <label for="password">Password</label>
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
                            <label for="password-confirm">Confirm Password</label>
                            <div class="password-input">
                                <input id="password-confirm" type="password" class="form-control"
                                    name="password_confirmation" required autocomplete="new-password" data-password-toggle>
                                <button type="button" class="password-toggle" data-password-toggle-button aria-label="Show password" aria-pressed="false">Show</button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success btn-block">Create Account</button>
                </form>
            </div>
        </section>
    </main>
    <script src="{{ asset('js/password-toggle.js') }}" defer></script>
</body>
</html>

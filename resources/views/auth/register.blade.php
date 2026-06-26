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

                        <div class="form-group full-width">
                            <label for="display_handle">Public Handle</label>
                            <input id="display_handle" type="text" class="form-control @error('display_handle') is-invalid @enderror"
                                name="display_handle" value="{{ old('display_handle') }}" autocomplete="nickname"
                                placeholder="@yourname">
                            <small class="form-text text-muted">Optional. Used on share cards instead of your full name.</small>
                            @error('display_handle')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="school_level">School Level</label>
                            <select id="school_level" class="form-control @error('school_level') is-invalid @enderror"
                                name="school_level" required autocomplete="education-level">
                                <option value="">Choose level</option>
                                @foreach (array_keys(config('profile.school_levels')) as $level)
                                    <option value="{{ $level }}" {{ old('school_level') === $level ? 'selected' : '' }}>
                                        {{ $level }}
                                    </option>
                                @endforeach
                            </select>
                            @error('school_level')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="class_year">Class / Year</label>
                            <select id="class_year" class="form-control @error('class_year') is-invalid @enderror"
                                name="class_year" required data-current="{{ old('class_year') }}">
                                <option value="">Choose school level first</option>
                            </select>
                            @error('class_year')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="country_of_study">Country of Study</label>
                            <select id="country_of_study" class="form-control @error('country_of_study') is-invalid @enderror"
                                name="country_of_study" required autocomplete="country-name">
                                <option value="">Choose country</option>
                                @foreach (config('profile.countries') as $country)
                                    <option value="{{ $country }}" {{ old('country_of_study') === $country ? 'selected' : '' }}>
                                        {{ $country }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country_of_study')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="city_town">City / Town</label>
                            <input id="city_town" type="text" list="city-options"
                                class="form-control @error('city_town') is-invalid @enderror"
                                name="city_town" value="{{ old('city_town') }}" required autocomplete="address-level2">
                            <datalist id="city-options"></datalist>
                            @error('city_town')
                                <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

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
    <script>
        window.crazyExamProfileOptions = {
            classYears: @json(config('profile.school_levels')),
            citySuggestions: @json(config('profile.city_suggestions')),
        };
    </script>
    <script src="{{ asset('js/register-profile.js') }}" defer></script>
    <script src="{{ asset('js/password-toggle.js') }}" defer></script>
</body>
</html>

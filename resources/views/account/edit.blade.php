@extends('layouts.ap')

@section('content')
    <div class="dashboard-hero mb-4">
        <div>
            <p class="metric-label mb-2">Account</p>
            <h1 class="h3 mb-2">Keep your access up to date.</h1>
            <p class="text-muted mb-0">Change your name, update your email, or set a new password without leaving your dashboard.</p>
        </div>
    </div>

    <div class="settings-grid">
        <section class="content-panel">
            <div class="panel-header">
                <div>
                    <h2 class="h5 mb-1">Profile</h2>
                    <p class="text-muted mb-0">Changing email requires your current password.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('account.profile.update') }}">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label for="name">Name</label>
                    <input id="name" type="text" class="form-control @error('name', 'profile') is-invalid @enderror"
                        name="name" value="{{ old('name', $user->name) }}" required autocomplete="name">
                    @error('name', 'profile')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" class="form-control @error('email', 'profile') is-invalid @enderror"
                        name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                    @error('email', 'profile')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="profile-current-password">Current password</label>
                    <div class="password-input">
                        <input id="profile-current-password" type="password"
                            class="form-control @error('current_password', 'profile') is-invalid @enderror"
                            name="current_password" autocomplete="current-password" data-password-toggle>
                        <button type="button" class="password-toggle" data-password-toggle-button aria-label="Show password" aria-pressed="false">Show</button>
                    </div>
                    @error('current_password', 'profile')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <button type="submit" class="btn btn-success">Save Profile</button>
            </form>
        </section>

        <section class="content-panel">
            <div class="panel-header">
                <div>
                    <h2 class="h5 mb-1">Password</h2>
                    <p class="text-muted mb-0">Use this when you know your current password and want to change it.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('account.password.update') }}">
                @csrf
                @method('PATCH')

                <div class="form-group">
                    <label for="password-current-password">Current password</label>
                    <div class="password-input">
                        <input id="password-current-password" type="password"
                            class="form-control @error('current_password', 'passwordUpdate') is-invalid @enderror"
                            name="current_password" required autocomplete="current-password" data-password-toggle>
                        <button type="button" class="password-toggle" data-password-toggle-button aria-label="Show password" aria-pressed="false">Show</button>
                    </div>
                    @error('current_password', 'passwordUpdate')
                        <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">New password</label>
                    <div class="password-input">
                        <input id="password" type="password" class="form-control @error('password', 'passwordUpdate') is-invalid @enderror"
                            name="password" required autocomplete="new-password" data-password-toggle>
                        <button type="button" class="password-toggle" data-password-toggle-button aria-label="Show password" aria-pressed="false">Show</button>
                    </div>
                    @error('password', 'passwordUpdate')
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

                <button type="submit" class="btn btn-success">Update Password</button>
            </form>
        </section>
    </div>
@endsection

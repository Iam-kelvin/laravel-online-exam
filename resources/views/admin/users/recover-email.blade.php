@extends('layouts.ap')

@section('content')
    <div class="content-panel">
        <div class="panel-header">
            <div>
                <h1 class="h4 mb-1">Recover Email</h1>
                <p class="text-muted mb-0">Use this when a learner lost access to their email but can verify their identity with support.</p>
            </div>
            <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-secondary">Back To Users</a>
        </div>

        <div class="row">
            <div class="col-lg-7">
                <form method="POST" action="{{ route('users.email.update', $user) }}">
                    @csrf
                    @method('PATCH')

                    <div class="form-group">
                        <label>Account</label>
                        <input type="text" class="form-control" value="{{ $user->name }}" disabled>
                    </div>

                    <div class="form-group">
                        <label>Current email</label>
                        <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                    </div>

                    <div class="form-group">
                        <label for="email">New email</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email', $user->email) }}" required autocomplete="email">
                        @error('email')
                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-success">Update And Send Verification</button>
                </form>
            </div>

            <div class="col-lg-5 mt-4 mt-lg-0">
                <div class="selection-card">
                    <h2 class="h5">What happens next?</h2>
                    <p class="text-muted mb-2">The old email is replaced, verification is cleared, and CrazyExam sends a fresh verification link to the new address.</p>
                    <p class="text-muted mb-0">The learner can still enter the dashboard, but they cannot take exams until the new email is verified.</p>
                </div>
            </div>
        </div>
    </div>
@endsection

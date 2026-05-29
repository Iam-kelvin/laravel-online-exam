@extends('layouts.ap')

@section('content')
    <div class="content-panel">
        <div class="panel-header">
            <div>
                <h1 class="h4 mb-1">Users</h1>
                <p class="text-muted mb-0">Manage roles and recover account email access.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Country</th>
                        <th>City</th>
                        <th>Level</th>
                        <th>Verified</th>
                        <th>Roles</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <th scope="row">{{ $user->id }}</th>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->country_of_study ?: $user->country }}</td>
                            <td>{{ $user->city_town ?: $user->county }}</td>
                            <td>
                                {{ $user->school_level ?: $user->level }}
                                <span class="text-muted d-block">{{ $user->class_year ?: $user->grade }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $user->hasVerifiedEmail() ? 'success' : 'warning' }}">
                                    {{ $user->hasVerifiedEmail() ? 'Verified' : 'Pending' }}
                                </span>
                            </td>
                            <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
                            <td>
                                <div class="dashboard-actions">
                                    @can('edit-users')
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                    @endcan
                                    @can('recover-user-email')
                                        <a href="{{ route('users.email.edit', $user) }}" class="btn btn-sm btn-outline-secondary">Recover Email</a>
                                    @endcan
                                    @can('delete-users')
                                        <form action="{{ route('users.destroy', $user) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

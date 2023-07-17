@extends('layouts.ap')

@section('content')
    <div class="container">
        <h2>Exam Results</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Attempt ID</th>
                    <th>Score</th>
                    <th>Duration Used</th>
                    <th>Attempted At</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($examResults as $examResult)
                    <tr>
                        <td>{{ $examResult->id }}</td>
                        <td>{{ $examResult->score }}</td>
                        <td>{{ $examResult->duration }} seconds</td>
                        <td>{{ $examResult->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

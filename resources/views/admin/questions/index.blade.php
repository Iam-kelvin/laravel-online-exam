@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Questions</h2>
        <a class="btn btn-primary mb-2" href="{{ route('questions.create') }}">Add New Question</a>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Question</th>
                    <th>Option A</th>
                    <th>Option B</th>
                    <th>Option C</th>
                    <th>Option D</th>
                    <th>Answer</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($questions as $question)
                    <tr>
                        <td>{{ $question->id }}</td>
                        <td>{{ $question->question }}</td>
                        <td>{{ $question->option_a }}</td>
                        <td>{{ $question->option_b }}</td>
                        <td>{{ $question->option_c }}</td>
                        <td>{{ $question->option_d }}</td>
                        <td>{{ $question->answer }}</td>
                        <td>
                            <a class="btn btn-primary" href="{{ route('questions.edit', $question->id) }}">Edit</a>
                            <form action="{{ route('questions.destroy', $question->id) }}" method="POST"
                                style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger"
                                    onclick="return confirm('Are you sure you want to delete this question?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

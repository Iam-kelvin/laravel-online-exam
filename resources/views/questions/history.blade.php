@extends('layouts.ap')


@section('content')
<div class="container">
	<div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><strong>Set History Question</strong></div>

                <div class="card-body">
				<form method="post" action="{{ route('history') }}">
					{{ csrf_field() }}

					<div class="form-group row">
                        <label for="Teacher_id" class="col-md-4 col-form-label text-md-left">Teacher_id:</label>

                    <div class="col-md-6">
                        <input id="Teacher_id" type="text" class="form-control @error('Teacher_id') is-invalid @enderror" name="Teacher_id" value="{{ old('Teacher_id') }}" required autocomplete="Teacher_id">
                        @error('Teacher_id')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    </div>

					<div class="form-group row">
                        <label for="Course" class="col-md-4 col-form-label text-md-left">Course Name:</label>

                    <div class="col-md-6">
                        <input id="Course" type="text" class="form-control @error('Course') is-invalid @enderror" name="Course" value="{{ old('Course') }}" required autocomplete="Course">
                        @error('Course')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    </div>

					<div class="form-group row">
                        <label for="question_lenth" class="col-md-4 col-form-label text-md-left">Number of Questions:</label>

                    <div class="col-md-6">
                        <input id="question_lenth" type="text" class="form-control @error('question_lenth') is-invalid @enderror" name="question_lenth" value="{{ old('question_lenth') }}" required autocomplete="question_lenth">
                        @error('question_lenth')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    </div>

					<div class="form-group row">
                        <label for="time" class="col-md-4 col-form-label text-md-left">Duration:</label>

                    <div class="col-md-6">
                        <input id="time" type="text" class="form-control @error('time') is-invalid @enderror" name="time" value="{{ old('time') }}" required autocomplete="time">
                        @error('time')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    </div>

					<button type="Submit" class="btn btn-success">Submit</button>
				</form>

			</div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.ap')

@section('content')
<div class="container">
	<div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header"><strong>Type in Mathematics Question</strong></div>

                <div class="card-body">
				<form method="post" action="{{ route('createmath') }}">
					{{ csrf_field() }}

				
					<div class="form-group row">
                        <label for="question" class="col-md-4 col-form-label text-md-left">Question:</label>

                    <div class="col-md-6">
                        <input id="question" type="text" class="form-control @error('question') is-invalid @enderror" name="question" value="{{ old('question') }}" required autocomplete="question">
                        @error('question')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    </div>

					<div class="form-group row">
                        <label for="option1" class="col-md-4 col-form-label text-md-left">Option 1:</label>

                    <div class="col-md-6">
                        <input id="option1" type="text" class="form-control @error('option1') is-invalid @enderror" name="option1" value="{{ old('option1') }}" required autocomplete="option1">
                        @error('option1')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    </div>

					<div class="form-group row">
                        <label for="option2" class="col-md-4 col-form-label text-md-left">Option 2:</label>

                    <div class="col-md-6">
                        <input id="option2" type="text" class="form-control @error('option2') is-invalid @enderror" name="option2" value="{{ old('option2') }}" required autocomplete="option2">
                        @error('option2')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    </div>

					<div class="form-group row">
                        <label for="option3" class="col-md-4 col-form-label text-md-left">Option 3:</label>

                    <div class="col-md-6">
                        <input id="option3" type="text" class="form-control @error('option3') is-invalid @enderror" name="option3" value="{{ old('option3') }}" required autocomplete="option3">
                        @error('option3')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    </div>

					<div class="form-group row">
                        <label for="option4" class="col-md-4 col-form-label text-md-left">Option 4:</label>

                    <div class="col-md-6">
                        <input id="option4" type="text" class="form-control @error('option4') is-invalid @enderror" name="option4" value="{{ old('option4') }}" required autocomplete="option4">
                        @error('option4')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    </div>

					<div class="form-group row">
                        <label for="answer" class="col-md-4 col-form-label text-md-left">Answer:</label>

                    <div class="col-md-6">
                        <input id="answer" type="text" class="form-control @error('answer') is-invalid @enderror" name="answer" value="{{ old('answer') }}" required autocomplete="answer">
                        @error('answer')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                    </div>

                    <div class="form-group">
                        <input type="hidden" name="mathid" class="form-control" id="mathid" value="{{$math->id}}" readonly>
                    </div>

					<button type="Submit" class="btn btn-success">Submit</button>
				</form>

			</div>
    	</div>
	</div>
</div>
@endsection
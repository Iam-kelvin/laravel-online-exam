<!DOCTYPE html>
<html>
<head>
	<title>Register</title>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="
	sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="{{url('../assets/css/main.css')}}">
</head>
<body class="subpage">

	
	<!-- Header -->
			<header id="header">
				<div class="inner">
					<a href="{{ url('/') }}" class="logo"><strong>Crazy</strong>Exam</a>
					<nav id="nav">
                        <a href="{{ url('/') }}">Home</a>
                        <a href="{{ route('login') }}">Login</a>
					</nav>
					<a href="#navPanel" class="navPanelToggle"><span class="fa fa-bars"></span></a>
				</div>
			</header>

<div id="bann">
<div class="please">
	<div class="login-bo">
    	<h1>Register</h1>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="textbox">
                <i class="fas fa-user"></i>
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email">
                @error('email')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
    
            <div class="textbox">
                <i class="fas fa-user"></i>
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" placeholder="Full name">
                @error('name')
                    <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
    
            <div class="textbox">
                <i class="fas fa-user"></i>
                <input id="country" type="text" class="form-control @error('country') is-invalid @enderror" name="country" value="{{ old('country') }}" required autocomplete="country" placeholder="Country">
                @error('country')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
    
            <div class="textbox">
                <i class="fas fa-user"></i>
                <input id="state" type="text" class="form-control @error('state') is-invalid @enderror" name="state" value="{{ old('state') }}" required autocomplete="state" placeholder="State">
                @error('state')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
    
            <div class="textbox">
                <i class="fas fa-user"></i>
                <input id="county" type="text" class="form-control @error('county') is-invalid @enderror" name="county" value="{{ old('county') }}" required autocomplete="county" placeholder="County">
                @error('county')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
    
            <div class="textbox">
                <i class="fas fa-user"></i>
                <input id="level" type="text" class="form-control @error('level') is-invalid @enderror" name="level" value="{{ old('level') }}" required autocomplete="level" placeholder="School Level">
                @error('level')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
    
            <div class="textbox">
                <i class="fas fa-user"></i>
                <input id="grade" type="text" class="form-control @error('grade') is-invalid @enderror" name="grade" value="{{ old('grade') }}" required autocomplete="grade" placeholder="Grade">
                @error('grade')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
    
            <div class="textbox">
                <i class="fas fa-user"></i>
                <input id="school" type="text" class="form-control @error('school') is-invalid @enderror" name="school" value="{{ old('school') }}" required autocomplete="school" placeholder="School">
                @error('school')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="textbox">
                <i class="fas fa-lock"></i>
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Password">
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="textbox">
                <i class="fas fa-lock"></i>
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm Password">
            </div>

            <input type="submit" class="btn" value="{{ __('Register') }}">
                        <!-- <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn">
                                    {{ __('Register') }}
                                </button>
                            </div>
                        </div> -->
        </form>
    </div>
</div>
</div>
	
	<!-- Scripts -->
			<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
			<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
			<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
	
	
	<!-- Scripts -->
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/skel.min.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>


</body>
</html>
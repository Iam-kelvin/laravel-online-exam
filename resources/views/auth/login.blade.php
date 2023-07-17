<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Login</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <link rel="stylesheet" type="text/css" href="{{url('../assets/css/main.css')}}">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="
        sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
        
    </head>
    <body class="please">
  
             <!-- Header -->
            <header id="header">
				<div class="inner">
					<a href="{{ url('/') }}" class="logo"><strong>Crazy</strong>Exam</a>
					<nav id="nav">
                        <a href="{{ url('/') }}">Home</a>
                        <a href="{{ route('register') }}">Register</a>
					</nav>
					<a href="#navPanel" class="navPanelToggle"><span class="fa fa-bars"></span></a>
				</div>
			</header>

    <div id="bann">
    <div class="login-box">
        <h1>Login</h1>

        <form method="POST" action="{{ route('login') }}">
            @csrf

        <div class="textbox">
            <i class="fa fa-user"></i>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email">
            @error('email')
                        <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                        </span>
            @enderror
        </div>

        <div class="textbox">
            <i class="fa fa-lock"></i>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password">
            @error('password')
                        <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                        </span>
            @enderror
        </div>


        <div class="form-check">
		    <input type="radio" class="form-check-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
		    <label class="form-check-label" id="remember" for="remember">{{ __('Keep me signed in') }}</label>
        </div>

                        
        <input type="submit" class="btn" value="{{ __('Login') }}">

        @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}"><input type="button" class="btn" href="{{ route('password.request') }}" value="{{ __('Forgot Password') }}"></a>
        @endif
                                   
        
        <!-- <footer>
                @if (Route::has('password.request'))
			<a class="button" href="{{ route('password.request') }}">{{ __('Forgot Password') }}</a>
                @endif
		</footer> -->

            <!-- @if (Route::has('password.request'))
        <input type="submit" class="btn btn-link" href="{{ route('password.request') }}" value="{{ __('Forgot Your Password?') }}">
            @endif -->
                           
        </form>
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
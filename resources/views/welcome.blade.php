<!DOCTYPE HTML>

<html lang="en">
	<head>
		<title>Crazy Exam</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" type="text/css" href="{{url('../assets/css/main.css')}}" >
		
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="
		sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	</head>


    <body>

    <!-- Header -->
			<header id="header">
				<div class="inner">
					<a href="{{ url('/') }}" class="logo"><strong>Crazy</strong>Exam</a>
					<nav id="nav">
                        <a href="{{ route('login') }}">Login</a>
                        <a href="{{ route('register') }}">Register</a>
					</nav>
					<a href="#navPanel" class="navPanelToggle"><span class="fa fa-bars"></span></a>
				</div>
			</header>


		<!-- Banner -->
        <section id="banner">
				<div class="inner">
					<header>
						<h1>Welcome to <span class="logo"><strong>Crazy</strong>Exam</span></h1>
					</header>

					<div class="flex ">

						<div>
							<span class="icon fa fa-user-plus"></span>
							<h3>Register</h3>
							<p>Register to Crazy Exam</p>
						</div>

						<div>
							<span class="icon fa-laptop"></span>
							<h3>Quiz</h3>
							<p>Take a Quiz</p>
						</div>

						<div>
							<span class="icon fas fa-graduation-cap"></span>
							<h3>Result</h3>
							<p>Get Your Score</p>
						</div>

					</div>

					<footer>
						<a href="{{ route('register') }}" class="button">Get Started</a>
					</footer>
				</div>
			</section>


		<!-- Three -->
			<section id="three" class="wrapper align-center">
				<div class="inner">
					<div class="flex flex-2">
						<article>
							<div class="image round">
								<img src="images/pic03.jpg" alt="Pic 01" />
							</div>
							<header>
								<h3>About Us<br /> </h3>
							</header>
							<p>CrazyExam is here to test your cognitive skills<br /> while you represent your school<br /> and get rewarded by us for it.</p>
							<footer>
								<a href="{{ route('login') }}" class="button">Login</a>
							</footer>
						</article>
						<article>
							<div class="image round">
								<img src="images/pic04.jpg" alt="Pic 02" />
							</div>
							<header>
								<h3>Take a Quiz!<br /> Put Your School Ahead<br /> Get Reward</h3>
							</header>
							<p>Join now and take a quiz<br /> anywhere and anytime on your smartphone or computer,<br /> represent your school and get rewarded.</p>
							<footer>
								<a href="{{ route('register') }}" class="button">Join Now</a>
							</footer>
						</article>
					</div>
				</div>
			</section>

		<!-- Footer -->
			<footer id="footer">
				<div class="inner">

					<h3>Get in touch</h3>

					<form action="#" method="post">

						<div class="field half first">
							<label for="name">Name</label>
							<input name="name" id="name" type="text" placeholder="Name">
						</div>
						<div class="field half">
							<label for="email">Email</label>
							<input name="email" id="email" type="email" placeholder="Email">
						</div>
						<div class="field">
							<label for="message">Message</label>
							<textarea name="message" id="message" rows="6" placeholder="Message"></textarea>
						</div>
						<ul class="actions">
							<li><input value="Send Message" class="button alt" type="submit"></li>
						</ul>
					</form>

					<i class="fa fa-facebook"></i>
					<i class="fa fa-twitter"></i>
					<i class="fa fa-pinterest"></i>
					<i class="fa fa-instagram"></i>
					<i class="fa fa-google-plus"></i> <br /> @CrazyExam

					<div class="copyright">
						&copy; 2020. CrazyExam: Quiz.
					</div>

				</div>
			</footer>
			
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

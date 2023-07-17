<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CrazyExam</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">

    <!-- Bootstrap core CSS -->
    <link href="{{ URL::to('/') }}/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.3/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous"> -->
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous"> -->

    <!-- Custom styles for this template -->
    <link href="{{ URL::to('/') }}/css/simple-sidebar.css" rel="stylesheet">

</head>

<body>

    <div class="d-flex" id="wrapper">

        <!-- Sidebar -->
        <div class="border-right" id="sidebar-wrapper">
        <div class="sidebar-heading"><a href="{{ url('/') }}" class="logo"><strong>Crazy</strong>Exam</a> </div>
        <div class="list-group">
            <ul>
                <div class="mag">
                    <img src="{{ URL::to('/') }}/images/crazyexam.jpg" width="100" height="100" alt="not found" />
                </div>
                <li><a href="/home"><i class="fas fa-user-edit"></i>Dashboard</a></li>
                @can('manage-questions')<li><a href="/admin/questions/create"><i class="fas fa-clipboard"></i>Make Exam</a></li>@endcan
                @can('manage-questions')<li><a href="/importExportView"><i class="fas fa-clipboard"></i>Import Exam</a></li>@endcan
                <li><a href="/exam/start"><i class="fas fa-pen"></i>Take Exam</a></li>
                <li><a href="/exam/results"><i class="far fa-file"></i> Results</a></li>
            </ul>
        </div>

        <footer id="footer" class="copyright">
            &copy; 2020. CrazyExam: Quiz.
        </footer>

        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">

        <nav class="navbar navbar-expand-lg navbar-light border-bottom navbar-default">
            <button class="btn" id="menu-toggle"><span class="navbar-toggler-icon"></span></button>

            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
            @guest
                <li class="nav-item active">
                <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                </li>
                @if (Route::has('register'))
                <li class="nav-item">
                <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                </li>
                @endif
                @else
                <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ Auth::user()->name }}
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                        {{ __('Logout') }}
                    </a>
                    @can('manage-users')
                    <a class="dropdown-item" href="{{ route('users.index') }}">
                        User Management
                    </a>
                    @endcan
                    @can('manage-questions')
                    <a class="dropdown-item" href="{{ route('questions.index') }}">
                        Question Management
                    </a>
                    @endcan
                    
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
                </li>
            @endguest
            </ul>
            </div>
        </nav>

        <main class="py-4">
            <div class='container'>
                <script src="{{ URL::to('/') }}../js/app.js"></script>
                    

            @include('alert.alerts')
            @yield('content')
            @yield('script')
            </div>
        </main>
    <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->

    <!-- Bootstrap core JavaScript -->
    <script src="{{ URL::to('/') }}/vendor/jquery/jquery.min.js"></script>
    <script src="{{ URL::to('/') }}/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
    <!-- <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.3/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script> -->
    <!-- <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js" integrity="sha384-LtrjvnR4Twt/qOuYxE721u19sVFLVSA4hf/rRt6PrZTmiPltdZcI7q7PXQBYTKyf" crossorigin="anonymous"></script> -->

    <!-- Menu Toggle Script -->
    <script>
        $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
        });
    </script>

    <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });

        $('.ansform').on('submit',function(e){
            var form = $(this);
            var submit = form.find("[type=submit]");
            var submitOriginalText = submit.attr("value");

            e.preventDefault();
            var data = form.serialize();
            var url = form.attr('action');
            var post = form.attr('method');
            $.ajax({
                type : post,
                url : url,
                data :data,
                success:function(data){
                submit.attr("value", "Submitted");
                },
                beforeSend: function(){
                submit.attr("value", "Loading...");
                submit.prop("disabled", true);
                },
                error: function() {
                    submit.attr("value", submitOriginalText);
                    submit.prop("disabled", false);
                // show error to end user
                }
            })
        })
    </script>

</body>

</html>

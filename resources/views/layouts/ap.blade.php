<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CrazyExam</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/simple-sidebar.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div id="app" class="app-shell">
        <aside id="sidebar" class="app-sidebar" aria-label="Main navigation">
            <div class="sidebar-brand">
                <a href="{{ url('/') }}"><strong>Crazy</strong>Exam</a>
            </div>

            <nav class="sidebar-nav">
                <a class="sidebar-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Dashboard</a>
                <a class="sidebar-link {{ request()->routeIs('exam.start') || request()->routeIs('exam.take') ? 'active' : '' }}" href="{{ Auth::user()->hasVerifiedEmail() ? route('exam.start') : route('verification.notice') }}">Take Exam</a>
                <a class="sidebar-link {{ request()->routeIs('exam.results') || request()->routeIs('exam.review') ? 'active' : '' }}" href="{{ route('exam.results') }}">Results</a>
                <a class="sidebar-link {{ request()->routeIs('account.*') ? 'active' : '' }}" href="{{ route('account.edit') }}">Account Settings</a>

                @can('manage-questions')
                    <div class="sidebar-section">Admin</div>
                    <a class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                    <a class="sidebar-link {{ request()->routeIs('questions.*') ? 'active' : '' }}" href="{{ route('questions.index') }}">Question Bank</a>
                    <a class="sidebar-link {{ request()->routeIs('subjects.*') ? 'active' : '' }}" href="{{ route('subjects.index') }}">Subjects</a>
                    <a class="sidebar-link {{ request()->routeIs('exam-presets.*') ? 'active' : '' }}" href="{{ route('exam-presets.index') }}">Durations</a>
                    <a class="sidebar-link {{ request()->is('importExportView') ? 'active' : '' }}" href="{{ url('/importExportView') }}">Import Questions</a>
                @endcan

                @can('manage-users')
                    <a class="sidebar-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">Users</a>
                @endcan
            </nav>

            <div class="sidebar-footer">
                &copy; {{ date('Y') }} CrazyExam
            </div>
        </aside>

        <button type="button" id="sidebarOverlay" class="sidebar-overlay" aria-label="Close sidebar"></button>

        <div id="content" class="app-content">
            <header class="app-topbar">
                <button type="button" id="sidebarCollapse" class="sidebar-toggle" aria-controls="sidebar" aria-expanded="true">
                    <span class="navbar-toggler-icon"></span>
                    <span class="sr-only">Toggle navigation</span>
                </button>

                <a class="topbar-brand" href="{{ url('/') }}"><strong>Crazy</strong>Exam</a>

                <div class="topbar-actions">
                    @auth
                        <span class="topbar-user">{{ Auth::user()->name }}</span>
                        <a href="{{ route('account.edit') }}" class="btn btn-sm btn-outline-secondary">Account</a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-secondary">Logout</button>
                        </form>
                    @endauth
                </div>
            </header>

            <main class="app-main">
                <div class="container-fluid">
                    @include('alert.alerts')

                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/password-toggle.js') }}" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var sidebarToggle = document.getElementById('sidebarCollapse');
            var sidebarOverlay = document.getElementById('sidebarOverlay');
            var collapsedClass = 'sidebar-collapsed';
            var openClass = 'sidebar-open';

            if (localStorage.getItem('crazyexam-sidebar-collapsed') === 'true') {
                document.body.classList.add(collapsedClass);
                sidebarToggle.setAttribute('aria-expanded', 'false');
            }

            function isMobile() {
                return window.matchMedia('(max-width: 768px)').matches;
            }

            function closeMobileSidebar() {
                document.body.classList.remove(openClass);
                sidebarToggle.setAttribute('aria-expanded', 'false');
            }

            sidebarToggle.addEventListener('click', function () {
                if (isMobile()) {
                    document.body.classList.toggle(openClass);
                    sidebarToggle.setAttribute('aria-expanded', document.body.classList.contains(openClass) ? 'true' : 'false');
                    return;
                }

                document.body.classList.toggle(collapsedClass);
                localStorage.setItem('crazyexam-sidebar-collapsed', document.body.classList.contains(collapsedClass) ? 'true' : 'false');
                sidebarToggle.setAttribute('aria-expanded', document.body.classList.contains(collapsedClass) ? 'false' : 'true');
            });

            sidebarOverlay.addEventListener('click', closeMobileSidebar);
            window.addEventListener('resize', function () {
                if (! isMobile()) {
                    document.body.classList.remove(openClass);
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>

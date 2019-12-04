<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
        <title>@yield('title')</title>
       
        <!-- Bootstrap - Latest compiled and minified CSS -->
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
		
		<!--Jquery -->
        <script src="{{ URL::asset('node_modules/jquery/dist/jquery.min.js') }}"></script>
        <script src="{{ URL::asset('/../node_modules/moment/moment.js') }}"></script>
        
       <!-- CSS modal -->
        <link rel="stylesheet" href="{{ URL::asset('node_modules/jquery-modal/jquery.modal.css') }}" type="text/css" />
        <link rel="stylesheet" href="{{ URL::asset('css/welcome.css') }}" type="text/css" />
 
        <link href="{{ URL::asset('node_modules/@fullcalendar/core/main.css') }}" rel='stylesheet' />
        <link href="{{ URL::asset('node_modules/@fullcalendar/daygrid/main.css') }}" rel='stylesheet' />
        <link href="{{ URL::asset('node_modules/@fullcalendar/list/main.css') }}" rel='stylesheet' />
    
        <script  src="{{ URL::asset('node_modules/@fullcalendar/core/main.js') }}"></script>
        <script type="module" src="{{ URL::asset('node_modules/@fullcalendar/daygrid/main.js') }}"></script>
        <script type="module" src="{{ URL::asset('node_modules/@fullcalendar/interaction/main.js') }}"></script>
        <script type="module" src="{{ URL::asset('node_modules/@fullcalendar/list/main.js') }}"></script>
        <script type="module" src="{{ URL::asset('node_modules/@fullcalendar/resource-common/main.js') }}"></script>
        <script src="{{ URL::asset('screenshot/html2canvas.min.js') }}"></script>
        <script src="{{ URL::asset('screenshot/canvas2image.js') }}"></script>

		@stack('stylesheet')
    </head>
    <body> 
        @if (Sentinel::check())
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="{{ route('dashboard') }}"">Duplico</a>
                    </div>
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                            @if (Sentinel::inRole('administrator'))
                                <li class="{{ Request::is('users*') ? 'active' : '' }}"><a href="{{ route('users.index') }}">Users</a></li>
                                <li class="{{ Request::is('roles*') ? 'active' : '' }}"><a href="{{ route('roles.index') }}">Roles</a></li>
                                <li class="{{ Request::is('employees*') ? 'active' : '' }}"><a href="{{ route('employees.index') }}">Djelatnici</a></li>
                                <li class="{{ Request::is('projects*') ? 'active' : '' }}"><a href="{{ route('projects.index') }}">Projekti</a></li>
                            @endif
                            <li class="{{ Request::is('preparations*') ? 'active' : '' }}"><a href="{{ route('preparations.index') }}">Priprema i mehanička obrada</a></li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            @if (Sentinel::check())
                                <li><p class="navbar-text">{{ Sentinel::getUser()->email }}</p></li>
                                <li><a href="{{ route('auth.logout') }}">Log Out</a></li>
                            @else
                                <li><a href="{{ route('auth.login.form') }}">Login</a></li>
                                <li><a href="{{ route('auth.register.form') }}">Register</a></li>
                            @endif
                        </ul>
                    </div><!-- /.navbar-collapse -->
                </div><!-- /.container-fluid -->
            </nav>
        @endif
        <section class="">
            @include('Centaur::notifications')
            @yield('content')
        </section>

        <!-- Latest compiled and minified Bootstrap JavaScript -->
        <!-- Bootstrap js -->
		<script src="{{ URL::asset('node_modules/bootstrap/dist/js/bootstrap.min.js') }}"></script>
		<script src="{{ URL::asset('node_modules/popper.js/dist/umd/popper.min.js') }}"></script>
        <!-- Restfulizer.js - A tool for simulating put,patch and delete requests -->
        <script src="{{ asset('restfulizer.js') }}"></script>
        
        <!-- Jquery modal -->
        <script src="{{ URL::asset('/../node_modules/jquery-modal/jquery.modal.js') }}"></script>
        <!-- Modal js -->
        <script src="{{URL::asset('/../js/open_modal.js') }}"></script>
        
        
		@stack('script')
    </body>
</html>
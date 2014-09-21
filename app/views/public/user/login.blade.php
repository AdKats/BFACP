<!DOCTYPE html>
<html class="bg-black">
    <head>
        <meta charset="UTF-8">
        <title>{{{ $title or 'No Title' }}} | BFAdminCP</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <!-- bootstrap 3.0.2 -->
        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- font Awesome -->
        <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="{{ asset('css/AdminLTE.css') }}" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="bg-black">

        <div class="form-box" id="login-box">

            @foreach($errors->all() as $error)
            <div class="alert alert-danger">{{{ $error }}}</div>
            @endforeach

            @if(Session::has('signup_player_error'))
                <div class="alert alert-info">{{{ Session::get('signup_player_error') }}}</div>
            @endif

            @if ( Session::get('notice') )
                <div class="alert alert-info">{{{ Session::get('notice') }}}</div>
            @endif

            <div class="header">Sign In</div>
            {{ Form::open(array('action' => 'ADKGamers\\Webadmin\\Controllers\\UserController@do_login', 'method' => 'post')) }}
                <div class="body bg-gray">
                    <div class="form-group">
                        <input type="text" name="identity" class="form-control" placeholder="Username or Email" value="{{ Input::old('identity') }}" required />
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" class="form-control" placeholder="Password" required />
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="remember_me" value="0">
                        <input type="checkbox" name="remember_me" value="1" /> Remember me
                    </div>
                </div>
                <div class="footer">
                    <button type="submit" class="btn bg-olive btn-block">Sign me in</button>

                    <p><a href="/forgot_password">I forgot my password</a></p>

                    <a href="/signup" class="text-center">Register a new membership</a>
                </div>
            {{ Form::close() }}
        </div>


        <!-- jQuery 2.0.2 -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
        <!-- Bootstrap -->
        <script src="{{ asset('js/bootstrap.min.js') }}" type="text/javascript"></script>

    </body>
</html>

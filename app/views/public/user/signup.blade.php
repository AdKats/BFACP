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
        <!-- Bootstrap Select -->
        <link href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.3.5/bootstrap-select.min.css" rel="stylesheet" type="text/css" />

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="bg-black">

        <div class="form-box" id="login-box">

            @if($errors->has('signup_failed'))
                <div class="alert alert-danger">{{ $errors->first('signup_failed') }}</div>
            @endif

            <div class="header">Register New Account</div>
            {{ Form::open(array('action' => 'ADKGamers\\Webadmin\\Controllers\\UserController@store', 'method' => 'post')) }}
                <div class="body bg-gray">
                    @if($errors->has('username'))
                    <div class="form-group has-error">
                        <label class="control-label" for="username">{{ $errors->first('username') }}</label>
                        <input type="text" name="username" class="form-control" placeholder="Username" required value="{{Input::old('username')}}" />
                    </div>
                    @else
                    <div class="form-group">
                        <input type="text" name="username" class="form-control" placeholder="Username" required value="{{Input::old('username')}}" />
                    </div>
                    @endif

                    @if($errors->has('email'))
                    <div class="form-group has-error">
                        <label class="control-label" for="email">{{ $errors->first('email') }}</label>
                        <input type="email" name="email" class="form-control" placeholder="Email address" required value="{{Input::old('email')}}" />
                    </div>
                    @else
                    <div class="form-group">
                        <input type="email" name="email" class="form-control" placeholder="Email address" required value="{{Input::old('email')}}" />
                    </div>
                    @endif

                    @if($errors->has('password'))
                    <div class="form-group has-error">
                        <label class="control-label" for="password">{{ $errors->first('password') }}</label>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required />
                        <div id="password_crack_result" class="label label-danger"></div>
                    </div>
                    @else
                    <div class="form-group">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" required />
                        <div id="password_crack_result" class="label label-danger"></div>
                    </div>
                    @endif

                    <div class="form-group">
                        <input type="password" name="password_confirmation" class="form-control" placeholder="Retype Password" required />
                    </div>

                    @if($errors->has('bf3pid'))
                    <div class="form-group has-error">
                        <label class="control-label" for="bf3pid">{{ $errors->first('bf3pid') }}</label>
                        <input type="text" name="bf3pid" class="form-control" placeholder="BF3 Player Name" value="{{Input::old('bf3pid')}}" />
                    </div>
                    @else
                    <div class="form-group">
                        <input type="text" name="bf3pid" class="form-control" placeholder="BF3 Player Name" value="{{Input::old('bf3pid')}}" />
                    </div>
                    @endif

                    @if($errors->has('bf4pid'))
                    <div class="form-group has-error">
                        <label class="control-label" for="bf4pid">{{ $errors->first('bf4pid') }}</label>
                        <input type="text" name="bf4pid" class="form-control" placeholder="BF4 Player Name" value="{{Input::old('bf4pid')}}" />
                    </div>
                    @else
                    <div class="form-group">
                        <input type="text" name="bf4pid" class="form-control" placeholder="BF4 Player Name" value="{{Input::old('bf4pid')}}" />
                    </div>
                    @endif

                    <div class="form-group">
                        {{ Form::select('timezone', Helper::generateTimezoneList(), 'UTC', array('class' => 'form-control')) }}
                    </div>

                    <div class="from-group">
                        {{ Form::select('lang', array('en' => 'English', 'de' => 'German'), 'en', array('class' => 'form-control')) }}
                    </div>

                </div>
                <div class="footer">
                    <button type="submit" class="btn bg-olive btn-block">Create Account</button>

                    <a href="{{ action('ADKGamers\\Webadmin\\Controllers\\UserController@showSignIn') }}">Already have an account? Sign in here!</a>
                </div>
            {{ Form::close() }}
        </div>


        <!-- jQuery 2.0.2 -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
        <!-- Bootstrap -->
        <script src="{{ asset('js/bootstrap.min.js') }}" type="text/javascript"></script>

        <script src="{{ asset('js/zxcvbn.js') }}" type="text/javascript"></script>

        <script type="text/javascript">
        $("#password").keyup(function(){var textValue=$(this).val();var result=zxcvbn(textValue);var div=$("#password_crack_result");div.show().html("Time to crack: "+result.crack_time_display);if(result.score>=0&&result.score<=1){div.addClass("label-danger");div.removeClass("label-primary");div.removeClass("label-success")}else if(result.score>1&&result.score<=3){div.removeClass("label-danger");div.addClass("label-primary");div.removeClass("label-success")}else if(result.score>3){div.removeClass("label-danger");
div.removeClass("label-primary");div.addClass("label-success")}});
        </script>

    </body>
</html>

<!DOCTYPE html>
<html>
    <head>
        <base href="/">
        <meta charset="UTF-8">
        <title>{{{ MainHelper::getTitle('Login', Config::get('bfacp.site.title')) }}}</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="//cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.0/animate.min.css" rel="stylesheet" type="text/css" />
        {{ HTML::style('css/ionicons.min.css') }}
        {{ HTML::style('css/style.min.css?v=1') }}
        {{ HTML::style('css/iCheck/all.css') }}
        {{ HTML::style('css/animate.css') }}

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>

    <body class="login-page">

        <div class="login-box">
            <div class="login-logo">
                <a href="{{ route('home') }}"><strong>BFAdmin</strong>CP</a>
            </div>

            @foreach(Session::get('messages', []) as $message)
            <div class="row">
                <div class="col-xs-12">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>&nbsp;{{ $message }}
                    </div>
                </div>
            </div>
            @endforeach

            <div class="login-box-body">
                @if(Session::has('error'))
                <div class="alert alert-error">
                    {{ HTML::faicon('fa-times') }} {{ Session::get('error') }}
                </div>
                @endif

                {{ Former::vertical_open()
                    ->method('POST')
                    ->route('user.login.post')
                    ->rules([
                        'username' => 'required',
                        'password' => 'required'
                    ]) }}

                    {{ Former::text('username')->placeholder('Username') }}
                    {{ Former::password('password')->placeholder('Password') }}

                    <div class="row">
                        <div class="col-xs-8">
                            <div class="checkbox icheck">
                                <label>
                                    {{ Form::checkbox('remember') }} Remember Me
                                </label>
                            </div>
                        </div>
                        <div class="col-xs-4">
                            {{ Former::primary_flat_block_submit('Sign In') }}
                        </div>
                    </div>
                {{ Former::close() }}

                @if(Config::get('bfacp.site.registration'))
                {{ link_to_route('user.register', 'Create an account', [], ['class' => 'text-center', 'target' => '_self']) }}
                @endif
            </div>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
        {{ HTML::script('js/plugins/iCheck/icheck.min.js') }}

        <script type="text/javascript">
            $(function() {
                $('input').iCheck({
                    checkboxClass: 'icheckbox_flat-blue',
                    radioClass: 'iradio_flat-blue'
                });
            });
        </script>
    </body>
</html>

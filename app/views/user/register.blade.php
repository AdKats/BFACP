<!DOCTYPE html>
<html>
    <head>
        <base href="/">
        <meta charset="UTF-8">
        <title>{{{ MainHelper::getTitle('Register', Config::get('bfacp.site.title')) }}}</title>
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

    <body class="register-page">

        <div class="register-box">
            <div class="register-logo">
                <a href="{{ route('home') }}"><strong>BFAdmin</strong>CP</a>
            </div>

            <div class="login-box-body">
                {{ Former::vertical_open()
                    ->method('POST')
                    ->route('user.register.post') }}

                    {{ Former::text('username')->placeholder('Username')->help('Username must be 4 or more characters') }}
                    {{ Former::email('email')->placeholder('Email') }}
                    {{ Former::password('password')->placeholder('Password')->help('Password must be 8 or more characters') }}
                    {{ Former::password('password_confirmation')->placeholder('Retype password') }}

                    <hr>

                    {{ Former::text('ign')->placeholder('In-Game Name') }}
                    {{ Former::select('lang')->options(Config::get('bfacp.site.languages'), 'en') }}

                    <div class="row">
                        <div class="col-xs-12">
                            {{ Former::primary_flat_block_submit('Create Account') }}
                        </div>
                    </div>
                {{ Former::close() }}

                {{ link_to_route('user.login', 'I already have an account', [], ['class' => 'text-center', 'target' => '_self']) }}
            </div>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
        {{ HTML::script('js/plugins/iCheck/icheck.min.js') }}
        {{ HTML::script('js/plugins/zxcvbn/zxcvbn.js') }}

        <script type="text/javascript">
            $(function() {
                $('input').iCheck({
                    checkboxClass: 'icheckbox_flat-blue',
                    radioClass: 'iradio_flat-blue'
                });

                var username              = $('input[name="username"]');
                var email                 = $('input[name="email"]');
                var password              = $('input[name="password"]');
                var password_confirmation = $('input[name="password_confirmation"]');
                var ign                   = $('input[name="ign"]');

                username.on('keyup keypress blue change', function(event) {
                    var val = $(this).val();
                    var parent = $(this).parent();

                    if(val.length < 4) {
                        if(!parent.hasClass('has-error')) {
                            parent.removeClass('has-success').addClass('has-error');
                            parent.prepend('<label class="control-label"><i class="fa fa-times-circle-o"></i>&nbsp;Username is too short</label>');
                        }
                    } else {
                        if(parent.hasClass('has-error')) {
                            parent.removeClass('has-error').addClass('has-success');
                            parent.find('label').remove();
                        }
                    }
                });

                password.on('keyup keypress blue change', function(event) {
                    var val = $(this).val();
                    var parent = $(this).parent();
                    var crackResult = zxcvbn(val);
                    var crackTime = crackResult.crack_time_display;
                    var crackScore = crackResult.score;

                    if(parent.find('label').length === 0) {
                        parent.prepend('<label class="control-label"><i class="fa"></li>&nbsp;<span></span></label>');
                    }

                    var label = parent.find('label');

                    if(crackScore == 0) {
                        parent.removeClass('has-warning').removeClass('has-success').addClass('has-error');
                        if(!label.find('i').hasClass('fa-times-circle-o')) {
                            label.find('i').removeClass('fa-check-circle-o').removeClass('fa-exclamation-circle').addClass('fa-times-circle-o');
                        }
                    } else if(crackScore >= 1 && crackScore <= 3) {
                        parent.removeClass('has-error').removeClass('has-success').addClass('has-warning');
                        if(!label.find('i').hasClass('fa-exclamation-circle')) {
                            label.find('i').removeClass('fa-check-circle-o').removeClass('fa-times-circle-o').addClass('fa-exclamation-circle');
                        }
                    } else if(crackScore > 3) {
                        parent.removeClass('has-error').removeClass('has-warning').addClass('has-success');
                        if(!label.find('i').hasClass('fa-check-circle-o')) {
                            label.find('i').removeClass('fa-exclamation-circle').removeClass('fa-times-circle-o').addClass('fa-check-circle-o');
                        }
                    }

                    if(crackScore == 0) {
                        label.find('span').text('Cracked: ' + crackTime);
                    } else {
                        label.find('span').text('Cracked in: ' + crackTime);
                    }
                });
            });
        </script>
    </body>
</html>

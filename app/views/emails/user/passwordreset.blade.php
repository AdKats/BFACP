<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Password Reset | BFAdminCP</title>
        <style type="text/css">
        <!-- bootstrap 3.0.2 -->
        <?php echo file_get_contents(public_path() . '/css/bootstrap.min.css'); ?>
        <!-- font Awesome -->
        <?php echo file_get_contents(public_path() . '/css/font-awesome.min.css'); ?>
        <!-- Theme style -->
        <?php echo file_get_contents(public_path() . '/css/AdminLTE.css'); ?>
        </style>
    </head>
    <body class="bg-black">
        <div class="form-box">
            <div class="header">{{ Lang::get('confide::confide.email.password_reset.subject') }}</div>
            <div class="body bg-gray">
                <p>{{ Lang::get('confide::confide.email.password_reset.greetings', array( 'name' => $user->username)) }},</p>
                <p>{{ Lang::get('confide::confide.email.password_reset.body') }}</p>
                <a href="{{ action('ADKGamers\\Webadmin\\Controllers\\UserController@showResetPassword', array($token)) }}">
                    {{ action('ADKGamers\\Webadmin\\Controllers\\UserController@showResetPassword', array($token)) }}
                </a>
            </div>
        </div>
    </body>
</html>

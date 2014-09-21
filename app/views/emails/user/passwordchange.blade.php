<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>Password Changed | BFAdminCP</title>
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
            <div class="header">Password Has Been Changed</div>
            <div class="body bg-gray">
                <p>{{ Lang::get('confide::confide.email.password_reset.greetings', array( 'name' => $user->username)) }},</p>
                <p>Your password has been changed by the Administrator. Your new password is <span class="label label-success">{{ $newpassword }}</span>.</p>
            </div>
        </div>
    </body>
</html>

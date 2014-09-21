<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>New User Registration | BFAdminCP</title>
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
            <div class="header">New User Registration</div>
            <div class="body bg-gray">
                <p>Hello {{$admin->username}},</p>
                <p>This email is to notify you that {{$user->username}} has just signed up on the BFAdminCP. You can view {{$user->username}}'s profile {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\AccountController@showUserProfile', 'here', [$user->id]) }}.</p>
            </div>
        </div>
    </body>
</html>

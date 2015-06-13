<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Requirements Not Met | BFAdminCP</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        {{ HTML::style('css/ionicons.min.css') }}
        {{ HTML::style('css/style.css?v=1') }}
        {{ HTML::style('css/_all-skins.min.css?v=1') }}

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="skin-blue sidebar-collapse sidebar-mini">
        <div class="wrapper">
            <div class="content-wrapper">
                <section class="content">
                    <div class="error-page">
                        <div class="error-content">
                            <h3>
                                <?php if (version_compare(phpversion(), '5.4.0', '<')): ?>
                                <i class="fa fa-warning text-danger"></i> <small>You are running <?php echo phpversion();?></small>
                                <?php else: ?>
                                <i class="fa fa-check text-success"></i>
                                <?php endif;?>
                                PHP 5.4 or Higher
                            </h3>
                            <h3>
                                <?php if (!extension_loaded('mcrypt')): ?>
                                <i class="fa fa-warning text-danger"></i>
                                <?php else: ?>
                                <i class="fa fa-check text-success"></i>
                                <?php endif;?>
                                Mcrypt Installed and Enabled
                            </h3>
                            <h3>
                                <?php if (!extension_loaded('pdo')): ?>
                                <i class="fa fa-warning text-danger"></i>
                                <?php else: ?>
                                <i class="fa fa-check text-success"></i>
                                <?php endif;?>
                                PDO Driver Installed and Enabled
                            </h3>
                        </div>
                    </div>
                </section>
            </div>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
        {{ HTML::script('js/boot.js?v=1') }}
    </body>
</html>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Requirements Not Met | BFAdminCP</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
        <link href="../../css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="../../css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="../../css/ionicons.min.css" rel="stylesheet" type="text/css" />
        <link href="../../css/AdminLTE.css" rel="stylesheet" type="text/css" />
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="skin-black">
        <div class="wrapper">
            <section class="content">
                <div class="error-page">
                    <div class="error-content">
                        <h3>
                            <?php if(version_compare(phpversion(), '5.4.0', '<')) : ?>
                            <i class="fa fa-warning text-danger"></i> <small>You are running <?php echo phpversion(); ?></small>
                            <?php else : ?>
                            <i class="fa fa-check text-success"></i>
                            <?php endif; ?>
                            PHP 5.4 or Higher
                        </h3>
                        <h3>
                            <?php if(!extension_loaded("mcrypt")) : ?>
                            <i class="fa fa-warning text-danger"></i>
                            <?php else : ?>
                            <i class="fa fa-check text-success"></i>
                            <?php endif; ?>
                            Mcrypt Installed and Enabled
                        </h3>
                        <h3>
                            <?php if(!extension_loaded("pdo")) : ?>
                            <i class="fa fa-warning text-danger"></i>
                            <?php else : ?>
                            <i class="fa fa-check text-success"></i>
                            <?php endif; ?>
                            PDO Driver Installed and Enabled
                        </h3>
                    </div>
                </div>
            </section>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
        <script src="../../js/bootstrap.min.js" type="text/javascript"></script>
    </body>
</html>

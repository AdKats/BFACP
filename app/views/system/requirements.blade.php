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
                        <div class="error-content" style="margin-left: 0px">
                            <h4>
                                <?php if (version_compare(phpversion(), $required_php_version, '<')): ?>
                                <i class="fa fa-warning text-danger"></i> Server is running PHP version <?php echo phpversion();?>, 5.5 or higher required.
                                <?php else: ?>
                                <i class="fa fa-check text-success"></i> Server is running PHP version <?php echo phpversion();?> and satisfies PHP requirement.
                                <?php endif;?>
                            </h4>
                            <h4>
                                <?php if (!extension_loaded('mcrypt')): ?>
                                <i class="fa fa-warning text-danger"></i> Mcrypt is either not Installed or not enabled.
                                <?php else: ?>
                                <i class="fa fa-check text-success"></i> Mcrypt installed and enabled.
                                <?php endif;?>
                            </h4>
                            <h4>
                                <?php if (!extension_loaded('pdo')): ?>
                                <i class="fa fa-warning text-danger"></i> PDO Driver either not installed or not enabled.
                                <?php else: ?>
                                <i class="fa fa-check text-success"></i> PDO Driver installed and enabled.
                                <?php endif;?>
                            </h4>
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

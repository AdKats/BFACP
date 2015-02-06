<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Fatal Error</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <?php echo HTML::style('css/ionicons.min.css'); ?>

        <?php echo HTML::style('css/style.css?v=1'); ?>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>

        <?php echo HTML::script('js/boot.js?v=1'); ?>

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>

    <body class="skin-blue">
        <div class="wrapper">

            <div class="content-wrapper">
                <section class="content-header">
                    <h1>Fatal Error</h1>
                </section>

                <section class="content">

                <div class="error-page">
                    <h2 class="headline text-red"><?php echo $exception->getCode(); ?></h2>
                    <div class="error-content">
                        <h3><i class="fa fa-warning text-red"></i> Oops! Something went wrong.</h3>

                        <p>
                            A fatal error occured and application could not continue. Please report this incident to the site administrator and give them the following error message.
                        </p>

                        <samp><?php echo $exception->getMessage(); ?></samp>
                    </div>
                </div>

                </section>
            </div>

            <footer class="main-footer">
                <div class="pull-right hidden-xs">
                    <b>Version</b> 2.0
                </div>
                <strong>Copyright &copy; 2013-<?php echo date('Y'); ?> <a href="http://www.adkgamers.com" target="_blank">A Different Kind, LLC</a>. All rights reserved.</strong>
            </footer>
        </div>
    </body>
</html>

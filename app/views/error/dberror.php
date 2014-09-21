<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Database Error | BFAdminCP</title>
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
                    <h2 class="headline"><?php echo $errcode; ?></h2>
                    <div class="error-content">
                        <h3><i class="fa fa-warning text-yellow"></i> <?php echo $errusrmsg; ?></h3>
                        <p>
                            <?php echo $errusrmsg2; ?>
                        </p>

                    </div>
                </div>
            </section>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
        <script src="../../js/bootstrap.min.js" type="text/javascript"></script>
    </body>
</html>

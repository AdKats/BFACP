<!DOCTYPE html>
<html>
    <head>
        <base href="{{ URL::to('/') }}" />
        <title>{{{ $title or 'No Title' }}} | BFAdminCP</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1' name='viewport'>
        <meta charset="UTF-8">
        <meta name="googlebot" content="noarchive">
        <meta http-equiv="cache-control" content="no-store">
        <meta name="copyright" content="&copy; 2013-{{date('y')}} A Different Kind, LLC">
        <meta name="publisher" content="A Different Kind, LLC">
        <meta name="distribution" content="global">
        <meta name="robots" content="index,follow">
        <meta name="author" content="Prophet731, ADKGamers">
        @yield('headermeta')

        <!-- bootstrap 3.0.2 -->
        <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- font Awesome -->
        <link href="{{ asset('css/font-awesome.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- Ionicons -->
        <link href="{{ asset('css/ionicons.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- Theme style -->
        <link href="{{ asset('css/AdminLTE.min.css') }}" rel="stylesheet" type="text/css" />
        <!-- daterange picker -->
        <link href="{{ asset('css/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet" type="text/css" />
        <!-- Animate -->
        <link href="{{ asset('css/animate.css') }}" rel="stylesheet" type="text/css" />
        <!-- Bootstrap Select -->
        <link href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.3.5/bootstrap-select.min.css" rel="stylesheet" type="text/css" />
        <!-- Toaster -->
        <link href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet" type="text/css" />
        @yield('stylesinclude')

        <script src="{{ asset('js/jquery-1.11.0.min.js') }}" ></script>
        <script src="{{ asset('js/angular.min.js') }}"></script>
        <script src="{{ asset('js/angular-animate.js') }}"></script>
        <script src="{{ asset('js/bootstrap.min.js') }}"></script>
        <script src="{{ asset('js/plugins/morris/morris.min.js') }}"></script>
        <script src="{{ asset('js/plugins/checklist-model/checklist-model.js') }}"></script>
        <script src="{{ asset('js/plugins/twitter/typeahead/typeahead.bundle.js') }}"></script>
        <script src="{{ asset('js/plugins/moment/moment-with-langs.min.js') }}"></script>
        <script src="{{ asset('js/plugins/livestamp/livestamp.min.js') }}"></script>
        <script src="{{ asset('js/plugins/daterangepicker/daterangepicker.js') }}"></script>
        <script src="{{ asset('js/plugins/bootstrapui-angularjs/ui-bootstrap-custom-tpls-0.10.0.js') }}"></script>
        <script src="{{ asset('js/plugins/highcharts/highcharts.js') }}"></script>
        <script src="{{ asset('js/plugins/highcharts/highcharts-more.js') }}"></script>
        <script src="{{ asset('js/plugins/highcharts/modules/solid-gauge.js') }}"></script>
        <script src="{{ asset('js/plugins/jquery-titlealert/jquery.titlealert.js') }}"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.3.5/bootstrap-select.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        <script src="{{ asset('js/AdminLTE/app.js') }}"></script>
        <script src="{{ asset('js/BFAdminCP/main.js') }}"></script>
        @yield('jsinclude')


        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <body class="skin-black fixed" ng-app="BFAdminCP">
        <header class="header">
            <a href="{{ action('ADKGamers\\Webadmin\\Controllers\\PublicController@showIndex') }}" class="logo">BFAdminCP</a>

            <nav class="navbar navbar-static-top" role="navigation">
                <!-- Sidebar toggle button-->
                <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </a>

                @include('layout.navbar')

            </nav>
        </header>
        <div class="wrapper row-offcanvas row-offcanvas-left">

            @include('layout.sidebar')

            <aside class="right-side">
                <section class="content-header">
                    <h1>{{{ $title or 'No Title' }}}</h1>
                </section>

                <!-- Main content -->
                <section class="content">
                    @yield('content')
                </section><!-- /.content -->

                <footer class="footer">
                    <div class="pull-right copyright">
                        <a href="{{route('copyrightNotice')}}">&copy; 2013-{{date('Y')}} A Different Kind, LLC</a> - <small>v{{ Config::get('webadmin.VERSION') }}</small>
                    </div>
                </footer>

            </aside>
        </div>



        @yield('javascript')

        @yield('modal_content')

        @if(Request::is("acp/*") === FALSE)
        <script>
          (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
          (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
          m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
          })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

          ga('create', 'UA-38778073-4', 'auto');
          ga('send', 'pageview');

        </script>
        @endif
    </body>
</html>

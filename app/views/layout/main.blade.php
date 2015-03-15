<!DOCTYPE html>
<html ng-app="bfacp">
    <head>
        <base href="/">
        <meta charset="UTF-8">
        <title>{{{ MainHelper::getTitle($page_title, NULL) }}}</title>
        <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

        @yield('meta')

        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="//cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.0/animate.min.css" rel="stylesheet" type="text/css" />

        {{ HTML::style('css/ionicons.min.css') }}

        {{ HTML::style('css/style.min.css?v=1') }}
        {{ HTML::style('css/skin-blue.min.css?v=1') }}
        {{ HTML::style('css/custom.css?v=1') }}
        {{ HTML::style('css/iCheck/all.css') }}
        {{ HTML::style('css/daterangepicker/daterangepicker-bs3.css') }}
        {{ HTML::style('css/timepicker/bootstrap-timepicker.min.css') }}
        {{ HTML::style('css/animate.css') }}

        @yield('styles')

        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>

    <body class="skin-blue">
        <div class="wrapper">
            <header class="main-header">
                {{ link_to_route('home', MainHelper::getTitle($page_title, NULL, TRUE), [], ['class' => 'logo', 'target' => '_self']) }}

                <nav class="navbar navbar-static-top" role="navigation">
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>

                    @include('layout.navbar')
                </nav>
            </header>

            @include('layout.sidebar')

            <div class="content-wrapper">
                <section class="content-header">
                    <h1>{{{ $page_title }}}</h1>
                </section>

                <section class="content">
                    @yield('content')
                </section>
            </div>

            <footer class="main-footer">
                <div class="pull-right hidden-xs">
                    <strong>{{ Lang::get('common.version') }}</strong> {{ BFACP_VERSION }}
                </div>
                <strong>&copy; 2013-{{ date('Y') }} <a href="http://www.adkgamers.com" target="_blank">A Different Kind, LLC</a>. All rights reserved.</strong> <em>{{ MainHelper::executionTime(true) }}</em>
            </footer>
        </div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
        <script src="//code.jquery.com/ui/1.11.2/jquery-ui.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.9/angular.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.9/angular-messages.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.9/angular-loader.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.9/angular-animate.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.9/angular-resource.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.9/angular-aria.min.js"></script>
        <script src="//ajax.googleapis.com/ajax/libs/angularjs/1.3.9/angular-sanitize.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/jstimezonedetect/1.0.4/jstz.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.9.0/moment-with-locales.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/0.12.0/ui-bootstrap-tpls.min.js"></script>
        {{ HTML::script('js/plugins/fastclick/fastclick.min.js') }}
        {{ HTML::script('js/plugins/angular-modules/count-to/count-to.js') }}
        {{ HTML::script('js/plugins/highcharts/highcharts.js') }}
        {{ HTML::script('js/plugins/iCheck/icheck.min.js') }}
        {{ HTML::script('js/boot.js?v=1') }}
        <script type="text/javascript">
            @if(Config::get('app.locale') != 'en')
            var lang = "{{ Config::get('app.locale') }}";
            @else
            var lang = null;
            @endif
            moment.locale( lang || navigator.language.split('-')[0] );
            $.widget.bridge('uibutton', $.ui.button);
        </script>
        {{ HTML::script('js/app.js?v=1') }}

        @yield('scripts')
    </body>
</html>

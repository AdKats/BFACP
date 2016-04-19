<!DOCTYPE html>
<html ng-app="bfacp">
    <head>
        <base href="/" target="_self">
        <meta charset="UTF-8">
        <title>{{ MainHelper::getTitle(isset($page_title) ? $page_title : false, Config::get('bfacp.site.title')) }}</title>
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
        @yield('meta')
        <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="//cdnjs.cloudflare.com/ajax/libs/animate.css/3.2.0/animate.min.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
        {{ HTML::style('css/ionicons.min.css') }}
        {{ HTML::style('css/style.css?v=1') }}
        {{ HTML::style('css/_all-skins.min.css?v=1') }}
        {{ HTML::style('css/custom.css?v=1') }}
        {{ HTML::style('css/iCheck/all.css') }}
        {{ HTML::style('css/daterangepicker/daterangepicker-bs3.css') }}
        {{ HTML::style('css/timepicker/bootstrap-timepicker.min.css') }}
        {{ HTML::style('css/animate.css') }}
        {{ HTML::style('css/ng-table/ng-table.min.css') }}
        @yield('styles')
        @yield('header-scripts')
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
          <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>

    <body class="skin-blue sidebar-mini fixed">
        <div class="wrapper">
            <header class="main-header">

                <a href="{{ route('home') }}" class="logo" target="_self">
                    <span class="logo-mini">ACP</span>
                    <span class="logo-lg">{{ MainHelper::getTitle(isset($page_title) ? $page_title : false, Config::get('bfacp.site.title'), true) }}</span>
                </a>

                <nav class="navbar navbar-static-top" role="navigation">
                    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                        <span class="sr-only">Toggle navigation</span>
                    </a>

                    @include('layout.navbar')
                </nav>
            </header>

            @include('layout.sidebar')

            <div class="content-wrapper">
                <section class="content-header">
                    <h1>{{ (isset($page_title) ? $page_title : false) }}</h1>

                    {{ Breadcrumbs::renderIfExists() }}
                </section>

                <section class="content">
                    @if(isset($appdown) && $appdown)
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle animated infinite flash"></i>
                        Maintenance Mode Enabled
                    </div>
                    @endif

                    @foreach(Session::get('messages', []) as $message)
                    <div class="row">
                        <div class="col-xs-12">
                            <alert type="info">
                                <i class="fa fa-info-circle"></i>&nbsp;{{ $message }}
                            </alert>
                        </div>
                    </div>
                    @endforeach

                    @foreach(Session::get('warnings', []) as $message)
                    <div class="row">
                        <div class="col-xs-12">
                            <alert type="warning">
                                <i class="fa fa-exclamation-circle"></i>&nbsp;{{ $message }}
                            </alert>
                        </div>
                    </div>
                    @endforeach

                    @foreach($errors->all() as $message)
                    <div class="row">
                        <div class="col-xs-12">
                            <alert type="error">
                                <i class="fa fa-times"></i>&nbsp;{{ $message }}
                            </alert>
                        </div>
                    </div>
                    @endforeach

                    @yield('content')
                </section>
            </div>

            <footer class="main-footer">
                <div class="pull-right hidden-xs">
                    <strong>{{ Lang::get('common.version') }}</strong> {{ BFACP_VERSION }}
                </div>
                <strong>&copy; 2013-{{ date('Y') }} <a href="http://www.adkgamers.com" target="_blank">A Different Kind, LLC</a>. All rights reserved.</strong> <em>{{ MainHelper::executionTime(true) }}</em>
            </footer>

            @if($bfacp->isLoggedIn && $bfacp->user->ability(null, ['admin.site.pusher.users.view', 'admin.site.pusher.chat.view']) && !empty(getenv('PUSHER_APP_KEY')))
            <!-- Control Sidebar -->
            <aside class="control-sidebar control-sidebar-dark">
                <!-- Create the tabs -->
                <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
                    <li><a href="#control-sidebar-users-tab" data-toggle="tab"><i class="fa fa-users"></i></a></li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    <!-- Home tab content -->
                    <div class="tab-pane active" id="control-sidebar-users-tab" ng-controller="PusherChatController">
                        @if($bfacp->user->ability(null, 'admin.site.pusher.users.view'))
                        <h3 class="control-sidebar-heading">{{ Lang::get('common.right_sidebar.online_users') }} (<span ng-bind="members.online"></span>)</h3>
                        <ul class="control-sidebar-menu" id="sidebar-users">
                            <li ng-repeat="member in members.list track by member.id">
                                <a href="javascript://">
                                    <img ng-src="@{{ member.avatar }}" class="img-circle menu-icon" style="max-width: 40px">

                                    <div class="menu-info">
                                        <h4 class="control-sidebar-subheading" ng-bind="member.username"></h4>

                                        <p ng-bind="member.role"></p>
                                    </div>
                                </a>
                            </li>
                        </ul>
                        <!-- /.control-sidebar-menu -->
                        @endif

                        @if($bfacp->user->ability(null, 'admin.site.pusher.chat.view'))
                        <h3 class="control-sidebar-heading">{{ Lang::get('common.right_sidebar.chat_room') }} <span class="badge" ng-bind="connectionState" ng-class="connStateClass"></span> </h3>
                        <ul class="control-sidebar-menu">
                            <li>
                                <div class="direct-chat-messages" id="sidebar-chat">
                                    <div class="direct-chat-msg" ng-repeat="msg in messages | orderBy:'timestamp':true track by $index">
                                        <div class="direct-chat-info clearfix">
                                            <span class="direct-chat-name pull-left" ng-bind="msg.user.username"></span>
                                            <span class="direct-chat-timestamp pull-right" ng-bind="moment(msg.timestamp).fromNow()"></span>
                                        </div>

                                        <img ng-src="@{{ msg.user.avatar }}" class="direct-chat-img">
                                        <div class="direct-chat-text" ng-bind="msg.text"></div>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="input-group">
                                    <input type="text" ng-enter="sendMessage()" ng-model="chat.message" class="form-control" ng-disabled="chat.input">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-warning btn-flat" ng-click="sendMessage()" ng-disabled="chat.input">
                                            <ng-switch on="chat.input">
                                                <span ng-switch-when="true"><i class="fa fa-cog fa-spin"></i> Sending...</span>
                                                <span ng-switch-default>Send</span>
                                            </ng-switch>
                                        </button>
                                    </span>
                                </div>
                            </li>
                        </ul>
                        @endif
                    </div>
                    <!-- /.tab-pane -->
                </div>
            </aside>
            <!-- /.control-sidebar -->
            <!-- This div must placed right after the sidebar for it to work-->
            <div class="control-sidebar-bg"></div>
            @endif
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
        <script src="//cdnjs.cloudflare.com/ajax/libs/angular-ui-bootstrap/0.14.3/ui-bootstrap-tpls.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
        <script src="//cdnjs.cloudflare.com/ajax/libs/zeroclipboard/2.1.6/ZeroClipboard.min.js"></script>
        <script src="//js.pusher.com/3.0/pusher.min.js"></script>
        {{ HTML::script('js/plugins/fastclick/fastclick.min.js') }}
        {{ HTML::script('js/plugins/angular-modules/count-to/count-to.js') }}
        {{ HTML::script('js/plugins/angular-modules/ng-idle/angular-idle.min.js') }}
        {{ HTML::script('js/plugins/angular-modules/ng-table/ng-table.min.js') }}
        {{ HTML::script('js/plugins/angular-modules/ng-clip/ng-clip.min.js') }}
        {{ HTML::script('js/plugins/highcharts/highcharts.js') }}
        {{ HTML::script('js/plugins/highcharts/modules/no-data-to-display.js') }}
        {{ HTML::script('js/plugins/iCheck/icheck.min.js') }}
        {{ HTML::script('js/plugins/howler/howler.min.js') }}
        {{ HTML::script('js/plugins/slimScroll/jquery.slimscroll.min.js') }}
        {{ HTML::script('js/boot.js?v=1') }}
        @if($bfacp->isLoggedIn && $bfacp->user->ability(null, ['admin.site.pusher.users.view', 'admin.site.pusher.chat.view']) && !empty(getenv('PUSHER_APP_KEY')))
        <script type="text/javascript">
            var pusher = new Pusher('{{ getenv('PUSHER_APP_KEY') }}', {
                authEndpoint: '/api/pusher/auth'
            });

            $('#site-navbar').append('<li><a href="#" data-toggle="control-sidebar" tooltip="Toggle the sidebar"><i class="fa fa-gears"></i></a></li>');

            @if($bfacp->user->ability(null, 'admin.site.pusher.users.view'))
            $('#sidebar-users').slimScroll({
                height: '250px',
                alwaysVisible: true
            });
            @endif

            @if($bfacp->user->ability(null, 'admin.site.pusher.chat.view'))
            $('#sidebar-chat').slimScroll({
                height: '350px',
                alwaysVisible: true
            });
            @endif
        </script>
        @endif
        {{ Minify::javascript(array_merge(
            ['/js/app.js'],
            MainHelper::files(public_path() . '/js/factorys', true, '/js/factorys/'),
            MainHelper::files(public_path() . '/js/services', true, '/js/services/'),
            MainHelper::files(public_path() . '/js/controllers', true, '/js/controllers/')
        )) }}
        <script type="text/javascript">
            @if($bfacp->isLoggedIn)
            var lang = "{{ Config::get('app.locale') }}";
            @else
            var lang = null;
            @endif
            moment.locale( lang || navigator.language.split('-')[0] );
            $.widget.bridge('uibutton', $.ui.button);
            Highcharts.setOptions({
                lang: {
                    noData: "No data available."
                }
            });
        </script>
        @yield('scripts')
    </body>
</html>

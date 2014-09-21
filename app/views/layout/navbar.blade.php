<div class="navbar-right">
    <ul class="nav navbar-nav">

        @if(Auth::check())

        @if(Entrust::can('view_reports') && Confide::user()->preferences->report_notify_alert)
            @include('angular.layout.reports-navbar')
            {{ Form::hidden('site_report_enable_sound', Confide::user()->preferences->report_notify_sound) }}
            {{ Form::hidden('site_report_sound_file', Confide::user()->preferences->report_notify_sound_file) }}
        @endif

        <!-- User Account: style can be found in dropdown.less -->
        <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="glyphicon glyphicon-user"></i>
                <span>{{{ Confide::user()->username }}} <i class="caret"></i></span>
            </a>
            <ul class="dropdown-menu">
                <!-- User image -->
                <li class="user-header bg-black">
                    <img src="{{ Helper::getGravatar( (Auth::check() ? Confide::user()->preferences->gravatar : '') ) }}" class="img-circle" alt="" />
                    <p>
                        {{{ Confide::user()->group() }}}
                        <small>{{ Lang::get('general.user.member_since') }} {{ Confide::user()->created_at->format('M Y') }}</small>
                    </p>
                </li>
                <!-- Menu Body -->
                <li class="user-body">
                    <div class="col-xs-6 text-center">
                        @if(!is_null(Confide::user()->preferences->bf3_playerid))
                        <a href="{{ action('ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo', array(Confide::user()->preferences->bf3_playerid, ADKGamers\Webadmin\Models\Battlefield\Player::find(Confide::user()->preferences->bf3_playerid)->SoldierName)) }}">
                            <img src="{{ asset('img/bf3-icon.png') }}"  data-toggle="tooltip" data-placement="bottom" title="BF3 {{ Lang::get('general.user.profile') }}">
                        </a>
                        @endif
                    </div>
                    <div class="col-xs-6 text-center">
                        @if(!is_null(Confide::user()->preferences->bf4_playerid))
                        <a href="{{ action('ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo', array(Confide::user()->preferences->bf4_playerid, ADKGamers\Webadmin\Models\Battlefield\Player::find(Confide::user()->preferences->bf4_playerid)->SoldierName)) }}">
                            <img src="{{ asset('img/bf4-icon.png') }}" data-toggle="tooltip" data-placement="bottom" title="BF4 {{ Lang::get('general.user.profile') }}">
                        </a>
                        @endif
                    </div>
                </li>
                <!-- Menu Footer-->
                <li class="user-footer">
                    <div class="pull-left">
                        <a href="{{ action('ADKGamers\\Webadmin\\Controllers\\AccountController@showUserProfile', [Confide::user()->id]) }}" class="btn btn-default btn-flat">{{ Lang::get('general.user.profile') }}</a>
                        <a href="{{ action('ADKGamers\\Webadmin\\Controllers\\AccountController@showAccountSettings') }}" class="btn btn-default btn-flat">{{ Lang::get('general.user.account') }}</a>
                    </div>
                    <div class="pull-right">
                        <a href="{{ action('ADKGamers\\Webadmin\\Controllers\\UserController@logout') }}" class="btn btn-default btn-flat">
                            <i class="fa fa-fw fa-sign-out"></i>
                            <span>{{ Lang::get('general.user.signout') }}</span>
                        </a>
                    </div>
                </li>
            </ul>
        </li>
        @else
        <li class="dropdown user user-menu">
            <a href="{{ action('ADKGamers\\Webadmin\\Controllers\\UserController@showSignIn') }}" class="dropdown-toggle">
                <i class="fa fa-fw fa-sign-in"></i>
                <span>{{ Lang::get('general.user.signin') }}</span>
            </a>
        </li>
        @endif

    </ul>
</div>

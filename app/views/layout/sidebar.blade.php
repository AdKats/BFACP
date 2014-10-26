<aside class="left-side sidebar-offcanvas">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel">
            <div class="pull-left image">
                <img src="{{ Helper::getGravatar( (Auth::check() ? Confide::user()->preferences->gravatar : '') ) }}" class="img-circle" alt="User Avatar" />
            </div>
            <div class="pull-left info">
                @if(Auth::check())
                <p>{{{ Confide::user()->username }}}</p>
                <a href="{{ action('ADKGamers\\Webadmin\\Controllers\\UserController@logout') }}">
                    <i class="fa fa-fw fa-sign-out"></i>
                    <span>{{ Lang::get('general.user.signout') }}</span>
                </a>
                @else
                <p>Hello, Guest</p>
                <a href="{{ action('ADKGamers\\Webadmin\\Controllers\\UserController@showSignIn') }}">
                    <i class="fa fa-fw fa-sign-in"></i>
                    <span>{{ Lang::get('general.user.signin') }}</span>
                </a>
                @endif
            </div>
        </div>

        {{ Form::open(array('method' => 'GET', 'action' => 'ADKGamers\\Webadmin\\Controllers\\PublicController@searchForPlayer', 'class' => 'sidebar-form')) }}
            <div class="input-group">
                <input type="text" name="player" class="form-control typeahead" placeholder="Search for player..." value="{{Input::get('player', NULL)}}" required />
                <span class="input-group-btn">
                    <button type='submit' id='search-btn' class="btn btn-flat"><i class="fa fa-search"></i></button>
                </span>
            </div>
        {{ Form::close() }}

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu">
            <li <?php echo (Request::segment(1) == '' ? 'class="active"' : NULL); ?>>
                <a href="{{ action('ADKGamers\\Webadmin\\Controllers\\PublicController@showIndex') }}">
                    <i class="glyphicon glyphicon-dashboard"></i> <span>{{ Lang::get('navigation.public.dashboard') }}</span>
                </a>
            </li>
            <li <?php echo (Request::segment(1) == 'chatlogs' ? 'class="active"' : NULL); ?>>
                <a href="{{ action('ADKGamers\\Webadmin\\Controllers\\ChatlogController@showChatSearch') }}">
                    <i class="fa fa-comments-o"></i> <span>{{ Lang::get('navigation.public.chatsearch') }}</span>
                </a>
            </li>
            <li <?php echo (Request::segment(1) == 'scoreboard' ? 'class="active"' : NULL); ?>>
                <a href="/scoreboard">
                    <i class="fa fa-gamepad"></i> <span>{{ Lang::get('navigation.public.scoreboard') }}</span>
                </a>
            </li>
            <li <?php echo (Request::segment(1) == 'stats' ? 'class="active"' : NULL); ?>>
                <a href="/stats">
                    <i class="glyphicon glyphicon-stats"></i> <span>{{ Lang::get('navigation.public.statistics') }}</span>
                </a>
            </li>
            <li <?php echo (Request::segment(1) == 'memberlist' ? 'class="active"' : NULL); ?>>
                <a href="/memberlist">
                    <i class="fa fa-users"></i> <span>{{ Lang::get('navigation.public.memberlist') }}</span>
                </a>
            </li>
            <li class="treeview <?php echo (Request::segment(1) == 'leaderboard' ? 'active' : NULL); ?>">
                <a href="#">
                    <i class="fa fa-folder"></i> <span>Leaderboards</span>
                    <i class="fa fa-angle-left pull-right"></i>

                    <ul class="treeview-menu">
                        <li <?php echo (Request::segment(2) == 'reputation' ? 'class="active"' : NULL); ?>>
                            <a href="/leaderboard/reputation">
                                <i class="fa fa-angle-double-right"></i> {{ Lang::get('navigation.public.leaderboards.reputation') }}
                            </a>
                        </li>
                        <li <?php echo (Request::segment(2) == 'playerstats' ? 'class="active"' : NULL); ?>>
                            <a href="/leaderboard/playerstats">
                                <i class="fa fa-angle-double-right"></i> {{ Lang::get('navigation.public.leaderboards.playerstats') }}
                            </a>
                        </li>
                    </ul>
                </a>
            </li>
            @if(Helper::hasPerm( [ 'manage_adkats_roles_perms', 'manage_adkats_users', 'manage_adkats_bans' ] ) )
            <li class="treeview <?php echo (Request::segment(2) == 'adkats' ? 'active' : NULL); ?>">
                <a href="#">
                    <i class="fa fa-folder"></i> <span>AdKats Management</span>
                    <i class="fa fa-angle-left pull-right"></i>

                    <ul class="treeview-menu">
                        @if(Entrust::can('manage_adkats_users'))
                        <li <?php echo (Request::segment(2) == 'adkats' && Request::segment(3) == 'user' ? 'class="active"' : NULL); ?>>
                            <a href="/acp/adkats/user">
                                <i class="fa fa-angle-double-right"></i> {{ Lang::get('navigation.adkats_management.user') }}
                            </a>
                        </li>
                        @endif
                        @if(Entrust::can('manage_adkats_roles_perms'))
                        <li <?php echo (Request::segment(2) == 'adkats' && Request::segment(3) == 'role' ? 'class="active"' : NULL); ?>>
                            <a href="/acp/adkats/role">
                                <i class="fa fa-angle-double-right"></i> {{ Lang::get('navigation.adkats_management.role') }}
                            </a>
                        </li>
                        @endif
                        @if(Entrust::can('manage_adkats_bans'))
                        <li <?php echo (Request::segment(2) == 'adkats' && Request::segment(3) == 'ban' ? 'class="active"' : NULL); ?>>
                            <a href="/acp/adkats/ban">
                                <i class="fa fa-angle-double-right"></i> {{ Lang::get('navigation.adkats_management.ban') }}
                            </a>
                        </li>
                        @endif
                    </ul>
                </a>
            </li>
            @endif

            @if( Helper::hasPerm( [ 'manage_site_users', 'manage_site_roles_perms', 'manage_site_settings', 'acp_info_database', 'acp_manage_game' ] ) )
            <li class="treeview <?php echo (Request::segment(2) == 'site' ? 'active' : NULL); ?>">
                <a href="#">
                    <i class="fa fa-folder"></i> <span>Site Management</span>
                    <i class="fa fa-angle-left pull-right"></i>

                    <ul class="treeview-menu">
                        @if(Entrust::can('manage_site_users'))
                        <li <?php echo (Request::segment(2) == 'site' && Request::segment(3) == 'user' ? 'class="active"' : NULL); ?>>
                            <a href="/acp/site/user">
                                <i class="fa fa-angle-double-right"></i> {{ Lang::get('navigation.site_management.user') }}
                            </a>
                        </li>
                        @endif

                        @if(Entrust::can('manage_site_roles_perms'))
                        <li <?php echo (Request::segment(2) == 'site' && Request::segment(3) == 'role' ? 'class="active"' : NULL); ?>>
                            <a href="/acp/site/role">
                                <i class="fa fa-angle-double-right"></i> {{ Lang::get('navigation.site_management.role') }}
                            </a>
                        </li>
                        @endif

                        @if(Entrust::can('manage_site_settings'))
                        <li <?php echo (Request::segment(2) == 'site' && Request::segment(3) == 'setting' ? 'class="active"' : NULL); ?>>
                            <a href="/acp/site/setting">
                                <i class="fa fa-angle-double-right"></i> {{ Lang::get('navigation.site_management.setting') }}
                            </a>
                        </li>
                        @endif

                        @if(Entrust::can('acp_manage_game'))
                        <li <?php echo (Request::segment(2) == 'site' && Request::segment(3) == 'gameserver' ? 'class="active"' : NULL); ?>>
                            <a href="/acp/site/gameserver">
                                <i class="fa fa-angle-double-right"></i> {{ Lang::get('navigation.site_management.game.setting') }}
                            </a>
                        </li>
                        @endif

                        @if(Entrust::can('acp_info_database'))
                        <li <?php echo (Request::segment(2) == 'site' && Request::segment(3) == 'info' && Request::segment(4) == 'database' ? 'class="active"' : NULL); ?>>
                            <a href="/acp/site/info/database">
                                <i class="fa fa-angle-double-right"></i> {{ Lang::get('navigation.site_management.info.database') }}
                            </a>
                        </li>
                        @endif

                    </ul>
                </a>
            </li>
            @endif
        </ul>
    </section>
    <!-- /.sidebar -->
</aside>

<div class="navbar-custom-menu">
    <ul class="nav navbar-nav">
        @if(Auth::check())
        <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="glyphicon glyphicon-user"></i>

                <span>{{{ $user->username }}} <i class="caret"></i></span>
            </a>
            <ul class="dropdown-menu">
                <li class="user-header bg-black">
                    {{ HTML::image($user->gravatar, NULL, ['class' => 'img-circle']) }}
                    <p>
                        {{{ $user->roles[0]->name }}}
                        <small>Member since {{ $user->created_at->format('M. Y') }}</small>
                    </p>
                </li>

                <li class="user-body">
                    @if($user->setting->bf3player)
                    <div class="col-xs-6 text-center">
                        <a href="{{ $user->setting->bf3player->profile_url }}">
                            {{ sprintf("[%s] %s", 
                            $user->setting->bf3player->game->Name,
                            $user->setting->bf3player->SoldierName) }}
                        </a>
                    </div>
                    @endif
                    @if($user->setting->bf4player)
                    <div class="col-xs-6 text-center">
                        <a href="{{ $user->setting->bf4player->profile_url }}">
                            {{ sprintf("[%s] %s", 
                            $user->setting->bf4player->game->Name,
                            $user->setting->bf4player->SoldierName) }}
                        </a>
                    </div>
                    @endif
                </li>

                <li class="user-footer">
                    <div class="pull-left">
                        {{ HTML::link('/profile', 'Profile', ['class' => 'btn btn-default btn-flat']) }}
                        {{ HTML::link('/account', 'Account', ['class' => 'btn btn-default btn-flat']) }}
                    </div>

                    <div class="pull-right">
                        {{ HTML::link('/logout', 'Logout', ['class' => 'btn btn-default btn-flat']) }}
                    </div>
                </li>
            </ul>
        </li>
        @else
        <li>
            <a href="{{ URL::to('/login') }}">
                <i class="fa fa-fw fa-sign-in"></i>
                <span>Login</span>
            </a>
        </li>
        @endif
    </ul>
</div>
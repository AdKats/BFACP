<div class="navbar-custom-menu">
    <ul class="nav navbar-nav">
        @if(Auth::check())
        <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <i class="glyphicon glyphicon-user"></i>

                <span>{{{ $user->username }}} <i class="caret"></i></span>
            </a>
            <ul class="dropdown-menu">
                <li class="user-header bg-light-blue">
                    {{ HTML::image($user->gravatar, NULL, ['class' => 'img-circle']) }}
                    <p>
                        {{{ $user->roles[0]->name }}}
                        <small>Member since <span ng-bind="moment('{{ $user->created_at->toIso8601String() }}').format('MMMM YYYY')"></span></small>
                    </p>
                </li>

                <li class="user-body">
                    @forelse($user->soldiers as $soldier)
                    <div class="col-xs-4">
                    {{ link_to_route('player.show', $soldier->player->game->Name, [
                        $soldier->player->PlayerID,
                        $soldier->player->SoldierName
                    ], ['target' => '_self', 'class' => $soldier->player->game->class_css, 'style' => 'color: white !important', 'tooltip' => $soldier->player->SoldierName]) }}
                    </div>
                    @empty
                    <alert type="info">
                        {{ HTML::faicon('fa-info-circle') }} No soldiers found.
                    </alert>
                    @endforelse
                </li>

                <li class="user-footer">
                    <div class="pull-left">
                        {{ HTML::link('/profile', 'Profile', ['class' => 'btn btn-default btn-flat']) }}
                        {{ HTML::link('/account', 'Account', ['class' => 'btn btn-default btn-flat']) }}
                    </div>

                    <div class="pull-right">
                        {{ link_to_route('user.logout', 'Logout', [], ['class' => 'btn btn-default btn-flat', 'target' => '_self']) }}
                    </div>
                </li>
            </ul>
        </li>
        @else
        <li>
            <a href="{{ route('user.login') }}" target="_self">
                {{ HTML::faicon('fa-sign-in') }}
                <span>Login</span>
            </a>
        </li>
        @endif
    </ul>
</div>

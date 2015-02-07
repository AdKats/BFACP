<aside class="left-side">
    <section class="sidebar">
        <div class="user-panel">
            @if(Auth::check())
            <div class="pull-left image">
                {{ HTML::image($user->gravatar, NULL, ['class' => 'img-circle']) }}
            </div>
            <div class="pull-left info">
                <p>{{{ $user->username }}}</p>

                <a href="{{ URL::to('/logout') }}">
                    <i class="fa fa-fw fa-sign-out"></i>
                    <span>Logout</span>
                </a>
            </div>
            @else
            <div class="pull-left image">
                {{ HTML::image("https://www.gravatar.com/avatar/", NULL, ['class' => 'img-circle']) }}
            </div>
            <div class="pull-left info">
                <p>Hello, Guest</p>
                <a href="{{ URL::to('/login') }}">
                    <i class="fa fa-fw fa-sign-in"></i>
                    <span>Login</span>
                </a>
            </div>
            @endif
        </div>

        {{ Form::open(['route' => 'player.listing', 'class' => 'sidebar-form', 'method' => 'GET']) }}
            <div class="input-group">
                {{ Former::text('player')
                        ->placeholder('Search for player...')
                        ->class('form-control') }}
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-flat">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
        {{ Form::close() }}

        @include('layout.navigation')
    </section>
</aside>

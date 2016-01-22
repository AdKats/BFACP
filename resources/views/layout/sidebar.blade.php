<aside class="main-sidebar">
    <section class="sidebar">
        <div class="user-panel">
            @if($bfacp->isLoggedIn)
                <div class="pull-left image">
                    {!! Html::image($bfacp->user->gravatar, null, ['class' => 'img-circle']) !!}
                </div>
                <div class="pull-left info">
                    <p>{{ $bfacp->user->username }}</p>

                    <a href="{{ route('user.logout') }}" target="_self">
                        {!! Macros::faicon('fa-sign-out') !!}
                        <span>{{ trans('common.logout') }}</span>
                    </a>
                </div>
            @else
                <div class="pull-left image">
                    {!! Html::image("https://www.gravatar.com/avatar/", null, ['class' => 'img-circle']) !!}
                </div>
                <div class="pull-left info">
                    <p>Hello, Guest</p>
                    <a href="{{ route('user.login') }}" target="_self">
                        {!! Macros::faicon('fa-sign-in') !!}
                        <span>{{ trans('common.login') }}</span>
                    </a>
                </div>
            @endif
        </div>

        {!! Former::open()->method('GET')
                ->route('player.listing')
                ->type('raw')
                ->class('sidebar-form')
                ->id('psearch') !!}
            <div class="input-group">
                <input type="text" class="form-control" name="player" id="player" @if(\Illuminate\Support\Facades\Input::has('player')) value="{{ old('player') }}" @endif placeholder="{{ trans('common.nav.extras.psearch.placeholder') }}">
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-flat">
                        <i class="fa fa-search"></i>
                    </button>
                </span>
            </div>
        {!! Former::close() !!}

        @include('layout.navigation')
    </section>
</aside>

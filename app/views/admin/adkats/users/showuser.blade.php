@section('content')

<div class="row">

    @if(Session::has('message'))
        <div class="col-md-12">
            <div class="alert alert-success alert-dismissable">
                <i class="fa fa-check"></i>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <b>Success!</b> {{ Session::get('message') }}
            </div>
        </div>
    @endif

    @if(Session::has('errors'))
        <div class="col-md-12">
            @foreach($errors->all() as $error)
            <div class="alert alert-danger alert-dismissable">
                <i class="fa fa-ban"></i>
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <b>Error!</b> {{ $error }}
            </div>
            @endforeach
        </div>
    @endif

    <div class="col-md-12">

        <p><strong>Expiration</strong>: {{ $user->user_expiration->timezone($user_tz)->toDayDateTimeString() }}
        <p><strong>Assigned Group</strong>: {{ $user->role->role_name }}</p>
        <p><strong>Notes</strong>: {{ $user->user_notes }}</p>
        <p>
            <strong>Soldiers</strong>:
            <ul>
                @forelse($user->soldiers as $soldier)
                <li>[{{ $soldier->player->gameIdent() }}] {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo', $soldier->player->SoldierName, [$soldier->player->PlayerID, $soldier->player->SoldierName]) }}
                @empty
                <p>No soldiers</p>
                @endforelse
            </ul>
        </p>

        <p>
            {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\UserController@edit', 'Edit User', [$user->user_id], ['class' => 'btn btn-primary']) }}
            {{ link_to_route('adkat_acc_del_confirm', 'Delete User', [$user->user_id], ['class' => 'btn btn-danger']) }}
            {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\UserController@index', 'Back To Userlist', [], ['class' => 'btn bg-olive']) }}
        </p>
    </div>

</div>

@stop

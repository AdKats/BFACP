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

    <div class="col-md-12">
        @foreach($errors->all() as $error)
            <div class="alert alert-danger">
                <i class="fa fa-ban"></i>
                <b>Error!</b> {{ $error }}
            </div>
        @endforeach

        <div class="box box-primary">
            {{ Form::open(array('action' => array('ADKGamers\\Webadmin\\Controllers\\Admin\\UserController@update', $user->id), 'method' => 'put', 'class' => 'form-horizontal')) }}
                <div class="box-body">
                    <div class="form-group">
                        {{ Form::label('username', 'Username', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::text('username', $user->username, ['class' => 'form-control']) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('email', 'Email', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::email('email', $user->email, ['class' => 'form-control', 'autocomplete' => 'off']) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('password', 'Password', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-5">
                            {{ Form::password('password', ['class' => 'form-control', 'autocomplete' => 'off']) }}
                            <p class="help-block">Leave blank to keep current password</p>
                        </div>
                        {{ Form::label('password_confirmation', 'Confirm Password', ['class' => 'col-sm-2 control-label']) }}
                        <div class="col-sm-4">
                            {{ Form::password('password_confirmation', ['class' => 'form-control', 'autocomplete' => 'off']) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('account_status', 'Membership', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::radio('account_status', 1, ($user->confirmed ? TRUE: FALSE)) }} Active
                            {{ Form::radio('account_status', 0, (!$user->confirmed ? TRUE: FALSE)) }} Disabled
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('timezone', 'Timezone', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::select('timezone', Helper::generateTimezoneList(), $user->preferences->timezone, array('class' => 'form-control selectpicker', 'data-live-search' => 'true')) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('lang', 'Language', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::select('lang', array('en' => 'English', 'de' => 'German'), $user->preferences->lang, array('class' => 'form-control')) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('role', 'Role', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::select('role', $rolelist, $user->roleId(), array('class' => 'form-control')) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('bf3_player_id', 'BF3 PID', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::text('bf3_player_id', $user->preferences->bf3_playerid, ['class' => 'form-control']) }}
                            <p class="help-block">Battlefield 3 Database Player ID</p>
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('bf4_player_id', 'BF4 PID', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::text('bf4_player_id', $user->preferences->bf4_playerid, ['class' => 'form-control']) }}
                            <p class="help-block">Battlefield 4 Database Player ID</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-1 control-label">Soldiers</label>
                        <ul class="col-sm-11">
                            @forelse($soldiers as $p)
                            <li>[{{ $p['gameIdent'] }}] [{{ $p['soldier']->PlayerID }}] {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo', $p['soldier']->SoldierName, [$p['soldier']->PlayerID, $p['soldier']->SoldierName], ['target' => '_blank']) }}
                            @empty
                            <p>No soldiers</p>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="box-footer clearfix">
                    {{ Form::submit('Update User', array('class' => 'btn btn-primary')) }}
                    {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\UserController@index', 'Cancel Update', [], ['class' => 'btn bg-olive']) }}
                    {{ link_to_route('site_acc_del_confirm', 'Delete User', [$user->id], ['class' => 'btn btn-danger']) }}
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@stop

@section('javascript')
<script type="text/javascript">
$('.selectpicker').selectpicker();
</script>
@stop

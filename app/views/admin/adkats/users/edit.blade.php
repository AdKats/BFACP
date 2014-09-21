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
            {{ Form::model($user, array('action' => array('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\UserController@update', $user->user_id), 'method' => 'put')) }}
                <div class="box-body">
                    <div class="form-group">
                        {{ Form::label('user_name', 'Username') }} <span class="label label-danger pull-right">Required</span>
                        <div class="input-group">
                            <span class="input-group-addon">@</span>
                            {{ Form::text('user_name', $user->user_name, ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Username']) }}
                        </div>
                        <p class="help-block">The user name to be associated with the player</p>
                    </div>

                    <div class="form-group">
                        {{ Form::label('user_email', 'E-Mail Address') }}
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                            {{ Form::email('user_email', $user->user_email, ['class' => 'form-control', 'placeholder' => 'example@example.com']) }}
                        </div>
                        <p class="help-block">Email address that admin reports should be sent to. Can be left blank to not send emails.</p>
                    </div>

                    <div class="form-group">
                        {{ Form::label('user_role', 'User Role') }} <span class="label label-danger pull-right">Required</span>
                        {{ Form::select('user_role', $rolelist, $user->user_role, ['class' => 'form-control']) }}
                    </div>

                    <div class="form-group">
                        {{ Form::label('user_notes', 'Notes (1000 characters max)') }}
                        {{ Form::text('user_notes', $user->user_notes, ['class' => 'form-control', 'maxlength' => 1000]) }}
                    </div>

                    <div class="form-group">
                        {{ Form::label('soldiers', 'Soldiers') }} <span class="label label-danger pull-right">Required</span>
                        {{ Form::text('soldiers', $soldiers, ['class' => 'form-control', 'placeholder' => '1234, 22345, 123', 'required' => 'required']) }}
                        <p class="help-block">Seprate multiple soldiers player id by a comma (,). You can find their player id on their player profile.</p>
                    </div>

                    <div class="form-group">
                        <strong>Associated Soldiers</strong>:
                        <ul>
                            @forelse($user->soldiers as $soldier)
                            <li>[{{ $soldier->player->gameIdent() }}] [{{$soldier->player->PlayerID}}] {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo', $soldier->player->SoldierName, [$soldier->player->PlayerID, $soldier->player->SoldierName], ['target' => '_blank']) }}
                            @empty
                            <p>No soldiers</p>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="box-footer clearfix">
                    {{ Form::submit('Update User', array('class' => 'btn btn-primary')) }}
                    {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\UserController@show', 'Cancel Update', [$user->user_id], ['class' => 'btn btn-primary']) }}
                    {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\UserController@index', 'Back To Userlist', [], ['class' => 'btn bg-olive']) }}
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

@stop

@section('javascript')
<script type="text/javascript">

$(function() {
    $('#expireDate').inputmask("mm/dd/yyyy", {
        "placeholder": "mm/dd/yyyy"
    });

    $('.timepicker').timepicker({
        showInputs: false,
        minuteStep: 10,
        defaultTime: '12:00 AM'
    });
});

</script>
@stop

@section('stylesinclude')
<link href="{{ asset('css/timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('jsinclude')
<script src="{{ asset('js/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>

<script src="{{ asset('js/plugins/input-mask/jquery.inputmask.js') }}"></script>
<script src="{{ asset('js/plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
<script src="{{ asset('js/plugins/input-mask/jquery.inputmask.extensions.js') }}"></script>
@stop

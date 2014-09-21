@section('content')

<div class="row">

    <div class="col-md-12">
        @foreach($errors->all() as $error)
            <div class="alert alert-danger">
                <i class="fa fa-ban"></i>
                <b>Error!</b> {{ $error }}
            </div>
        @endforeach

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Add User</h3>
            </div>

            {{ Form::open(array('action' => 'ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\UserController@store')) }}
                <div class="box-body">
                    <div class="form-group">
                        {{ Form::label('username', 'Username') }} <span class="label label-danger pull-right">Required</span>
                        <div class="input-group">
                            <span class="input-group-addon">@</span>
                            {{ Form::text('username', Session::get('username', NULL), ['class' => 'form-control', 'required' => 'required', 'placeholder' => 'Username']) }}
                        </div>
                        <p class="help-block">The user name to be associated with the player</p>
                    </div>

                    <div class="form-group">
                        {{ Form::label('email', 'E-Mail Address') }}
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-envelope"></i></span>
                            {{ Form::email('email', Session::get('email', NULL), ['class' => 'form-control', 'placeholder' => 'example@example.com']) }}
                        </div>
                        <p class="help-block">Email address that admin reports should be sent to. Can be left blank to not send emails.</p>
                    </div>

                    <div class="form-group">
                        {{ Form::label('role', 'User Role') }} <span class="label label-danger pull-right">Required</span>
                        {{ Form::select('role', $rolelist, Session::get('role', 1), ['class' => 'form-control']) }}
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-6">
                                {{ Form::label('expireDate', 'Expire Date') }}
                                {{ Form::text('expireDate', Session::get('expireDate', NULL), ['class' => 'form-control']) }}
                            </div>
                            <div class="col-xs-6">
                                {{ Form::label('expireTime', 'Expire Time') }}
                                <div class="bootstrap-timepicker">
                                    <div class="input-group">
                                        <input type="text" class="form-control timepicker" name="expireTime" id="expireTime" value="{{ Session::get('expireTime', NULL) }}" />
                                        <div class="input-group-addon">
                                            <i class="fa fa-clock-o"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        {{ Form::hidden('expireDefault', strtotime($expireDate)) }}
                        <p class="help-block">If no date is provided it will default to {{ $expireDate->timezone($user_tz)->toDayDateTimeString() }}</p>
                    </div>

                    <div class="form-group">
                        {{ Form::label('notes', 'Notes (1000 characters max)') }}
                        {{ Form::text('notes', Session::get('notes', NULL), ['class' => 'form-control', 'maxlength' => 1000]) }}
                    </div>

                    <div class="form-group">
                        {{ Form::label('soldiers', 'Soldiers') }}
                        {{ Form::text('soldiers', Session::get('soldiers', NULL), ['class' => 'form-control', 'placeholder' => '1234, 22345, 123']) }}
                        <p class="help-block">Seprate multiple soldiers player id by a comma (,). You can find their player id on their player profile.</p>
                    </div>
                </div>

                <div class="box-footer clearfix">
                    {{ Form::submit('Create User', array('class' => 'btn btn-primary')) }}
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

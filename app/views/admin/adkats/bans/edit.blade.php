@section('content')

<div class="row">

    <div class="col-md-12">
        <div class="alert alert-info">
            <i class="fa fa-info"></i>
            Bans issued from here will not kick the player out of the server if they are currently in it. Use the ban options from the live scoreboard to issue the ban and remove the player from the server.
        </div>
    </div>

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
            {{ Form::open(array('action' => array('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\BanController@update', $ban->ban_id), 'method' => 'put', 'class' => 'form-horizontal')) }}
            {{ Form::hidden('_gameID', $ban->GameID) }}
            {{ Form::hidden('_gameName', $ban->Name) }}
                <div class="box-body">

                    <div class="form-group">
                        {{ Form::label('target_name', 'Player', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::text('target_name', $ban->target_name, ['class' => 'form-control', 'readonly' => 'readonly']) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('source_name', 'Admin', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::text('source_name', $ban->source_name, ['class' => 'form-control', 'readonly' => 'readonly']) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('ban_reason', 'Reason', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::text('ban_reason', $ban->record_message, ['class' => 'form-control', 'maxlength' => 500]) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('ban_notes', 'Notes', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::text('ban_notes', $ban->ban_notes, ['class' => 'form-control', 'maxlength' => 150]) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('ban_server', 'Server', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::select('ban_server', $_servers, $ban->server_id, ['class' => 'form-control']) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('ban_start_date', 'Start Date', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-5">
                            {{ Form::text('ban_start_date', Helper::UTCToLocal($ban->ban_startTime)->format('m/d/Y'), ['class' => 'form-control']) }}
                        </div>
                        {{ Form::label('ban_start_time', 'Start Time', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-5 bootstrap-timepicker">
                            {{ Form::text('ban_start_time', Helper::UTCToLocal($ban->ban_startTime)->format('g:i A'), ['class' => 'form-control timepicker']) }}
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('ban_end_date', 'End Date', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-5">
                            {{ Form::text('ban_end_date', Helper::UTCToLocal($ban->ban_endTime)->format('m/d/Y'), ['class' => 'form-control']) }}
                        </div>
                        {{ Form::label('ban_end_time', 'End Time', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-5 bootstrap-timepicker">
                            {{ Form::text('ban_end_time', Helper::UTCToLocal($ban->ban_endTime)->format('g:i A'), ['class' => 'form-control timepicker']) }}
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-1 control-label">Ban Range</label>
                        <div class="col-sm-11">
                            <div id="ban_range">
                                <i class="fa fa-calendar fa-lg"></i>&nbsp;
                                <span></span> <b class="caret"></b>
                            </div>
                            <p class="help-block">Use the ban range to do quick date selections</p>
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('ban_status', 'Status', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::radio('ban_status', 'Active', ($ban->ban_status == 'Active' ? TRUE: FALSE)) }} Active
                            {{ Form::radio('ban_status', 'Disabled',  ($ban->ban_status == 'Disabled' || $ban->ban_status == 'Expired' ? TRUE: FALSE)) }} Disabled
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('ban_type', 'Type', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::radio('ban_type', 8, ($ban->command_action == 8 ? TRUE: FALSE)) }} Perm
                            {{ Form::radio('ban_type', 7,  ($ban->command_action == 7 ? TRUE: FALSE)) }} Temp
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('ban_enforceName', 'Enforce by Name', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::radio('ban_enforceName', 'Y', ($ban->ban_enforceName == 'Y' ? TRUE: FALSE)) }} Yes
                            {{ Form::radio('ban_enforceName', 'N',  ($ban->ban_enforceName == 'N' ? TRUE: FALSE)) }} No
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('ban_enforceGUID', 'Enforce by GUID', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::radio('ban_enforceGUID', 'Y', ($ban->ban_enforceGUID == 'Y' ? TRUE: FALSE)) }} Yes
                            {{ Form::radio('ban_enforceGUID', 'N',  ($ban->ban_enforceGUID == 'N' ? TRUE: FALSE)) }} No
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('ban_enforceIP', 'Enforce by IP', ['class' => 'col-sm-1 control-label']) }}
                        <div class="col-sm-11">
                            {{ Form::radio('ban_enforceIP', 'Y', ($ban->ban_enforceIP == 'Y' ? TRUE: FALSE)) }} Yes
                            {{ Form::radio('ban_enforceIP', 'N',  ($ban->ban_enforceIP == 'N' ? TRUE: FALSE)) }} No
                        </div>
                    </div>
                </div>

                <div class="box-footer clearfix">
                    {{ Form::submit('Update Ban', array('class' => 'btn btn-primary')) }}
                    {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\Admin\\AdKats\\BanController@index', 'Back To Banlist', [], ['class' => 'btn bg-olive']) }}
                    {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo', 'Back To Player Profile', [$ban->target_id, $ban->target_name], ['class' => 'btn bg-navy']) }}
                </div>
            {{ Form::close() }}
        </div>
    </div>
</div>

@stop

@section('javascript')
<script type="text/javascript">
$(function() {
    $('#ban_start_date, #ban_end_date').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        format: 'MM/DD/YYYY',
        minDate: moment(),
        startDate: moment()
    });

    $('#ban_range span').html(moment().format('MMMM D, YYYY') + ' - ' + moment().format('MMMM D, YYYY'));

    $("#ban_range").daterangepicker({
        ranges: {
            'Today': [moment(), moment()],
            '1 Week': [moment(), moment().add(7, 'd')],
            '2 Weeks': [moment(), moment().add(2, 'w')],
            '3 Weeks': [moment(), moment().add(3, 'w')],
            '1 Month': [moment(), moment().add(1, 'M')],
            '2 Months': [moment(), moment().add(2, 'M')],
            '3 Months': [moment(), moment().add(3, 'M')]
        },
        startDate: moment(),
        endDate: moment(),
        minDate: moment().subtract(7, 'd')
    }, function(start, end) {
        $('#ban_range span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        $("#ban_start_date").val(start.format('MM/DD/YYYY'));
        $("#ban_end_date").val(end.format('MM/DD/YYYY'));
    });

    $('.timepicker').timepicker({
        showInputs: false,
        minuteStep: 5,
        defaultTime: moment().format('h:mm A')
    });


    if($("input[name=ban_type]:checked").val() == 8) {
        $("#ban_start_date, #ban_start_time, #ban_end_date, #ban_end_time, #ban_range").parent().parent().hide();
    }

    $("input[name=ban_type]:radio").on('ifChecked', function() {
        var bantype = this.value;

        if(bantype == 8) {
            $("#ban_start_date, #ban_start_time, #ban_end_date, #ban_end_time, #ban_range").parent().parent().hide();
        } else if(bantype == 7) {
            $("#ban_start_date, #ban_start_time, #ban_end_date, #ban_end_time, #ban_range").parent().parent().show();
        }
    });
});
</script>
@stop

@section('stylesinclude')
<link href="{{ asset('css/timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('css/daterangepicker/daterangepicker-bs3.css') }}" rel="stylesheet" type="text/css" />
@stop

@section('jsinclude')
<script src="{{ asset('js/plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
<script src="{{ asset('js/plugins/input-mask/jquery.inputmask.js') }}"></script>
<script src="{{ asset('js/plugins/input-mask/jquery.inputmask.date.extensions.js') }}"></script>
<script src="{{ asset('js/plugins/input-mask/jquery.inputmask.extensions.js') }}"></script>
<script src="{{ asset('js/plugins/daterangepicker/daterangepicker.js') }}"></script>
@stop

@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-xs-12 col-md-4">
        <div class="box box-primary">
            <div class="box-body">

                <div class="form-group">
                    <label>Server</label>
                    <select class="form-control">
                        <option value="-1" selected>Select Server...</option>
                        @foreach($games as $game)
                        <optgroup label="{{ $game->Name }}">
                            @foreach($game->servers as $server)
                            <option value="{{ $server->ServerID }}">{{{ $server->ServerName }}}</option>
                            @endforeach
                        </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Players</label>
                    {{ Former::text('players') }}
                    <p class="help-block">Seperate multiple players by a comma (,). Partal names accepted.</p>
                </div>

                <div class="form-group">
                    <label>Keywords</label>
                    {{ Former::text('keywords') }}
                    <p class="help-block">
                        Seperate multiple keywords by a comma (,).
                    </p>
                </div>

                <div class="form-group">
                    <label>Date &amp; Time Range</label>
                    <div class="input-group">
                        <div class="input-group-addon">
                            <i class="fa fa-clock-o"></i>
                        </div>
                        {{ Former::text('between')
                            ->addClass('pull-right') }}
                    </div>
                    <p class="help-block" id="between-display"></p>
                </div>

                <div class="form-group">
                    <div class="checkbox">
                        {{ Former::checkbox('nospam')->text('Exclude Server &amp; Commo Rose Spam') }}
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="col-xs-12 col-md-8">

    </div>
</div>
@stop

@section('scripts')
{{ HTML::script('js/plugins/daterangepicker/daterangepicker.js') }}
{{ HTML::script('js/plugins/timepicker/bootstrap-timepicker.min.js') }}
<script type="text/javascript">
    $('#between').daterangepicker({
        ranges: {
            'Today': [ moment(), moment() ],
            'Yesterday': [ moment().subtract(1, 'days'), moment().subtract(1, 'days') ],
            'Last 7 Days': [ moment().subtract(6, 'days'), moment() ],
            'Last 30 Days': [ moment().subtract(29, 'days'), moment() ],
            'This Month': [ moment().startOf('month'), moment().endOf('month') ],
            'Last Month': [ moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('Month') ]
        },
        startDate: moment().subtract(29, 'days'),
        endDate: moment()
    }, function(start, end) {
        $('#between-display').html(start.format('lll') + ' - ' + end.format('lll'));
    });

    $('input').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue',
        increaseArea: '-10%'
    });
</script>
@stop

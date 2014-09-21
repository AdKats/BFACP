@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="box box-primary">

            <!-- form start -->
            {{ Form::open(array('action' => 'ADKGamers\\Webadmin\\Controllers\\ChatlogController@showChatSearch', 'method' => 'get')) }}
                <div class="box-body">
                    <div class="form-group">
                        <label for="serverid">Select Server</label>
                        {{ Form::select('serverid', $serverlist, Input::get('serverid', NULL), array('class' => 'form-control', 'id' => 'serverid')) }}
                    </div>

                    <div class="form-group">
                        <label for="players">Players</label>
                        <input type="text" class="form-control" id="players" name="players" value="{{Input::get('players')}}" />
                        <p class="help-block">Seperate multiple players by a comma (,). Partal names accepted.<br>Example: Player1, Player2, Player3</p>
                    </div>

                    <div class="form-group">
                        <label for="keywords">Keywords</label>
                        <input type="text" class="form-control" id="keywords" name="keywords" value="{{Input::get('keywords')}}" />
                        <p class="help-block">Seperate multiple keywords by a comma (,).<br>Example: hackers, aimbot, need help</p>
                    </div>

                    <div class="form-group">
                        <label>Date and time range</label>
                        <div class="input-group">
                            <div class="input-group-addon">
                                <i class="fa fa-clock-o"></i>
                            </div>
                            <input type="text" class="form-control pull-right" name="daterange" id="daterange" value="{{ (Input::has('daterange') ? Input::get('daterange') : $startDateString ) }}" />
                        </div>
                        <p class="help-block">Without a date range only one month of logs will be returned</p>
                    </div>
                    <div class="checkbox">
                        <label>
                            {{ Form::checkbox('excludeServer', 1, Input::has('excludeServer') ?: false) }}
                            Ignore Server Spam
                        </label>
                    </div>
                    <div class="checkbox">
                        <label>
                            {{ Form::checkbox('excludeCommoRose', 1, Input::has('excludeCommoRose') ?: false) }}
                            Ignore Commo Rose Spam
                        </label>
                    </div>
                </div>

                <div class="box-footer">
                    <button type="submit" class="btn btn-primary" id="chat-search-btn" data-loading-text="Please wait...">Search</button>
                </div>
            {{ Form::close() }}
        </div>
    </div>
    @if(Input::has('serverid') && $results->count() > 0)
    <div class="col-md-9">
        <div class="box box-success">
            <div class="box-header">
                <div class="box-title">Results</div>
                <div class="box-tools pull-right">
                    {{$results->appends($appendString)->links('pagination::simple')}}
                </div>
            </div>

            <div class="box-body">
                <table class="table table-condensed table-striped">
                    <thead>
                        <th width="200px">Date</th>
                        <th>Player</th>
                        <th>Channel</th>
                        <th>Message</th>
                    </thead>

                    <tbody>
                        @foreach($results as $result)
                        <tr>
                            <td>{{ Helper::UTCToLocal($result->logDate, $_tz)->format('M j, Y g:ia T') }}</td>
                            <td>{{ (is_null($result->logPlayerID) ? $result->logSoldierName : link_to_action('ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo', $result->logSoldierName, [$result->logPlayerID, $result->logSoldierName])) }}</td>
                            <td>{{ $result->logSubset }}</td>
                            <td>{{ $result->logMessage }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="box-footer clearfix">
                <div class="pull-right">
                    {{$results->appends($appendString)->links('pagination::simple')}}
                </div>
            </div>
        </div>
    </div>
    @elseif(Input::has('serverid') && $results->count() == 0)
    <div class="col-md-9">
        <div class="box box-primary">
            <div class="box-body">
                <div class="callout callout-info">
                    <h4>No results found</h4>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@stop

@section('javascript')
<script type="text/javascript">

    <?php if(Auth::check()) : ?>
    $("#daterange").daterangepicker({
        ranges: {
            'Today': [moment().startOf('day'), moment()],
            'Yesterday': [moment().subtract(1, 'd').startOf('day'), moment().subtract(1, 'd')],
            'Last 7 Days': [moment().subtract(6, 'd').startOf('day'), moment()],
            'Last 30 Days': [moment().subtract(29, 'd').startOf('day'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: moment().subtract(29, 'd'),
        endDate: moment(),
        timePicker: true,
        timePickerIncrement: 1,
        format: 'MM/DD/YYYY h:mm A',
        opens: 'right'
    });
    <?php else : ?>
    $("#daterange").daterangepicker({
        ranges: {
            'Today': [moment().startOf('day').utc(), moment().utc()],
            'Yesterday': [moment().subtract(1, 'd').startOf('day').utc(), moment().subtract(1, 'd').utc()],
            'Last 7 Days': [moment().subtract(6, 'd').startOf('day').utc(), moment().utc()],
            'Last 30 Days': [moment().subtract(29, 'd').startOf('day').utc(), moment().utc()],
            'This Month': [moment().startOf('month').utc(), moment().endOf('month').utc()],
            'Last Month': [moment().subtract(1, 'month').startOf('month').utc(), moment().subtract(1, 'month').endOf('month').utc()]
        },
        startDate: moment().subtract(29, 'd').utc(),
        endDate: moment().utc(),
        timePicker: true,
        timePickerIncrement: 1,
        format: 'MM/DD/YYYY h:mm A',
        opens: 'right'
    });
    <?php endif; ?>

    $("#chat-search-btn").click(function()
    {
        var btn = $(this);
        btn.button('loading');
    });
</script>
@stop

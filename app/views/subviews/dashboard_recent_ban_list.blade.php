<div class="table-responsive">
    <table class="table table-condensed">
        <thead>
            <th>{{ Lang::get('dashboard.banfeed.table_header.col1') }}</th>
            <th>{{ Lang::get('dashboard.banfeed.table_header.col2') }}</th>
            <th>{{ Lang::get('dashboard.banfeed.table_header.col3') }}</th>
        </thead>

        <tbody>
            @foreach($bans as $ban)
            <tr>
                <td>
                    <span data-toggle="tooltip" data-placement="top" title="{{ $ban->record_message }}">
                        {{ link_to_action('ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo', $ban->target_name, [$ban->target_id, $ban->target_name]) }}
                    </span>
                </td>
                <td>{{ Helper::UTCToLocal($ban->ban_startTime, $user_timezone)->format('M j, Y g:ia T') }}</td>
                <td>
                    @if(Carbon::now( $user_timezone )->diffInYears($ban->ban_endTime) > 1)
                    <span class="label label-danger">Permanent Ban</span>
                    @else
                    <span data-toggle="tooltip" data-placement="top" title="{{ Helper::UTCToLocal($ban->ban_endTime, $user_timezone)->format('M j, Y g:ia T') }}">
                        {{ $ban->ban_endTime->diffForHumans() }}
                    </span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

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
                <td>{{ link_to_action('ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo', $ban->target_name, [$ban->target_id, $ban->target_name]) }}</td>
                <td>{{ Helper::UTCToLocal($ban->ban_startTime)->format('M j, Y g:ia T') }}</td>
                <td>
                    @if(Carbon::now( ( Auth::check() ? Auth::user()->preferences->timezone : 'UTC' ) )->diffInYears($ban->ban_endTime) > 1)
                    <span class="label label-danger">Permanent Ban</span>
                    @else
                    {{ $ban->ban_endTime->diffForHumans() }}
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

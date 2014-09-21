<div class="box box-info">
    <div class="box-header">
        <h3 class="box-title">{{ $block_title }}</h3>
    </div>

    <div class="box-body">
        <div class="table-responsive">
            <table class="table table-condensed">
                <thead>
                    <th width="20px">#</th>
                    <th>Player</th>
                    <th>Score</th>
                    <th>Kills</th>
                    <th>Deaths</th>
                    <th>Headshots</th>
                    <th>Rounds</th>
                    <th>Wins</th>
                    <th>Losses</th>
                    <th>Playtime</th>
                </thead>

                <tbody>
                    @foreach($stats as $key => $lb)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ link_to_action('ADKGamers\\Webadmin\\Controllers\\PlayerController@showInfo', $lb->SoldierName, [$lb->PlayerID, $lb->SoldierName]) }}</td>
                        <td>{{ number_format($lb->Score) }}</td>
                        <td>{{ number_format($lb->Kills) }}</td>
                        <td>{{ number_format($lb->Deaths) }}</td>
                        <td>{{ number_format($lb->Headshots) }}</td>
                        <td>{{ number_format($lb->Rounds) }}</td>
                        <td>{{ number_format($lb->Wins) }}</td>
                        <td>{{ number_format($lb->Losses) }}</td>
                        <td>{{ Helper::convertSecToStr($lb->Playtime) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

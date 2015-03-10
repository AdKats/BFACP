<div class="table-responsive">
    <table class="table table-striped table-condensed">
        <thead>
            <th>{{ Lang::get('player.profile.stats.server.table.col1') }}</th>
            <th>{{ Lang::get('player.profile.stats.server.table.col2') }}</th>
            <th>{{ Lang::get('player.profile.stats.server.table.col3') }}</th>
            <th>{{ Lang::get('player.profile.stats.server.table.col5') }}</th>
            <th class="hidden-sm hidden-md">{{ Lang::get('player.profile.stats.server.table.col6') }}</th>
            <th class="hidden-sm hidden-md">
                <span tooltip="Headshot Kill Ratio">{{ Lang::get('player.profile.stats.server.table.extra.hskr') }}</span>
            </th>
            <th>{{ Lang::get('player.profile.stats.server.table.col7') }}</th>
            <th class="hidden-sm hidden-md">
                <span tooltip="Kill/Death Ratio">{{ Lang::get('player.profile.stats.server.table.extra.kd') }}</span>
            </th>
            <th class="hidden-sm hidden-md">{{ Lang::get('player.profile.stats.server.table.col8') }}</th>
            <th class="hidden-sm hidden-md">{{ Lang::get('player.profile.stats.server.table.col9') }}</th>
            <th>{{ Lang::get('player.profile.stats.server.table.col10') }}</th>
            <th class="hidden-sm hidden-md">{{ Lang::get('player.profile.stats.server.table.col11') }}</th>
            <th class="hidden-sm hidden-md">{{ Lang::get('player.profile.stats.server.table.col14') }}</th>
            <th class="hidden-sm hidden-md">{{ Lang::get('player.profile.stats.server.table.col15') }}</th>
            <th class="hidden-sm hidden-md">
                <span tooltip="Win/Loss Ratio">{{ Lang::get('player.profile.stats.server.table.extra.wlr') }}</span>
            </th>
            <th>{{ Lang::get('player.profile.stats.server.table.col16') }}</th>
        </thead>

        <tbody>
            @foreach($stats as $stat)
                @unless($stat->server[0]->is_active != $unless)
                <tr>
                    <td ng-bind="moment('{{ $stat->first_seen }}').format('lll')"></td>
                    <td ng-bind="moment('{{ $stat->last_seen }}').calendar()"></td>
                    <td ng-bind="{{ (int) $stat->Score }} | number"></td>
                    <td ng-bind="{{ (int) $stat->Kills }} | number"></td>
                    <td class="hidden-sm hidden-md" ng-bind="{{ (int) $stat->Headshots }} | number"></td>
                    <td class="hidden-sm hidden-md" ng-bind="{{ MainHelper::divide($stat->Headshots, $stat->Kills) }} | number"></td>
                    <td ng-bind="{{ (int) $stat->Deaths }} | number"></td>
                    <td class="hidden-sm hidden-md" ng-bind="{{ BattlefieldHelper::kd($stat->Kills, $stat->Deaths) }} | number"></td>
                    <td class="hidden-sm hidden-md" ng-bind="{{ (int) $stat->Suicide }} | number"></td>
                    <td class="hidden-sm hidden-md" ng-bind="{{ (int) $stat->TKs }} | number"></td>
                    <td ng-bind="momentDuration({{ (int) $stat->Playtime }}, 'seconds')"></td>
                    <td class="hidden-sm hidden-md" ng-bind="{{ (int) $stat->Rounds }} | number"></td>
                    <td class="hidden-sm hidden-md" ng-bind="{{ (int) $stat->Wins }} | number"></td>
                    <td class="hidden-sm hidden-md" ng-bind="{{ (int) $stat->Losses }} | number"></td>
                    <td class="hidden-sm hidden-md" ng-bind="{{ MainHelper::divide($stat->Wins, $stat->Losses) }} | number"></td>
                    <td>
                        <span tooltip="{{ $stat->server[0]->ServerName }}">
                        {{ $stat->server[0]->server_name_short or str_limit($stat->server[0]->ServerName, 30) }}
                        </span>
                    </td>
                </tr>
                @endunless
            @endforeach
        </tbody>
    </table>
</div>

<div class="table-responsive">
    <table class="table table-striped table-condensed" ng-table="sessionTable" show-filter="true">
        <tbody>
            <tr ng-repeat="(key, session) in $data">
                <td ng-bind="moment(session.session_start).format('lll')" sortable="'session_start'" data-title="'<?php echo Lang::get('player.profile.stats.server.table.col1'); ?>'"></td>
                <td ng-bind="moment(session.session_end).format('lll')" sortable="'session_end'" data-title="'<?php echo Lang::get('player.profile.stats.server.table.col2'); ?>'"></td>
                <td ng-bind="session.Score | number" sortable="'Score'" data-title="'<?php echo Lang::get('player.profile.stats.server.table.col3'); ?>'"></td>
                <td ng-bind="session.Kills | number" sortable="'Kills'" data-title="'<?php echo Lang::get('player.profile.stats.server.table.col5'); ?>'"></td>
                <td ng-bind="session.Headshots | number" sortable="'Headshots'" data-title="'<?php echo Lang::get('player.profile.stats.server.table.col6'); ?>'"></td>
                <td ng-bind="session.Deaths | number" sortable="'Deaths'" data-title="'<?php echo Lang::get('player.profile.stats.server.table.col7'); ?>'"></td>
                <td ng-bind="session.Suicide | number" sortable="'Suicide'" data-title="'<?php echo Lang::get('player.profile.stats.server.table.col8'); ?>'"></td>
                <td ng-bind="session.TKs | number" sortable="'TKs'" data-title="'<?php echo Lang::get('player.profile.stats.server.table.col9'); ?>'"></td>
                <td ng-bind="momentDuration(session.Playtime, 'seconds')" sortable="'Playtime'" data-title="'<?php echo Lang::get('player.profile.stats.server.table.col10'); ?>'"></td>
                <td ng-bind="session.RoundCount | number" sortable="'RoundCount'" data-title="'<?php echo Lang::get('player.profile.stats.server.table.col11'); ?>'"></td>
                <td ng-bind="session.Wins | number" sortable="'Wins'" data-title="'<?php echo Lang::get('player.profile.stats.server.table.col14'); ?>'"></td>
                <td ng-bind="session.Losses | number" sortable="'Losses'" data-title="'<?php echo Lang::get('player.profile.stats.server.table.col15'); ?>'"></td>
                <td data-title="'<?php echo Lang::get('player.profile.stats.server.table.col16'); ?>'">
                    <span ng-bind="session.server[0].server_name_short || (session.server[0].ServerName | limitTo: 30)" tooltip="{{ session.server[0].ServerName }}"></span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="table-responsive">
    <table class="table table-striped table-condensed">
        <thead>
            <th><?php echo Lang::get('player.profile.records.table.col1'); ?></th>
            <th class="hidden-sm"><?php echo Lang::get('player.profile.records.table.col2'); ?></th>
            <th><?php echo Lang::get('player.profile.records.table.col3'); ?></th>
            <th><?php echo Lang::get('player.profile.records.table.col4'); ?></th>
            <th class="hidden-sm"><?php echo Lang::get('player.profile.records.table.col5'); ?></th>
            <th class="hidden-sm"><?php echo Lang::get('player.profile.records.table.col6'); ?></th>
            <th><?php echo Lang::get('player.profile.records.table.col7'); ?></th>
        </thead>
        <tbody>
            <tr ng-repeat="(key, record) in records.data">
                <td ng-bind="moment(record.stamp).format('MMM D, YYYY h:mm:ss a')"></td>
                <td class="hidden-sm">
                    <span ng-bind="record.type.command_name"></span>&nbsp;
                    <span ng-show="record.is_web" class="badge bg-blue">Web</span>
                </td>
                <td ng-bind="record.action.command_name"></td>
                <td>
                    <ng-switch on="record.target !== null && record.target.PlayerID != playerId">
                        <a ng-switch-when="true" ng-href="{{ record.target.profile_url }}" target="_blank" ng-bind="record.target.SoldierName"></a>
                        <span ng-switch-default ng-bind="record.target_name"></span>
                    </ng-switch>
                </td>
                <td class="hidden-sm">
                    <ng-switch on="record.source !== null && record.source.PlayerID != playerId">
                        <a ng-switch-when="true" ng-href="{{ record.source.profile_url }}" target="_blank" ng-bind="record.source.SoldierName"></a>
                        <span ng-switch-default ng-bind="record.source_name"></span>
                    </ng-switch>
                </td>
                <td class="hidden-sm">
                    <span ng-bind="record.server.server_name_short || (record.server.ServerName | limitTo: 30)" tooltip="{{ record.server.ServerName }}"></span>
                </td>
                <td>
                    <span ng-if="record.action.command_id == 7 || record.action.command_id == 72" class="badge bg-purple" ng-bind="momentDuration(record.command_numeric, 'minutes')"></span>
                    <span ng-bind="record.record_message" class="limit-text" popover="{{ record.record_message }}" popover-trigger="mouseenter" clip-copy="record.record_message" popover-placement="left"></span>
                </td>
            </tr>
        </tbody>
    </table>
</div>

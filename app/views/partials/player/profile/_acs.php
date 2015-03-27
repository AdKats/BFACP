<alert type="success" ng-if="weapons.acs.length === 0 && !refresh.acs && !weapons.acsError"><i class="fa fa-check"></i>&nbsp;No Suspicious Weapons Found</alert>
<alert type="info" ng-if="weapons.acs.length === 0 && refresh.acs && !weapons.acsError"><i class="fa fa-spinner fa-pulse"></i>&nbsp;Checking player&hellip;</alert>
<alert type="error" ng-if="!refresh.acs && weapons.acsError"><i class="fa fa-times"></i>&nbsp;{{ weapons.acsErrorMsg }}</alert>

<div class="table-responsive" ng-if="weapons.acs.length > 0">
    <table class="table table-striped table-condensed">
        <thead>
            <th width="200px"><span class="pull-left">Weapon</span><span class="pull-right">Class</span></th>
            <th>Kills</th>
            <th>Headshots</th>
            <th>Fired</th>
            <th>Hit</th>
            <th>Accuracy</th>
            <th>Playtime</th>
            <th>DPS</th>
            <th>HSKP</th>
            <th>KPM</th>
        </thead>

        <tbody>
            <tr ng-repeat="(key, weapon) in weapons.acs | orderBy:'kills':true">
                <td>
                    <a ng-href="{{ weapon.weapon_link }}" class="pull-left" ng-bind="weapon.slug | uppercase" target="_blank"></a>
                    <span class="pull-right" ng-bind="weapon.category.replace('_', ' ')"></span>
                </td>
                <td ng-bind="weapon.kills | number"></td>
                <td ng-bind="weapon.headshots | number"></td>
                <td ng-bind="weapon.fired | number"></td>
                <td ng-bind="weapon.hit | number"></td>
                <td>{{ weapon.accuracy }}%</td>
                <td ng-bind="momentDuration(weapon.timeEquipped, 'seconds')"></td>
                <td ng-class="{'bg-green': !weapon.triggered.DPS, 'bg-red': weapon.triggered.DPS}">{{ weapon.dps }}%</td>
                <td ng-class="{'bg-green': !weapon.triggered.HKP, 'bg-red': weapon.triggered.HKP}">{{ weapon.hskp }}%</td>
                <td ng-class="{'bg-green': !weapon.triggered.KPM, 'bg-red': weapon.triggered.KPM}">{{ weapon.kpm }}</td>
            </tr>
        </tbody>
    </table>
</div>

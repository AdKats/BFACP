<div class="row">
    <div class="col-xs-12 col-sm-6">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">(WIP) Admin Actions</h3>
            </div>

            <div class="box-body">
                {{ Former::select()
                    ->options($validPermissions)
                    ->ng_model('admin.action')
                }}

                <ul class="list-inline">
                    <li ng-repeat="(key, player) in selectedPlayers track by player">
                        <button class="btn btn-xs bg-red" ng-click="admin.removePlayer(key)">
                            <i class="fa fa-times"></i>
                            <span ng-bind="player"></span>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

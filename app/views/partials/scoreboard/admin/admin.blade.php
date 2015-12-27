<div class="row">
    <div class="col-xs-12 col-sm-6">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Admin Actions</h3>
            </div>

            {{ Former::vertical_open()->ng_submit('admin.submit()') }}

            <div class="box-body">
                {{ Former::select()
                    ->options($validPermissions)
                    ->ng_model('admin.action')
                    ->label('Select Command')
                }}

                <div class="form-group" ng-if="presetMessages.length > 0 && admin.hidePreset === false">
                    <label class="control-label">Preset Messages</label>
                    <select class="form-control" ng-options="msg for msg in presetMessages" ng-model="admin.message"></select>
                </div>

                <div ng-switch="admin.action">
                    <div ng-switch-when="nuke">
                        <div class="form-group">
                            <label class="control-label">Team</label>
                            <select class="form-control" ng-model="admin.actions.nuke.team" ng-options="team.id as team.label for team in teamsList"></select>
                        </div>
                    </div>

                    <div ng-switch-when="teamswitch">
                        <div class="form-group">
                            <label class="control-label">Team</label>
                            <select class="form-control" ng-model="admin.actions.teamswitch.team" ng-options="team.id as team.label for team in teamsList"></select>
                        </div>

                        <div class="form-group">
                            <label class="control-label">Squad</label>
                            <select class="form-control" ng-model="admin.actions.teamswitch.squad" ng-options="squad.id as squad.name for squad in squadlist"></select>
                        </div>

                        <div class="form-group">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox" ng-model="admin.actions.teamswitch.locked">
                                    Check to Lock Squad
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <p>
                                <span ng-bind="selectedPlayers.length"></span> player<span ng-if="selectedPlayers.length != 1">s</span> will be moved to the
                                <span ng-bind="getTeam(admin.actions.teamswitch.team)" class="badge bg-purple"></span> and placed in
                                <span ng-bind="getSquad(admin.actions.teamswitch.squad)" ng-class="{'badge bg-blue': admin.actions.teamswitch.squad != 0}"></span>
                                <span ng-if="admin.actions.teamswitch.squad != 0">squad</span>.
                            </p>
                        </div>
                    </div>

                    <div ng-switch-when="yell">
                        <div class="form-group">
                            <label class="control-label">Duration</label>
                            <input type="number" class="form-control" ng-model="admin.actions.yell.duration" min="1" max="60">
                        </div>
                    </div>
                </div>

                <ul class="list-inline">
                    <li ng-repeat="(key, player) in selectedPlayers track by player">
                        <button class="btn btn-xs bg-red" ng-click="admin.removePlayer(key)">
                            <i class="fa fa-times"></i>
                            <span ng-bind="player"></span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="box-footer">
                <div class="input-group">
                    <input type="text" ng-model="admin.message" placeholder="Type or select message..." class="form-control" ng-disabled="admin.processing || admin.action == 'nuke'" ng-enter="admin.submit()">
                    <span class="input-group-btn">
                        <button type="button" class="btn btn-danger btn-flat" ng-click="admin.submit()" ng-disabled="admin.processing">
                            <ng-switch on="admin.processing">
                                <span ng-switch-when="true"><i class="fa fa-cog fa-spin"></i> Processing...</span>
                                <span ng-switch-default>Submit</span>
                            </ng-switch>
                        </button>
                    </span>
                </div>
            </div>

            {{ Former::close() }}
        </div>
    </div>
</div>

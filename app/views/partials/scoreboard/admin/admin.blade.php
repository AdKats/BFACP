<div class="row">
    <div class="col-xs-12 col-sm-6">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">(WIP) Admin Actions</h3>
            </div>

            {{ Former::vertical_open()->ng_submit('admin.submit()') }}

            <div class="box-body">
                {{ Former::select()
                    ->options($validPermissions)
                    ->ng_model('admin.action')
                    ->label('Select Command')
                }}

                <div class="form-group" ng-show="presetMessages.length > 0 && admin.action != 'nuke'">
                    <label class="control-label">Preset Messages</label>
                    <select class="form-control" ng-options="msg for msg in presetMessages" ng-model="admin.message"></select>
                </div>

                <div ng-switch="admin.action">
                    <div ng-switch-when="nuke">
                        <div class="form-group">
                            <label class="control-label">Team</label>
                            <select class="form-control" ng-model="admin.actions.nuke.team" ng-options="team as team.label for team in teamsList track by team.id"></select>
                        </div>
                    </div>

                    <div ng-switch-when="pban"></div>

                    <div ng-switch-when="punish"></div>

                    <div ng-switch-when="say"></div>

                    <div ng-switch-when="tban"></div>

                    <div ng-switch-when="teamswitch"></div>

                    <div ng-switch-when="tell"></div>

                    <div ng-switch-when="yell"></div>

                    <div ng-switch-when="forgive"></div>
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

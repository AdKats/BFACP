<div class="row">
    <div class="col-xs-12 col-sm-6">
        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">(WIP) Admin Actions</h3>
            </div>

            <div class="box-body">
                {{ Former::vertical_open()->ng_submit('admin.submit()') }}
                    {{ Former::select()
                        ->options($validPermissions)
                        ->ng_model('admin.action')
                    }}

                    {{ Former::select()->ng_if('server._presetmessages.length > 0')->ng_options('msg for msg in server._presetmessages')->ng_model('admin.message') }}

                    <div ng-switch="admin.action">
                        <div ng-switch-when="kick">
                            {{ Former::text()->ng_model('admin.message')->placeholder(Lang::get('scoreboard.admin.inputs.message', ['action' => 'kick'])) }}
                        </div>

                        <div ng-switch-when="kickall">
                            {{ Former::text()->ng_model('admin.message')->placeholder(Lang::get('scoreboard.admin.inputs.message', ['action' => 'kick'])) }}
                        </div>

                        <div ng-switch-when="kill">
                            {{ Former::text()->ng_model('admin.message')->placeholder(Lang::get('scoreboard.admin.inputs.message', ['action' => 'kill'])) }}
                        </div>

                        <div ng-switch-when="mute">
                            {{ Former::text()->ng_model('admin.message')->placeholder(Lang::get('scoreboard.admin.inputs.message', ['action' => 'mute'])) }}
                        </div>

                        <div ng-switch-when="nuke">
                            {{ Former::text()->ng_model('admin.message')->placeholder(Lang::get('scoreboard.admin.inputs.message', ['action' => 'nuke'])) }}
                        </div>

                        <div ng-switch-when="pban">
                            {{ Former::text()->ng_model('admin.message')->placeholder(Lang::get('scoreboard.admin.inputs.message', ['action' => 'perm ban'])) }}
                        </div>

                        <div ng-switch-when="punish">
                            {{ Former::text()->ng_model('admin.message')->placeholder(Lang::get('scoreboard.admin.inputs.message', ['action' => 'punish'])) }}
                        </div>

                        <div ng-switch-when="say">
                            {{ Former::text()->ng_model('admin.message')->placeholder(Lang::get('scoreboard.admin.inputs.message', ['action' => 'say'])) }}
                        </div>

                        <div ng-switch-when="tban">
                            {{ Former::text()->ng_model('admin.message')->placeholder(Lang::get('scoreboard.admin.inputs.message', ['action' => 'temp ban'])) }}
                        </div>

                        <div ng-switch-when="teamswitch">
                            {{ Former::text()->ng_model('admin.message')->placeholder(Lang::get('scoreboard.admin.inputs.message', ['action' => 'team switch'])) }}
                        </div>

                        <div ng-switch-when="tell">
                            {{ Former::text()->ng_model('admin.message')->placeholder(Lang::get('scoreboard.admin.inputs.message', ['action' => 'tell'])) }}
                        </div>

                        <div ng-switch-when="yell">
                            {{ Former::text()->ng_model('admin.message')->placeholder(Lang::get('scoreboard.admin.inputs.message', ['action' => 'yell'])) }}
                        </div>

                        <div ng-switch-when="forgive">
                            {{ Former::text()->ng_model('admin.message')->placeholder(Lang::get('scoreboard.admin.inputs.message', ['action' => 'forgive'])) }}
                        </div>
                    </div>

                    {{ Former::submit('Submit') }}

                {{ Former::close() }}

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

<div class="col-xs-12" ng-controller="PlayerInfoRecordsOn">
    <div class="box box-primary" ng-show="main.total==0">
        <div class="box-header">
            <div class="box-title"><?php echo Lang::get('player.profile.section_titles.commands_on', ['playername' => $player->SoldierName]); ?></div>
            <div class="box-tools pull-right">
                <div class="form-inline">
                    <div class="form-group">
                        <select class="form-control" ng-model="main.filters.cmdid" ng-change="sendQuery()" ng-init="main.filters.cmdid = 'none'">
                            <option value="none" selected="selected">Show All</option>
                            <?php foreach($_cmds as $cmd) : ?>
                            <option value="<?php echo $cmd->command_id; ?>"><?php echo $cmd->command_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="alert alert-info">No commands issued on <strong><?php echo $player->SoldierName; ?></strong></div>
        </div>

        <div class="overlay" ng-show="isLoading"></div>
        <div class="loading-img" ng-show="isLoading"></div>
    </div>
    <div class="box box-primary" ng-hide="main.total == 0">
        <div class="box-header">
            <div class="box-title"><?php echo Lang::get('player.profile.section_titles.commands_on', ['playername' => $player->SoldierName]); ?></div>
            <div class="box-tools pull-right">
                <div class="form-inline">
                    <div class="form-group">
                        <select class="form-control" ng-model="main.filters.cmdid" ng-change="sendQuery()" ng-init="main.filters.cmdid = 'none'">
                            <option value="none" selected="selected">Show All</option>
                            <?php foreach($_cmds as $cmd) : ?>
                            <option value="<?php echo $cmd->command_id; ?>"><?php echo $cmd->command_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="box-body table-responsive">
            <table id="recordsOn" class="table table-striped table-condensed ">
                <thead>
                    <th width="190px">Date</th>
                    <th width="150px">Source</th>
                    <th width="250px" class="hidden-sm">Server</th>
                    <th>Cmd Issued</th>
                    <th>Cmd Action</th>
                    <th>Message</th>
                    <th width="40px">Web</th>
                </thead>
                <tbody>
                    <tr ng-repeat="record in main.records track by record.record_id">
                        <td>{{ record.record_time}}</td>
                        <td ng-if="record.source_link != undefined"><a href="{{record.source_link}}">{{ record.source_name }}</a></td>
                        <td ng-if="record.source_link == undefined">{{ record.source_name }}</td>
                        <td class="hidden-sm"><span class="trim-server-name" tooltip-placement="top" tooltip="{{record.ServerName}}">{{record.ServerName}}</span></td>
                        <td>{{record.command_type}}</td>
                        <td>{{record.command_action}}</td>
                        <td>
                            <span ng-if="record.command_numeric !== null" class="label label-primary">{{record.command_numeric}}</span>

                            <ng-switch on="record.linked !== undefined">
                                <span ng-switch-when="true">{{record.record_message.replace("[]", "")}} <a href="{{record.linked.url}}">[{{ record.linked.text }}]</a></span>
                                <span ng-switch-default>{{record.record_message}}</span>
                            </ng-switch>
                        </td>
                        <td><span class="label" ng-class='{"label-danger": !record.adkats_web, "label-success": record.adkats_web}'>{{ (record.adkats_web ? 'Yes' : 'No') }}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="box-footer clearfix">
            <div class="pull-left">
                Showing {{main.from | number}} to {{main.to | number}} out of {{main.total | number}} - Page {{main.page | number}} of {{main.pages | number}}
            </div>
            <div class="pull-right">
                <button ng-click="firstPage()" class="btn btn-primary btn-xs" ng-class="{'disabled': main.page==1}">First</button>
                <button ng-click="previousPage()" class="btn btn-primary btn-xs" ng-class="{'disabled': main.page==1}">Previous</button>
                <button ng-click="nextPage()" class="btn btn-primary btn-xs" ng-class="{'disabled': main.page==main.pages}">Next</button>
                <button ng-click="lastPage()" class="btn btn-primary btn-xs" ng-class="{'disabled': main.page==main.pages}">Last</button>
            </div>
        </div>

        <div class="overlay" ng-show="isLoading"></div>
        <div class="loading-img" ng-show="isLoading"></div>
    </div>
</div>

<div class="col-xs-12" ng-controller="PlayerInfoRecordsBy">
    <div class="box box-primary" ng-show="main.total==0">
        <div class="box-header">
            <div class="box-title"><?php echo Lang::get('player.profile.section_titles.commands_by', ['playername' => $player->SoldierName]); ?></div>
            <div class="box-tools pull-right">
                <div class="form-inline">
                    <div class="form-group">
                        <select class="form-control" ng-model="main.filters.cmdid" ng-change="sendQuery()" ng-init="main.filters.cmdid = 'none'">
                            <option value="none" selected="selected">Show All</option>
                            <?php foreach($_cmds as $cmd) : ?>
                            <option value="<?php echo $cmd->command_id; ?>"><?php echo $cmd->command_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="alert alert-info">No commands issued by <strong><?php echo $player->SoldierName; ?></strong></div>
        </div>

        <div class="overlay" ng-show="isLoading"></div>
        <div class="loading-img" ng-show="isLoading"></div>
    </div>
    <div class="box box-primary" ng-hide="main.total == 0">
        <div class="box-header">
            <div class="box-title"><?php echo Lang::get('player.profile.section_titles.commands_by', ['playername' => $player->SoldierName]); ?></div>
            <div class="box-tools pull-right">
                <div class="form-inline">
                    <div class="form-group">
                        <select class="form-control" ng-model="main.filters.cmdid" ng-change="sendQuery()" ng-init="main.filters.cmdid = 'none'">
                            <option value="none" selected="selected">Show All</option>
                            <?php foreach($_cmds as $cmd) : ?>
                            <option value="<?php echo $cmd->command_id; ?>"><?php echo $cmd->command_name ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="box-body table-responsive">
            <table id="recordsOn" class="table table-striped table-condensed ">
                <thead>
                    <th width="190px">Date</th>
                    <th width="150px">Target</th>
                    <th width="250px" class="hidden-sm">Server</th>
                    <th>Cmd Issued</th>
                    <th>Cmd Action</th>
                    <th>Message</th>
                    <th width="40px">Web</th>
                </thead>
                <tbody>
                    <tr ng-repeat="record in main.records track by record.record_id">
                        <td>{{record.record_time}}</td>
                        <td ng-if="record.target_link != undefined"><a href="{{record.target_link}}">{{ record.target_name }}</a></td>
                        <td ng-if="record.target_link == undefined">{{ record.target_name }}</td>
                        <td class="hidden-sm"><span class="trim-server-name" tooltip-placement="top" tooltip="{{record.ServerName}}">{{record.ServerName}}</span></td>
                        <td>{{record.command_type}}</td>
                        <td>{{record.command_action}}</td>
                        <td><span ng-if="record.command_numeric !== null" class="label label-primary">{{record.command_numeric}}</span> {{record.record_message}}</td>
                        <td><span class="label" ng-class='{"label-danger": !record.adkats_web, "label-success": record.adkats_web}'>{{ (record.adkats_web ? 'Yes' : 'No') }}</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="box-footer clearfix">
            <div class="pull-left">
                Showing {{main.from | number}} to {{main.to | number}} out of {{main.total | number}} - Page {{main.page | number}} of {{main.pages | number}}
            </div>
            <div class="pull-right">
                <button ng-click="firstPage()" class="btn btn-primary btn-xs" ng-class="{'disabled': main.page==1}">First</button>
                <button ng-click="previousPage()" class="btn btn-primary btn-xs" ng-class="{'disabled': main.page==1}">Previous</button>
                <button ng-click="nextPage()" class="btn btn-primary btn-xs" ng-class="{'disabled': main.page==main.pages}">Next</button>
                <button ng-click="lastPage()" class="btn btn-primary btn-xs" ng-class="{'disabled': main.page==main.pages}">Last</button>
            </div>
        </div>

        <div class="overlay" ng-show="isLoading"></div>
        <div class="loading-img" ng-show="isLoading"></div>
    </div>
</div>

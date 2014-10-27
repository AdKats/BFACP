<div class="col-xs-12" ng-controller="PlayerInfoChatlog" id="p_chatlogs" ng-hide="main.chatlogs.length == 0 && main.filters.message.length == 0 && main.filters.serverid == 'none'">
    <div class="box box-primary">
        <div class="box-header">
            <div class="box-title"><?php echo Lang::get('player.profile.section_titles.chatlogs'); ?></div>
            <div class="box-tools pull-right">
                <div class="form-inline">
                    <div class="form-group">
                        <select class="form-control" id="serversel" ng-model="main.filters.serverid" ng-change="sendQuery()" ng-init="main.filters.serverid = 'none'">
                            <option value="none" selected="selected">Show All Servers</option>
                            <?php if(!empty($servers['bf3']) && $_gameIdent == 'BF3') : ?>
                            <optgroup label="Battlefield 3">
                                <?php foreach($servers['bf3'] as $server) : ?>
                                <option value="<?php echo $server->ServerID; ?>">[<?php echo $server->ServerID; ?>] <?php echo $server->ServerName ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                            <?php endif; ?>
                            <?php if(!empty($servers['bf4']) && $_gameIdent == 'BF4') : ?>
                            <optgroup label="Battlefield 4">
                                <?php foreach($servers['bf4'] as $server) : ?>
                                <option value="<?php echo $server->ServerID; ?>">[<?php echo $server->ServerID; ?>] <?php echo $server->ServerName ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" placeholder="Filter messages" class="form-control" ng-model="main.filters.message" ng-blur="sendQuery()">
                    </div>
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-condensed table-striped">
                    <thead>
                        <th width="190px">Date</th>
                        <th width="70px">Subset</th>
                        <th class="hidden-sm" width="250px">Server</th>
                        <th>Message</th>
                    </thead>
                    <tbody>
                        <tr ng-repeat="chat in main.chatlogs track by chat.ID">
                            <td>{{ chat.logDate }}</td>
                            <td><span class="label" ng-class='{"label-primary": chat.logSubset=="Global", "label-success": chat.logSubset=="Team", "label-warning": chat.logSubset=="Squad"}'>{{ chat.logSubset }}</span></td>
                            <td class="hidden-sm"><span class="trim-server-name" tooltip-placement="top" tooltip="{{ chat.ServerName }}">{{ chat.ServerName }}</span></td>
                            <td>{{ chat.logMessage }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
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
                <div class="form-group">
                    <label>Skip Pages:</label>
                    <input class="form-control" ng-model="main.skip" type="number" min="1" value="1">
                </div>
            </div>
        </div>

        <div class="overlay" ng-show="isLoading"></div>
        <div class="loading-img" ng-show="isLoading"></div>
    </div>
</div>

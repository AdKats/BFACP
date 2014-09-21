<section class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
    <?php if(Config::get('webadmin.BF3') == 1) : ?>
    <div class="box box-primary" ng-controller="BF3PopulationFeed">
        <div class="box-header">
            <h3 class="box-title"><?php echo Lang::get('dashboard.population.title', ['game' => 'BF3']); ?></h3>
            <div class="pull-right box-tools">
                <i class="fa fa-refresh fa-spin" ng-show="refresh"></i>
            </div>
        </div>

        <div class="box-body no-padding" ng-show="loaded">
            <div class="alert alert-danger" ng-if="fault"><strong>ERROR</strong> {{message}}</div>
            <div class="table-responsive" ng-if="fault == false">
                <table class="table table-condensed">
                    <thead>
                        <th><?php echo Lang::get('dashboard.population.table_header.col1'); ?></th>
                        <th><?php echo Lang::get('dashboard.population.table_header.col2'); ?></th>
                        <th><?php echo Lang::get('dashboard.population.table_header.col3'); ?></th>
                    </thead>

                    <tbody>
                        <tr ng-repeat="server in servers track by server.id" ng-class="{'bg-black': server.max == 0, 'bg-red': (server.max > 0 && server.percentage <= 30), 'bg-blue': (server.percentage > 30 && server.percentage <= 80), 'bg-green': (server.percentage > 80)}">
                            <td><a ng-href="/scoreboard#/server/{{server.id}}" style="color: white;">
                                {{ server.short_server_name !== null ? server.short_server_name : server.full_server_name }}
                                </a>
                            </td>
                            <td>{{server.used}} / {{server.max}}</td>
                            <td>{{server.map}}</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="{{total.percentage}}" aria-valuemin="0" aria-valuemax="100" style="width: {{total.percentage}}%; min-width: 40px;">
                                        {{total.percentage}}%
                                    </div>
                                </div>
                            </td>
                            <td colspan="2"><?php echo Lang::get('dashboard.population.total', ['current' => "{{total.totalUsed}}", 'max' => "{{total.totalMax}}"]); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <?php endif; if(Config::get('webadmin.BF4') == 1) : ?>
    <div class="box box-primary" ng-controller="BF4PopulationFeed">
        <div class="box-header">
            <h3 class="box-title"><?php echo Lang::get('dashboard.population.title', ['game' => 'BF4']); ?></h3>
            <div class="pull-right box-tools">
                <i class="fa fa-refresh fa-spin" ng-show="refresh"></i>
            </div>
        </div>

        <div class="box-body no-padding" ng-show="loaded">
            <div class="alert alert-danger" ng-if="fault"><strong>ERROR</strong> {{message}}</div>
            <div class="table-responsive" ng-if="fault == false">
                <table class="table table-condensed">
                    <thead>
                        <th><?php echo Lang::get('dashboard.population.table_header.col1'); ?></th>
                        <th><?php echo Lang::get('dashboard.population.table_header.col2'); ?></th>
                        <th><?php echo Lang::get('dashboard.population.table_header.col3'); ?></th>
                    </thead>

                    <tbody>
                        <tr ng-repeat="server in servers track by server.id" ng-class="{'bg-red': server.percentage <= 30, 'bg-blue': (server.percentage > 30 && server.percentage <= 80), 'bg-black': (server.used==0 && server.max==0), 'bg-green': (server.percentage > 80)}">
                            <td><a ng-href="/scoreboard#/server/{{server.id}}" style="color: white;">
                                {{ server.short_server_name !== null ? server.short_server_name : server.full_server_name }}
                                </a>
                            </td>
                            <td>{{server.used}} / {{server.max}}</td>
                            <td>{{server.map}}</td>
                        </tr>
                        <tr>
                            <td>
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="{{total.percentage}}" aria-valuemin="0" aria-valuemax="100" style="width: {{total.percentage}}%; min-width: 40px;">
                                        {{total.percentage}}%
                                    </div>
                                </div>
                            </td>
                            <td colspan="2"><?php echo Lang::get('dashboard.population.total', ['current' => "{{total.totalUsed}}", 'max' => "{{total.totalMax}}"]); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</section>

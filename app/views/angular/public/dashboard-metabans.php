<section class="col-xs-12 col-sm-6 col-md-6 col-lg-6">
    <div class="box box-primary" ng-controller="MetabansFeed">
        <div class="box-header">
            <h3 class="box-title"><?php echo Lang::get('dashboard.banfeed.metabans.title'); ?></h3>
            <div class="pull-right box-tools">
                <i class="fa fa-refresh fa-spin" ng-show="refresh"></i>
            </div>
        </div>

        <div class="box-body" ng-show="loaded">
            <div class="alert alert-danger" ng-if="fault"><strong>ERROR</strong> {{message}}</div>
            <div class="table-responsive" ng-if="fault == false">
                <table class="table table-condensed">
                    <thead>
                        <th><?php echo Lang::get('dashboard.banfeed.metabans.table_header.col1'); ?></th>
                        <th><?php echo Lang::get('dashboard.banfeed.metabans.table_header.col2'); ?></th>
                        <th><?php echo Lang::get('dashboard.banfeed.metabans.table_header.col3'); ?></th>
                        <th><?php echo Lang::get('dashboard.banfeed.metabans.table_header.col4'); ?></th>
                    </thead>

                    <tbody>
                        <tr ng-repeat="ban in banlist track by ban.id">
                            <td><a href="{{ ban.playercard_url }}" target="_blank">{{ ban.player }}</a></td>
                            <td>{{ ban.timestamp }}</td>
                            <td>{{ ban.game_name }}</td>
                            <td>
                                <a href="{{ ban.assessment_url }}" target="_blank" tooltip-placement="left" tooltip="{{ban.reason}}">
                                    <?php echo Lang::get('dashboard.banfeed.metabans.assess_view'); ?>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

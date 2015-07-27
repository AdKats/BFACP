angular.module('bfacp').controller('ReportsController', ['$scope', '$http', '$interval', '$modal', function($scope, $http, $interval, $modal) {
    $scope.reports = {
        refresh: false,
        last_id: null,
        data: []
    };

    $scope.actions = [];

    /**
     * Fetches the actions that can be used on reports.
     * @return void
     */
    $scope.getActions = function() {
        $http.get('api/reports/actions').success(function(data, status) {
            angular.forEach(data, function(action, key) {
                $scope.actions.push({
                    name: action,
                    id: key
                });
            });
        }).error(function(data, status) {
            console.error('Unable to get report actions. Will retry in 5 seconds.');
            setTimeout(function() {
                $scope.getActions();
            }, 5 * 1000);
        });
    };

    $scope.getActions();

    /**
     * Fetches the latest reports.
     * @return void
     */
    $scope.latestReports = function() {
        $scope.reports.refresh = true;
        $http({
            url: 'api/reports',
            method: 'GET',
            params: {
                last_id: $scope.reports.last_id
            }
        }).success(function(data) {
            if(data.data.length > 0) {
                angular.forEach(data.data, function(obj, key) {
                    $scope.reports.data.push(obj);
                });
                $scope.reports.last_id = data.data[0].record_id;
            }
        }).error(function(data, status) {
            console.error('Error in reports request.', data);
            $scope.latestReports();
        }).finally(function() {
            $scope.reports.refresh = false;
        });
    };

    // Re-fetch the reports every 30 seconds
    $interval($scope.latestReports, 30 * 1000);

    $scope.open = function(report, key) {
        var reportInstance = $modal.open({
            animation: true,
            templateUrl: 'js/templates/modals/report.html',
            controller: 'ReportInstanceController',
            resolve: {
                report: function() {
                    return report;
                },
                actions: function() {
                    return $scope.actions;
                }
            }
        });

        reportInstance.result.then(function (report) {
            $scope.reports.data.splice(key, 1);
        });
    };
}])
.controller('ReportInstanceController', ['$scope', '$modalInstance', 'report', 'actions', 'ReportFactory', function($scope, $modalInstance, report, actions, ReportFactory) {
    $scope.report = report;
    $scope.actions = actions;
    $scope.reportReason = report.record_message;
    $scope.actionSelected = null;
    $scope.working = false;
    $scope.edit = false;
    $scope.extra = {
        tban: {
            duration: 30
        }
    };

    $scope.ok = function () {
        var action = $scope.actions[$scope.actionSelected];

        if(action === undefined) {
            alert('You must select a valid action.');
            return;
        }

        if(action.id == 8) {
            if(!confirm('Are you sure you want to permanently ban ' + report.target_name + '?')) {
                return;
            }
        }

        if(action.id == 7) {
            if(!confirm('Are you sure you want to temporarily ban ' + report.target_name + '?')) {
                return;
            }
        }

        $scope.working = true;

        ReportFactory.setAction(action);
        ReportFactory.setRecordId($scope.report);
        ReportFactory.setReason($scope.reportReason);

        if(action.id == 7) {
            ReportFactory.setExtras($scope.extra);
        }

        ReportFactory.updateReport().then(function(data) {
            toastr.success(data.message);
            $modalInstance.close(data.data);
        }, function(data) {
            if(data.errors !== undefined) {
                angular.forEach(data.errors, function(error, key) {
                    for(var i=0; i < error.length; i++) {
                        toastr.error(error[i]);
                    }
                });

                return;
            }

            toastr.error(data.message);

            if(data.status_code == 422) {
                $modalInstance.close(data.data);
            }
        }).finally(function() {
            $scope.working = false;
        });
    };

    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
}]);

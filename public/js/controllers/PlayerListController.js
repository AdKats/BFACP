angular.module('bfacp').controller('PlayerListController', ['$scope', '$http', function($scope, $http) {

    $scope.players = [];
    $scope.alerts = [];

    $scope.loaded = false;

    $scope.main = {
        page: 1,
        last_page: 2,
        take: 30,
        total: null
    };

    $scope.reputation = function(val) {
        val = parseFloat(val);
        var className = '';
        if (val === 0) {
            className = 'label-default';
        } else if (val > 0 && val <= 70) {
            className = 'bg-light-blue';
        } else if (val > 70) {
            className = 'label-success';
        } else if (val < 0 && val >= -70) {
            className = 'label-warning';
        } else if (val < -70) {
            className = 'label-danger';
        }

        return className;
    }

    $scope.closeAlert = function(index) {
        $scope.alerts.splice(index, 1);
    };

    $scope.$watch('main.page', function(newVal, oldVal) {
        if ($scope.main.page > $scope.main.last_page && $scope.main.total !== null) {
            $scope.main.page = oldVal;
        }
    });

    $scope.getListing = function() {

        if ($scope.main.page > $scope.main.last_page && $scope.main.total !== null) {
            $scope.alerts.push({
                type: 'danger',
                msg: 'You can\'t go to page ' + $scope.main.page + ' when there is only ' + $scope.main.last_page + ' page(s).',
                timeout: 5000
            });

            return false;
        }

        $scope.loaded = false;

        var url = 'api/players?page=' + $scope.main.page + '&limit=' + $scope.main.take;

        var playerName = getParameterByName('player');

        if (playerName !== '') {
            url += '&player=' + playerName;
        }

        $http.get(url).success(function(data) {
            $scope.loaded = true;
            $scope.players = data.data.data;
            $scope.main.last_page = data.data.last_page;
            $scope.main.total = data.data.total;
        }).error(function() {
            $scope.getListing();
        });
    };

    $scope.nextPage = function() {
        if ($scope.main.page < $scope.main.last_page) {
            $scope.main.page++;
            $scope.getListing();
        }
    };

    $scope.previousPage = function() {
        if ($scope.main.page > 1) {
            $scope.main.page--;
            $scope.getListing();
        }
    };

    $scope.getListing();

}]);

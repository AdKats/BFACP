angular.module('bfacp').controller('PlayerController', ['$scope', '$resource', '$filter', 'ngTableParams', '$modal', function($scope, $resource, $filter, ngTableParams, $modal) {

    var Player = $resource('api/players/:playerId', {
        playerId: '@id'
    });

    var Records = $resource('api/players/:playerId/records?page=:pageNum', {
        playerId: '@id',
        pageNum: '@id'
    });

    var ACS = $resource('api/battlelog/players/:playerId/acs', {
        playerId: '@id'
    });

    $scope.playerId = $("input[name='player_id']").val();

    $scope.player = [];

    $scope.weapons = {
        acs: [],
        acsError: false,
        acsErrorMsg: null
    };

    $scope.records = {
        current_page: 1,
        from: 1,
        last_page: null,
        per_page: null,
        to: null,
        total: null,
        data: []
    };

    $scope.refresh = {
        sessions: true,
        records: true,
        acs: true
    };

    $scope.fetchRecords = function() {
        $scope.refresh.records = true;

        Records.get({
            playerId: $scope.playerId,
            pageNum: $scope.records.current_page
        }, function(data) {
            $scope.refresh.records = false;
            $scope.records = data.data;

        }, function(e) {
            $scope.fetchRecords();
            console.error('fatal error', e);
        });
    };

    $scope.fetchRecords();

    Player.get({
        playerId: $scope.playerId
    }, function(data) {
        $scope.player = data.data;
        $scope.refresh.sessions = false;
        $scope.sessionTable = new ngTableParams({
            page: 1,
            count: 10,
            sorting: {
                session_end: 'desc'
            }
        }, {
            total: $scope.player.sessions.length,
            getData: function($defer, params) {
                var orderedData = params.sorting() ? $filter('orderBy')($scope.player.sessions, params.orderBy()) : $scope.player.sessions

                $defer.resolve(
                    orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count())
                );
            }
        });
    });

    ACS.get({
        playerId: $scope.playerId
    }, function(data) {
        $scope.weapons.acs = data.data;
        $scope.refresh.acs = false;
    }, function(e) {
        $scope.weapons.acsError = true;
        $scope.weapons.acsErrorMsg = e.data.message;
        $scope.refresh.acs = false;
    });
}])

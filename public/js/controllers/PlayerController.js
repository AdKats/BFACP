angular.module('bfacp').controller('PlayerController', ['$scope', '$resource', '$filter', 'ngTableParams', '$modal', '$http',
    function ($scope, $resource, $filter, ngTableParams, $modal, $http) {

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

        var acsErrorCount = 0;

        /**
         * TODO: Add ability to modify player groups
         */
            //$scope.specialgroups = [];
            //
            //var getSpecialGroups = function () {
            //    $http.get('api/helpers/adkats/special_groups').success(function (data) {
            //        $scope.specialgroups = data.data;
            //    }).error(function () {
            //        getSpecialGroups();
            //    });
            //};

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

        $scope.fetchRecords = function () {
            $scope.refresh.records = true;

            Records.get({
                playerId: $scope.playerId,
                pageNum: $scope.records.current_page
            }, function (data) {
                $scope.refresh.records = false;
                $scope.records = data.data;

            }, function (e) {
                $scope.fetchRecords();
                console.error('fatal error', e);
            });
        };

        $scope.fetchAcs = function () {
            if (!$scope.refresh.acs) {
                $scope.refresh.acs = true;
            }

            ACS.get({
                playerId: $scope.playerId
            }, function (data) {
                $scope.weapons.acs = data.data;
                $scope.refresh.acs = false;
                acsErrorCount = 0;
                if ($scope.weapons.acsError) {
                    $scope.weapons.acsError = false;
                }
            }, function (e) {
                $scope.weapons.acsError = true;
                $scope.weapons.acsErrorMsg = e.data.message;
                $scope.refresh.acs = false;

                if (acsErrorCount < 6) {
                    setTimeout($scope.fetchAcs, 5 * 1000);
                }
            });
        };

        $scope.fetchExtendedDetails = function () {
            Player.get({
                playerId: $scope.playerId
            }, function (data) {
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
                    getData: function ($defer, params) {
                        var orderedData = params.sorting() ? $filter('orderBy')($scope.player.sessions, params.orderBy()) : $scope.player.sessions

                        $defer.resolve(
                            orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count())
                        );
                    }
                });
            }, function () {
                setTimeout($scope.fetchExtendedDetails, 3 * 1000);
            });
        };

        $scope.geoPopover = {
            templateUrl: 'js/templates/geoinfo.html',
            content: {
                city: 'No Data',
                country: 'No Data',
                org: 'No Data',
                region: 'No Data'
            }
        };

        var geoRequest = function (ip) {
            var url = "http://ipinfo.io/" + ip + "/json";
            if (location.protocol === 'https:') {
                url = 'api/helpers/ip/' + ip;
            }

            if (ip === '' || ip === null) {
                console.log('Invalid IP Given: ' + ip);
                return;
            }

            $http.get(url).success(function (data) {
                $scope.geoPopover.content.city = data.city;
                $scope.geoPopover.content.country = data.country;
                $scope.geoPopover.content.org = data.org;
                $scope.geoPopover.content.region = data.region;
            }).error(function (data) {
                console.error(data);
            });
        };

        var player_ip = $("input[name='player_ip']");

        if (player_ip.length != 0) {
            geoRequest(player_ip.val());
        }

        $scope.fetchRecords();
        $scope.fetchAcs();
        $scope.fetchExtendedDetails();

        $scope.admin = {
            forgive: {
                points: 1,
                message: 'ForgivePlayer',
                processing: false,
                server: null
            }
        }

        $scope.issueForgive = function () {
            if ($scope.admin.forgive.server === null) {
                toastr.error('No server selected.');
                return false;
            }

            $scope.admin.forgive.processing = true;
            $http.post('players/' + $scope.playerId + '/forgive', {
                server_id: $scope.admin.forgive.server,
                forgive_points: $scope.admin.forgive.points,
                message: $scope.admin.forgive.message
            }).success(function (data) {
                if (data.status == 'error') {
                    toastr.error(data.message);
                } else if (data.status == 'warning') {
                    toastr.warning(data.message);
                } else {
                    toastr.success(data.message);
                }
            }).error(function (data) {
                toastr.error(data.message, 'error');
                console.error(data);
            }).finally(function () {
                $scope.admin.forgive.processing = false;
            });
        };

        /**
         * TODO: Add ability to modify player groups
         */
        //getSpecialGroups();
        //
        //$scope.groups = function (player) {
        //    $modal.open({
        //        animation: true,
        //        templateUrl: 'js/templates/modals/player/groups.html',
        //        controller: 'PlayerGroupsController',
        //        resolve: {
        //            player: function () {
        //                return player;
        //            },
        //            groups: function () {
        //                return $scope.specialgroups;
        //            }
        //        }
        //    });
        //};
    }]);
/**
 * TODO: Add ability to modify player groups
 */
//.controller('PlayerGroupsController', ['$scope', '$modalInstance', 'player', 'groups', function ($scope, $modalInstance, player, groups) {
//    $scope.player = player;
//    $scope.groups = groups;
//}]);

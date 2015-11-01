angular.module('bfacp').controller('DashboardController', ['$scope', '$http', '$interval', '$timeout', function($scope, $http, $interval, $timeout) {
    $scope.results = {
        bans: {
            columns: [],
            data: []
        },
        metabans: {
            failed: false,
            feed: {
                data: []
            },
            assessments: {
                banned_total: 0,
                protected_total: 0,
                watched_total: 0,
                enforced_bans_total: 0,
                data: [],
                locales: []
            }
        },
        population: {
            columns: [],
            title: '',
            footer: '',
            old: {
                online: 0,
                total: 0
            },
            online: 0,
            total: 0,
            percentage: 0,
            data: []
        },
        banstats: {
            yesterday: 0,
            average: 0
        },
        online_admins: []
    };

    $scope.opts = {
        bans: {
            personal: false
        }
    };

    $scope.loaded = {
        bans: false
    };

    $scope.$watch('results.population.online', function(newValue, oldValue) {
        $scope.results.population.old.online = oldValue;
    });

    $scope.$watch('results.population.total', function(newValue, oldValue) {
        $scope.results.population.old.total = oldValue;
    });

    $scope.metabans = function() {
        // Fetch feed
        $http({
            url: 'api/bans/metabans/feed_assessments',
            method: 'GET'
        }).success(function(data, status) {
            $scope.results.metabans.feed.data = data.data.feed.feed;
            $scope.results.metabans.assessments.data = data.data.assessments.assessments;
            $scope.results.metabans.assessments.banned_total = data.data.assessments.banned_total;
            $scope.results.metabans.assessments.protected_total = data.data.assessments.protected_total;
            $scope.results.metabans.assessments.watched_total = data.data.assessments.watched_total;
            $scope.results.metabans.assessments.enforced_bans_total = data.data.assessments.enforced_bans_total;
            $scope.results.metabans.locales = data.data.locales;
        }).error(function(data, status) {
            if (data.status_code == 500 || data.status_code == 400) {
                $scope.results.metabans.failed = true;
                return false;
            }

            $scope.metabans();
        });
    };

    /**
     * Fetchs the latest bans. If personal is true only fetch the users issued bans.
     * @return void
     */
    $scope.latestBans = function() {
        if ($scope.loaded.bans) {
            $("#latest-ban-refresh-btn").addClass('fa-spin');
            $scope.loaded.bans = false;
        }

        $http({
            url: 'api/bans/latest',
            method: 'GET',
            params: {
                personal: $scope.opts.bans.personal
            }
        }).success(function(data, status) {
            $scope.results.bans.columns = data.data.cols;
            $scope.results.bans.data = data.data.bans;
            $scope.loaded.bans = true;
            $("#latest-ban-refresh-btn").removeClass('fa-spin');
        }).error(function(data, status) {
            $scope.latestBans();
        });
    };

    /**
     * Fetchs the population statistics
     * @return void
     */
    $scope.population = function() {
        $http.get('api/servers/population').success(function(data, status) {
            $scope.results.population.data = data.data.games;
            $scope.results.population.online = data.data.online;
            $scope.results.population.total = data.data.totalSlots;
            $scope.results.population.percentage = data.data.percentage;
            $scope.results.population.columns = data.data.columns;
            $scope.results.population.title = data.data.title;
            $scope.results.population.footer = data.data.footer;
        }).error(function(data, status) {
            $scope.population();
        });
    };

    /**
     * Fetchs the ban statistics
     * @return void
     */
    $scope.banStats = function() {
        $http.get('api/bans/stats').success(function(data, status) {
            $scope.results.banstats.yesterday = data.data.bans.yesterday;
            $scope.results.banstats.average = data.data.bans.average;
        }).error(function(data, status) {
            $scope.banStats();
        });
    };

    /**
     * Returns the class based on percentage number
     * @param  integer pct   Percentage
     * @param  boolean usebg Prepend bg- to the class if true
     * @return string        Class Name
     */
    $scope.populationColor = function(pct, usebg) {
        var classColor;

        if (pct <= 30) {
            classColor = usebg ? 'red' : 'danger';
        } else if (pct > 30 && pct <= 80) {
            classColor = usebg ? 'blue' : 'warning';
        } else if (pct > 80) {
            classColor = usebg ? 'green' : 'success';
        }

        if (usebg) {
            classColor = 'bg-' + classColor;
        }

        return classColor;
    };

    /**
     * Converts assessment type to correct label
     * @param  object assessment
     * @return string
     */
    $scope.assessmentType = function(assessment) {
        var typeLabel = '';

        switch (assessment.action_type) {
            case "none":
                typeLabel = $scope.results.metabans.locales.type.none;
                break;
            case "watch":
                typeLabel = $scope.results.metabans.locales.type.watch;
                break;
            case "white":
                typeLabel = $scope.results.metabans.locales.type.white;
                break;
            case "black":
                typeLabel = $scope.results.metabans.locales.type.black;
                break;
            default:
                typeLabel = assessment.action_type;
                break;
        }

        return typeLabel;
    };

    $scope.onlineAdmins = function() {
        $http.get('api/helpers/online/admins').success(function(data) {
            if(data.data.length > 0) {
                $scope.results.online_admins = data.data;
            } else {
                $scope.results.online_admins = [];
            }
        }).error(function() {
            $scope.onlineAdmins();
        });
    };

    // Re-fetch the population every 30 seconds.
    $interval($scope.population, 30 * 1000);

    // Re-fetch the online admins every minute.
    $interval($scope.onlineAdmins, 60 * 1000);

    $scope.banStats();
}]);

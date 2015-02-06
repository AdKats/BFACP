angular.module('bfacp', [
        'ngResource',
        'ngMessages',
        'ngAnimate',
        'ngAria',
        'ngSanitize',
        'ui.bootstrap',
        'countTo'
    ])
    .run(['$rootScope', function($rootScope) {
        $rootScope.moment = function(date) { return moment(date); };
    }])
    .filter('nl2br', function() {
        var span = document.createElement('span');
        return function(input) {
            if (!input) return input;
            var lines = input.split('\n');

            for (var i = 0; i < lines.length; i++) {
                span.innerText = lines[i];
                span.textContent = lines[i];  //for Firefox
                lines[i] = span.innerHTML;
            }
            return lines.join('<br />');
        }
    })
    .controller('DashboardController', ['$scope', '$http', '$interval', function($scope, $http, $interval) {
        $scope.results = {
            bans: {
                columns: [],
                data: []
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
            }
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

        /**
         * Fetchs the latest bans. If personal is true only fetch the users issued bans.
         * @return void
         */
        $scope.latestBans = function() {
            if($scope.loaded.bans) {
                $("#latest-ban-refresh-btn").addClass('fa-spin');
                $scope.loaded.bans = false;
            }

            $http({
                url: 'api/bans/latest',
                method:'GET',
                params:{
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

            if(pct <= 30) {
                classColor = usebg ? 'red' : 'danger';
            } else if(pct > 30 && pct <= 80) {
                classColor = usebg ? 'blue' : 'warning';
            } else if(pct > 80) {
                classColor = usebg ? 'green' : 'success';
            }

            if(usebg) {
                classColor = 'bg-' + classColor;
            }

            return classColor;
        };

        // Re-fetch the population every 30 seconds
        $interval($scope.population, 30 * 1000);

        $scope.latestBans();
        $scope.population();
        $scope.banStats();

    }]);

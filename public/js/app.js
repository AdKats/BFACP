'use strict';
angular.module('bfacp', [
        'ngResource',
        'ngMessages',
        'ngAnimate',
        'ngAria',
        'ngSanitize',
        'ngIdle',
        'ngTable',
        'ui.bootstrap',
        'countTo'
    ])
    .config(['$locationProvider', '$idleProvider', function($locationProvider, $idleProvider) {
        $locationProvider.html5Mode(true).hashPrefix('!');
        $idleProvider.idleDuration(window.idleDurationSeconds || 60);
        $idleProvider.warningDuration(window.warningDurationSeconds || 60);
    }])
    .run(['$rootScope', function($rootScope) {
        $rootScope.moment = function(date) { return moment(date); };
        $rootScope.momentDuration = function(duration, type) { return moment.duration(duration, type).humanize(); };
        $rootScope.divide = function(num1, num2, precision) {
            if(precision === undefined || precision === null) {
                precision = 2;
            }

            var dividedNum = 0;

            try {
                if(num1 === 0 || num2 === 0) {
                    throw new Error('Divide by zero');
                }

                dividedNum = num1 / num2
            } catch(e) {
                dividedNum = num1;
            }

            return dividedNum.toFixed(precision);
        };
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
    .filter('range', function() {
        return function(input, low, high, step) {
            //  discuss at: http://phpjs.org/functions/range/
            // original by: Waldo Malqui Silva
            //   example 1: range ( 0, 12 );
            //   returns 1: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
            //   example 2: range( 0, 100, 10 );
            //   returns 2: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]
            //   example 3: range( 'a', 'i' );
            //   returns 3: ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i']
            //   example 4: range( 'c', 'a' );
            //   returns 4: ['c', 'b', 'a']

            var matrix = [];
            var inival, endval, plus;
            var walker = step || 1;
            var chars = false;

            if (!isNaN(low) && !isNaN(high)) {
              inival = low;
              endval = high;
            } else if (isNaN(low) && isNaN(high)) {
              chars = true;
              inival = low.charCodeAt(0);
              endval = high.charCodeAt(0);
            } else {
              inival = (isNaN(low) ? 0 : low);
              endval = (isNaN(high) ? 0 : high);
            }

            plus = ((inival > endval) ? false : true);
            if (plus) {
              while (inival <= endval) {
                matrix.push(((chars) ? String.fromCharCode(inival) : inival));
                inival += walker;
              }
            } else {
              while (inival >= endval) {
                matrix.push(((chars) ? String.fromCharCode(inival) : inival));
                inival -= walker;
              }
            }

            return matrix;
        };
    })
    .controller('DashboardController', ['$scope', '$http', '$interval', function($scope, $http, $interval) {
        $scope.results = {
            bans: {
                columns: [],
                data: []
            },
            metabans: {
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
                $scope.metabans();
            });

        };

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
                $scope.results.bans.data    = data.data.bans;
                $scope.loaded.bans          = true;
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
                $scope.results.population.data       = data.data.games;
                $scope.results.population.online     = data.data.online;
                $scope.results.population.total      = data.data.totalSlots;
                $scope.results.population.percentage = data.data.percentage;
                $scope.results.population.columns    = data.data.columns;
                $scope.results.population.title      = data.data.title;
                $scope.results.population.footer     = data.data.footer;
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
                $scope.results.banstats.average   = data.data.bans.average;
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

        /**
         * Converts assessment type to correct label
         * @param  object assessment
         * @return string
         */
        $scope.assessmentType = function(assessment) {
            var typeLabel = '';

            switch(assessment.action_type)
            {
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

        // Re-fetch the population every 30 seconds
        $interval($scope.population, 30 * 1000);

        $scope.banStats();

    }])
    .controller('PlayerListController', ['$scope', '$http', '$location', function($scope, $http, $location) {

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
            if(val === 0) {
                className = 'label-default';
            } else if(val > 0 && val <= 70) {
                className = 'bg-light-blue';
            } else if(val > 70) {
                className = 'label-success';
            } else if(val < 0 && val >= -70) {
                className = 'label-warning';
            } else if(val < -70) {
                className = 'label-danger';
            }

            return className;
        }

        $scope.closeAlert = function(index) {
            $scope.alerts.splice(index, 1);
        };

        $scope.$watch('main.page', function(newVal, oldVal) {
            if($scope.main.page > $scope.main.last_page && $scope.main.total !== null) {
                $scope.main.page = oldVal;
            }
        });

        $scope.getListing = function() {

            if($scope.main.page > $scope.main.last_page && $scope.main.total !== null) {
                $scope.alerts.push({
                    type: 'danger',
                    msg: 'You can\'t go to page ' + $scope.main.page + ' when there is only ' + $scope.main.last_page + ' page(s).',
                    timeout: 5000
                });

                return false;
            }

            $scope.loaded = false;

            var url = 'api/players?page=' + $scope.main.page + '&limit=' + $scope.main.take;

            if($location.search().player !== undefined) {
                url += '&player=' + $location.search().player;
            }

            $http.get(url).success(function(data, status) {
                $scope.loaded = true;
                $scope.players = data.data.data;
                $scope.main.last_page = data.data.last_page;
                $scope.main.total = data.data.total;
            }).error(function(data, status) {
                $scope.getListing();
            });
        };

        $scope.nextPage = function() {
            if($scope.main.page < $scope.main.last_page) {
                $scope.main.page++;
                $scope.getListing();
            }
        };

        $scope.previousPage = function() {
            if($scope.main.page > 1) {
                $scope.main.page--;
                $scope.getListing();
            }
        };

        $scope.getListing();

    }])
    .controller('PlayerController', ['$scope', '$resource', '$filter', 'ngTableParams', '$modal', function($scope, $resource, $filter, ngTableParams, $modal) {

        var Player = $resource('api/players/:playerId', {
            playerId: '@id'
        });

        var Records = $resource('api/players/:playerId/records?page=:pageNum', {
            playerId: '@id',
            pageNum: '@id'
        });

        $scope.playerId = $("input[name='player_id']").val();

        $scope.player = [];
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
            records: true
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

        Player.get({playerId: $scope.playerId}, function(data) {
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
                        orderedData.slice( (params.page() - 1) * params.count(), params.page() * params.count() )
                    );
                }
            });
        });
    }])
    .controller('ScoreboardController', ['$scope', '$rootScope', '$http', '$timeout', '$location', '$idle', '$modal',
        function($scope, $rootScope, $http, $timeout, $location, $idle, $modal) {

        // How often the data should be fetched in seconds
        var refresh = 10;
        var refreshTimeout;
        var requestErrorCount = 0;

        // Idle Detector
        $scope.idleStarted = false;
        $scope.idleWarning = null;
        $scope.idleTimedOut = null;
        $scope.idleServerId = null;

        function closeModels() {
            if($scope.idleWarning) {
                $scope.idleWarning.close();
                $scope.idleWarning = null;
            }

            if($scope.idleTimedOut) {
                $scope.idleTimedOut.close();
                $scope.idleTimedOut = null;
            }
        }

        $scope.$on('$idleStart', function() {
            closeModels();

            $scope.idleWarning = $modal.open({
                templateUrl: 'warning-dialog.html',
                windowClass: 'modal-warning'
            });
        });

        $scope.$on('$idleEnd', function() {
            closeModels();

            if($scope.idleServerId !== null) {
                setTimeout(function() {
                    $scope.$apply(function() {
                        $scope.selectedId = $scope.idleServerId;
                        $scope.idleServerId = null;
                    });

                    $scope.switchServer();
                }, 100);
            }
        });

        $scope.$on('$idleTimeout', function() {
            closeModels();

            setTimeout(function() {
                $scope.$apply(function() {
                    $scope.idleServerId = $scope.selectedId;
                    $scope.selectedId = -1;
                    $scope.roundId = null;
                });

            }, 100);

            $scope.disableServerRequests();

            $scope.idleTimedOut = $modal.open({
                templateUrl: 'timedout-dialog.html',
                windowClass: 'modal-danger'
            });
        });

        $scope.$watch('selectedId', function() {
            if($scope.selectedId != -1) {
                if( ! $scope.idleStarted) {
                    $idle.watch();
                    $scope.idleStarted = true;
                }
            } else {
                if($scope.idleStarted && $scope.idleServerId !== null) {
                    // Do nothing
                    return false;
                }

                $idle.unwatch();
                $scope.idleStarted = false;
            }
        });

        // Init vars
        $scope.loading = false;
        $scope.refresh = false;
        $scope.requestError = false;
        $scope.selectedId = -1;
        $scope.roundId = null;

        $scope.alerts = [];

        $scope.sort = {
            column: 'score',
            desc: true
        };

        $scope.server = [];
        $scope.teams = [];
        $scope.netural = [];
        $scope.messages = [];
        $scope.winning = {
            '1': false,
            '2': false,
            '3': false,
            '4': false
        };

        $scope.search = {
            chat: '',
            scoreboard: ''
        };

        var addAlert = function(message, alertType)
        {
            $scope.alerts.push({
                msg: message,
                type: alertType
            });
        };

        $scope.closeAlert = function(index)
        {
            $scope.alerts.splice(index, 1);
        };

        $scope.disableServerRequests = function()
        {
            $scope.loading = false;
            $scope.refresh = false;
            $scope.server = [];
            $scope.teams = [];
            $scope.netural = [];
            $scope.messages = [];
            $location.hash('');

            if($scope.requestError) {
                $scope.requestError = false;
            }

            $timeout.cancel(refreshTimeout);
        };

        $scope.switchServer = function()
        {
            $scope.loading = true;
            $scope.refresh = true;
            $scope.server = [];
            $scope.teams = [];
            $scope.netural = [];
            $scope.messages = [];

            if($scope.selectedId == -1) {
                $location.hash('');
            }
            else {
                $location.hash('id-' + $scope.selectedId);
            }

            if($scope.requestError) {
                $scope.requestError = false;
            }

            $timeout.cancel(refreshTimeout);
            $timeout($scope.fetchServerData, 500);
            $scope.fetchRoundStats();
        };

        $scope.kd = function(kills, deaths)
        {
            var ratio = $rootScope.divide(kills, deaths);

            if(kills === 0 && deaths > 0) {
                ratio = -deaths.toFixed(2);
            }

            return ratio;
        };

        $scope.avg = function(items, prop, precision)
        {
            if(items === null) {
                return 0;
            }

            var sum = $scope.sum(items, prop);

            return $rootScope.divide(sum, items.length, precision || 0);
        };

        $scope.sum = function(items, prop)
        {
            if(items === null) {
                return 0;
            }

            return items.reduce(function(a, b) {
                return b[prop] === null ? a : a + b[prop];
            }, 0);
        };

        $scope.pingColor = function(ping) {
            if(ping === null) {
                return 'bg-blue';
            }

            var color;

            if(ping < 140) {
                color = 'bg-green';
            } else if(ping >= 140 && ping < 250) {
                color = 'bg-yellow';
            } else if(ping >= 250 && ping < 65535) {
                color = 'bg-red';
            }

            return color;
        };

        $scope.setWinningTeam = function() {
            var team1 = $scope.teams[1] || {score: null};
            var team2 = $scope.teams[2] || {score: null};
            var team3 = $scope.teams[3] || {score: null};
            var team4 = $scope.teams[4] || {score: null};
            var tickets_needed = $scope.server.tickets_needed;
            var tickets_starting = $scope.server.tickets_starting;
            var mode = $scope.server.mode;
            var num = null;

            if(tickets_needed == null || mode.uri == "RushLarge0" || mode.uri == "Heist0") {
                $scope.winning[1] = false;
                $scope.winning[2] = false;
                $scope.winning[3] = false;
                $scope.winning[4] = false;

                return false;
            }

            var teamTickets = [];

            if(team1.score !== null) {
                teamTickets.push(team1.score);
            }

            if(team2.score !== null) {
                teamTickets.push(team2.score);
            }

            if(team3.score !== null) {
                teamTickets.push(team3.score);
            }

            if(team4.score !== null) {
                teamTickets.push(team4.score);
            }

            if(tickets_needed > 0) {
                if(mode.uri == "TeamDeathMatch0" || mode.uri == "BloodMoney0") {
                    num = Math.max.apply(null, teamTickets);
                } else {
                    num = Math.min.apply(null, teamTickets);
                }

            } else {
                num = Math.max.apply(null, teamTickets);
            }

            switch(mode.uri) {
                case "Domination0":
                case "Obliteration":
                case "Chainlink0":
                case "ConquestLarge0":
                case "ConquestSmall0":
                case "TeamDeathMatch0":
                case "TurfWarLarge0":
                case "TurfWarSmall0":
                case "Heist0":
                case "Hotwire0":
                case "BloodMoney0":
                case "Hit0":
                case "Hostage0":
                    if(team1.score < 0 || team2.score < 0) {
                        return false;
                    }

                    if(team1.score == team2.score) {
                        $scope.winning[1] = false;
                        $scope.winning[2] = false;
                    } else if(num == team1.score) {
                        $scope.winning[1] = true;
                        $scope.winning[2] = false;
                    } else if(num == team2.score) {
                        $scope.winning[1] = false;
                        $scope.winning[2] = true;
                    }
                    break;

                case "SquadDeathMatch0":

                    // Team 1 Is Winning
                    if(team1.score > team2.score && team1.score > team3.score && team1.score > team4.score || num == team1.score) {
                        $scope.winning[1] = true;
                        $scope.winning[2] = false;
                        $scope.winning[3] = false;
                        $scope.winning[4] = false;
                    }

                    // Team 2 Is Winning
                    else if(team2.score > team1.score && team2.score > team3.score && team2.score > team4.score || num == team2.score) {
                        $scope.winning[1] = false;
                        $scope.winning[2] = true;
                        $scope.winning[3] = false;
                        $scope.winning[4] = false;
                    }

                    // Team 3 Is Winning
                    else if(team3.score > team1.score && team3.score > team2.score && team3.score > team4.score || num == team3.score) {
                        $scope.winning[1] = false;
                        $scope.winning[2] = false;
                        $scope.winning[3] = true;
                        $scope.winning[4] = false;
                    }

                    // Team 4 Is Winning
                    else if(team4.score > team1.score && team4.score > team2.score && team4.score > team3.score || num == team4.score) {
                        $scope.winning[1] = false;
                        $scope.winning[2] = false;
                        $scope.winning[3] = false;
                        $scope.winning[4] = true;
                    }
                break;

                default:
                    console.debug('Unknown gametype: ' + mode.uri);
                break;
            }
        };

        $scope.fetchServerData = function()
        {
            if($scope.selectedId == -1) {
                $scope.loading = false;
                $scope.refresh = false;
                return false;
            }

            if( ! $scope.loading ) {
                $scope.loading = true;
            }

            $scope.refresh = true;

            $http({
                url: 'api/servers/scoreboard/' + $scope.selectedId,
                method: 'GET',
                params: {}
            }).success(function(data, status) {
                if($scope.alerts.length > 0) {
                    $scope.alerts = [];
                }

                $scope.server = data.data.server;
                $scope.teams = data.data.teams;
                $scope.setWinningTeam();

                if(data.data.teams[0] !== undefined || data.data.teams[0] !== null)
                {
                    $scope.netural = data.data.teams[0];
                    delete $scope.teams[0];
                }

                $scope.refresh = false;

                if($scope.requestError) {
                    $scope.requestError = false;
                    requestErrorCount = 0;
                }

                var chart = $("#round-graph").highcharts();

                if(
                    ($scope.server.mode.uri == 'RushLarge0' || $scope.server.mode.uri == 'Heist0') && (chart.series[1] !== undefined || chart.series[1] !== null) && chart.series[1].visible) {
                    chart.series[1].hide();
                }

                refreshTimeout = $timeout($scope.fetchServerData, refresh * 1000);
            }).error(function(data, status) {
                if(status == 410) {
                    $scope.refresh = false;
                    $scope.loading = false;
                    addAlert(data.message, 'danger');
                    setTimeout(function() {
                        $scope.$apply(function() {
                            $scope.selectedId = -1;
                        });
                    }, 800);

                    return false;
                }

                if(status == 500) {
                    requestErrorCount++;
                }

                if(requestErrorCount > 4) {
                    $scope.refresh = false;
                    $scope.loading = false;
                    $scope.requestError = true;
                    addAlert(data.message, 'danger');
                    return false;
                }

                $scope.fetchServerData();
            });

            $scope.fetchServerChat();
        };

        $scope.fetchServerChat = function()
        {
            if($scope.selectedId == -1) {
                $scope.messages = [];
                return false;
            }

            $http({
                url: 'api/servers/chat/' + $scope.selectedId,
                method: 'GET',
                params: {
                    sb: 1,
                    nospam: 1
                }
            }).success(function(data, status) {
                $scope.messages = data.data;
            }).error(function(data, status) {
                $timeout($scope.fetchServerChat, 2 * 1000);
            });
        };

        $scope.colSort = function(col)
        {
            var sort = $scope.sort;

            if(sort.column == col) {
                sort.desc = ! sort.desc;
            } else {
                sort.column = col;
                sort.desc = false;
            }
        };

        $scope.colSortClass = function(col)
        {
            var cssClass = '';

            if($scope.sort.column == col) {
                if($scope.sort.desc) {
                    cssClass = 'fa fa-sort-desc';
                } else {
                    cssClass = 'fa fa-sort-asc';
                }
            } else {
                cssClass = 'fa fa-sort';
            }

            return cssClass;
        };

        $scope.isSelectAll = function(e) {
            var table = $(e.target).closest('table');
            if($('thead th input:checkbox', table).is(':checked')) {
                $('thead th input:checkbox', table).prop('checked', false);
            }
        }

        $scope.selectAll = function(e) {
            var table = $(e.target).closest('table');
            $('tbody td input:checkbox', table).prop('checked', e.target.checked);
        };

        $scope.fetchRoundStats = function()
        {
            var chart = $("#round-graph").highcharts();
            if($scope.selectedId == -1 || $scope.requestError) {
                return false;
            }

            $http({
                url: 'api/servers/scoreboard/roundstats/' + $scope.selectedId,
                method: 'GET',
                params: {}
            }).success(function(data, status) {
                for(var i=0; i < data.data.stats.length; i++) {
                    if(chart.series[i] === undefined || chart.series[i] === null) {
                        chart.addSeries(data.data['stats'][i]);
                    } else {
                        if($scope.roundId != data.data.roundId) {
                            chart.series[i].setData([]);
                        }

                        chart.series[i].setData(data.data['stats'][i].data);
                    }
                }

                chart.redraw();

                $timeout($scope.fetchRoundStats, 30 * 1000);
            }).error(function(data, status) {
                $timeout($scope.fetchRoundStats, 2 * 1000);
            });
        };

        $("#round-graph").highcharts({
            chart:  {
                type: 'spline',
                zoomType: 'x'
            },
            title: {
                text: 'Round Stats'
            },
            subtitle: {
                text: 'Times shown in UTC'
            },
            xAxis: {
                type: 'datetime',
                title: {
                    text: 'Time'
                }
            },
            yAxis: {
                title: {
                    text: ''
                },
                min: 0
            },
            tooltip: {
                headerFormat: '<b>{series.name}</b> - <small>{point.x:%H:%M:%S}</small><br>',
                pointFormat: '{point.y}'
            },
            plotOptions: {
                spline: {
                    marker: {
                        enabled: true
                    },
                    dataLabels: {
                        enabled: true
                    }
                }
            },
            series: []
        });

        if($location.hash() !== '')
        {
            var path = $location.hash().split('-');
            $scope.selectedId = parseInt(path[1], 10);
            $scope.switchServer();
        };

    }]);

$('#psearch').submit(function() {
    $(this).find('input:text').each(function() {
        var inputVal = $(this).val();
        $(this).val(inputVal.split(' ').join(''));
    });
});

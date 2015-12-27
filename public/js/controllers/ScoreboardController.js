angular.module('bfacp').controller('ScoreboardController', ['$scope', '$rootScope', '$http', '$timeout', '$location', '$idle', '$modal', 'SBA',
    function ($scope, $rootScope, $http, $timeout, $location, $idle, $modal, SBA) {

        // How often the data should be fetched in seconds
        var refresh = 10;
        var refreshTimeout;
        var requestErrorCount = 0;

        // Idle Detector
        $scope.idleStarted = false;
        $scope.idleWarning = null;
        $scope.idleTimedOut = null;
        $scope.idleServerId = null;

        var idleSound = new Howl({
            urls: ['audio/ready.mp3', 'audio/ready.ogg'],
            volume: 0.5,
            buffer: true
        });

        function closeModels() {
            if ($scope.idleWarning) {
                $scope.idleWarning.close();
                $scope.idleWarning = null;
            }

            if ($scope.idleTimedOut) {
                $scope.idleTimedOut.close();
                $scope.idleTimedOut = null;
            }
        }

        $scope.$on('$idleWarn', function (e, countdown) {
            if (countdown < 20) {
                idleSound.play();
            }
        });

        $scope.$on('$idleStart', function () {
            closeModels();

            $scope.idleWarning = $modal.open({
                templateUrl: 'warning-dialog.html',
                windowClass: 'modal-warning'
            });
        });

        $scope.$on('$idleEnd', function () {
            closeModels();
            idleSound.stop();

            if ($scope.idleServerId !== null) {
                setTimeout(function () {
                    $scope.$apply(function () {
                        $scope.selectedId = $scope.idleServerId;
                        $scope.idleServerId = null;
                    });

                    $scope.switchServer();
                }, 100);
            }
        });

        $scope.$on('$idleTimeout', function () {
            closeModels();

            setTimeout(function () {
                $scope.$apply(function () {
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

        $scope.$watch('selectedId', function () {
            if ($scope.selectedId != -1) {
                if (!$scope.idleStarted) {
                    $idle.watch();
                    $scope.idleStarted = true;
                }
            } else {
                if ($scope.idleStarted && $scope.idleServerId !== null) {
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
        $scope.selectedPlayers = [];

        $scope.sort = {
            column: 'score',
            desc: true
        };

        $scope.server = [];
        $scope.teams = [];
        $scope.neutral = [];
        $scope.admins = null;
        $scope.messages = [];
        $scope.teamsList = [];
        $scope.presetMessages = [];
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

        $scope.chat = {
            message: '',
            sending: false
        };

        var addAlert = function (message, alertType) {
            $scope.alerts.push({
                msg: message,
                type: alertType
            });
        };

        $scope.closeAlert = function (index) {
            $scope.alerts.splice(index, 1);
        };

        $scope.disableServerRequests = function () {
            $scope.loading = false;
            $scope.refresh = false;
            $scope.server = [];
            $scope.teams = [];
            $scope.neutral = [];
            $scope.messages = [];
            $location.hash('');

            if ($scope.requestError) {
                $scope.requestError = false;
            }

            $timeout.cancel(refreshTimeout);
        };

        $scope.switchServer = function () {
            $scope.loading = true;
            $scope.refresh = true;
            $scope.server = [];
            $scope.teams = [];
            $scope.neutral = [];
            $scope.messages = [];
            $scope.selectedPlayers = [];

            if ($scope.selectedId == -1) {
                $location.hash('');
            } else {
                $location.hash('id-' + $scope.selectedId);
            }

            if ($scope.requestError) {
                $scope.requestError = false;
            }

            $timeout.cancel(refreshTimeout);
            $timeout($scope.fetchServerData, 500);
            $scope.fetchRoundStats();
        };

        $scope.kd = function (kills, deaths) {
            var ratio = $rootScope.divide(kills, deaths);

            if (kills === 0 && deaths > 0) {
                ratio = -deaths.toFixed(2);
            }

            return ratio;
        };

        $scope.avg = function (items, prop, precision) {
            if (items === null || items === undefined) {
                return 0;
            }

            var sum = $scope.sum(items, prop);

            return $rootScope.divide(sum, items.length, precision || 0);
        };

        $scope.sum = function (items, prop) {
            if (items === null || items === undefined) {
                return 0;
            }

            return items.reduce(function (a, b) {
                return b[prop] === null ? a : a + b[prop];
            }, 0);
        };

        $scope.pingColor = function (ping) {
            if (ping === null) {
                return 'bg-blue';
            }

            var color;

            if (ping < 140) {
                color = 'bg-green';
            } else if (ping >= 140 && ping < 250) {
                color = 'bg-yellow';
            } else if (ping >= 250 && ping < 65535) {
                color = 'bg-red';
            }

            return color;
        };

        $scope.setWinningTeam = function () {
            var team1 = $scope.teams[1] || {
                    score: null
                };
            var team2 = $scope.teams[2] || {
                    score: null
                };
            var team3 = $scope.teams[3] || {
                    score: null
                };
            var team4 = $scope.teams[4] || {
                    score: null
                };
            var tickets_needed = $scope.server.tickets_needed;

            var mode = $scope.server.mode;
            var num = null;

            if (tickets_needed == null || mode.uri == "RushLarge0" || mode.uri == "Heist0") {
                $scope.winning[1] = false;
                $scope.winning[2] = false;
                $scope.winning[3] = false;
                $scope.winning[4] = false;

                return false;
            }

            var teamTickets = [];

            if (team1.score !== null) {
                teamTickets.push(team1.score);
            }

            if (team2.score !== null) {
                teamTickets.push(team2.score);
            }

            if (team3.score !== null) {
                teamTickets.push(team3.score);
            }

            if (team4.score !== null) {
                teamTickets.push(team4.score);
            }

            if (tickets_needed > 0) {
                if (mode.uri == "TeamDeathMatch0" || mode.uri == "BloodMoney0") {
                    num = Math.max.apply(null, teamTickets);
                } else {
                    num = Math.min.apply(null, teamTickets);
                }

            } else {
                num = Math.max.apply(null, teamTickets);
            }

            switch (mode.uri) {
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
                    if (team1.score < 0 || team2.score < 0) {
                        return false;
                    }

                    if (team1.score == team2.score) {
                        $scope.winning[1] = false;
                        $scope.winning[2] = false;
                    } else if (num == team1.score) {
                        $scope.winning[1] = true;
                        $scope.winning[2] = false;
                    } else if (num == team2.score) {
                        $scope.winning[1] = false;
                        $scope.winning[2] = true;
                    }
                    break;

                case "SquadDeathMatch0":

                    // Team 1 Is Winning
                    if (team1.score > team2.score && team1.score > team3.score && team1.score > team4.score || num == team1.score) {
                        $scope.winning[1] = true;
                        $scope.winning[2] = false;
                        $scope.winning[3] = false;
                        $scope.winning[4] = false;
                    }

                    // Team 2 Is Winning
                    else if (team2.score > team1.score && team2.score > team3.score && team2.score > team4.score || num == team2.score) {
                        $scope.winning[1] = false;
                        $scope.winning[2] = true;
                        $scope.winning[3] = false;
                        $scope.winning[4] = false;
                    }

                    // Team 3 Is Winning
                    else if (team3.score > team1.score && team3.score > team2.score && team3.score > team4.score || num == team3.score) {
                        $scope.winning[1] = false;
                        $scope.winning[2] = false;
                        $scope.winning[3] = true;
                        $scope.winning[4] = false;
                    }

                    // Team 4 Is Winning
                    else if (team4.score > team1.score && team4.score > team2.score && team4.score > team3.score || num == team4.score) {
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

        $scope.fetchServerData = function () {
            if ($scope.selectedId == -1) {
                $scope.loading = false;
                $scope.refresh = false;
                return false;
            }

            if (!$scope.loading) {
                $scope.loading = true;
            }

            $scope.refresh = true;

            $http({
                url: 'api/servers/scoreboard/' + $scope.selectedId,
                method: 'GET',
                params: {}
            }).success(function (data) {
                if ($scope.alerts.length > 0) {
                    $scope.alerts = [];
                }

                $scope.server = data.data.server;
                $scope.teams = data.data.teams;

                if ($scope.presetMessages.length == 0) {
                    $scope.presetMessages = data.data._presetmessages;
                }

                if ($scope.teamsList.length == 0) {
                    $scope.teamsList = data.data._teams;
                }

                if (data.data.admins !== undefined) {
                    $scope.admins = data.data.admins;
                } else {
                    $scope.admins = null;
                }

                $scope.setWinningTeam();

                if (data.data.teams[0] !== undefined || data.data.teams[0] !== null) {
                    $scope.neutral = data.data.teams[0];
                    delete $scope.teams[0];
                }

                $scope.refresh = false;

                if ($scope.requestError) {
                    $scope.requestError = false;
                    requestErrorCount = 0;
                }

                var chart = $("#round-graph").highcharts();

                if (
                    ($scope.server.mode.uri == 'RushLarge0' || $scope.server.mode.uri == 'Heist0') && (chart.series[1] !== undefined || chart.series[1] !== null) && chart.series[1].visible) {
                    chart.series[1].hide();
                }

                refreshTimeout = $timeout($scope.fetchServerData, refresh * 1000);
            }).error(function (data, status) {
                if (status == 410) {
                    $scope.refresh = false;
                    $scope.loading = false;
                    addAlert(data.message, 'danger');
                    setTimeout(function () {
                        $scope.$apply(function () {
                            $scope.selectedId = -1;
                        });
                    }, 800);

                    return false;
                }

                if (status == 500) {
                    requestErrorCount++;
                }

                if (requestErrorCount > 4) {
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

        $scope.fetchServerChat = function () {
            if ($scope.selectedId == -1) {
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
            }).success(function (data) {
                $scope.messages = data.data;
            }).error(function () {
                $timeout($scope.fetchServerChat, 2 * 1000);
            });
        };

        $scope.colSort = function (col) {
            var sort = $scope.sort;

            if (sort.column == col) {
                sort.desc = !sort.desc;
            } else {
                sort.column = col;
                sort.desc = false;
            }
        };

        $scope.colSortClass = function (col) {
            var cssClass = '';

            if ($scope.sort.column == col) {
                if ($scope.sort.desc) {
                    cssClass = 'fa fa-sort-desc';
                } else {
                    cssClass = 'fa fa-sort-asc';
                }
            } else {
                cssClass = 'fa fa-sort';
            }

            return cssClass;
        };

        $scope.isSelectAll = function (e, input) {
            var table;
            if (input !== undefined && input !== null) {
                table = $(input).closest('table');
            } else {
                table = $(e.target).closest('table');
            }
            if ($('thead th input:checkbox', table).is(':checked')) {
                $('thead th input:checkbox', table).prop('checked', false);
            }
        };

        $scope.selectAll = function (e) {
            var table = $(e.target).closest('table');
            var checkboxes = $('tbody td input:checkbox', table);
            checkboxes.prop('checked', e.target.checked);
            $scope.updateSelectedPlayers();
        };

        $scope.fetchRoundStats = function () {
            var chart = $("#round-graph").highcharts();
            if ($scope.selectedId == -1 || $scope.requestError) {
                return false;
            }

            $http({
                url: 'api/servers/scoreboard/roundstats/' + $scope.selectedId,
                method: 'GET',
                params: {}
            }).success(function (data) {
                for (var i = 0; i < data.data.stats.length; i++) {
                    if (chart.series[i] === undefined || chart.series[i] === null) {
                        chart.addSeries(data.data['stats'][i]);
                    } else {
                        if ($scope.roundId != data.data.roundId) {
                            chart.series[i].setData([]);
                        }

                        chart.series[i].setData(data.data['stats'][i].data);
                    }
                }

                chart.redraw();

                $timeout($scope.fetchRoundStats, 30 * 1000);
            }).error(function () {
                $timeout($scope.fetchRoundStats, 2 * 1000);
            });
        };

        $("#round-graph").highcharts({
            chart: {
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

        if ($location.hash() !== '') {
            var path = $location.hash().split('-');
            $scope.selectedId = parseInt(path[1], 10);
            $scope.switchServer();
        }

        /**
         * Admin functionality
         */

        $scope.updateSelectedPlayers = function () {
            $scope.selectedPlayers = $('input[name="players"]:checked').map(function () {
                return this.value;
            }).get();
        };

        $scope.squadlist = [];

        $http.get('api/helpers/squads').success(function (data) {
            $scope.squadlist = data;
        });

        $scope.$watch("admin.action", function (action) {
            if (action == 'nuke' || action == 'teamswitch') {
                $scope.admin.hidePreset = true;
            } else {
                $scope.admin.hidePreset = false;
            }
        });

        $scope.getSquad = function (squadId) {
            var squads = $scope.squadlist;

            if (squadId === 0) {
                return 'no squad';
            }

            for (var i = 0; i < squads.length; i++) {
                if (squadId == i) {
                    return squads[i].name;
                }
            }
        };

        $scope.getTeam = function (teamId) {
            var teams = $scope.teamsList;

            for (var i = 0; i < teams.length; i++) {
                if (teamId == teams[i].id) {
                    return teams[i].label;
                }
            }
        };

        $scope.admin = {
            action: 'say',
            processing: false,
            hidePreset: false,
            message: '',
            actions: {
                nuke: {
                    team: 1
                },
                teamswitch: {
                    team: 1,
                    squad: 0,
                    locked: false
                },
                yell: {
                    duration: 10
                }
            },
            doCheck: function (players, needsConfirm, skipPlayerCheck) {
                if (needsConfirm === undefined) {
                    needsConfirm = true;
                }

                if (skipPlayerCheck === undefined) {
                    skipPlayerCheck = false;
                }

                var action = $scope.admin.action;
                var count = players;

                if (count < 1 && !skipPlayerCheck) {
                    toastr.error('You need to have at least 1 player selected.');
                    $scope.admin.processing = false;
                    return false;
                }

                if (needsConfirm) {
                    if (count > 5 || action == "kickall") {
                        switch (action) {
                            case "kickall":
                                action = "kick all";
                                break;
                            case "teamswitch":
                                action = "team/squad switch";
                                break;
                        }

                        return confirm("Are you sure you want to " + action + " " + count + " players?");
                    }
                }

                return true;
            },
            submit: function () {
                var action = $scope.admin.action;
                var message = $scope.admin.message;
                var players = $scope.selectedPlayers.join();
                var playerCount = $scope.selectedPlayers.length;
                var skipPlayerCheck = false;
                var team = null;
                var _players = $("input[name='players']");
                $scope.admin.processing = true;

                switch (action) {
                    case "punish":
                        if (!$scope.admin.doCheck(playerCount)) {
                            $scope.admin.processing = false;
                            break;
                        }

                        $scope.admin.punishPlayer(players, message);
                        break;

                    case "forgive":
                        if (!$scope.admin.doCheck(playerCount)) {
                            $scope.admin.processing = false;
                            break;
                        }

                        $scope.admin.forgivePlayer(players, message);
                        break;

                    case "mute":
                        if (!$scope.admin.doCheck(playerCount)) {
                            $scope.admin.processing = false;
                            break;
                        }

                        $scope.admin.mutePlayer(players, message);
                        break;

                    case "kill":
                        if (!$scope.admin.doCheck(playerCount)) {
                            $scope.admin.processing = false;
                            break;
                        }

                        $scope.admin.killPlayer(players, message);
                        break;

                    case "nuke":
                        team = $scope.admin.actions.nuke.team;
                        try {
                            if (confirm('Are you sure you want to NUKE the ' + $scope.getTeam(team) + '?')) {
                                $scope.admin.sendNuke(team, $scope.getTeam(team));
                            }
                        } catch (e) {
                            $scope.admin.processing = false;
                            toastr.error('Team not selected.');
                        }
                        break;

                    case "kick":
                    case "kickall":
                        if (action == 'kickall') {
                            if (confirm("You are about to kick all players from the server. Continue?")) {
                                players = _players.map(function () {
                                    return this.value;
                                }).get().join();
                                playerCount = _players.map(function () {
                                    return this.value;
                                }).get().length;
                                skipPlayerCheck = true;
                            } else {
                                $scope.admin.processing = false;
                                break;
                            }
                        }

                        if (!$scope.admin.doCheck(playerCount, true, skipPlayerCheck)) {
                            $scope.admin.processing = false;
                            break;
                        }

                        $scope.admin.kickPlayer(players, message);
                        break;

                    case "teamswitch":
                        team = $scope.admin.actions.teamswitch.team;

                        if (!$scope.admin.doCheck(playerCount)) {
                            $scope.admin.processing = false;
                            break;
                        }

                        $scope.admin.switchPlayer(players, $scope.admin.actions.teamswitch.team, $scope.admin.actions.teamswitch.squad, $scope.admin.actions.teamswitch.locked);
                        break;

                    case "say":
                        if (playerCount > 0) {
                            $scope.admin.sendSay(players, 'Player', message);
                        } else {
                            $scope.admin.sendSay(undefined, 'All', message);
                        }
                        break;

                    case "tell":
                        if (!$scope.admin.doCheck(playerCount, false)) {
                            $scope.admin.processing = false;
                            break;
                        }

                        $scope.admin.sendTell(players, message);
                        break;

                    case "yell":
                        if (playerCount > 0) {
                            $scope.admin.sendYell(players, 'Player', message, undefined, $scope.admin.actions.yell.duration);
                        } else {
                            $scope.admin.sendYell(undefined, 'All', message, undefined, $scope.admin.actions.yell.duration);
                        }
                        break;

                    default:
                        $scope.admin.processing = false;
                        break;
                }
            },
            resetSys: function () {
                $scope.admin.action = 'say';
                $scope.admin.message = '';
                $scope.admin.hidePreset = false;
                $scope.admin.actions = {
                    nuke: {
                        team: 1
                    },
                    teamswitch: {
                        team: 1,
                        squad: 0,
                        locked: false
                    }
                };
                $('input[name="players"]').attr('checked', false);
                $scope.selectedPlayers = [];
            },
            removePlayer: function (index) {
                var player = $scope.selectedPlayers[index];
                var input = $('input[value="' + player + '"]');
                input.prop('checked', false);
                $scope.selectedPlayers.splice(index, 1);
                $scope.isSelectAll(null, input);
            },
            sendMessage: function () {
                var message = $scope.chat.message;
                if (message === '') return;

                $scope.chat.sending = true;

                SBA.say($scope.selectedId, undefined, undefined, message).success(function (data) {
                    var chatrecord = data.data.passed[0].record;
                    $scope.messages.push(chatrecord);
                    $scope.chat.message = '';
                }).error(function (e) {
                    console.error('Error: ', e);
                    toastr.error('An error was occurred when sending your message. Please try again.');
                }).finally(function () {
                    $scope.chat.sending = false;
                });
            },
            killPlayer: function (players, message) {
                SBA.kill($scope.selectedId, players, message).success(function (data) {
                    var status = data.status;
                    var player = null;
                    var res = null;
                    var failed = data.data.failed;
                    var passed = data.data.passed;

                    if (status == 'success') {
                        for (var i = 0; i < failed.length; i++) {
                            player = failed[i];
                            toastr.warning(player.message);
                        }
                        for (var i = 0; i < passed.length; i++) {
                            player = passed[i];
                            toastr.success(player.message, player.player);
                        }
                        $scope.admin.resetSys();
                    } else {
                        toastr.error(data.message);
                    }

                    $scope.admin.processing = false;
                }).error(function (e) {
                    console.error(e);
                });
            },
            punishPlayer: function (players, message) {
                SBA.punish($scope.selectedId, players, message).success(function (data) {
                    var status = data.status;
                    var player = null;
                    var res = null;
                    var failed = data.data.failed;
                    var passed = data.data.passed;

                    if (status == 'success') {
                        for (var i = 0; i < failed.length; i++) {
                            player = failed[i];
                            toastr.warning(player.message);
                        }
                        for (var i = 0; i < passed.length; i++) {
                            player = passed[i];
                            toastr.success(player.message, player.player);
                        }
                        $scope.admin.resetSys();
                    } else {
                        toastr.error(data.message);
                    }

                    $scope.admin.processing = false;
                }).error(function (e) {
                    console.error(e);
                });
            },
            forgivePlayer: function (players, message) {
                SBA.forgive($scope.selectedId, players, message).success(function (data) {
                    var status = data.status;
                    var player = null;
                    var res = null;
                    var failed = data.data.failed;
                    var passed = data.data.passed;

                    if (status == 'success') {
                        for (var i = 0; i < failed.length; i++) {
                            player = failed[i];
                            toastr.warning(player.message);
                        }
                        for (var i = 0; i < passed.length; i++) {
                            player = passed[i];
                            toastr.success(player.message, player.player);
                        }
                        $scope.admin.resetSys();
                    } else {
                        toastr.error(data.message);
                    }

                    $scope.admin.processing = false;
                }).error(function (e) {
                    console.error(e);
                });
            },
            mutePlayer: function (players, message) {
                SBA.mute($scope.selectedId, players, message).success(function (data) {
                    var status = data.status;
                    var player = null;
                    var res = null;
                    var failed = data.data.failed;
                    var passed = data.data.passed;

                    if (status == 'success') {
                        for (var i = 0; i < failed.length; i++) {
                            player = failed[i];
                            toastr.warning(player.message);
                        }
                        for (var i = 0; i < passed.length; i++) {
                            player = passed[i];
                            toastr.success(player.message, player.player);
                        }
                        $scope.admin.resetSys();
                    } else {
                        toastr.error(data.message);
                    }

                    $scope.admin.processing = false;
                }).error(function (e) {
                    console.error(e);
                });
            },
            kickPlayer: function (players, message) {
                SBA.kick($scope.selectedId, players, message).success(function (data) {
                    var status = data.status;
                    var player = null;
                    var res = null;
                    var failed = data.data.failed;
                    var passed = data.data.passed;

                    if (status == 'success') {
                        for (var i = 0; i < failed.length; i++) {
                            player = failed[i];
                            toastr.warning(player.message);
                        }
                        for (var i = 0; i < passed.length; i++) {
                            player = passed[i];
                            toastr.success(player.message, player.player);
                        }
                        $scope.admin.resetSys();
                    } else {
                        toastr.error(data.message);
                    }

                    $scope.admin.processing = false;
                }).error(function (e) {
                    console.error(e);
                });
            },
            switchPlayer: function (players, team, squad, locked) {
                SBA.teamswitch($scope.selectedId, players, team, squad, locked).success(function (data) {
                    var status = data.status;
                    var player = null;
                    var res = null;
                    var failed = data.data.failed;
                    var passed = data.data.passed;

                    if (status == 'success') {
                        for (var i = 0; i < failed.length; i++) {
                            player = failed[i];
                            toastr.warning(player.message);
                        }
                        for (var i = 0; i < passed.length; i++) {
                            player = passed[i];
                            toastr.success(player.message, player.player);
                        }
                        $scope.admin.resetSys();
                    } else {
                        toastr.error(data.message);
                    }

                    $scope.admin.processing = false;
                }).error(function (e) {
                    console.error(e);
                });
            },
            sendSay: function (players, type, message, teamID) {
                SBA.say($scope.selectedId, players, type, message, teamID, true).success(function (data) {
                    var status = data.status;
                    var player = null;
                    var failed = data.data.failed;
                    var passed = data.data.passed;

                    if (status == 'success') {
                        for (var i = 0; i < failed.length; i++) {
                            player = failed[i];
                            toastr.warning(player.message);
                        }
                        for (var i = 0; i < passed.length; i++) {
                            player = passed[i];
                            toastr.success(player.message, player.player);
                        }
                        $scope.admin.resetSys();
                    } else {
                        toastr.error(data.message);
                    }

                    $scope.admin.processing = false;
                }).error(function (e) {
                    console.error(e);
                });
            },
            sendTell: function (players, message) {
                SBA.tell($scope.selectedId, players, message).success(function (data) {
                    var status = data.status;
                    var player = null;
                    var failed = data.data.failed;
                    var passed = data.data.passed;

                    if (status == 'success') {
                        for (var i = 0; i < failed.length; i++) {
                            player = failed[i];
                            toastr.warning(player.message);
                        }
                        for (var i = 0; i < passed.length; i++) {
                            player = passed[i];
                            toastr.success(player.message, player.player);
                        }
                        $scope.admin.resetSys();
                    } else {
                        toastr.error(data.message);
                    }

                    $scope.admin.processing = false;
                }).error(function (e) {
                    console.error(e);
                });
            },
            sendYell: function (players, type, message, teamID, duration) {
                SBA.yell($scope.selectedId, players, type, message, teamID, duration).success(function (data) {
                    var status = data.status;
                    var player = null;
                    var res = null;
                    var failed = data.data.failed;
                    var passed = data.data.passed;

                    if (status == 'success') {
                        for (var i = 0; i < failed.length; i++) {
                            player = failed[i];
                            toastr.warning(player.message);
                        }
                        for (var i = 0; i < passed.length; i++) {
                            player = passed[i];
                            toastr.success(player.player, player.message);
                        }
                        $scope.admin.resetSys();
                    } else {
                        toastr.error(data.message);
                    }

                    $scope.admin.processing = false;
                }).error(function (e) {
                    console.error(e);
                });
            },
            sendNuke: function (teamId, teamName) {
                SBA.nuke($scope.selectedId, teamId).success(function (data) {
                    var status = data.status;

                    if (status == 'success') {
                        toastr.success('You NUKED the ' + teamName);
                        $scope.admin.resetSys();
                    } else {
                        toastr.error(data.message);
                    }

                    $scope.admin.processing = false;
                }).error(function (e) {
                    console.error(e);
                });
            }
        };
    }
]);

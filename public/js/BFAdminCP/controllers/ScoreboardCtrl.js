app.factory('Scoreboard', ['$http', function($http)
{
    return {

        // Get the server information and players
        get: function(id) {
            return $http.get('api/v1/common/general/scoreboard/' + id);
        }
    }
}]);

app.factory('Chat', ['$http', function($http)
{
    return {
        get: function(id) {
            return $http.get('api/v1/common/general/scoreboard-chat/' + id);
        }
    }
}]);

app.factory('UserPerms', function()
{
    var data = {
        UserPermsData: []
    };

    return {
        get: function() {
            return data.UserPermsData;
        },
        set: function(perms) {
            data.UserPermsData = perms;
        }
    }
});

app.factory('SBAdmin', ['$http', function($http)
{
    var _data = {};

    var _return = {

        pinfo: function(id, player) {
            _data = {
                'server_id': id,
                'player_name': player
            };

            return $http.post('api/v1/admin/server/player', _data);
        },

        say: function(id, message) {
            _data = {
                'server_id': id,
                'type': 'all',
                'message': message
            };

            return $http.post('api/v1/admin/server/message', _data);
        },

        psay: function(id, player, message) {
            _data = {
                'server_id': id,
                'type': 'player',
                'message': message,
                'players': player
            };

            return $http.post('api/v1/admin/server/message', _data);
        },

        punish: function(id, player, message) {
            _data = {
                'server_id': id,
                'players': player,
                'message': message
            };

            return $http.post('api/v1/admin/server/punish', _data);
        },

        checkPunish: function(id) {
            _data = {
                'record_id': id
            };

            return $http.post('api/v1/admin/server/check-punish-record', _data);
        },

        forgive: function(id, player, message) {
            _data = {
                'server_id': id,
                'players': player,
                'message': message
            };

            return $http.post('api/v1/admin/server/forgive', _data);
        },

        kill: function(id, player, message) {
            _data = {
                'server_id': id,
                'players': player,
                'message': message
            };

            return $http.post('api/v1/admin/server/kill', _data);
        },

        kick: function(id, player, message) {
            _data = {
                'server_id': id,
                'players': player,
                'message': message
            };

            return $http.post('api/v1/admin/server/kick', _data);
        },

        mute: function(id, player, message) {
            _data = {
                'server_id': id,
                'players': player,
                'message': message
            };

            return $http.post('api/v1/admin/server/mute', _data);
        },

        teamswitch: function(id, player) {
            _data = {
                'server_id': id,
                'players': player
            };

            return $http.post('api/v1/admin/server/team-swap', _data);
        },

        squadswitch: function(id, player, squad) {
            _data = {
                'server_id': id,
                'players': player,
                'newSquad': squad
            };

            return $http.post('api/v1/admin/server/squad-swap', _data);
        },

        tban: function(id, player, message, duration) {
            _data = {
                'server_id': id,
                'players': player,
                'message': message,
                'duration': duration
            };

            return $http.post('api/v1/admin/server/temp-ban', _data);
        },

        pban: function(id, player, message) {
            _data = {
                'server_id': id,
                'players': player,
                'message': message
            };

            return $http.post('api/v1/admin/server/perma-ban', _data);
        },

        kickall: function(id, message) {
            _data = {
                'server_id': id,
                'message': message
            };

            return $http.post('api/v1/admin/server/kick-all', _data);
        },

        killall: function(id, message) {
            _data = {
                'server_id': id,
                'message': message
            };

            return $http.post('api/v1/admin/server/kill-all', _data);
        },

        yell: function(id, message, duration) {
            _data = {
                'server_id': id,
                'message': message,
                'type': 'all',
                'duration': duration
            };

            return $http.post('api/v1/admin/server/yell-message', _data);
        },

        pyell: function(id, player, message, duration) {
            _data = {
                'server_id': id,
                'message': message,
                'type': 'player',
                'players': player,
                'duration': duration
            };

            return $http.post('api/v1/admin/server/yell-message', _data);
        }

    };

    return _return;
}]);

var toastrOptions = {
    "closeButton": true,
    "debug": false,
    "positionClass": "toast-bottom-left",
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "5000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "slideDown",
    "hideMethod": "slideUp"
};

app.controller("ScoreboardCtrl", ['$scope', '$timeout', '$location', 'Scoreboard', 'UserPerms', 'SBAdmin', 'ReportsStorage', function($scope, $timeout, $location, Scoreboard, UserPerms, SBAdmin, ReportsStorage)
{
    var refreshInterval = 10,
        id = null,
        teaminfo = null;

    $scope.loaded = false;

    $scope.sort = {
        column: 'player_score',
        descending: true
    };

    if($location.path() !== '')
    {
        var pathArray = $location.path().split('/');

        $scope.serverSelect = pathArray[2];

        $("#serversel").val(pathArray[2]);
    }

    id = $("#serversel").val();

    $scope.pingcheck = function(ping)
    {
        var classStyle;

        if(ping < 140) {
            classStyle = 'bg-green';
        } else if(ping >= 140 && ping < 250) {
            classStyle = 'bg-yellow';
        } else if(ping >= 250 && ping < 1000) {
            classStyle = 'bg-red';
        } else {
            classStyle = 'bg-blue';
        }

        return classStyle;
    };

    $scope.switchServer = function()
    {
        id = $("#serversel").val();
        $scope.loaded = false;
        $scope.$emit('changeServerEvent', {'id': id});

        $location.path("/server/" + id);

        $scope.clearPlayer();

        $timeout.cancel($scope.boardTimeout);

        setTimeout(function() {$scope.fetchBoardData();}, 400);
    };

    $scope.changeSorting = function(column)
    {
        var sort = $scope.sort;

        if(sort.column == column)
        {
            sort.descending = !sort.descending;
        }
        else
        {
            sort.column     = column;
            sort.descending = false;
        }
    };

    $scope.sortClass = function(column)
    {
        if($scope.sort.column == column)
        {
            if($scope.sort.descending) {
                return 'fa fa-sort-desc';
            } else {
                return 'fa fa-sort-asc';
            }
        }
        else
        {
            return 'fa fa-sort';
        }
    };

    $scope.whoIsWinning = function()
    {
        var team1 = $scope.team1,
            team2 = $scope.team2,
            target = $scope.serverinfo.ticket_cap,
            mode = $scope.serverinfo.gamemode_uri,
            num = null;

        if(target === null || mode == "RushLarge0")
        {
            $scope.team1lead = false;
            $scope.team2lead = false;
            return;
        }

        if(target > 0)
        {
            num = Math.max(team1.ticketcount, team2.ticketcount);
        }
        else
        {
            num = Math.min(team1.ticketcount, team2.ticketcount);
        }

        switch(mode)
        {
            case "Domination0":
            case "Obliteration":
            case "Chainlink0":
            case "ConquestLarge0":
            case "ConquestSmall0":
                if(team1.ticketcount == team2.ticketcount)
                {
                    $scope.team1lead = false;
                    $scope.team2lead = false;
                }
                else if(num == team1.ticketcount)
                {
                    $scope.team1lead = false;
                    $scope.team2lead = true;
                }
                else if(num == team2.ticketcount)
                {
                    $scope.team1lead = true;
                    $scope.team2lead = false;
                }
            break;

            default:
                if(team1.ticketcount == team2.ticketcount)
                {
                    $scope.team1lead = false;
                    $scope.team2lead = false;
                }
                else if(num == team1.ticketcount)
                {
                    $scope.team1lead = true;
                    $scope.team2lead = false;
                }
                else if(num == team2.ticketcount)
                {
                    $scope.team1lead = false;
                    $scope.team2lead = true;
                }
            break;
        }
    };

    $scope.wasReported = function(name)
    {
        var _reports = ReportsStorage.get();

        angular.forEach(_reports, function(report, key) {
            if(report.server.id == id) {
                if(name == report.source) {
                    if(report.action_id == 18 || report.action_id == 20) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        });
    };

    $scope.fetchBoardData = function()
    {
        if(id == 'none')
        {
            $scope.loaded = false;
            return;
        }

        $scope.refreshing = true;

        Scoreboard.get(id).success(function(data)
            {
                if(data.status == 'error')
                {
                    toastr.error(data.message, null, toastrOptions);
                    return;
                }

                $scope.perm = data.data._permission;
                $scope.premessages = data.data._premessages

                UserPerms.set($scope.perm);

                teaminfo = data.data.teaminfo;

                $scope.loaded = true;
                $scope.refreshing = false;

                $scope.isBF3 = data.data.isBF3;
                $scope.isBF4 = data.data.isBF4;

                $scope.serverinfo = data.data.serverinfo;
                $scope.team1 = teaminfo[1];
                $scope.team2 = teaminfo[2];

                $scope.online_admins = data.data.online_admins;

                $scope.connecting = ( teaminfo[0].playerlist !== undefined ? teaminfo[0].playerlist : [] );

                if($scope.isBF4)
                {
                    $scope.spectators = ( teaminfo[0].spectators !== undefined ? teaminfo[0].spectators : [] );
                }

                $scope.whoIsWinning();

                $("#online_admins_list").html('');
                angular.forEach($scope.online_admins, function(value, key) {
                    $("#online_admins_list").append('<li>' + value.player_name + '</li>');
                });
            }).error(function(data)
            {
                toastr.error(data.message, null, toastrOptions);
                $scope.loaded = false;
                $scope.refreshing = false;
            });

        $scope.boardTimeout = $timeout(function() {$scope.fetchBoardData();}, refreshInterval*1000);
    };

    $scope.fetchBoardData();

    // Admin

    $scope._messages = {
        errors: {
            denied: 'Access Denied! You do not have permission to preform that action.',
            msgshort: 'Your message is less than 3 characters. Please enter a few more characters.'
        }
    };

    $scope.admin = {
        message: null,
        squad: {
            new: null,
            old: null
        },
        views: {
            punish: false,
            forgive: false,
            kill: false,
            kick: false,
            mute: false,
            teamswitch: false,
            squadswitch: false,
            tempban: false,
            permban: false,
            kickall: false,
            nuke: false,
            yell: false,
            pyell: false,
            psay: false
        }
    };

    $scope.player = {
        player_deaths: null,
        player_id: null,
        player_kdr: null,
        player_kills: null,
        player_name: null,
        player_ping: null,
        player_rank: null,
        player_score: null,
        player_squad: null,
        player_squad_id: null,
        player_team: null,
        player_db_id: null
    };

    $scope.squadlist = [
        'None',
        'Alpha',
        'Bravo',
        'Charlie',
        'Delta',
        'Echo',
        'Foxtrot',
        'Golf',
        'Hotel',
        'India',
        'Juliet',
        'Kilo',
        'Lima',
        'Mike',
        'November',
        'Oscar',
        'Papa',
        'Quebec',
        'Romeo',
        'Sierra',
        'Tango',
        'Uniform',
        'Victor',
        'Whiskey',
        'Xray',
        'Yankee',
        'Zulu',
        'Haggard',
        'Sweetwater',
        'Preston',
        'Redford',
        'Faith',
        'Celeste'
    ];

    $scope.showView = function(view) {
        angular.forEach($scope.admin.views, function(value, key) {
            if(view == key) {
                if($scope.admin.views[key]) {
                    $scope.admin.views[key] = false;
                } else {
                    $scope.admin.views[key] = true;
                }
            } else {
                $scope.admin.views[key] = false;
            }
        });
    };

    function checkPunish(id) {
        SBAdmin.checkPunish(id).success(function(data) {
            if(data.status == 'success') {
                toastr.success(data.message, '', {
                    "closeButton": true
                });

                return;
            } else {
                checkPunish(id);
            }
        }).error(function(data) {
            toastr.error(data.message, "Error");
        });
    }

    $scope.hasPermission = function(permCheck) {
        switch(permCheck) {
            case "kill":
                if($scope.perm.kill) {
                    return true;
                } else {
                    return false;
                }
            break;

            case "kick":
                if($scope.perm.kick) {
                    return true;
                } else {
                    return false;
                }
            break;

            case "tban":
                if($scope.perm.tban) {
                    return true;
                } else {
                    return false;
                }
            break;

            case "ban":
                if($scope.perm.ban) {
                    return true;
                } else {
                    return false;
                }
            break;

            case "yell":
                if($scope.perm.yell) {
                    return true;
                } else {
                    return false;
                }
            break;

            case "tyell":
                if($scope.perm.tyell) {
                    return true;
                } else {
                    return false;
                }
            break;

            case "pyell":
                if($scope.perm.pyell) {
                    return true;
                } else {
                    return false;
                }
            break;

            case "pmute":
                if($scope.perm.pmute) {
                    return true;
                } else {
                    return false;
                }
            break;

            case "team":
                if($scope.perm.team) {
                    return true;
                } else {
                    return false;
                }
            break;

            case "squad":
                if($scope.perm.squad) {
                    return true;
                } else {
                    return false;
                }
            break;

            case "punish":
                if($scope.perm.punish) {
                    return true;
                } else {
                    return false;
                }
            break;

            case "forgive":
                if($scope.perm.forgive) {
                    return true;
                } else {
                    return false;
                }
            break;

            case "kickall":
                if($scope.perm.kickall) {
                    return true;
                } else {
                    return false;
                }
            break;

            case "nuke":
                if($scope.perm.nuke) {
                    return true;
                } else {
                    return false;
                }
            break;

            case "psay":
                if($scope.perm.psay) {
                    return true;
                } else {
                    return false;
                }
            break;
        }
    };

    $scope.clearPlayer = function() {
        $scope.player = {
            player_deaths: null,
            player_id: null,
            player_kdr: null,
            player_kills: null,
            player_name: null,
            player_ping: null,
            player_rank: null,
            player_score: null,
            player_squad: null,
            player_squad_id: null,
            player_team: null,
            player_db_id: null
        };
    };

    $scope.setPlayer = function(player) {
        if(!$scope.perm.bf3 || !$scope.perm.bf4) return;
        $scope.clearPlayer();
        $scope.player = player;
        SBAdmin.pinfo(id, player.player_name).success(function(data) {
            if(data.status == 'success') {
                $scope.player.player_db_id = data.data.player_id;
            } else {
                $scope.player.player_db_id = null;
            }
        });
        jumpToAnchor("admin_controls");
    };

    $scope.haveDbId = function() {
        var dbid = $scope.player.player_db_id;
        if(dbid === null || angular.isUndefined(dbid)) {
            return false;
        } else {
            return true;
        }
    };

    $scope.issuePunish = function() {

        var _this = $("#admin_punish");

        if(!$scope.hasPermission('punish')) {
            toastr.error($scope._messages.errors.denied, null, toastrOptions);
            return;
        }

        if($scope.admin.message === null || $scope.admin.message.length < 3) {
            toastr.error($scope._messages.errors.msgshort, null, toastrOptions);
            return;
        }

        _this.button('loading');

        SBAdmin.punish(id, $scope.player.player_name, $scope.admin.message).success(function(data) {
            if(data.status == 'success') {
                toastr.info(data.message, null, toastrOptions);
                checkPunish(data.data.record.record_id);
            } else {
                toastr.error(data.message, null, toastrOptions);
            }

            $scope.clearPlayer();
        }).finally(function() {
            _this.button('reset');
        });
    };

    $scope.issueForgive = function() {

        var _this = $("#admin_forgive");

        if(!$scope.hasPermission('forgive')) {
            toastr.error($scope._messages.errors.denied, null, toastrOptions);
            return;
        }

        if($scope.admin.message === null || $scope.admin.message.length < 3) {
            toastr.error($scope._messages.errors.msgshort, null, toastrOptions);
            return;
        }

        _this.button('loading');

        SBAdmin.forgive(id, $scope.player.player_name, $scope.admin.message).success(function(data) {
            if(data.status == 'success') {
                toastr.info(data.message, null, toastrOptions);
            } else {
                toastr.error(data.message, null, toastrOptions);
            }

            $scope.clearPlayer();
        }).finally(function() {
            _this.button('reset');
        });
    };

    $scope.issueKill = function() {

        var _this = $("#admin_kill");

        if(!$scope.hasPermission('kill')) {
            toastr.error($scope._messages.errors.denied, null, toastrOptions);
            return;
        }

        if($scope.admin.message === null || $scope.admin.message.length < 3) {
            toastr.error($scope._messages.errors.msgshort, null, toastrOptions);
            return;
        }

        _this.button('loading');

        SBAdmin.kill(id, $scope.player.player_name, $scope.admin.message).success(function(data) {
            if(data.status == 'success') {
                toastr.info(data.message, null, toastrOptions);
            } else {
                toastr.error(data.message, null, toastrOptions);
            }

            $scope.clearPlayer();
        }).finally(function() {
            _this.button('reset');
        });
    };

    $scope.issueKick = function() {

        var _this = $("#admin_kick");

        if(!$scope.hasPermission('kick')) {
            toastr.error($scope._messages.errors.denied, null, toastrOptions);
            return;
        }

        if($scope.admin.message === null || $scope.admin.message.length < 3) {
            toastr.error($scope._messages.errors.msgshort, null, toastrOptions);
            return;
        }

        _this.button('loading');

        SBAdmin.kick(id, $scope.player.player_name, $scope.admin.message).success(function(data) {
            if(data.status == 'success') {
                toastr.info(data.message, null, toastrOptions);
            } else {
                toastr.error(data.message, null, toastrOptions);
            }

            $scope.clearPlayer();
        }).finally(function() {
            _this.button('reset');
        });
    };

    $scope.issueMute = function() {

        var _this = $("#admin_mute");

        if(!$scope.hasPermission('pmute')) {
            toastr.error($scope._messages.errors.denied, null, toastrOptions);
            return;
        }

        if($scope.admin.message === null || $scope.admin.message.length < 3) {
            toastr.error($scope._messages.errors.msgshort, null, toastrOptions);
            return;
        }

        _this.button('loading');

        SBAdmin.mute(id, $scope.player.player_name, $scope.admin.message).success(function(data) {
            if(data.status == 'success') {
                toastr.info(data.message, null, toastrOptions);
            } else {
                toastr.error(data.message, null, toastrOptions);
            }

            $scope.clearPlayer();
        }).finally(function() {
            _this.button('reset');
        });
    };

    $scope.issueTeamSwitch = function() {

        var _this = $("#admin_teamswap");

        if(!$scope.hasPermission('team')) {
            toastr.error($scope._messages.errors.denied, null, toastrOptions);
            return;
        }

        _this.button('loading');

        SBAdmin.teamswitch(id, $scope.player.player_name).success(function(data) {
            if(data.status == 'success') {
                toastr.info(data.message, null, toastrOptions);
            } else {
                toastr.error(data.message, null, toastrOptions);
            }

            $scope.clearPlayer();
        }).finally(function() {
            _this.button('reset');
        });
    };

    $scope.issueSquadSwitch = function() {

        var _this = $("#admin_squadswap");

        if(!$scope.hasPermission('squad')) {
            toastr.error($scope._messages.errors.denied, null, toastrOptions);
            return;
        }

        _this.button('loading');

        SBAdmin.squadswitch(id, $scope.player.player_name, $scope.admin.squad.new).success(function(data) {
            if(data.status == 'success') {
                toastr.info(data.message, null, toastrOptions);
            } else {
                toastr.error(data.message, null, toastrOptions);
            }

            $scope.clearPlayer();
        }).finally(function() {
            _this.button('reset');
        });
    };

    $scope.issueTempBan = function() {
        var _this = $("#admin_tban");

        if(!$scope.hasPermission('tban')) {
            toastr.error($scope._messages.errors.denied, null, toastrOptions);
            return;
        }

        _this.button('loading');

        SBAdmin.tban(id, $scope.player.player_name, $scope.admin.message, $scope.admin.ban.duration).success(function(data) {
            if(data.status == 'success') {
                toastr.info(data.message, null, toastrOptions);
            } else {
                toastr.error(data.message, null, toastrOptions);
            }

            $scope.clearPlayer();
        }).finally(function() {
            _this.button('reset');
        });
    };

    $scope.issuePermBan = function() {

        var _this = $("#admin_pban");

        if(!$scope.hasPermission('ban')) {
            toastr.error($scope._messages.errors.denied, null, toastrOptions);
            return;
        }

        _this.button('loading');

        SBAdmin.pban(id, $scope.player.player_name, $scope.admin.message).success(function(data) {
            if(data.status == 'success') {
                toastr.info(data.message, null, toastrOptions);
            } else {
                toastr.error(data.message, null, toastrOptions);
            }

            $scope.clearPlayer();
        }).finally(function() {
            _this.button('reset');
        });
    };

    $scope.issueKickAll = function() {

        var _this = $("#admin_kickall");

        if(!$scope.hasPermission('kickall')) {
            toastr.error($scope._messages.errors.denied, null, toastrOptions);
            return;
        }

        if(confirm("Are you sure you want to kick all the players from the server?"))
        {
            _this.button('loading');

            SBAdmin.kickall(id, $scope.admin.message).success(function(data) {
                if(data.status == 'success') {
                    toastr.info(data.message, null, toastrOptions);
                } else {
                    toastr.error(data.message, null, toastrOptions);
                }

                $scope.clearPlayer();
            }).finally(function() {
                _this.button('reset');
            });
        }
        else
        {
            $scope.clearPlayer();
            toastr.info("Command was not sent to server. Aborting!", null, toastrOptions);
        }
    };

    $scope.issueNuke = function() {

        var _this = $("#admin_killall");

        if(!$scope.hasPermission('nuke')) {
            toastr.error($scope._messages.errors.denied, null, toastrOptions);
            return;
        }

        if(confirm("Are you sure you want to kill all the players on the server?"))
        {
            _this.button('loading');

            SBAdmin.killall(id, $scope.admin.message).success(function(data) {
                if(data.status == 'success') {
                    toastr.info(data.message, null, toastrOptions);
                } else {
                    toastr.error(data.message, null, toastrOptions);
                }

                $scope.clearPlayer();
            }).finally(function() {
                _this.button('reset');
            });
        }
        else
        {
            $scope.clearPlayer();
            toastr.info("Command was not sent to server. Aborting!", null, toastrOptions);
        }
    };

    $scope.issueYell = function() {

        var _this = $("#admin_yell");

        if(!$scope.hasPermission('yell')) {
            toastr.error($scope._messages.errors.denied, null, toastrOptions);
            return;
        }

        _this.button('loading');

        SBAdmin.yell(id, $scope.admin.message, $scope.admin.yell.duration).success(function(data) {
            if(data.status == 'success') {
                toastr.info(data.message, null, toastrOptions);
            } else {
                toastr.error(data.message, null, toastrOptions);
            }
        }).finally(function() {
            _this.button('reset');
        });
    };

    $scope.issuePlayerYell = function() {

        var _this = $("#admin_pyell");

        if(!$scope.hasPermission('pyell')) {
            toastr.error($scope._messages.errors.denied, null, toastrOptions);
            return;
        }

        _this.button('loading');

        SBAdmin.pyell(id, $scope.player.player_name, $scope.admin.message, $scope.admin.yell.duration).success(function(data) {
            if(data.status == 'success') {
                toastr.info(data.message, null, toastrOptions);
            } else {
                toastr.error(data.message, null, toastrOptions);
            }
        }).finally(function() {
            _this.button('reset');
        });
    };

    $scope.issuePlayerSay = function() {

        var _this = $("#admin_psay");

        if(!$scope.hasPermission('psay')) {
            toastr.error($scope._messages.errors.denied, null, toastrOptions);
            return;
        }

        _this.button('loading');

        SBAdmin.psay(id, $scope.player.player_name, $scope.admin.message).success(function(data) {
            if(data.status == 'success') {
                toastr.info(data.message, null, toastrOptions);
            } else {
                toastr.error(data.message, null, toastrOptions);
            }
        }).finally(function() {
            _this.button('reset');
        });
    };
}]);

app.controller("ScoreboardChat", [ '$scope', '$timeout', 'Chat', 'UserPerms', 'SBAdmin', function($scope, $timeout, Chat, UserPerms, SBAdmin) {
    $scope.refreshInterval = 10;
    $scope.isLoaded = false;
    $scope.user = {
        message: ''
    };

    var id = angular.element('#serversel').val();

    $scope.$on('changeServerEvent', function(event, args) {
        $scope.isLoaded = false;
        id = args.id;
        $timeout.cancel($scope.chatTimeout);
        setTimeout(function() {$scope.fetchChat();}, 400);
    });

    $scope.perm = UserPerms.get();

    $scope.fetchChat = function() {

        if(id == 'none')
        {
            $scope.loaded = false;
            return false;
        }

        $scope.refreshing = true;

        Chat.get(id)
            .success(function(data) {

                $scope.isLoaded = true;
                $scope.refreshing = false;

                $scope.entrys = data.data;
            })
            .error(function(data, status, headers, config) {
                $scope.isLoaded = true;
                $scope.refreshing = false;
            });

        if($("#chat-box").parent().hasClass("slimScrollDiv"))
        {
            if($("#chat-box").is(":hover") === false)
            {
                $('#chat-box').slimScroll({scrollTo: $('#chat-box').prop('scrollHeight') + 'px'});
            }
        }

        $scope.chatTimeout = $timeout(function() { $scope.fetchChat(); }, $scope.refreshInterval * 1000);
    };

    $scope.fetchChat();

    $timeout(function() {
        $('#chat-box').slimScroll({height:'450px'});
    }, 800);

    // Admin

    $scope.sending = false;

    $scope.sendMessage = function(message) {
        if(!$scope.perm.say) {
            toastr.error("You do not have permission to preform that action.", "Access Denied!", toastrOptions);
            return false;
        }

        if(message === undefined || message.length === 0) {
            toastr.info("You can't send a blank message.", null, toastrOptions);
            return false;
        }

        $scope.sending = true;

        SBAdmin.say(id, message, 'all').success(function(data) {
            if(data.status == 'success') {
                $timeout.cancel($scope.chatTimeout);
                $scope.fetchChat();
                $("#userMessage").val('');
                $scope.user.message = '';
                toastr.success(message, 'Message Sent', toastrOptions);
            } else {
                toastr.error(data.message, null, toastrOptions);
            }
        }).error(function(data) {
            toastr.error(data.message, null, toastrOptions);
        }).finally(function() {
            $scope.sending = false;
        });
    };

}]);

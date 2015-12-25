angular.module('bfacp').factory('SBA', ['$http', function ($http) {
    var baseurl = 'api/servers/admin/scoreboard';

    return {
        say: function (server_id, players, type, message, teamID) {
            var payload = {
                server_id: server_id,
                type: type,
                message: message,
                team: teamID,
                players: players
            };

            return $http.post(baseurl + '/say', payload);
        },

        yell: function (server_id, players, type, message, teamID, duration) {
            var payload = {
                server_id: server_id,
                type: type,
                message: message,
                team: teamID,
                players: players,
                duration: duration
            };

            return $http.post(baseurl + '/yell', payload);
        },

        tell: function (server_id, players, message) {
            var payload = {
                server_id: server_id,
                message: message,
                players: players
            };

            return $http.post(baseurl + '/tell', payload);
        },

        kill: function (server_id, players, message) {
            var payload = {
                server_id: server_id,
                message: message,
                players: players
            };

            return $http.post(baseurl + '/kill', payload);
        },

        kick: function (server_id, players, message) {
            var payload = {
                server_id: server_id,
                message: message,
                players: players
            };

            return $http.post(baseurl + '/kick', payload);
        },

        teamswitch: function (server_id, players, team, squad, locked) {
            var payload = {
                server_id: server_id,
                team: team,
                squad: squad,
                locked: locked,
                players: players
            };

            return $http.post(baseurl + '/teamswitch', payload);
        },

        punish: function (server_id, players, message) {
            var payload = {
                server_id: server_id,
                message: message,
                players: players
            };

            return $http.post(baseurl + '/punish', payload);
        }
    }
}]);

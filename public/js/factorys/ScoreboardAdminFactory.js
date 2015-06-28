angular.module('bfacp').factory('SBA', ['$http', function($http) {
    var payload = {};
    var url = 'api/servers/scoreboard/admin';

    return {
        say: function(sid, players, type, message, teamID) {
            payload = {
                method: 'say',
                server_id: sid,
                type: type,
                message: message,
                team: teamID,
                players: players
            };

            return $http.post(url, payload);
        }
    }
}]);

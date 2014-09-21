app.factory('Scoreboard', function($http)
{
    return {

        // Get the server information and players
        get: function(id) {
            return $http.get('api/v1/common/general/scoreboard/' + id);
        }
    }
});

app.factory('Chat', ['$http', function($http)
{
    return {
        get: function(id) {
            return $http.get('api/v1/common/general/scoreboard-chat/' + id);
        }
    }
}]);

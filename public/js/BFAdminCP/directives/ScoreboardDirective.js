app.directive('scoreboard', function()
{
    return {
        restrict: 'E',
        templateUrl: 'js/BFAdminCP/templates/scoreboard/scoreboard.html',
        controller: 'ScoreboardCtrl'
    }
});

app.directive('scoreboardChat', function()
{
    return {
        restrict: 'E',
        templateUrl: 'js/BFAdminCP/templates/scoreboard/livechat.html',
        controller: 'ScoreboardChat'
    }
});

app.directive('playerlist', function() {
    return {
        restrict: 'A',
        templateUrl: 'js/BFAdminCP/templates/scoreboard/playerlist.html',
        scope: {
            details: '=',
            imagepath: '=',
            isBF3: '=',
            isBF4: '=',
            isAdmin: '=',
            sortClass: "&"
        }
    }
});

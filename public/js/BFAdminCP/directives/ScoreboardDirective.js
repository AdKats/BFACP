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

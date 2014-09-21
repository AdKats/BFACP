app.controller("BF3BanFeed", ['$scope', '$http', '$timeout', function($scope, $http, $timeout)
{
    $scope.refreshInt = 10;
    $scope.loaded = false;
    $scope.fault = false;

    $scope.fetch = function()
    {
        $scope.refresh = true;

        $http({ method: 'GET', url: 'api/v1/bf3/population'})
            .success(function(data, status)
            {
                if(data.status == 'error')
                {
                    $scope.loaded  = true;
                    $scope.fault   = true;
                    $scope.refresh = false;
                    $scope.message = data.message;
                    return false;
                }

                $scope.servers = data.data.servers;
                $scope.total   = data.data.total;
                $scope.loaded  = true;
                $scope.refresh = false;
                $scope.fault   = false;
            })
            .error(function(data, status)
            {
                if(data.status == 'error')
                {
                    console.error(data.message);
                }
            });

        $timeout(function() { $scope.fetch(); }, $scope.refreshInt * 1000);
    };

    $scope.fetch();

}]);

app.controller("BF4BanFeed", ['$scope', '$http', '$timeout', function($scope, $http, $timeout)
{
    $scope.refreshInt = 10;
    $scope.loaded = false;
    $scope.fault = false;

    $scope.fetch = function()
    {
        $scope.refresh = true;

        $http({ method: 'GET', url: 'api/v1/bf4/population'})
            .success(function(data, status)
            {
                if(data.status == 'error')
                {
                    $scope.loaded  = true;
                    $scope.fault   = true;
                    $scope.refresh = false;
                    $scope.message = data.message;
                    return false;
                }

                $scope.servers = data.data.servers;
                $scope.total   = data.data.total;
                $scope.loaded  = true;
                $scope.refresh = false;
                $scope.fault   = false;
            })
            .error(function(data, status)
            {
                if(data.status == 'error')
                {
                    console.error(data.message);
                }
            });

        $timeout(function() { $scope.fetch(); }, $scope.refreshInt * 1000);
    };

    $scope.fetch();

}]);

app.controller("MetabansFeed", ['$scope', '$http', '$timeout', function($scope, $http, $timeout)
{
    $scope.refreshInt = 300;
    $scope.loaded = false;
    $scope.fault = false;

    $scope.fetch = function()
    {
        $scope.refresh = true;

        $http({ method: 'POST', url: 'api/v1/common/metabans_feed'})
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

                $scope.banlist = data.data;
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

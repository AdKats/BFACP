app.factory('AdKatsSpecial', ['$http', function($http)
{
    return {

        getList: function(pageId) {
            return $http.get('api/v1/acp/adkats/special_playerlist', {params: {page: pageId} });
        }
    }
}]);

app.controller('PlayerList', ['$scope', 'AdKatsSpecial', function ($scope, AdKatsSpecial) {

    moment.locale($("#locale").val());

    $scope.main = {
        page: 1,
        from: null,
        to: null,
        current_page: null,
        total: null,
        per_page: null
    };

    $scope.filters = [];

    $scope.players = [];

    $scope.isLoading = false;

    $scope.loadPage = function()
    {
        $scope.isLoading = true;

        if($scope.players.length > 0) $scope.players = [];

        AdKatsSpecial.getList($scope.main.page)
            .success(function(data) {
                $scope.isLoading = false;
                $scope.players = data.data.data;
                $scope.main.records      = data.data.data;
                $scope.main.pages        = data.data.last_page;
                $scope.main.total        = data.data.total;
                $scope.main.from         = data.data.from;
                $scope.main.to           = data.data.to;
                $scope.main.current_page = data.data.current_page;
                $scope.main.per_page     = data.data.per_page;
            })
            .error(function(data, status) {
                console.error(data);
            });
    };

    $scope.nextPage = function() {
        if ($scope.main.page < $scope.main.pages) {
            $scope.main.page++;
            $scope.loadPage();
        }
    };

    $scope.previousPage = function() {
        if ($scope.main.page > 1) {
            $scope.main.page--;
            $scope.loadPage();
        }
    };

    $scope.firstPage = function() {
        if ($scope.main.page > 1) {
            $scope.main.page = 1;
            $scope.loadPage();
        }
    };

    $scope.lastPage = function() {
        if ($scope.main.page < $scope.main.pages) {
            $scope.main.page = $scope.main.pages;
            $scope.loadPage();
        }
    };

    $scope.formatDate = function(date)
    {
        var datetime = moment(date);

        return datetime.format('lll');
    };

    $scope.loadPage();
}]);

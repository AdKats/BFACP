angular.module('bfacp').controller('ServerController', ['$scope', '$http', '$filter', 'ngTableParams', function ($scope, $http, $filter, ngTableParams) {
    $scope.population = [];
    $scope.maps = {
        'popular': []
    };
    $scope.loading = false;

    var server_id = $('#server_id').val();

    var popular_maps = new Highcharts.Chart({
        chart: {
            renderTo: 'popular-maps',
            plotBackgroundColor: null,
            plotBorderWidth: null,
            plotShadow: false,
            type: 'pie'
        },
        title: {
            text: 'Popular Maps (2 Weeks)'
        },
        tooltip: {
            pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
        },
        plotOptions: {
            pie: {
                allowPointSelect: true,
                cursor: 'pointer',
                dataLabels: {
                    enabled: true,
                    format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                    style: {
                        color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                    }
                }
            }
        },
        series: [{
            name: 'Popular Maps',
            colorByPoint: true,
            data: $scope.maps
        }]
    });
    var population_history = new Highcharts.Chart({
        chart: {
            renderTo: 'population-history',
            zoomType: 'x'
        },
        title: {
            text: 'Population (2 Weeks)'
        },
        subtitle: {
            text: document.ontouchstart === undefined ?
                'Click and drag in the plot area to zoom in' : 'Pinch the chart to zoom in'
        },
        xAxis: {
            type: 'datetime'
        },
        yAxis: {
            title: {
                text: 'Players'
            },
            min: 0
        },
        legend: {
            enabled: false
        },
        plotOptions: {
            area: {
                fillColor: {
                    linearGradient: {
                        x1: 0,
                        y1: 0,
                        x2: 0,
                        y2: 1
                    },
                    stops: [
                        [0, Highcharts.getOptions().colors[0]],
                        [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
                    ]
                },
                marker: {
                    radius: 2
                },
                lineWidth: 1,
                states: {
                    hover: {
                        lineWidth: 1
                    }
                },
                threshold: null
            }
        },
        series: [{
            type: 'area',
            name: 'Players Online',
            data: $scope.population
        }]
    });

    var fetchServerStats = function () {
        $scope.loading = true;
        $http.get('api/servers/extras/' + server_id).success(function (data) {
            $scope.population = data.data.population;
            $scope.maps.popular = data.data.maps_popular;
            $scope.maps.table = new ngTableParams({
                page: 1,
                count: 10,
                sorting: {
                    map_load: 'desc'
                }
            }, {
                total: data.data.maps.length,
                getData: function ($defer, params) {
                    var orderedData = params.sorting() ? $filter('orderBy')(data.data.maps, params.orderBy()) : data.data.maps

                    $defer.resolve(
                        orderedData.slice((params.page() - 1) * params.count(), params.page() * params.count())
                    );
                }
            });

            popular_maps.series[0].setData($scope.maps.popular);
            population_history.series[0].setData($scope.population);
            $scope.loading = false;
            $(window).resize();
        }).error(function () {
            setTimeout(fetchServerStats, 2000);
        });
    };

    setTimeout(fetchServerStats, 200);
}]);

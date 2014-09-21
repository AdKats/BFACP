app.factory('Player', function($http)
{
    return {

        // Get the server information and players
        graphs: function(pid) {
            return $http.post('/player/' + pid + '/extended/history');
        },
        reputation: function(pid) {
            return $http.post('/player/' + pid + '/extended/rep');
        },
        stats: function(pid) {
            return $http.post('/player/' + pid + '/extended/stats');
        },
        chatlog: function(pid, pageNum, serverid, message) {
            return $http.post('/player/' + pid + '/extended/chatlog', {page: pageNum, filter_server_id: serverid, filter_message_string: message});
        },
        thirdparty: function(pid) {
            return $http.post('/player/' + pid + '/extended/sites');
        },
        records: function(pid, pageNum, searchBy, filterCmd) {
            return $http.post('/player/' + pid + '/extended/records', {type: searchBy, page: pageNum, filter_command: filterCmd})
        },
        forgive: function(pid, server, message, xtimes) {
            return $http.post('/player/' + pid + '/forgive', {xtimes: xtimes, server: server, message: message});
        }
    }
});

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

app.controller("PlayerIssueForgive", ['$scope', 'Player', function($scope, Player)
{
    var pid = angular.element('#PID').val();

    $scope.showActions = false;
    $scope.forgive = {
        xtimes: 1,
        message: 'ForgivePlayer',
        server: null
    };

    $scope.forgiveCheck = function(id)
    {
        $scope.showActions = true;
        $scope.forgive.server = id;
    };

    $scope.cancelForgive = function()
    {
        $scope.showActions = false;
        $scope.forgive = {
            xtimes: 1,
            message: 'ForgivePlayer',
            server: null
        };
    };

    $scope.issueForgive = function()
    {
        $("#forgive_submit").button('loading');

        if($scope.forgive.server === null)
        {
            $scope.showActions = false;
            return false;
        }

        Player.forgive(pid, $scope.forgive.server, $scope.forgive.message, $scope.forgive.xtimes).success(function(data)
        {
            if(data.status == 'success') {
                if(data.data.class == 'success')
                {
                    toastr.success(data.message, null, toastrOptions);
                }
                else if(data.data.class == 'warning')
                {
                    toastr.warning(data.message, null, toastrOptions);
                }
            } else {
                toastr.error(data.message, null, toastrOptions);
            }

            $scope.showActions = false;
            $scope.forgive = {
                xtimes: 1,
                message: 'ForgivePlayer',
                server: null
            };
        }).finally(function() {
            $("#forgive_submit").button('reset');
        });
    };

}]);

app.controller("PlayerInfoRecordsOn", ['$scope', 'Player', function($scope, Player)
{
    var pid = angular.element('#PID').val();

    $scope.main = {
        page: 1,
        from: null,
        to: null,
        current_page: null,
        total: null,
        per_page: null,
        filters: {
            cmdid: null
        }
    };

    $scope.isLoading = false;

    $scope.loadPage = function()
    {
        $scope.isLoading = true;

        Player.records(pid, $scope.main.page, 'on', $scope.main.filters.cmdid).success(function(data)
        {
            $scope.isLoading = false;

            $scope.main.records      = data.data.data;
            $scope.main.pages        = data.data.last_page;
            $scope.main.total        = data.data.total;
            $scope.main.from         = data.data.from;
            $scope.main.to           = data.data.to;
            $scope.main.current_page = data.data.current_page;
            $scope.main.per_page     = data.data.per_page;
        });
    };

    $scope.sendQuery = function() {
        $scope.main.page = 1;
        $scope.loadPage();
    }

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

    $scope.loadPage();
}]);

app.controller("PlayerInfoRecordsBy", ['$scope', 'Player', function($scope, Player)
{
    var pid = angular.element('#PID').val();

    $scope.main = {
        page: 1,
        from: null,
        to: null,
        current_page: null,
        total: null,
        per_page: null,
        filters: {
            cmdid: null
        }
    };

    $scope.isLoading = false;

    $scope.loadPage = function()
    {
        $scope.isLoading = true;

        Player.records(pid, $scope.main.page, 'by', $scope.main.filters.cmdid).success(function(data)
        {
            $scope.isLoading = false;

            $scope.main.records      = data.data.data;
            $scope.main.pages        = data.data.last_page;
            $scope.main.total        = data.data.total;
            $scope.main.from         = data.data.from;
            $scope.main.to           = data.data.to;
            $scope.main.current_page = data.data.current_page;
            $scope.main.per_page     = data.data.per_page;
        });
    };

    $scope.sendQuery = function() {
        $scope.main.page = 1;
        $scope.loadPage();
    }

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

    $scope.loadPage();
}]);

app.controller("PlayerInfoHackerCheck", ['$scope', '$http', function($scope, $http)
{
    $scope.isLoading = false;

    $scope.loadCheckerStats = function()
    {
        $scope.isLoading = true;
        $scope.failed = false;

        $http({ method: 'POST', url: '/player/' + $("#PID").val() + '/extended/hackerchecker'})
            .success(function(data)
            {
                $scope.isLoading = false;

                if(data.status == 'success')
                {
                    $scope.weapons = data.data.weapons;
                }
                else if(data.status == 'error')
                {
                    $scope.failed = true;
                    $scope.message = data.message;
                }
            });

    };

    $scope.loadCheckerStats();

}]);

app.controller("PlayerInfoGraphs", ['$scope', 'Player', function($scope, Player)
{
    var pid = angular.element('#PID').val();

    $scope.loaded = false;

    $scope.commandhistory = [];
    $scope.commandusage   = [];
    $scope.iphistory      = [];
    $scope.namehistory    = [];

    Player.graphs(pid)
        .success(function(data)
        {
            $scope.loaded = true;

            $scope.commandhistory = data.data.splinechart;
            $scope.commandusage   = data.data.piechart;
            $scope.iphistory      = data.data.piechart_ips;
            $scope.namehistory    = data.data.piechart_soldiers;

            $('#graph-spline').highcharts({
                chart: {
                    type: 'spline',
                    zoomType: 'x'
                },
                title: {
                    text: 'Command History'
                },
                subtitle: {
                    text: 'Commands issued per day'
                },
                credits:{enabled:false},
                xAxis: {
                    type: 'datetime',
                    dateTimeLabelFormats: { // don't display the dummy year
                        month: '%b %e',
                        year: '%b'
                    },
                    title: {
                        text: 'Date'
                    }
                },
                yAxis: {
                    title: {
                        text: '# Issued'
                    },
                    min: 0
                },
                tooltip: {
                    headerFormat: '<b>{series.name}</b><br>',
                    pointFormat: '{point.x:%b %e, %Y}: {point.y} time(s)'
                },

                series: $scope.commandhistory
            });

            $('#graph-pie').highcharts({
                title: {
                    text: 'Command Usage'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                credits:{enabled:false},
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
                    type: 'pie',
                    name: 'Command usage',
                    data: $scope.commandusage
                }]
            });

            $('#graph-pie-ips').highcharts({
                title: {
                    text: 'IP History'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                credits:{enabled:false},
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
                    type: 'pie',
                    name: 'Ip address usage',
                    data: $scope.iphistory
                }]
            });

            $('#graph-pie-soldiers').highcharts({
                title: {
                    text: 'Aliases'
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                credits:{enabled:false},
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
                    type: 'pie',
                    name: 'Aliases usage',
                    data: $scope.namehistory
                }]
            });

            if($scope.commandusage.length == 0)
            {
                $("#graph-pie").hide();
                $("#graph-pie").parent().parent().hide();
            }
        });
}]);

app.controller("PlayerInfoReputation", ['$scope', 'Player', function($scope, Player)
{
    var pid = angular.element('#PID').val();

    $scope.loaded = false;

    Player.reputation(pid)
        .success(function(data)
        {
            $scope.loaded = true;

            var gaugeOptions = {

                chart: {
                    type: 'solidgauge'
                },

                title: null,

                pane: {
                    center: ['50%', '85%'],
                    size: '140%',
                    startAngle: -90,
                    endAngle: 90,
                    background: {
                        backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#EEE',
                        innerRadius: '60%',
                        outerRadius: '100%',
                        shape: 'arc'
                    }
                },

                tooltip: {
                    enabled: false
                },

                // the value axis
                yAxis: {
                    stops: [
                        [0.1, '#DF5353'],
                        [0.4, '#DDDF0D'],
                        [0.6, '#8AE68A'],
                        [0.8, '#55BF3B']
                    ],
                    lineWidth: 0,
                    minorTickInterval: null,
                    tickPixelInterval: 400,
                    tickWidth: 0,
                    title: {
                        y: -70
                    },
                    labels: {
                        y: 16
                    }
                },

                plotOptions: {
                    solidgauge: {
                        dataLabels: {
                            y: 5,
                            borderWidth: 0,
                            useHTML: true
                        }
                    }
                }
            };

            angular.element('#graph-reputation').highcharts(Highcharts.merge(gaugeOptions, {
                yAxis: {
                    min: -1000,
                    max: 1000,
                    title: {
                        text: 'Reputation'
                    }
                },

                credits: {
                    enabled: false
                },

                series: [{
                    name: 'Reputation',
                    data: [data.data.total_rep_co],
                    dataLabels: {
                        format: '<div style="text-align:center"><span style="font-size:25px;color:' +
                            ((Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black') + '">{y}</span><br/>' +
                            '<span style="font-size:12px;color:silver">points</span></div>'
                    },
                    tooltip: {
                        valueSuffix: ' points'
                    }
                }]

            }));
        });
}]);

app.controller("PlayerInfoStats", ['$scope', 'Player', function($scope, Player)
{
    var pid = angular.element('#PID').val();

    $scope.isLoading = true;

    Player.stats(pid)
        .success(function(data)
        {
            $scope.isLoading = false;

            $scope.overview = data.data.summary;
            $scope.sessions = data.data.sessions;
            $scope.weapons = data.data.weapons;

            $("#session_stats").dataTable({
                "aaData": $scope.sessions,
                "aaSorting": [[ 0, "desc" ]],
                "aoColumns": [
                    {"title": "ID"},
                    {"title": "Joined"},
                    {"title": "Left"},
                    {"title": "Score"},
                    {"title": "Highest Score"},
                    {"title": "Kills"},
                    {"title": "Deaths"},
                    {"title": "Headshots"},
                    {"title": "Suicides"},
                    {"title": "TKs"},
                    {"title": "Wins"},
                    {"title": "Losses"},
                    {"title": "Rounds"},
                    {"title": "Playtime"},
                    {"title": "Server", "sClass": "trim-server-name"}
                ]
            });
        });
}]);

app.controller("PlayerInfoChatlog", ['$scope', 'Player', function($scope, Player)
{
    $scope.main = {
        page: 1,
        from: null,
        to: null,
        current_page: null,
        total: null,
        per_page: null,
        skip: 1,
        filters: {
            serverid: null,
            message: null
        }
    };

    var pid = angular.element('#PID').val();

    $scope.isLoading = false;

    $scope.loadPage = function()
    {
        $scope.isLoading = true;

        Player.chatlog(pid, $scope.main.page, $scope.main.filters.serverid, $scope.main.filters.message).success(function(data)
        {
            $scope.isLoading = false;

            $scope.main.chatlogs     = data.data.data;
            $scope.main.pages        = data.data.last_page;
            $scope.main.total        = data.data.total;
            $scope.main.from         = data.data.from;
            $scope.main.to           = data.data.to;
            $scope.main.current_page = data.data.current_page;
            $scope.main.per_page     = data.data.per_page;
        });
    };

    $scope.sendQuery = function() {
        $scope.main.page = 1;
        $scope.loadPage();
    }

    $scope.nextPage = function() {
        if ($scope.main.page < $scope.main.pages) {
            if($scope.main.skip > 1 && ($scope.main.page + $scope.main.skip) < $scope.main.pages) {
                $scope.main.page = $scope.main.page + $scope.main.skip
            } else if($scope.main.skip > 1 && ($scope.main.page + $scope.main.skip) >= $scope.main.pages) {
                $scope.main.page = $scope.main.pages;
            } else {
                $scope.main.page++;
            }

            $scope.loadPage();
        }
    };

    $scope.previousPage = function() {
        if ($scope.main.page > 1) {
            if($scope.main.skip > 1 && ($scope.main.page - $scope.main.skip) > 1) {
                $scope.main.page = $scope.main.page - $scope.main.skip
            } else if($scope.main.skip > 1 && ($scope.main.page - $scope.main.skip) <= 1) {
                $scope.main.page = 1;
            } else {
                $scope.main.page--;
            }
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

    $scope.loadPage();
}]);

app.controller("PlayerInfoExternalRequests", ['$scope', 'Player', function($scope, Player)
{
    var pid = $("#PID").val();

    $scope.isLoading = true;

    $scope.player = {
        game: null,
        battlelog: null,
        bf4db: null,
        name: null
    };

    Player.thirdparty(pid)
        .success(function(data)
        {
            $scope.isLoading        = false;
            $scope.player.game      = data.data.game;
            $scope.player.battlelog = data.data.battlelog;
            $scope.player.bf4db     = data.data.bf4db;
            $scope.player.name      = data.data.player;
        });
}]);

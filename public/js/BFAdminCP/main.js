var app = angular.module('BFAdminCP', ['ui.bootstrap', 'ngAnimate', 'checklist-model']);

app.factory('reportNotificationsAudio', function ($document)
{
    var audioElement = $document[0].createElement('audio');
    var soundFile = $("input[name=site_report_sound_file]").val()

    return {
        audioElement: audioElement,

        play: function() {
            audioElement.src = '/audio/'+ soundFile + '/' + soundFile + '.mp3';
            audioElement.play();
        }
    }
});

app.factory('Reports', function($http)
{
    return {
        // Get the server information and players
        get: function(id) {
            return $http.post('api/v1/common/general/reports', {last_report_id: id});
        }
    }
});

app.factory('ReportsStorage', function()
{
    var data = {
        ReportsData: []
    };

    return {
        get: function() {
            return data.ReportsData;
        },
        set: function(reports) {
            data.ReportsData = reports;
        }
    }
});

app.controller("ReportsCheckCtrl", [ '$scope', '$timeout', 'Reports', 'ReportsStorage', 'reportNotificationsAudio', function($scope, $timeout, Reports, ReportsStorage, reportNotificationsAudio) {
    $scope.refreshInterval = 10;
    $scope.results = [];
    $scope.count = 0;
    $scope.hasRead = true;
    $scope.countNew = 0;
    $scope.refresh = false;
    $scope.reports = [];

    var isLoaded = false,
        msg = '',
        title = '',
        lastReportId = null,
        playCount = 0;

    $scope.markRead = function() {
        $scope.hasRead = true;
        $scope.countNew = 0;
        playCount = 0;
    };

    $scope.displayServerName = function(server) {
        if(server.short !== null) {
            return server.short;
        } else {
            return server.full;
        }
    };

    function lastArrayKey(array) {
        var last = array[array.length-1];

        return last.record_id;
    }

    $scope.fetchReports = function() {
        $scope.refresh = true;

        Reports.get(lastReportId)
            .success(function(data)
            {
                $scope.refresh = false;
                $scope.results = data.data;

                if($scope.results.length > 0)
                {
                    lastReportId = lastArrayKey($scope.results);
                }

                angular.forEach($scope.results, function(v, k) {
                    if(isLoaded)
                    {
                        $scope.countNew++;
                        msg = v.message + '<br/>Reported by: <em>' +
                            v.source + '</em><br/>' + $scope.displayServerName(v.server);

                        title = v.target + ' [' + v.id + ']';

                        toastr.info(msg, title, {
                            "closeButton": true,
                            "debug": false,
                            "positionClass": "toast-bottom-left",
                            "onclick": null,
                            "showDuration": "300",
                            "hideDuration": "1000",
                            "timeOut": "30000",
                            "extendedTimeOut": "10000",
                            "showEasing": "swing",
                            "hideEasing": "linear",
                            "showMethod": "slideDown",
                            "hideMethod": "slideUp"
                        });

                        $.titleAlert("New Admin Report!", {
                            stopOnMouseMove: true
                        });
                    }

                    $scope.reports.push(v);
                });

                if(isLoaded && $scope.countNew > 0)
                {
                    $scope.hasRead = false;
                }

                if(isLoaded && $scope.results.length > 0 && $("input[name=site_report_enable_sound]").val() == 1)
                {
                    reportNotificationsAudio.play();
                }

                $scope.count = $scope.results.length;

                isLoaded = true;
            });

        $scope.boardTimeout = $timeout(function() { $scope.fetchReports(); }, $scope.refreshInterval * 1000);
    };

    $scope.fetchReports();
}]);

app.directive('ngEnter', function () {
    return function (scope, element, attrs) {
        element.bind("keydown keypress", function (event) {
            if(event.which === 13) {
                scope.$apply(function (){
                    scope.$eval(attrs.ngEnter);
                });

                event.preventDefault();
            }
        });
    };
});

function jumpToAnchor(id) {
    setTimeout(function() {
        $('html,body').animate({scrollTop: $("#" + id).offset().top - 52}, 'slow');
    }, 100);

    return false;
}

/**
 * @license @product.name@ JS v@product.version@ (@product.date@)
 * Plugin for displaying a message when there is no data visible in chart.
 *
 * (c) 2010-2014 Highsoft AS
 * Author: Oystein Moseng
 *
 * License: www.highcharts.com/license
 */

(function (H) { // docs

    var seriesTypes = H.seriesTypes,
        chartPrototype = H.Chart.prototype,
        defaultOptions = H.getOptions(),
        extend = H.extend;

    // Add language option
    extend(defaultOptions.lang, {
        noData: 'No data to display'
    });

    // Add default display options for message
    defaultOptions.noData = {
        position: {
            x: 0,
            y: 0,
            align: 'center',
            verticalAlign: 'middle'
        },
        attr: {
        },
        style: {
            fontWeight: 'bold',
            fontSize: '12px',
            color: '#60606a'
        }
    };

    /**
     * Define hasData functions for series. These return true if there are data points on this series within the plot area
     */
    function hasDataPie() {
        return !!this.points.length; /* != 0 */
    }

    if (seriesTypes.pie) {
        seriesTypes.pie.prototype.hasData = hasDataPie;
    }

    if (seriesTypes.gauge) {
        seriesTypes.gauge.prototype.hasData = hasDataPie;
    }

    if (seriesTypes.waterfall) {
        seriesTypes.waterfall.prototype.hasData = hasDataPie;
    }

    H.Series.prototype.hasData = function () {
        return this.dataMax !== undefined && this.dataMin !== undefined;
    };

    /**
     * Display a no-data message.
     *
     * @param {String} str An optional message to show in place of the default one
     */
    chartPrototype.showNoData = function (str) {
        var chart = this,
            options = chart.options,
            text = str || options.lang.noData,
            noDataOptions = options.noData;

        if (!chart.noDataLabel) {
            chart.noDataLabel = chart.renderer.label(text, 0, 0, null, null, null, null, null, 'no-data')
                .attr(noDataOptions.attr)
                .css(noDataOptions.style)
                .add();
            chart.noDataLabel.align(extend(chart.noDataLabel.getBBox(), noDataOptions.position), false, 'plotBox');
        }
    };

    /**
     * Hide no-data message
     */
    chartPrototype.hideNoData = function () {
        var chart = this;
        if (chart.noDataLabel) {
            chart.noDataLabel = chart.noDataLabel.destroy();
        }
    };

    /**
     * Returns true if there are data points within the plot area now
     */
    chartPrototype.hasData = function () {
        var chart = this,
            series = chart.series,
            i = series.length;

        while (i--) {
            if (series[i].hasData() && !series[i].options.isInternal) {
                return true;
            }
        }

        return false;
    };

    /**
     * Show no-data message if there is no data in sight. Otherwise, hide it.
     */
    function handleNoData() {
        var chart = this;
        if (chart.hasData()) {
            chart.hideNoData();
        } else {
            chart.showNoData();
        }
    }

    /**
     * Add event listener to handle automatic display of no-data message
     */
    chartPrototype.callbacks.push(function (chart) {
        H.addEvent(chart, 'load', handleNoData);
        H.addEvent(chart, 'redraw', handleNoData);
    });

}(Highcharts));

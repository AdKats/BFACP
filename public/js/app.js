'use strict';
angular.module('bfacp', [
    'ngResource',
    'ngMessages',
    'ngAnimate',
    'ngAria',
    'ngSanitize',
    'ngIdle',
    'ngTable',
    'ngClipboard',
    'ui.bootstrap',
    'countTo'
])
    .config(['$locationProvider', '$idleProvider', 'ngClipProvider', function ($locationProvider, $idleProvider, ngClipProvider) {
        $locationProvider.html5Mode(false).hashPrefix('!');
        $idleProvider.idleDuration(window.idleDurationSeconds || 60);
        $idleProvider.warningDuration(window.warningDurationSeconds || 60);
        ngClipProvider.setPath("//cdnjs.cloudflare.com/ajax/libs/zeroclipboard/2.1.6/ZeroClipboard.swf");
    }])
    .run(['$rootScope', function ($rootScope) {
        $rootScope.moment = function (date) {
            return moment(date);
        };
        $rootScope.momentDuration = function (duration, type) {
            return moment.duration(duration, type).humanize();
        };
        $rootScope.divide = function (num1, num2, precision) {
            if (precision === undefined || precision === null) {
                precision = 2;
            }

            var dividedNum = 0;

            try {
                if (num1 === 0 || num2 === 0) {
                    throw new Error('Divide by zero');
                }

                dividedNum = num1 / num2
            } catch (e) {
                dividedNum = num1;
            }

            return dividedNum.toFixed(precision);
        };
    }])
    .filter('nl2br', function () {
        var span = document.createElement('span');
        return function (input) {
            if (!input) return input;
            var lines = input.split('\n');

            for (var i = 0; i < lines.length; i++) {
                span.innerText = lines[i];
                span.textContent = lines[i];  //for Firefox
                lines[i] = span.innerHTML;
            }
            return lines.join('<br />');
        }
    })
    .filter('range', function () {
        return function (input, low, high, step) {
            //  discuss at: http://phpjs.org/functions/range/
            // original by: Waldo Malqui Silva
            //   example 1: range ( 0, 12 );
            //   returns 1: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12]
            //   example 2: range( 0, 100, 10 );
            //   returns 2: [0, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100]
            //   example 3: range( 'a', 'i' );
            //   returns 3: ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i']
            //   example 4: range( 'c', 'a' );
            //   returns 4: ['c', 'b', 'a']

            var matrix = [];
            var inival, endval, plus;
            var walker = step || 1;
            var chars = false;

            if (!isNaN(low) && !isNaN(high)) {
                inival = low;
                endval = high;
            } else if (isNaN(low) && isNaN(high)) {
                chars = true;
                inival = low.charCodeAt(0);
                endval = high.charCodeAt(0);
            } else {
                inival = (isNaN(low) ? 0 : low);
                endval = (isNaN(high) ? 0 : high);
            }

            plus = ((inival > endval) ? false : true);
            if (plus) {
                while (inival <= endval) {
                    matrix.push(((chars) ? String.fromCharCode(inival) : inival));
                    inival += walker;
                }
            } else {
                while (inival >= endval) {
                    matrix.push(((chars) ? String.fromCharCode(inival) : inival));
                    inival -= walker;
                }
            }

            return matrix;
        };
    })
    .directive('ngEnter', function () {
        return function (scope, element, attrs) {
            element.bind("keydown keypress", function (event) {
                if (event.which === 13) {
                    scope.$apply(function () {
                        scope.$eval(attrs.ngEnter, {'event': event});
                    });

                    event.preventDefault();
                }
            });
        };
    });

$('#psearch').submit(function () {
    $(this).find('input:text').each(function () {
        var inputVal = $(this).val();
        $(this).val(inputVal.split(' ').join(''));
    });
});

function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results === null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}

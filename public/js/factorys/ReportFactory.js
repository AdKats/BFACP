angular.module('bfacp').factory('ReportFactory', ['$http', '$q', function($http, $q) {
    var service = {};
    var _action = null;
    var _recordId = null;
    var _extras = {};
    var _reason = '';

    service.setExtras = function(x) {
        _extras = x;
    };

    service.setReason = function(message) {
        _reason = message;
    };

    service.getReason = function() {
        return _reason;
    };

    service.setAction = function(action) {
        _action = action;
    };

    service.getActionName = function() {
        return _action.name;
    };

    service.setRecordId = function(record) {
        _recordId = record.record_id;
    };

    service.getRecordId = function() {
        return _recordId;
    };

    service.updateReport = function() {
        var deferred = $q.defer();
        $http.put('api/reports', {
            id: _recordId,
            action: _action.id,
            reason: _reason,
            extras: _extras
        }).success(function(data) {
            deferred.resolve(data);
        }).error(function(data) {
            deferred.reject(data);
        });

        return deferred.promise;
    };

    return service;
}]);

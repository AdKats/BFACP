angular.module('bfacp').controller('PusherUsersController', ['$scope', function($scope) {

    $scope.members = {
        online: 0,
        list: []
    };

    var PresenceChannel = pusher.subscribe("presence-users");

    PresenceChannel.bind('pusher:subscription_succeeded', function(members) {
        $scope.members.online = members.count;
        members.each(function(member) {
            $scope.members.list.push(member.info);
        });
    });
}]);

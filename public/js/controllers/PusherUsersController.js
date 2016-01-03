angular.module('bfacp').controller('PusherUsersController', ['$scope', '$interval', function ($scope, $interval) {

    $scope.members = {
        online: 0,
        list: []
    };

    var PresenceChannel = pusher.subscribe("presence-users");

    var update_members_online = function () {
        $scope.members.online = $scope.members.list.length;
    };

    PresenceChannel.bind('pusher:subscription_succeeded', function (members) {
        members.each(function (member) {
            $scope.members.list.push(member.info);
        });
        update_members_online();
    });

    PresenceChannel.bind('pusher:member_added', function (member) {
        $scope.members.list.push(member.info);
        update_members_online();
    });

    PresenceChannel.bind('pusher:member_removed', function (member) {
        for (var i = 0; i < $scope.members.list.length; i++) {
            if ($scope.members.list[i].id == member.id) {
                $scope.members.list.splice(i, 1);
                break;
            }
        }
        update_members_online();
    });
}]);

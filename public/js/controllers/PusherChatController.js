angular.module('bfacp').controller('PusherChatController', ['$scope', '$http', function ($scope, $http) {

    var ChatroomChannel = pusher.subscribe('presence-chatroom');

    var update_members_online = function () {
        $scope.members.online = $scope.members.list.length;
    };

    $scope.members = {
        online: 0,
        list: []
    };

    $scope.connectionState = '';
    $scope.connStateClass = 'bg-red';

    $scope.chat = {
        message: '',
        input: false
    };

    $scope.messages = [];

    $http.get('/api/pusher/chat-history').success(function(data) {
        var messages = data.data;

        for(var i=0; i < messages.length; i++) {
            $scope.messages.push(messages[i]);
        }
    }).error(function(e) {
        console.error('Unable to get chat history.', e);
    });

    $scope.sendMessage = function () {
        $scope.chat.input = true;
        $http.post('api/pusher/chat', {
            channel_name: 'presence-chatroom',
            event: 'message-sent',
            message: $scope.chat.message
        }).success(function () {
            $scope.chat.input = false;
            $scope.chat.message = '';
        }).error(function () {
            $scope.chat.input = false;
        });
    };

    pusher.connection.bind('state_change', function (states) {
        $scope.connectionState = states.current;

        switch (states.current) {
            case "connecting":
                $scope.connStateClass = 'bg-yellow';
                break;
            case "connected":
                $scope.connStateClass = 'bg-green';
                break;
            case "unavailable":
            case "failed":
            case "disconnected":
                $scope.connStateClass = 'bg-red';
                break;
        }
    });


    ChatroomChannel.bind('message-sent', function (data) {
        $scope.messages.push(data);
    });

    ChatroomChannel.bind('pusher:subscription_succeeded', function (members) {
        members.each(function (member) {
            $scope.members.list.push(member.info);
        });

        update_members_online();
    });

    ChatroomChannel.bind('pusher:member_added', function (member) {
        $scope.members.list.push(member.info);
        update_members_online();
    });

    ChatroomChannel.bind('pusher:member_removed', function (member) {
        for (var i = 0; i < $scope.members.list.length; i++) {
            if ($scope.members.list[i].id == member.id) {
                $scope.members.list.splice(i, 1);
                break;
            }
        }
        update_members_online();
    });
}]);

@extends('layout.main')

@section('content')
<div class="row">
    <div class="col-sm-12">
        @foreach($users as $user)
        <div class="member-entry">
            <a href="{{ action('ADKGamers\\Webadmin\\Controllers\\AccountController@showUserProfile', [$user->id]) }}" class="member-img">
                <img src="{{ Helper::getGravatar(($user->gravatar ?: $user->email)) }}" class="img-rounded" />
                <i class="entypo-forward"></i>
            </a>
            <div class="member-details">
                <h4>{{ link_to_action('ADKGamers\\Webadmin\\Controllers\\AccountController@showUserProfile', $user->username, [$user->id]) }}</h4>
                <div class="row info-list">
                    <div class="col-sm-4">
                        <strong>Group</strong>: {{ $user->groupname }}<br>
                        <strong>Joined</strong>: {{ Helper::UTCToLocal($user->created_at)->format('M j, Y') }}<br>
                        <strong>Last Active</strong>:
                        <?php $diff = Carbon::parse($user->lastseen_at)->diffInMinutes(Carbon::now(), FALSE); ?>
                        @if($diff <= 15 && $diff >= 0)
                        <small class="label label-success">ONLINE</small>
                        @else
                        <small class="label label-danger">OFFLINE</small>
                        @endif
                        {{ Helper::UTCToLocal($user->lastseen_at)->diffForHumans() }}
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        <div class="pull-left">
            Showing {{ number_format($users->getFrom()) }} to {{ number_format($users->getTo()) }} of {{ number_format($users->getTotal()) }} users.
        </div>
        <div class="pull-right">
            {{ $users->links() }}
        </div>
    </div>
</div>
@stop

@section('stylesinclude')
<style type="text/css">
.member-entry {
    border: 1px solid #ebebeb;
    padding: 15px;
    margin-top: 15px;
    margin-bottom: 30px;
    -moz-box-shadow: 1px 1px 1px rgba(0,1,1,0.02);
    -webkit-box-shadow: 1px 1px 1px rgba(0,1,1,0.02);
    box-shadow: 1px 1px 1px rgba(0,1,1,0.02);
    -moz-transition: all 300ms ease-in-out;
    -o-transition: all 300ms ease-in-out;
    -webkit-transition: all 300ms ease-in-out;
    transition: all 300ms ease-in-out;
    -webkit-border-radius: 3px;
    -webkit-background-clip: padding-box;
    -moz-border-radius: 3px;
    -moz-background-clip: padding;
    border-radius: 3px;
    background-clip: padding-box
}

.member-entry:before,.member-entry:after {
    content: " ";
    display: table
}

.member-entry:after {
    clear: both
}

.member-entry:hover {
    background: rgba(235,235,235,0.3);
    -moz-box-shadow: 1px 1px 1px rgba(0,1,1,0.06);
    -webkit-box-shadow: 1px 1px 1px rgba(0,1,1,0.06);
    box-shadow: 1px 1px 1px rgba(0,1,1,0.06)
}

.member-entry .member-img,.member-entry .member-details {
    float: left
}

.member-entry .member-img {
    position: relative;
    display: block;
    width: 5%
}

.member-entry .member-img img {
    width: 100%;
    display: block;
    max-width: 100%;
    height: auto
}

.member-entry .member-img i {
    position: absolute;
    display: block;
    left: 50%;
    top: 50%;
    margin-top: -12.5px;
    margin-left: -12.5px;
    color: #FFF;
    font-size: 25px;
    -webkit-opacity: 0;
    -moz-opacity: 0;
    opacity: 0;
    filter: alpha(opacity=0);
    -webkit-transform: scale(0.5);
    -moz-transform: scale(0.5);
    -o-transform: scale(0.5);
    -ms-transform: scale(0.5);
    transform: scale(0.5);
    -webkit-transform: scale(0.5,);
    -ms-transform: scale(0.5,);
    transform: scale(0.5,);
    -moz-transition: all 300ms ease-in-out;
    -o-transition: all 300ms ease-in-out;
    -webkit-transition: all 300ms ease-in-out;
    transition: all 300ms ease-in-out
}

.member-entry .member-img:hover i {
    -webkit-transform: scale(1);
    -moz-transform: scale(1);
    -o-transform: scale(1);
    -ms-transform: scale(1);
    transform: scale(1);
    -webkit-transform: scale(1,);
    -ms-transform: scale(1,);
    transform: scale(1,);
    -webkit-opacity: 1;
    -moz-opacity: 1;
    opacity: 1;
    filter: alpha(opacity=100)
}

.member-entry .member-details {
    width: 89.9%
}

.member-entry .member-details h4 {
    font-size: 18px;
    margin-left: 20px
}

.member-entry .member-details h4 a {
    -moz-transition: all 300ms ease-in-out;
    -o-transition: all 300ms ease-in-out;
    -webkit-transition: all 300ms ease-in-out;
    transition: all 300ms ease-in-out
}

.member-entry .member-details .info-list {
    margin-left: 5px
}

.member-entry .member-details .info-list>div {
    margin-top: 5px;
    font-size: 13px
}

.member-entry .member-details .info-list>div a {
    -moz-transition: all 300ms ease-in-out;
    -o-transition: all 300ms ease-in-out;
    -webkit-transition: all 300ms ease-in-out;
    transition: all 300ms ease-in-out
}

.member-entry .member-details .info-list>div i {
    -moz-transition: all 300ms ease-in-out;
    -o-transition: all 300ms ease-in-out;
    -webkit-transition: all 300ms ease-in-out;
    transition: all 300ms ease-in-out
}

.member-entry .member-details .info-list>div:hover i {
    color: #4f5259
}
.member-entry .label {
    font-size: 68% !important;
}
</style>
@stop

<div class="text-center" ng-controller="PlayerInfoExternalRequests" ng-hide="isLoading">

    <section ng-if="player.game == 'BF3'">
        <a ng-if="player.battlelog === null" ng-href="http://battlelog.battlefield.com/bf3/user/{{player.name}}" class="btn btn-primary btn-sm" role="button" target="_blank">Battlelog</a>
        <a ng-if="player.battlelog !== null" ng-href="http://battlelog.battlefield.com/bf3/soldier/{{player.name}}/stats/{{player.battlelog.persona_id}}/pc/" class="btn btn-primary btn-sm" role="button" target="_blank">Battlelog</a>
        <a ng-href="http://bf3stats.com/stats_pc/{{player.name}}" class="btn btn-primary btn-sm" role="button" target="_blank">BF3Stats</a>
        <a ng-href="http://www.team-des-fra.fr/CoM/bf3.php?p={{player.name}}" class="btn btn-primary btn-sm" role="button" target="_blank">TeamDes</a>
        <a ng-href="http://i-stats.net/index.php?action=pcheck&amp;game=BF3&amp;player={{player.name}}" class="btn btn-primary btn-sm" role="button" target="_blank">I-Stats</a>
        <a ng-href="http://history.anticheatinc.com/bf3/?searchvalue={{player.name}}" class="btn btn-primary btn-sm" role="button" target="_blank">Anticheat Inc.</a>
        <a ng-href="http://metabans.com/search/?phrase={{player.name}}" class="btn btn-primary btn-sm" role="button" target="_blank">Metabans</a>
    </section>

    <section ng-if="player.game == 'BF4'">
        <a ng-if="player.battlelog === null" ng-href="http://battlelog.battlefield.com/bf4/user/{{player.name}}" class="btn btn-primary btn-sm" role="button" target="_blank">Battlelog</a>
        <a ng-if="player.battlelog !== null" ng-href="http://battlelog.battlefield.com/bf4/soldier/{{player.name}}/stats/{{player.battlelog.persona_id}}/pc/" class="btn btn-primary btn-sm" role="button" target="_blank">Battlelog</a>
        <a ng-href="http://bf4stats.com/pc/{{player.name}}" class="btn btn-primary btn-sm" role="button" target="_blank">BF4Stats</a>
        <a ng-href="http://i-stats.net/index.php?action=pcheck&amp;game=BF4&amp;player={{player.name}}" class="btn btn-primary btn-sm" role="button" target="_blank">I-Stats</a>
        <a ng-href="http://history.anticheatinc.com/bf4/?searchvalue={{player.name}}" class="btn btn-primary btn-sm" role="button" target="_blank">Anticheat Inc.</a>
        <a ng-href="http://metabans.com/search/?phrase={{player.name}}" class="btn btn-primary btn-sm" role="button" target="_blank">Metabans</a>
        <a ng-if="player.bf4db === null" ng-href="http://bf4db.com/players?name={{player.name}}" class="btn btn-primary btn-sm" role="button" target="_blank">BF4DB</a>
        <a ng-if="player.bf4db !== null" ng-href="http://bf4db.com/players/{{player.bf4db.id}}" class="btn btn-primary btn-sm" role="button" target="_blank">BF4DB <span>Cheatscore: {{ player.bf4db.response.cheatscore }}%</span></a>
    </section>

</div>

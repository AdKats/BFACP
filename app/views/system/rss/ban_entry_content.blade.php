<p>Player: <strong>{{ link_to_route('player.show', $playerName, [$playerId, $playerName]) }}</strong></p>
@if(is_null($sourceId))
    <p>Banning Admin: <strong>{{ $sourceName }}</strong></p>
@else
    <p>Banning Admin: <strong>{{ link_to_route('player.show', $sourceName, [sourceId, $sourceName]) }}</strong></p>
@endif
<p>Reason: <strong>{{ $banReason }}</strong></p>

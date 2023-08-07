<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerJoinedLobby implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $playerListJson;
    public $roomCode;

    public function __construct($playerList, $roomCode)
    {
        $this->playerListJson = json_encode($playerList);
        $this->roomCode = $roomCode;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('presence-' . $this->roomCode);
    }

    public function broadcastAs()
    {
        return 'PlayerJoinedLobby';
    }
}

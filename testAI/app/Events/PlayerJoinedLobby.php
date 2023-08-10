<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;

class PlayerJoinedLobby implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $playerName;
    public $roomCode;

    public function __construct($playerName, $roomCode)
    {
        \Log::info("PlayerJoinedLobby Event Fired for playerName: $playerName and roomCode: $roomCode");

        $this->playerName = $playerName;
        $this->roomCode = $roomCode;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('room.' . $this->roomCode);
    }

    public function broadcastAs()
    {
        return 'PlayerJoinedLobby';
    }

    public function broadcastWith()
    {
        $playerList = Redis::smembers("room:{$this->roomCode}:players");
        return ['playerName' => $this->playerName, 'playerList' => $playerList];
    }
}

//Creating broadcast within the function that I want to be called. Should already be on the channel that it's actually creating
//Thinking I need to move it within the RoomController.php file
//EVENT IS RUNNING NOW

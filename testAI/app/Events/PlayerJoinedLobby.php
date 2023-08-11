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

    // public function broadcastWith()
    // {
    //     $playerList = Redis::smembers("room:{$this->roomCode}:players");
    //     \Log::info("PlayerJoinedLobby Event broadcastWith: " . json_encode($playerList));
    //     return ['playerName' => $this->playerName, 'playerList' => $playerList];
    // }
}


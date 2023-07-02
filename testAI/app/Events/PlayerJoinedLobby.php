<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PlayerJoinedLobby implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $playerList;
    public $roomCode;

     /**
     * Create a new event instance.
     *
     * @param array  $playerList // Update the type from string to array
     * @param string $roomCode
     * @return void
     */
    public function __construct(array $playerList, string $roomCode) // Update the type from string to array
    {
        $this->playerList = $playerList; // Rename from $playerName
        $this->roomCode = $roomCode;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('lobby-' . $this->roomCode);
    }
}

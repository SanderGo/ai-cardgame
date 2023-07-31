<?php

namespace App\Services;

use Illuminate\Support\Facades\Session;

class SessionService
{

    public function getRoomCode()
    {
        return Session::get('roomCode');
    }

    public function setPlayerListForRoom(string $roomCode, array $playerList)
    {
        Session::put('playerList.' . $roomCode, $playerList);
    }

    public function getPlayerListForRoom(string $roomCode)
    {
        return Session::get('playerList.' . $roomCode, []);
    }

    // Add a player to a room
    public function addPlayerToRoom(string $roomCode, string $playerName)
    {
        $playerList = $this->getPlayerListForRoom($roomCode);
        if (!in_array($playerName, $playerList)) {
            $playerList[] = $playerName;
            $this->setPlayerListForRoom($roomCode, $playerList);
        }

        return $playerList;
    }
}

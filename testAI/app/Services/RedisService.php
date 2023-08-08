<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

class RedisService
{
    public function addRoomToSet($roomCode)
    {
        Redis::sadd('game_rooms', $roomCode);
    }

    public function removeRoomFromSet($roomCode)
    {
        Redis::srem('game_rooms', $roomCode);
    }

    public function isRoomInSet($roomCode)
    {
        return Redis::sismember('game_rooms', $roomCode);
    }

    public function setRoomCode(string $roomCode)
    {
        \Log::info('Setting room code: ' . $roomCode);
        Redis::set('roomCode', $roomCode);
    }

    public function getRoomCode()
    {
        return Redis::get('roomCode');
    }

    public function setPlayerListForRoom(string $roomCode, array $playerList)
    {
        Redis::hset('playerList', $roomCode, json_encode($playerList));
    }

    public function getPlayerListForRoom(string $roomCode)
    {
        $playerList = Redis::hget('playerList', $roomCode);
        return $playerList ? json_decode($playerList, true) : [];
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

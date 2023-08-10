<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;
use App\Services\RedisService;
use Ramsey\Uuid\Uuid;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Events\PlayerJoinedLobby;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}

class RoomController extends Controller
{
    protected $redisService;

    public function __construct(RedisService $redisService)
    {
        $this->redisService = $redisService;
    }

    public function generateRoomCode()
    {
        do {
            $roomCode = Str::upper(Str::random(5));
        } while (Redis::sismember('active_rooms', $roomCode));
        
        // Add the generated room code to the set of active rooms
        Redis::sadd('active_rooms', $roomCode);

        return $roomCode;
    }

    private function isValidRoomCode($roomCode)
    {
        return Redis::sismember('active_rooms', $roomCode);
    }

    public function createRoom(Request $request)
    {
        $roomCode = $this->generateRoomCode();
        $uuid = $this->associatePlayerWithRoom($roomCode);
            
        return view('create', [
            'roomCode' => $roomCode,
            'uuid' => $uuid,
        ]);
    }


    public function joinRoom(Request $request)
    {
        $roomCode = $request->input('roomCode');
        $playerName = $request->input('playerName');

        if ($this->isValidRoomCode($roomCode)) {

            $uuid = $this->associatePlayerWithRoom($roomCode);

            event(new PlayerJoinedLobby($playerName, $roomCode));

            return view('create', [
                'roomCode' => $roomCode,
                'uuid' => $uuid,
            ]);
        } else {
            return redirect()->back()->with('error', 'Room not found');
        }
    }

    public function setPlayerName(Request $request)
    {
        $uuid = $request->input('uuid');
        $roomCode = $request->input('roomCode');
        $playerName = $request->input('player_name');
        
        Redis::hmset("player:{$uuid}", ['playerName' => $playerName]);
        \Log::info("Player {$playerName} joined room {$roomCode}");
        event(new PlayerJoinedLobby($playerName, $roomCode));
        
        return redirect()->route('lobby');
    }
    
    private function associatePlayerWithRoom($roomCode)
    {
        $uuid = Uuid::uuid4()->toString();

        // Add the player's UUID to the room's set of players
        Redis::sadd("room:{$roomCode}:players", $uuid);
        Redis::hmset("player:{$uuid}", [
            'roomCode' => $roomCode,
        ]);

        return $uuid;
    }

    public function viewLobby()
    {
        return view('lobby');
    }
    
    public function authChannel(Request $request)
    {
        $channelName = $request->input('channel_name');
        $roomCode = str_replace('room.', '', $channelName);
        $isValidRoomCode = $this->isValidRoomCode($roomCode);

        return response()->json(['isValidRoomCode' => $isValidRoomCode]);
    }

    public function updatePlayer(Request $request)
    {
        $uuid = $request->input('uuid');
        $playerName = $request->input('playerName');

        Redis::hmset("player:{$uuid}", [
            'playerName' => $playerName,
        ]);

        return response()->json(['success' => true]);
    }

}

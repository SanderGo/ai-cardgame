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
        } while (Redis::sismember('rooms', $roomCode));
        
        $this->redisService->setRoomCode($roomCode);
        \Log::info('Room code lol: ' . $roomCode);
        
        return $roomCode;
    }


    public function createRoom(Request $request) {
        $roomCode = $this->generateRoomCode();
        $uuid = Uuid::uuid4()->toString();
        
        Redis::sadd('rooms', $roomCode); // Add to room set
        Redis::sadd("room:{$roomCode}:players", $uuid);
        Redis::hmset("player:{$uuid}", [
            'roomCode' => $roomCode,
        ]);
        return view('create', [
            'roomCode' => $roomCode,
            'uuid' => $uuid,
        ]);
    }

    public function joinRoom(Request $request) {
        $roomCode = $request->input('roomCode');
        $uuid = Uuid::uuid4()->toString();

        if ($this->isRoomInSet($roomCode)) {
            $this->redisService->addPlayerToRoom($roomCode, $uuid);
            return view('create', [
                'roomCode' => $roomCode,
                'uuid' => $uuid,            
            ]);
        } else {
            return redirect()->back()->with('error', 'Room not found');
        }
    }

    private function isRoomInSet($roomCode) {
        return Redis::sismember('rooms', $roomCode);
    }

    public function setRoomCode(Request $request)
    {
        Redis::set('roomCode', $request->roomCode);
        return response()->json(['success' => 'Room code set successfully']);
    }

    public function viewLobby()
    {
        $roomCode = Redis::get('roomCode');
        if ($roomCode) {
            return view('lobby');
        } else {
            return redirect()->route('home');
        }
    }

    public function authChannel(Request $request)
    {
        $channelName = $request->input('channel_name');
        \Log::info('Channel Name: ' . $channelName);

        // Extract roomCode from channel_name
        $roomCode = str_replace('presence-room.', '', $channelName);

        $validRoomCodes = Redis::smembers('rooms');
        \Log::info('Valid room codes: ' . json_encode($validRoomCodes));
        
        $isValidRoomCode = in_array($roomCode, $validRoomCodes);
        \Log::info('Code: ' . $roomCode);
        \Log::info('Is valid room code: ' . $isValidRoomCode);
        
        return response()->json(['isValidRoomCode' => $isValidRoomCode]);
    }

    public function updatePlayer(Request $request)
    {
        $uuid = $request->input('uuid');
        $playerName = $request->input('playerName');

        // Assuming you're storing players in a hash in Redis:
        Redis::hmset("player:{$uuid}", [
            'playerName' => $playerName,
        ]);

        return response()->json(['success' => true]);
    }

    public function joinLobby(Request $request)
    {
        $playerName = $request->input('player_name');
        $uuid = Uuid::uuid4()->toString(); // generate new uuid or use existing one if needed
    
        // Save player to Redis or your DB
        // For example, if you use Redis:
        Redis::hmset("player:{$uuid}", ['playerName' => $playerName]);
        
        // Return the list of all players in the room. You will have to fetch this info from Redis/DB
        $playerList =  Redis::smembers("room:{$roomCode}:players");
    
        return response()->json(['success' => true, 'playerList' => $playerList]);
    }
    

}

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
use App\Models\User;

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

    private function isValidRoomCode()
    {
        $uuid = session('uuid');
        $playerData = Redis::hgetall("player:{$uuid}");
        $playerName = $playerData['playerName'] ?? null;
        $roomCode = $playerData['roomCode'] ?? null;


        \Log::info("Checking if room code {$roomCode} is valid");

        $activeRooms = Redis::smembers('active_rooms');

        // Check if the roomCode is in the activeRooms array
        $roomExists = in_array($roomCode, $activeRooms);

        \Log::info("Room code exists: " . $roomExists);
        return $roomExists;
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
        if ($this -> isValidRoomCode($roomCode)) {
            $uuid = $this->associatePlayerWithRoom($roomCode);
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
        
        return redirect()->route('lobby', $uuid);
    }
    
    private function associatePlayerWithRoom($roomCode, $playerName = 'Player')
    {
        // Create and login new User
        $user = User::create(['name' => $playerName]);
        auth()->login($user, true);

        $uuid = $user->id;

        // Add the player's UUID to the room's set of players
        Redis::sadd("room:{$roomCode}:players", $uuid);
        Redis::hmset("player:{$uuid}", [
            'roomCode' => $roomCode,
        ]);

        return $uuid;
    }


    public function updatePlayer(Request $request)
    {
        \Log::info("Inside updatePlayer method");
        $playerName = $request->input('playerName');
        
        $user = User::find(auth()->id());
        \Log::info("Found user: " . ($user ? $user->id : 'null'));
    
        $user->update(['name' => $playerName]);
    
        Redis::hmset("player:{$user->id}", ['playerName' => $playerName]);
    
        \Log::info("Updated Redis for player: " . $user->id);
        session(['uuid' => $user->id]);

        return response()->json(['success' => true]);
    }
    
    public function viewLobby() 
    {
        $uuid = session('uuid');
        \Log::info("Inside viewLobby method with UUID: $uuid");
    
        if (!$uuid) {
            \Log::error("UUID not found in session");
            return redirect()->back()->with('error', 'Session expired or invalid UUID.');
        }
    
        $playerData = Redis::hgetall("player:{$uuid}");
        $playerName = $playerData['playerName'] ?? null;
        $roomCode = $playerData['roomCode'] ?? null;
    
        \Log::info("Fetched playerName ($playerName) and roomCode ($roomCode) from Redis");
    
        if ($playerName && $roomCode) {
            event(new PlayerJoinedLobby($playerName, $roomCode));
            return view('lobby');
        } else {
            \Log::error("Failed to load playerName and/or roomCode");
            return redirect()->back()->with('error', 'Failed to load lobby. Please try again.');
        }
    }

    
    public function authChannel(Request $request)
    {
        $channelName = $request->input('channel_name');
        $roomCode = $channelName;
        $isValidRoomCode = $this->isValidRoomCode($roomCode);
        \Log::info("Authenticating channel {$channelName} for room {$roomCode}. Valid room code: {$isValidRoomCode}");

        return response()->json(['isValidRoomCode' => $isValidRoomCode]);
    }


    public function clientJoinedChannel(Request $request) {
        $uuid = $request->input('uuid');
        $roomCode = $request->input('roomCode');
        
        $playerData = Redis::hgetall("player:{$uuid}");
        $playerName = $playerData['playerName'] ?? null;

        if ($playerName && $roomCode) {
            event(new PlayerJoinedLobby($playerName, $roomCode));
            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => 'Failed to fire PlayerJoinedLobby event.'], 400);
        }
    }

}

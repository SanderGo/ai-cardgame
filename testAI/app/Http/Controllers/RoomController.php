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

        Redis::sadd('active_rooms', $roomCode);

        return $roomCode;
    }

    private function isValidRoomCode()
    {
        $uuid = session('uuid');
        $playerData = Redis::hgetall("player:{$uuid}");
        $playerName = $playerData['playerName'] ?? null;
        $roomCode = $playerData['roomCode'] ?? null;

        \Log::info("isValidRoomCode: Checking if room code {$roomCode} is valid");

        $activeRooms = Redis::smembers('active_rooms');
        $roomExists = in_array($roomCode, $activeRooms);

        \Log::info("isValidRoomCode: Room code exists: " . $roomExists);

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
        \Log::info("setPlayerName: Player {$playerName} joined room {$roomCode}");        
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
        $playerName = $request->input('playerName');
        \Log::info("updatePlayer: Updating Player Name");

        $user = User::find(auth()->id());
        if (!$user) {
            \Log::error("updatePlayer: User not found");
            return response()->json(['error' => 'User not found'], 404);
        }        
        else {
            \Log::info("updatePlayer: Found user: " . ($user ? $user->id : 'null')); 
        }
        
        $user->update(['name' => $playerName]);
    
        Redis::hmset("player:{$user->id}", ['playerName' => $playerName]);
    
        \Log::info("updatePlayer: Updated Redis for player: " . $user->id);
        session(['uuid' => $user->id]);

        return response()->json(['success' => true]);
    }
    
    public function viewLobby() 
    {
        $uuid = session('uuid');
        \Log::info("viewLobby: Inside viewLobby method with UUID: $uuid");    

        if (!$uuid) {
            \Log::error("viewLobby: UUID not found in session");
            return redirect()->back()->with('error', 'Session expired or invalid UUID.');
        }
    
        $playerData = Redis::hgetall("player:{$uuid}");
        $playerName = $playerData['playerName'] ?? null;
        $roomCode = $playerData['roomCode'] ?? null;
    
        \Log::info("viewLobby: Fetched playerName ($playerName) and roomCode ($roomCode) from Redis");

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

        \Log::info("authChannel: Authenticating channel {$channelName} for room {$roomCode}. Valid room code: {$isValidRoomCode}");
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

    public function cleanupPlayer(Request $request) {
        $uuid = $request->input('uuid');
        $roomCode = $request->input('roomCode');
    
        // 1. Remove the player from Redis.
        Redis::del("player:{$uuid}");
    
        // 2. Remove the player from the room's set of players.
        Redis::srem("room:{$roomCode}:players", $uuid);
    
        // 3. Remove the player from SQL.
        User::find($uuid)->delete();
    
        // 4. Check if the room is now empty.
        if (!Redis::scard("room:{$roomCode}:players")) {
            // Remove the room's set of players.
            Redis::del("room:{$roomCode}:players");
    
            // Remove the room from the set of active rooms.
            Redis::srem('active_rooms', $roomCode);
        }
    
        return response()->json(['message' => 'Player and potentially room cleaned up']);
    }
    
}

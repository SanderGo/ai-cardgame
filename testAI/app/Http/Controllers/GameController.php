<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\PlayerJoinedLobby;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Services\RedisService;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}

class GameController extends Controller
{
    protected $redisService;

    public function __construct(RedisService $redisService)
    {
        $this->redisService = $redisService;
    }

    public function joinGame(Request $request)
    {
        try {
            $playerName = $request->input('player_name');

            // Generate a unique playerID. This is a simplified example, replace with your own logic
            $playerID = Str::random(10);

            $roomCode = $this->redisService->getRoomCode(); // Retrieve the room code from Redis

            // Check if the player name is not empty
            if (empty($playerName)) {
                return response()->json(['error' => 'Player name cannot be empty.'], 400);
            }

            // Store player details in Redis
            $roomCode = $this->redisService->getRoomCode();
            if (!$roomCode) {
                $roomCode = 'default_room_code'; // Replace 'default_room_code' with your desired default value
                $this->redisService->setRoomCode($roomCode); // Set the default value in Redis
            }

            // Retrieve the player list from Redis and include the new player
            $playerList = $this->redisService->addPlayerToRoom($roomCode, $playerName);

            broadcast(new PlayerJoinedLobby($playerList, $roomCode))->toOthers(); // Pass the updated player list to the event and exclude the current user from the broadcast

            return response()->json(['success' => true, 'playerList' => $playerList]); // Return the updated player list in the response
        } catch (\Exception $e) {
            // Log the exception
            \Log::error($e->getMessage());
            \Log::error($e->getTraceAsString());

            // Return a response indicating the error
            return response()->json(['error' => 'An error occurred. Please try again.'], 500);
        }
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\PlayerJoinedLobby;
use App\Services\RedisService;
use Illuminate\Support\Str;

class GameController extends Controller
{
    protected $redisService;

    public function __construct(RedisService $redisService)
    {
        $this->redisService = $redisService;
    }

    public function createOrJoinGame(Request $request)
    {
        try {
            $playerName = $request->input('player_name');
            if (empty($playerName)) {
                return response()->json(['error' => 'Player name cannot be empty.'], 400);
            }
            $playerID = Str::random(10);
            $roomCode = $this->redisService->getRoomCode();
            if (!$roomCode) {
                $roomCode = Str::upper(Str::random(5));
                $this->redisService->setRoomCode($roomCode);
            }
            $playerList = $this->redisService->addPlayerToRoom($roomCode, $playerName, $playerID);
            broadcast(new PlayerJoinedLobby($playerList, $roomCode))->toOthers();
            return response()->json(['success' => true, 'roomCode' => $roomCode, 'playerList' => $playerList]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            \Log::error($e->getTraceAsString());
            return response()->json(['error' => 'An error occurred. Please try again.'], 500);
        }
    }

    public function viewGame()
    {
        $roomCode = Redis::get('roomCode');
        if ($roomCode) {
            return view('game');
        } else {
            return redirect()->route('home');
        }
    }

    public function joinGame(Request $request)
    {
        // Your logic to handle joining a game should be here.
        // I'm assuming you'll add the logic later or maybe missed it.
    }
}

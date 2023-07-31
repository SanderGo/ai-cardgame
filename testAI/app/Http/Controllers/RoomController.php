<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Redis;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Helpers\RoomCodeGenerator;
use App\Events\PlayerJoinedLobby;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\View;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}

class RoomController extends Controller
{
    // Moved the character set to a constant to avoid redundancy
    const CHARACTER_SET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    public function generateRoomCode($length = 5)
    {
        $roomCode = '';
        for ($i = 0; $i < $length; $i++) {
            $randomIndex = rand(0, strlen(self::CHARACTER_SET) - 1);
            $roomCode .= self::CHARACTER_SET[$randomIndex];
        }

        return $roomCode;
    }

    public function isRoomInSet($joinCode)
    {
        return Redis::sismember('rooms', $joinCode);
    }

    public function joinOrCreateRoom(Request $request)
    {
        $action = $request->input('action');
        $roomCode = $request->input('roomCode');
        $playerName = $request->input('playerName');
        $playerId = $request->session()->getId();
        $nickname = $request->input('nickname');

        if ($action === 'join') {
            // Attempt to join a room
            if ($this->isRoomInSet($roomCode)) {
                // Add player to room
                $this->redisService->addPlayerToRoom($roomCode, $playerId);

                // Store player info
                $this->redisService->setPlayerListForRoom("player:{$playerId}", [
                    'nickname' => $nickname,
                    'roomCode' => $roomCode,
                ]);

                // Return the 'lobby' view with the joined room code
                return view('lobby', ['roomCode' => $roomCode]);
            } else {
                // Room not found, handle this case accordingly (e.g., redirect back to the form with error)
                return redirect()->back()->with('error', 'Room not found');
            }
        } else {
            // Create a room
            $roomCode = $this->generateRoomCode();

            // Add player to room
            Redis::sadd("room:{$roomCode}:players", $playerId);

            // Store player info
            Redis::hmset("player:{$playerId}", [
                'nickname' => $nickname,
                'roomCode' => $roomCode,
            ]);

            return view('create', ['roomCode' => $roomCode]);
        }
    }

    public function leaveRoom(Request $request)
    {
        $validatedData = $request->validate([
            'roomCode' => 'required|max:5',
        ]);

        $playerId = $request->session()->getId();
        $roomCode = $validatedData['roomCode'];

        // Remove player from room
        Redis::srem("room:{$roomCode}:players", $playerId);

        // Remove room code from player info
        Redis::hdel("player:{$playerId}", 'roomCode');

        return response()->json([
            'playerId' => $playerId,
            'roomCode' => null,
        ]);
    }


    public function getRoomCode()
    {
        $roomCode = Redis::get('roomCode');
        return response()->json(['roomCode' => $roomCode]);
    }
}

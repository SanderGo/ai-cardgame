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
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}

class RoomController extends Controller
{
    const CHARACTER_SET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    public function generateRoomCode(Request $request)
    {
        $roomCode = $this->redisService->getRoomCode();
            if (!$roomCode) {
                $roomCode = Str::upper(Str::random(5));
                $this->redisService->setRoomCode($roomCode);
            }

        return $roomCode;
    }


    public function joinOrCreateRoom(Request $request)
    {
        $action = $request->input('action');
        $roomCode = $request->input('roomCode');
        $playerName = $request->input('playerName');
        $playerId = $request->session()->getId();
        $nickname = $request->input('nickname');

        if ($action === 'join') {
            if ($this->isRoomInSet($roomCode)) {
                $this->redisService->addPlayerToRoom($roomCode, $playerId);
                $this->redisService->setPlayerListForRoom("player:{$playerId}", [
                    'nickname' => $nickname,
                    'roomCode' => $roomCode,
                ]);
                return view('lobby', ['roomCode' => $roomCode]);
            } else {
                return redirect()->back()->with('error', 'Room not found');
            }
        } else {
            $roomCode = $this->generateRoomCode();
            Redis::sadd("room:{$roomCode}:players", $playerId);
            Redis::hmset("player:{$playerId}", [
                'nickname' => $nickname,
                'roomCode' => $roomCode,
            ]);
            return view('create', ['roomCode' => $roomCode]);
        }
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

    public function getRoomCode()
    {
        $roomCode = Redis::get('roomCode');
        return response()->json(['roomCode' => $roomCode]);
    }

}

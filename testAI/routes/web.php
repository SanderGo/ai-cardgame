<?php

use Illuminate\Support\Facades\Route;
use App\Helpers\RoomCodeGenerator;
use Illuminate\Http\Request;
use App\Events\PlayerJoinedLobby;
use App\Http\Controllers\GameController;
use App\Http\Controllers\RoomController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redis;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/create', [RoomController::class, 'joinOrCreateRoom'])->name('create');


Route::post('/set-room-code', function (Request $request) {
    Redis::set('roomCode', $request->roomCode);
    return response()->json(['success' => 'Room code set successfully']);
});

Route::get('/get-room-code', [RoomController::class, 'getRoomCode'])->name('get-room-code');

Route::get('/lobby', function () {
    $roomCode = Redis::get('roomCode');
    if ($roomCode) {
        return view('lobby');
    } else {
        return redirect()->route('home');
    }
})->name('lobby');

//Route::post('/join', [GameController::class, 'joinGame'])->name('join');

Route::post('/join', function (Request $request) {
    $playerName = $request->input('player_name');

    // Generate a unique room code
    $roomCode = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4);

    // Store the room code and player name in Redis
    Redis::set("roomCode:$roomCode", $roomCode);
    Redis::hset("room:$roomCode", $request->session()->getId(), $playerName);

    return response()->json([
        'roomCode' => $roomCode
    ]);
});

Route::get('/game', function () {
    $roomCode = Redis::get('roomCode');
    if ($roomCode) {
        return view('game');
    } else {
        return redirect()->route('home');
    }
})->name('game');

Route::post('/join-room', [RoomController::class, 'joinOrCreateRoom']);

Route::post('/broadcasting/auth', function () {
    return Auth::check() ? Auth::user() : abort(403);
});

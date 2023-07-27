<?php

use Illuminate\Support\Facades\Route;
use App\Helpers\RoomCodeGenerator;
use Illuminate\Http\Request;
use App\Events\PlayerJoinedLobby;
use App\Http\Controllers\GameController;


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

Route::get('/create', function () {
    $roomCode = RoomCodeGenerator::generateRoomCode(); // Call a function to generate a unique room code
    session(['roomCode' => $roomCode]);
    return view('create');
});

Route::get('/lobby', function () {
    if (session('roomCode')) {
        return view('lobby');
    } else {
        return redirect()->route('home');
    }
})->name('lobby');

Route::post('/join-room', function (Request $request) {
    session(['roomCode' => $request->roomCode]);
    return response()->json(['success' => 'Room code set successfully']);
});


Route::post('/set-room-code', function (Request $request) {
    session(['roomCode' => $request->roomCode]);
    return response()->json(['success' => 'Room code set successfully']);
});

Route::post('/join', [GameController::class, 'joinGame'])->name('join');

Route::get('/game', function () {
    if (session('roomCode')) {
        return view('game');
    } else {
        return redirect()->route('home');
    }
})->name('game');





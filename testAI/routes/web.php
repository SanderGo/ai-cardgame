<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\RoomController;
use App\Events\PlayerJoinedLobby;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', fn() => view('home'))->name('home');

// Room routes
Route::get('create', [RoomController::class, 'createRoom'])->name('create');
Route::get('code', [RoomController::class, 'getRoomCode'])->name('get-room-code');
Route::post('join', [RoomController::class, 'joinRoom'])->name('joinRoom');

Route::middleware('auth:web')->group(function () {
  Route::get('lobby', [RoomController::class, 'viewLobby'])->name('lobby');
  Route::post('update-player', [RoomController::class, 'updatePlayer'])->name('update-player');
  Route::post('set-player-name', [RoomController::class, 'setPlayerName']);
  Route::post('/client-joined-channel', [RoomController::class, 'clientJoinedChannel']);
});

// Game routes
Route::get('game', [GameController::class, 'viewGame'])->name('game');
Route::post('game/join', [GameController::class, 'joinGame'])->name('join');
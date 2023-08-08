<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GameController;
use App\Http\Controllers\RoomController;

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
Route::post('set-code', [RoomController::class, 'setRoomCode'])->name('set-room-code');
Route::get('code', [RoomController::class, 'getRoomCode'])->name('get-room-code');
Route::get('lobby', [RoomController::class, 'viewLobby'])->name('lobby');
Route::post('join-room', [RoomController::class, 'joinRoom'])->name('join-room');
Route::post('update-player', [RoomController::class, 'updatePlayer'])->name('update-player');

// Game routes
Route::get('game', [GameController::class, 'viewGame'])->name('game');
Route::post('game/join', [GameController::class, 'joinGame'])->name('join');

// Broadcasting routes
Route::post('/broadcasting/auth', [RoomController::class, 'authChannel'])->withoutMiddleware(['csrf']);



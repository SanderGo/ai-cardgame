<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('lobby', function ($user) {
    return true; // Adjust the logic to check authorization if needed
});

Broadcast::channel('{roomCode}', function ($user, $roomCode) {
    // Allow public access to the presence channel without authentication
    return true;
});

Broadcast::channel('presence-room.{roomCode}', function ($user, $roomCode) {
    return true; 
});

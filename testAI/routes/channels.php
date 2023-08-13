<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------

*/

Broadcast::channel('room.{roomCode}', function ($user, $roomCode) {
    \Log::info('Attempting to authorize', ['user' => $user, 'roomCode' => $roomCode]);
    return true;
});


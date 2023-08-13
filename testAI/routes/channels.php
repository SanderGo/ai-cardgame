<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------

*/

Broadcast::channel('room.{roomCode}', function (User $user, $roomCode) {
    return auth()->id() === $user->id;
});


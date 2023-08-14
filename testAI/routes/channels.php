<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------

*/

Broadcast::channel('room.{roomCode}', function (User $user, $roomCode) {
    if (auth()->check() && auth()->id() === $user->id) {
        return [
            'id' => $user->id,
            'name' => $user->name,
            // ... any other user-specific data you want to include.
        ];
    }
    return false;  // Deny access if conditions aren't met.
});

<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;




Broadcast::channel('room.{roomCode}', function (User $user, $roomCode) {
    if (auth()->check() && auth()->id() === $user->id) {
        return [
            'id' => $user->id,
            'name' => $user->name,
        ];
    }
    return false; 
});

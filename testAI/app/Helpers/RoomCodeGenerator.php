<?php

namespace App\Helpers;

class RoomCodeGenerator
{
    public static function generateRoomCode($length = 5)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $roomCode = '';

        for ($i = 0; $i < $length; $i++) {
            $randomIndex = rand(0, strlen($characters) - 1);
            $roomCode .= $characters[$randomIndex];
        }

        return $roomCode;
    }
}

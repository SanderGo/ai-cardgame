<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\PlayerJoinedLobby;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}

class GameController extends Controller
{
    public function joinGame(Request $request)
    {
        try {
            $playerName = $request->input('player_name');

            // Add the player to the lobby (e.g., store in session or database)

            $roomCode = Session::get('roomCode'); // Retrieve the room code from the session

            // Retrieve the player list (e.g., from session or database) and include the new player
            $playerList = Session::get('playerList.' . $roomCode, []); // Retrieve the existing player list for the current room code or initialize it as an empty array
            $playerList[] = $playerName; // Add the new player to the list
            Session::put('playerList.' . $roomCode, $playerList); // Store the updated player list back in the session

            event(new PlayerJoinedLobby($playerList, $roomCode))->toOthers(); // Pass the updated player list to the event and exclude the current user from the broadcast

            return response()->json(['success' => true, 'playerList' => $playerList]); // Return the updated player list in the response
        } catch (\Exception $e) {
            // Log the exception
            \Log::error($e->getMessage());
            \Log::error($e->getTraceAsString());

            // Return a response indicating the error
            return response()->json(['error' => 'An error occurred. Please try again.'], 500);
        }
    }
}
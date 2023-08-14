<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css"
          integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65"
          crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <title>Lobby</title>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    
</head>

<body>
<div id="titleContainer">
    <div id="smallTitle">
        <p>Room Code: <span id="room-code"></span></p>
    </div>
</div>
<div id="lobbyContainer">
    <div id="lobbyTitle">
        <h1>Waiting for players...</h1>
    </div>
    <div id="center">
        <form action="/action_page.php">
            <label for="cars">
                <h1>How many questions for this game?</h1>
            </label>
            <select name="cars" id="cars">
                <option value="3">3</option>
                <option value="5">5</option>
                <option value="10">10</option>
                <option value="15">15</option>
            </select>
            <br/>
        </form>
        <button class="btn btn-light custom-button1"
                onclick="window.location.href='{{ route('game') }}'">Start Game
        </button>
        <ul id="player-list" class="text-white"></ul>
    </div>

    <script>
        let playerName = sessionStorage.getItem('playerName');
        let roomCode = sessionStorage.getItem('roomCode');
        let isConnected = false; // Flag to check if the client is connected

        document.getElementById('room-code').textContent = roomCode;

    </script>
    <script src="{{ asset('/js/app.js') }}"></script>
</body>
</html>

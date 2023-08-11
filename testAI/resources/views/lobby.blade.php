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

        // Display the current player's name
        addPlayerToList(playerName);

        console.log(sessionStorage.getItem('roomCode'));
        
        // Echo.join(`room.${sessionStorage.getItem("roomCode")}`)
        //     .here((users) => {
        //         isConnected = true; // Set the flag to true when connected

        //         console.log('Users here:', users);
        //         users.forEach(user => {
        //             addPlayerToList(user.name);
        //         });
        //     })
        //     .joining((user) => {
        //         console.log('A new user joined:', user.name);
        //     })
        //     .leaving((user) => {
        //         removePlayerFromList(user.name);
        //         console.log('A user left:', user.name);
        //     })
        //     .listen('PlayerJoinedLobby', (e) => {
        //         if (!isConnected) {
        //             console.log('Not connected yet. Ignoring PlayerJoinedLobby event.');
        //             return;
        //         }

        //         console.log('Player joined lobby:', e.playerName);
                
        //         // Get the updated player list from the event data
        //         let playerList = e.playerList;
        //         // Clear the current player list in the HTML
        //         let playerListElement = document.getElementById('player-list');
        //         playerListElement.innerHTML = '';

        //         // Loop through the player list and add players to the HTML
        //         playerList.forEach(player => {
        //             addPlayerToList(player);
        //         });
        //     });

        function addPlayerToList(newPlayerName) {
            let playerListElement = document.getElementById('player-list');
            let listItem = document.createElement('li');
            listItem.textContent = newPlayerName;
            playerListElement.appendChild(listItem);
        }

        function removePlayerFromList(playerNameToRemove) {
            let playerListElement = document.getElementById('player-list');
            let listItems = playerListElement.getElementsByTagName('li');
            for (let item of listItems) {
                if (item.textContent === playerNameToRemove) {
                    playerListElement.removeChild(item);
                    break;
                }
            } 
        }

    </script>
    <script src="{{ asset('/js/app.js') }}"></script>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo asset('css/styles.css')?>">
    <title>Lobby</title>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
    <script src="{{ asset('/js/app.js') }}"></script>
</head>
<body>
    <div id="titleContainer">
        <div id="smallTitle">
            <p>Room Code: <span id="room-code">{{ session('roomCode') }}</span></p>
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
            <!-- <input type="submit" value="Submit">  change this to be the start button so people chose # of questions then start game!  -->
        </form>
        <button class="btn btn-light custom-button1" onclick="window.location.href='{{ route('game') }}'">Start Game</button>
        <ul id="player-list" class="text-white"></ul>
    </div>
    
    <script>
        let roomCode = localStorage.getItem('roomCode'); // get room code from localStorage
        let currentRoomCode = roomCode;

        Echo.channel('presence-room.' + roomCode)
            .listen('PlayerJoinedLobby', (e) => {
                console.log(e.playerListJson);
                try {
                    let playerList = JSON.parse(e.playerListJson);
                    addPlayerToList(playerList);
                } catch (error) {
                    console.error('Error parsing player list:', error);
                }
            });


        var playerName = localStorage.getItem('playerName');

        if (playerName) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            axios.post('/join', { player_name: playerName })
                .then(response => {
                    console.log(response.data.success);
                    addPlayerToList(response.data.playerList);
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Handle error case
                });
            }

        function addPlayerToList(playerList) {
            console.log('Updating player list:', playerList);
            var playerListElement = document.getElementById('player-list');
            var roomCodeElement = document.getElementById('room-code');

            if (currentRoomCode !== roomCode) {
                currentRoomCode = roomCode;
                playerListElement.innerHTML = ''; // Clear the existing player list when switching room codes
            }

            // Clear the existing player list before adding new one
            playerListElement.innerHTML = '';

            playerList.forEach(playerName => {
                var listItem = document.createElement('li');
                listItem.textContent = playerName;
                playerListElement.appendChild(listItem);
            });
        }

    </script>
</body>
</html>

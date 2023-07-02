<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Lobby</title>
    <script src="{{ asset('/js/app.js') }}"></script>
    <script src="https://js.pusher.com/7.0/pusher.min.js"></script>
</head>
<body>
    <h1>Lobby</h1>
    <p>Room Code: <span id="room-code">{{ session('roomCode') }}</span></p>
    <ul id="player-list"></ul>
    <script>
        let roomCode = '{{ session('roomCode') }}';
        let currentRoomCode = roomCode;

        Echo.channel('lobby-' + roomCode)
            .listen('PlayerJoinedLobby', (e) => {
                console.log(e.playerList);
                addPlayerToList(e.playerList);
            });

        var playerName = localStorage.getItem('playerName');

        if (playerName) {
            axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            axios.post('/join', { player_name: playerName })
                .then(response => {
                    console.log(response.data.success);
                    updatePlayerList(response.data.playerList);
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

            playerList.forEach(playerName => {
                var listItem = document.createElement('li');
                listItem.textContent = playerName;
                playerListElement.appendChild(listItem);
            });
        }
    </script>
</body>
</html>

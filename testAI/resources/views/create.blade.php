<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI:OH</title>
    <script>
        var playerData = {
            roomCode: @json($roomCode),
            uuid: @json($uuid),
        }
        localStorage.setItem('roomCode', playerData.roomCode);
        async function joinLobby() {
            var playerNameInput = document.getElementById("player_name").value.trim();

            if (playerNameInput === '') {
                alert('Please enter a nickname.');
                return;
            }

            // Using the embedded playerData
            localStorage.setItem('playerName', playerNameInput);

            localStorage.setItem('uuid', playerData.uuid);

            // Make an async request to Laravel to save player name and UUID in Redis
            try {
                const response = await fetch('/update-player', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest', // for Laravel's CSRF token
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        uuid: playerData.uuid,
                        playerName: playerNameInput
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Redirect to the lobby page
                    window.location.href = '/lobby';
                } else {
                    alert('There was a problem joining the lobby. Please try again.');
                }
            } catch (error) {
                alert('Error: ' + error);
            }
        }


    </script>
    <script src="{{ mix('js/app.js') }}"></script>
    <script src="{{ asset('js/stringInput.js') }}"></script>
</head>

<body>
    <div id="titleContainer">
        <div id="bigTitle">AI OH</div>
        <h1>An AI Card Game For Small Businesses</h1>
    </div>

    <div id="center">
        <div id="join-button">
            <button onclick="joinLobby()" class="btn btn-light custom-button1">Create a room!</button>
        </div>
        <input class="form-control input1" type="text" id="player_name" name="player_name" placeholder="Enter Nickname Here" onkeypress="return isAlphanumeric(event)">
    </div>
</body>
</html>

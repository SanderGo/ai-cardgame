<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <title>AI:OH</title>
    <script type="text/javascript">
        function joinLobby() {
            var userNameInput = document.getElementById("player_name").value.trim();

            // Check if the name is not empty
            if (userNameInput === '') {
                alert('Please enter a nickname.');
                return;
            }

            var roomCodeElement = document.getElementById('room-code');
            var roomCode = roomCodeElement.textContent; // Retrieve the room code from the DOM

            // Set room code via Fetch API
            fetch('/set-room-code', { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ roomCode: roomCode })
            })
            .then(response => response.json())
            .then(data => {
                console.log(data.success); // Room code set successfully
                localStorage.setItem("roomCode", roomCode); // Store the room code in localStorage
                window.location.href = '{{ route('lobby') }}'; // Redirect to the lobby page
            })
            .catch((error) => {
                console.error('Error:', error);
            });


            localStorage.setItem("playerName", userNameInput);
            localStorage.setItem("roomInput", roomCode);
        }

        function generateRoomCode() {
            var length = 5; // Length of the room code
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'; // Allowed characters

            var roomCode = '';
            for (var i = 0; i < length; i++) {
                var randomIndex = Math.floor(Math.random() * characters.length);
                roomCode += characters.charAt(randomIndex);
            }

            return roomCode;
        }
    </script>

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
        <p>Room Code: <span id="room-code">{{ session('roomCode') }}</span></p>
        <input class="form-control input1" type="text" id="player_name" name="player_name" placeholder="Enter Nickname Here" onkeypress="return isAlphanumeric(event)">
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var roomCodeElement = document.getElementById('room-code');
            if (roomCodeElement.textContent === '') {
                roomCodeElement.textContent = generateRoomCode();
            }
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <title>AI:OH</title>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script type="text/javascript">
        function joinLobby() {
            var userInput2 = document.getElementById("name").value;
            localStorage.setItem("nameInput", userInput2);

            var roomCode = generateRoomCode(); // Call the function to generate a unique room code
            localStorage.setItem("roomInput", roomCode);

            // Set room code via AJAX
            axios.post('/set-room-code', { roomCode: roomCode })
            .then(function (response) {
                console.log(response.data.success); // Room code set successfully
                window.location.href = '{{ route('lobby') }}'; // Redirect to the lobby page
            })
            .catch(function (error) {
                console.log(error);
            });
        }


        function generateRoomCode() {
            var length = 5; // Length of the room code
            var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789'; // Allowed characters

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
            <button onclick="joinLobby()" class="btn btn-light custom-button1">Join a room!</button>
        </div>
        <input class="form-control input1" type="text" id="name" name="name" placeholder="Enter Nickname Here" onkeypress="return isAlphanumeric(event)">
    </div>
</body>
</html>

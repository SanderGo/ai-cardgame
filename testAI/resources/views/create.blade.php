<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <script src="{{ mix('js/app.js') }}"></script>
    <title>AI:OH</title>
    <script type="text/javascript">
    function joinLobby() {
    var userNameInput = document.getElementById("player_name").value.trim();

    // Check if the name is not empty
    if (userNameInput === '') {
        alert('Please enter a nickname.');
        return;
    }

    // Create a new room
    fetch('/join', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            player_name: userNameInput
        })
    })
    .then(response => response.json())
    .then(data => {
        // Check if the server returned an error
        if (data.error) {
            alert(data.error);
            return;
        }

        // Player joined successfully, store playerName and roomCode in localStorage
        localStorage.setItem('playerName', userNameInput);
        localStorage.setItem('roomCode', data.roomCode);

        // Join the presence channel using the roomCode
        window.Echo.join(`presence-${data.roomCode}`)

            .here((users) => {
                // Update the player list with the current users in the room
                updatePlayerList(users);
            })

            .joining((user) => {
                // A new user has joined the room, update the player list
                updatePlayerList([...window.Echo.presenceChannel(`presence-${data.roomCode}`).users, user]);
            })

            .leaving((user) => {
                // A user has left the room, update the player list
                updatePlayerList([...window.Echo.presenceChannel(`presence-${data.roomCode}`).users].filter(u => u.id !== user.id));
            })

            .listen('GameStarted', (event) => {
                // The game has started, redirect to the game page
                window.location.href = `/game/${data.roomCode}`;
            });
    });
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
        <input class="form-control input1" type="text" id="player_name" name="player_name" placeholder="Enter Nickname Here" onkeypress="return isAlphanumeric(event)">
    </div>
</body>
</html>

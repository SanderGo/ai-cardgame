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
            window.Echo.join(`presence-room.${data.roomCode}`)

                .here((users) => {
                    console.log('Users in channel:', users);
                })
                .joining((user) => {
                    console.log('A new user joined:', user.name);
                })
                .leaving((user) => {
                    console.log('A user left:', user.name);
                })
                .listen('.PlayerJoinedLobby', (event) => {
                    // Here, instead of "playerName", you should use the actual data property name sent by the server.
                    console.log('Player joined:', event.playerName); 
                });

                const isAuthenticated = localStorage.getItem('playerName') !== null && localStorage.getItem('roomCode') !== null;

            if (isAuthenticated) {
                // Redirect to the lobby page
                window.location.href = '{{ route('lobby') }}';
            } else {
                // Handle the case when the user is not authenticated (e.g., display an error message)
                console.error('User is not authenticated.');
            }
        })
        .catch((error) => {
            console.error('Error:', error);
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

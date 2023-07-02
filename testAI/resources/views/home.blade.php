<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo asset('css/styles.css')?>">
    <title>AI:OH</title>
    <script type="text/javascript">
        function roomGrab() {
            var roomInput = document.getElementById("code").value.trim();

            // Check if roomInput has exactly 5 characters
            if (roomInput.length !== 5) {
                alert('Please enter a valid room code with exactly 5 characters.');
                return;
            }

            if (roomInput !== '') {
                fetch('/join-room', { 
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ roomCode: roomInput })
                })
                .then(response => response.json())
                .then(data => {
                    console.log(data.success); // Room code set successfully
                    window.location.href = '{{ route('lobby') }}'; // Redirect to the lobby page
                })
                .catch((error) => {
                    console.error('Error:', error);
                });
            } else {
                alert('Please enter a valid room code.');
            }
        }


    </script>

    <<script src="{{ asset('js/stringInput.js') }}"></script>
</head>
<body>
    <div id="titleContainer">
        <div id="bigTitle">AI OH</div>
        <h1>An AI Card Game For Small Businesses</h1>
    </div>

    <div id="center">
        <div> 
            <button onclick="window.location='<?php echo url('/create')?>'" class="btn btn-light custom-button1">CREATE A ROOM</button>
        </div>
        <br>
        <div> 
            <button onclick="roomGrab()" class="btn btn-light custom-button1">JOIN A ROOM</button>
        </div>
        <input class="form-control input1" type="text" id="code" roomCode="code" placeholder="Enter Room Code Here!" onfocus="this.value=''" onkeypress="return isAlphanumeric(event)">
    </div>

    <?php
        if (session('roomCode')) {
            session(['roomCode' => null]);
        }
    ?>
</body>
</html>

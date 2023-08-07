<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <title>AI:OH</title>
    <script type="text/javascript">
        let y = 0;
        const interval = setInterval(function(){
            y += 1;
            document.body.style.backgroundPosition = '0 ' + y + 'px';
        }, 10);

        window.onunload = function() {
            clearInterval(interval);
        };
    </script>
    <script type="text/javascript">
        localStorage.clear();
        async function roomGrab() {
            const roomInput = document.getElementById("code").value.trim();

            if (roomInput.length !== 5) {
                alert('Please enter a valid room code with exactly 5 characters.');
                return;
            }

            try {
                const response = await fetch('/join-room', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        roomCode: roomInput,
                        action: 'join'
                    })
                });

                if (!response.ok) {
                    throw new Error("HTTP error " + response.status);
                }

                const data = await response.json();
                if (data.error) {
                    alert(data.error);
                } else {
                    console.log(data.success); // Room code set successfully
                    localStorage.setItem('roomCode', roomInput);
                    window.location.href = '{{ route('lobby') }}';
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            }
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
        <div> 
            <button onclick="location.href='{{ url('/create') }}'" class="btn btn-light custom-button1">CREATE A ROOM</button>
        </div>
        <br>
        <div> 
            <button onclick="roomGrab()" class="btn btn-light custom-button1">JOIN A ROOM</button>
        </div>
        <input class="form-control input1" type="text" id="code" placeholder="Enter Room Code Here!" onfocus="this.value=''" onkeypress="return isAlphanumeric(event)">
    </div>
    @php
        if (session('roomCode')) {
            session(['roomCode' => null]);
        }
    @endphp
</body>
</html>

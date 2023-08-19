<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI:OH</title>
    <script src="{{ asset('js/bgMove.js') }}"></script>
    <script>
    // Get uuid from sessionStorage if exists, then set it to var and clear sessionStorage.
    var uuid = sessionStorage.getItem('uuid');
    sessionStorage.clear();
    </script>
</head>
<body>
    <div id="titleContainer">
        <div id="bigTitle">AI OH</div>
        <h1>An AI Card Game For Small Businesses</h1>
    </div>
    <div id="center">
        <!-- Displaying the error message if any -->
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div> 
                    <button onclick="location.href='{{ route('create') }}'" class="btn btn-light custom-button1">CREATE A ROOM</button>
                </div>
            </body>
            </html>
        <br>
        
        <!-- Form for joining a room -->
        <form action="/join" method="post">
            @csrf
            <button type="submit" class="btn btn-light custom-button1">JOIN A ROOM</button>
            <input class="form-control input1" type="text" id="roomCodeInput" name="roomCode" placeholder="Enter Room Code Here!" data-maxlength="5">
        </form>
    </div>
    <script src="{{ asset('js/handleInput.js') }}"></script>
</body>
</html>

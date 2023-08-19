import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

window.Pusher = Pusher;
window.Pusher.logToConsole = true;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    useTLS: false,
    scheme: 'http',
    authEndpoint: '/broadcasting/auth',
    auth: {
       headers: {
           'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
       }
    }
});

window.Echo.join(`room.${sessionStorage.getItem("roomCode")}`)
    .here((users) => {
        isConnected = true;

        users.forEach(user => {
            addPlayerToList(user.name);
        });
        console.log('Users here:', users);
    })
    .joining((user) => {
        addPlayerToList(user.name);
        console.log('A new user joined:', user.name);
    })
    .leaving((user) => {
        removePlayerFromList(user.name);
        console.log('A user left:', user.name);
    
        // Send an AJAX request to handle individual player cleanups.
        axios.post('/cleanup-player', {
            uuid: user.id,  // Assuming user ID is the UUID.
            roomCode: sessionStorage.getItem("roomCode")
        });
    })    
    .listen('PlayerJoinedLobby', (e) => {
        if (!isConnected) {
            console.log('Not connected yet. Ignoring PlayerJoinedLobby event.');
            return;
        }
        console.log('Player joined lobby:', e.playerName);
        
        let playerList = e.playerList;

        let playerListElement = document.getElementById('player-list');
        playerListElement.innerHTML = '';

        playerList.forEach(player => {
            addPlayerToList(player);
        });
    })
    .error((error) => {
        console.error('Error:', error);
    });



    
    function addPlayerToList(playerName) {
        let playerListElement = document.getElementById('player-list');
        let playerElement = document.createElement('li');
        playerElement.innerText = playerName;
        playerListElement.appendChild(playerElement);
    }

    function removePlayerFromList(playerName) {
        let playerListElement = document.getElementById('player-list');
        let playerElements = Array.from(playerListElement.children);  // Convert HTMLCollection to array
    
        playerElements.forEach(function(element) {
            if (element.textContent === playerName) {
                playerListElement.removeChild(element);
            }
        });
    }
    


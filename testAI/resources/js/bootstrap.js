import axios from 'axios';
window.axios = axios;

// Set the X-Requested-With header
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Add the CSRF token to axios defaults
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

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
    });

function addPlayerToList(playerName) {
    let playerListElement = document.getElementById('player-list');
    let playerElement = document.createElement('li');
    playerElement.innerText = playerName;
    playerListElement.appendChild(playerElement);
}

function removePlayerFromList(playerName) {
    let playerListElement = document.getElementById('player-list');
    let playerElement = playerListElement.querySelector(`li:contains(${playerName})`);
    playerListElement.removeChild(playerElement);
}


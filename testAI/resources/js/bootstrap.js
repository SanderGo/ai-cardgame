import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    encrypted: false,
    scheme: 'http',
    authEndpoint: '/broadcasting/auth',
    forceTLS: true,
});


window.Echo.join(`room.${sessionStorage.getItem("roomCode")}`)
    .here((users) => {
        isConnected = true; // Set the flag to true when connected

        console.log('Users here:', users);
        users.forEach(user => {
            addPlayerToList(user.name);
        });
    })
    .joining((user) => {
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
        
        // Get the updated player list from the event data
        let playerList = e.playerList;
        // Clear the current player list in the HTML
        let playerListElement = document.getElementById('player-list');
        playerListElement.innerHTML = '';

        // Loop through the player list and add players to the HTML
        playerList.forEach(player => {
            addPlayerToList(player);
        });
    });
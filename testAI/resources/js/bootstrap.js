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
    encrypted: true,
});
  
window.Echo.join(`room.${localStorage.getItem("roomCode")}`)
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

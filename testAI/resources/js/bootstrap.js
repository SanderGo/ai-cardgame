import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '4c231fdc01a893cb3773',
    cluster: 'us2',
    forceTLS: 'true',
    encrypted: true,
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
    },
});
  
window.Echo.channel('lobby')
.listen('.PlayerJoinedLobby', (event) => {
    // Update the player list in the UI
    console.log('Player joined:', event.playerName);
});

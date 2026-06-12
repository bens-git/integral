import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.content;

const getMeta = (name) => document.querySelector(`meta[name="${name}"]`)?.content;

const key = getMeta('broadcast-key');
const host = getMeta('broadcast-host');
const port = parseInt(getMeta('broadcast-port')) || 443;
const scheme = getMeta('broadcast-scheme') || 'http';

const hasBroadcastConfig = key && key !== 'null' && host;

window.Pusher = Pusher;
window.Pusher.logToConsole = false;

if (hasBroadcastConfig) {
    // Create pusher client with custom options for self-hosted Reverb/Soketi
    const pusher = new Pusher(key, {
        cluster: 'local',
        wsHost: host,
        wsPort: port,
        wssPort: port,
        forceTLS: scheme === 'https',
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
    });
    
    window.Echo = new Echo({
        broadcaster: 'pusher',
        client: pusher,
        authEndpoint: '/broadcasting/auth',
        withCredentials: true,
    });
} else {
    window.Echo = {
        private: () => ({ listen: () => {} }),
        leave: () => {},
    };
}
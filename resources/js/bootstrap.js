import axios from 'axios';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true;
window.axios.defaults.headers.common = window.axios.defaults.headers.common || {};

window.axios.interceptors.request.use((config) => {
    const token = document.cookie
        .split('; ')
        .find((c) => c.startsWith('XSRF-TOKEN='))
        ?.split('=')[1];
    if (token) {
        config.headers.common = config.headers.common || {};
        config.headers.common['X-XSRF-TOKEN'] = decodeURIComponent(token);
    }
    return config;
});

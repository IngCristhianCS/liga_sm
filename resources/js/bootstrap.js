import axios from 'axios';

window.axios = axios;
//window.axios.defaults.baseURL = 'https://liga-san-miguel-develop-vjpi3t.laravel.cloud';
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

axios.defaults.withCredentials = true;

// Añadir CSRF Token
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}

if (window.authUser && window.authUser.apiToken) {
    window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + window.authUser.apiToken;
} else {
    console.log('Token no disponible');
}
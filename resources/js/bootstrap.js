import axios from 'axios';

window.axios = axios;
window.axios.defaults.baseURL = 'http://localhost:8000'; // Añade esto
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Añadir CSRF Token
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
}
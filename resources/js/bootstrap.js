import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[csrf-token]').content;

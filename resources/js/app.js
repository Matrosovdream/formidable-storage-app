import { createApp } from 'vue';
import axios from 'axios';
import router from './router';
import App from './pages/App.vue';

// Axios base config
axios.defaults.baseURL = 'http://localhost'; // adjust if needed
axios.defaults.withCredentials = true;

const app = createApp(App);

app.config.globalProperties.$axios = axios;

app.use(router);

app.mount('#app');

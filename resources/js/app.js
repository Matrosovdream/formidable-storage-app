import { createApp } from 'vue';
import axios from 'axios';
import router from './router';
import App from './pages/App.vue';
import "../css/app.css";
import "../css/admin/style.bundle.css";

// Axios base config
axios.defaults.withCredentials = true;

axios.defaults.baseURL =
    import.meta.env.VITE_API_URL   // API URL from env
    ?? window.location.origin;     // fallback to same-origin

const app = createApp(App);
app.config.globalProperties.$axios = axios;

app.use(router);
app.mount('#app');

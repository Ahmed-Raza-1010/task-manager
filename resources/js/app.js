import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import axios from "axios";
window.axios = axios;
import { createVuetify } from 'vuetify';
import 'vuetify/styles';
//  Set Axios base URL
axios.defaults.baseURL = "http://task-manager.test";

// Ensure Axios has token after refresh
const token = localStorage.getItem("token");
if (token) {
    axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
}

//  Fetch user data if token exists
if (token) {
    axios.get("/api/user")
        .then(response => {
            localStorage.setItem("user", JSON.stringify(response.data)); // ✅ Store user info
        })
        .catch(() => {
            localStorage.removeItem("token"); // ✅ Remove invalid token
            localStorage.removeItem("user");
        });
}

// Setup Vuetify
const vuetify = createVuetify();

// Create Vue app
const app = createApp(App);
app.use(router);
app.use(vuetify);
app.mount('#app');

import { createRouter, createWebHistory } from 'vue-router';
import Login from '../components/Login.vue';
import Register from '../components/Register.vue';
import TaskList from '../components/TaskList.vue';

// Define routes
const routes = [
    { path: "/", redirect: "/login" }, 
    { path: "/login", component: Login, meta: { requiresGuest: true } },
    { path: "/register", component: Register, meta: { requiresGuest: true } },
    { path: "/tasks", component: TaskList, meta: { requiresAuth: true } },
];

// Create router instance
const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Navigation Guard (Ensures user stays logged in after refresh)
router.beforeEach((to, from, next) => {
    const token = localStorage.getItem("token");

    if (to.meta.requiresAuth && !token) {
        next("/login"); // Redirect to login if not authenticated
    } else if (to.meta.requiresGuest && token) {
        next("/tasks"); // Redirect logged-in users away from login/register
    } else {
        next(); // Allow navigation
    }
});

export default router;

import { createRouter, createWebHistory } from 'vue-router';
import axios from 'axios';

import Login from '../pages/Login.vue';
import Register from '../pages/Register.vue';
import Dashboard from '../pages/Dashboard.vue';

const routes = [
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { guest: true },
    },
    {
        path: '/register',
        name: 'register',
        component: Register,
        meta: { guest: true },
    },
    {
        path: '/dashboard',
        name: 'dashboard',
        component: Dashboard,
        meta: { requiresAuth: true },
    },
    {
        path: '/:pathMatch(.*)*',
        redirect: '/login',
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// NO global cached currentUser anymore, always ask backend
router.beforeEach(async (to, from, next) => {
    let currentUser = null;

    try {
        const { data } = await axios.get('/api/user');
        currentUser = data;
    } catch (e) {
        currentUser = null;
    }

    if (to.meta.requiresAuth && !currentUser) {
        // wants auth-only page but not logged in
        return next({ name: 'login' });
    }

    if (to.meta.guest && currentUser) {
        // wants guest-only page (login/register) but is logged in
        return next({ name: 'dashboard' });
    }

    next();
});

export default router;

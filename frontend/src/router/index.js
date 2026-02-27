import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
  {
    path: '/',
    name: 'Home',
    component: () => import('../views/Home.vue'),
  },
  {
    path: '/polls/:slug',
    name: 'PollView',
    component: () => import('../views/PollView.vue'),
  },
  {
    path: '/admin/login',
    name: 'AdminLogin',
    component: () => import('../views/admin/Login.vue'),
    meta: { guest: true }
  },
  {
    path: '/login',
    name: 'UserLogin',
    component: () => import('../views/auth/Login.vue'),
    meta: { guest: true }
  },
  {
    path: '/register',
    name: 'UserRegister',
    component: () => import('../views/auth/Register.vue'),
    meta: { guest: true }
  },
  {
    path: '/admin/dashboard',
    name: 'AdminDashboard',
    component: () => import('../views/admin/Dashboard.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/admin/poll/:id',
    name: 'AdminPollView',
    component: () => import('../views/admin/AdminPollView.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/setup',
    name: 'AdminSetup',
    component: () => import('../views/admin/Setup.vue'),
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(async (to) => {
    const authStore = useAuthStore();
    
    // Ensure we check auth status on first load if we have a token/cookie
    if (!authStore.initialized) {
        await authStore.fetchUser().catch(() => {});
    }

    if (to.meta.requiresAuth && !authStore.user) {
        return { name: 'UserLogin' };
    } 
    
    if (to.meta.requiresAuth && to.path.startsWith('/admin') && !authStore.user?.is_admin) {
        return { name: 'Home' };
    } 
    
    if (to.meta.guest && authStore.user) {
        return authStore.user.is_admin ? { name: 'AdminDashboard' } : { name: 'Home' };
    }
});

export default router;

import { defineStore } from 'pinia';
import api, { csrf } from '../services/api';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        loading: false,
        initialized: false,
    }),
    getters: {
        isAuthenticated: (state) => !!state.user,
    },
    actions: {
        async login(credentials) {
            const response = await api.post('/login', credentials);
            this.setAuth(response.data);
        },
        async register(data) {
            const response = await api.post('/register', data);
            this.setAuth(response.data);
        },
        async setupAdmin(data) {
            const response = await api.post('/admin-setup', data);
            this.setAuth(response.data);
        },
        setAuth(data) {
            this.user = data.user;
            if (data.access_token) {
                localStorage.setItem('auth_token', data.access_token);
            }
        },
        async logout() {
            try {
                await api.post('/logout');
            } finally {
                this.user = null;
                localStorage.removeItem('auth_token');
            }
        },
        async fetchUser(force = false) {
            if (this.initialized && !force) return;
            
            // If we have no token, no need to fetch (unless we want to support cookie fallback)
            if (!localStorage.getItem('auth_token')) {
                this.initialized = true;
                return;
            }

            this.loading = true;
            try {
                const response = await api.get('/user');
                this.user = response.data;
            } catch (error) {
                this.user = null;
                localStorage.removeItem('auth_token');
            } finally {
                this.loading = false;
                this.initialized = true;
            }
        }
    }
});

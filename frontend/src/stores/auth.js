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
            await csrf(); // Get CSRF token
            const response = await api.post('/login', credentials);
            this.user = response.data.user;
        },
        async logout() {
            await api.post('/logout');
            this.user = null;
        },
        async fetchUser(force = false) {
            if (this.initialized && !force) return;
            this.loading = true;
            try {
                const response = await api.get('/user');
                this.user = response.data;
            } catch (error) {
                this.user = null;
            } finally {
                this.loading = false;
                this.initialized = true;
            }
        }
    }
});

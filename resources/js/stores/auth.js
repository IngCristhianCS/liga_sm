import { defineStore } from 'pinia'
import axios from 'axios'

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: window.authUser || null,
        isLoading: false,
        error: null
    }),
    
    actions: {
        async initialize() {
            if (this.user) return;
            await this.checkAuth();
        },

        async login(credentials) {
            this.isLoading = true;
            this.error = null;
            
            try {
                const response = await axios.post('/login', credentials);
                
                // Actualizar usuario y recargar
                this.user = response.data.user;
                window.location.reload();
            } catch (error) {
                this.error = error.response?.data?.message || 'Error de autenticación';
                throw error;
            } finally {
                this.isLoading = false;
            }
        },

        async checkAuth() {
            try {
                //const { data } = await axios.get('/api/user');
                this.user = window.authUser || null;
            } catch (error) {
                this.user = null;
            }
        }
    },
    
    getters: {
        isAuthenticated: (state) => !!state.user,
        isAdmin: (state) => state.user?.role_id === 1,
        isEntrenador: (state) => state.user?.role_id === 2,
        isJugador: (state) => state.user?.role_id === 3,
        userName: (state) => state.user?.name || 'Invitado'
    }
});
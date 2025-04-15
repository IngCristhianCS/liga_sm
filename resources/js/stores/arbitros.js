import { defineStore } from 'pinia';
import axios from 'axios';

export const useArbitrosStore = defineStore('arbitros', {
    state: () => ({
        arbitros: [],
        loading: false,
        error: null
    }),
    actions: {
        async loadArbitros() {
            // If we already have the arbitros, don't fetch again
            if (this.arbitros.length > 0) {
                return this.arbitros;
            }
            
            try {
                this.loading = true;
                this.error = null;
                const response = await axios.get('/api/arbitros');
                this.arbitros = response.data.data;
                return this.arbitros;
            } catch (error) {
                this.error = error.response?.data?.message || error.message || 'Error cargando arbitros';
                return [];
            } finally {
                this.loading = false;
            }
        }
    },
});
import { defineStore } from 'pinia';
import axios from 'axios';

export const useUbicacionesStore = defineStore('ubicaciones', {
    state: () => ({
        ubicaciones: [],
        loading: false,
        error: null
    }),
    actions: {
        async loadUbicaciones() {
            // If we already have the ubicaciones, don't fetch again
            if (this.ubicaciones.length > 0) {
                return this.ubicaciones;
            }
            
            try {
                this.loading = true;
                this.error = null;
                const response = await axios.get('/api/ubicaciones');
                this.ubicaciones = response.data.data;
                return this.ubicaciones;
            } catch (error) {
                this.error = 'Error al cargar ubicaciones';
                return [];
            } finally {
                this.loading = false;
            }
        }
    },
});
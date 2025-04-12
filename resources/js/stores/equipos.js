import { defineStore } from 'pinia';
import axios from 'axios';

export const useEquiposStore = defineStore('equipos', {
    state: () => ({
        equipos: [],
        equiposByTorneo: {},
        loading: false,
        error: null
    }),
    actions: {
        async loadEquipos() {
            try {
                this.loading = true;
                this.error = null;
                const response = await axios.get('/api/equipos');
                this.equipos = response.data.data;
            } catch (error) {
                this.error = 'Error al cargar equipos';
            } finally {
                this.loading = false;
            }
        },
        
        async loadEquiposByTorneo(torneoId) {
            // If we already have the equipos for this tournament, don't fetch again
            if (this.equiposByTorneo[torneoId]) {
                return this.equiposByTorneo[torneoId];
            }
            
            try {
                this.loading = true;
                this.error = null;
                const response = await axios.get(`/api/equipos?torneo_id=${torneoId}`);
                this.equiposByTorneo[torneoId] = response.data.data;
                return this.equiposByTorneo[torneoId];
            } catch (error) {
                this.error = 'Error al cargar equipos del torneo';
                return [];
            } finally {
                this.loading = false;
            }
        }
    },
});
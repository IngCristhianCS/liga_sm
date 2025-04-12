import { defineStore } from 'pinia';
import axios from 'axios';

export const useJornadasStore = defineStore('jornadas', {
    state: () => ({
        jornadas: [],
        jornadasByTorneo: {},
        loading: false,
        error: null
    }),
    actions: {
        async loadJornadas() {
            try {
                this.loading = true;
                this.error = null;
                const response = await axios.get('/api/jornadas/partidos-equipo');
                this.jornadas = response.data.data;
            } catch (error) {
                this.error = 'Error al cargar datos';
            } finally {
                this.loading = false;
            }
        },
        
        async loadJornadasByTorneo(torneoId) {
            // If we already have the jornadas for this tournament, don't fetch again
            if (this.jornadasByTorneo[torneoId]) {
                return this.jornadasByTorneo[torneoId];
            }
            
            try {
                this.loading = true;
                this.error = null;
                const response = await axios.get(`/api/jornadas?torneo_id=${torneoId}`);
                this.jornadasByTorneo[torneoId] = response.data.data;
                return this.jornadasByTorneo[torneoId];
            } catch (error) {
                this.error = 'Error al cargar jornadas del torneo';
                return [];
            } finally {
                this.loading = false;
            }
        }
    },
});
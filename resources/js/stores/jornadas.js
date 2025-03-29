import { defineStore } from 'pinia';
import axios from 'axios';

export const useJornadasStore = defineStore('jornadas', {
    state: () => ({
        jornadas: [],
        loading: false,
        error: null
    }),
    actions: {
        async loadJornadas() {
            try {
                this.loading = true
                this.error = null
                const response = await axios.get('/api/jornadas/partidos-equipo');
                this.jornadas = response.data.data;
            } catch (error) {
                this.error = 'Error al cargar datos'
            } finally {
                this.loading = false
            }
        },
    },
});
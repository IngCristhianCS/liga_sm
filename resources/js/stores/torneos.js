import { defineStore } from 'pinia';
import axios from 'axios';

export const useTorneoStore = defineStore('torneos', {
  state: () => ({
    torneos: [],
    loading: false,
    error: null,
  }),
  actions: {
    async fetchTorneos() {
      try {
        this.loading = true;
        this.error = null;
        const response = await axios.get('/api/torneos');
        this.torneos = response.data.data;
      } catch (error) {
        this.error = 'Error al cargar torneos';
        console.error('Error fetching torneos:', error);
      } finally {
        this.loading = false;
      }
    }
  },
  getters: {
    primerTorneo: (state) => state.torneos[0] || null
  }
});
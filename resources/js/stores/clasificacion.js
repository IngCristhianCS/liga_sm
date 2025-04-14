import { defineStore } from 'pinia';
import axios from 'axios';

export const useClasificacionStore = defineStore('clasificacion', {
  state: () => ({
    clasificacion: [],
    clasificacionByTorneo: {}, // Store classification data organized by tournament ID
    loading: false,
    error: null,
  }),
  actions: {
    async fetchClasificacion(id = null) {
      try {
        this.loading = true;
        this.error = null;
        
        const url = id ? `/api/clasificacion/${id}` : '/api/clasificacion';
        const response = await axios.get(url, {
          params: { nocache: new Date().getTime() },
        });
        
        this.clasificacion = response.data;
        
        // If we have a tournament ID, store the data in the clasificacionByTorneo object
        if (id) {
          this.clasificacionByTorneo[id] = response.data;
        }
        
      } catch (error) {
        this.error = 'Error al cargar datos';
        console.error('Error en fetchClasificacion:', error);
      } finally {
        this.loading = false;
      }
    },
    
    // Get classification for a specific tournament
    async getClasificacionByTorneo(torneoId) {
      // If we already have the data, return it
      if (this.clasificacionByTorneo[torneoId]) {
        return this.clasificacionByTorneo[torneoId];
      }
      
      // Otherwise fetch it
      await this.fetchClasificacion(torneoId);
      return this.clasificacionByTorneo[torneoId] || [];
    },
    
    // Clear classification data for a specific tournament or all tournaments
    clearClasificacion(torneoId = null) {
      if (torneoId) {
        delete this.clasificacionByTorneo[torneoId];
      } else {
        this.clasificacionByTorneo = {};
        this.clasificacion = [];
      }
    }
  },
  getters: {
    getClasificacion: (state) => (torneoId) => {
      return state.clasificacionByTorneo[torneoId] || [];
    }
  }
});
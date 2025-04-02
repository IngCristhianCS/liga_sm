import { defineStore } from 'pinia';
import axios from 'axios';

export const useClasificacionStore = defineStore('clasificacion', {
  state: () => ({
    clasificacion: [],
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
        
      } catch (error) {
        this.error = 'Error al cargar datos';
        console.error('Error en fetchClasificacion:', error);
      } finally {
        this.loading = false;
      }
    },
  },
});
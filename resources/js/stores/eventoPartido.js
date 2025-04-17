import { defineStore } from 'pinia';
import axios from 'axios';

export const useEventoPartidoStore = defineStore('eventoPartido', {
  state: () => ({
    eventos: {},
    loading: false,
    error: null
  }),
  
  getters: {
    getEventosByPartidoId: (state) => (partidoId) => {
      return state.eventos[partidoId] || [];
    }
  },
  
  actions: {
    async fetchEventosByPartido(partidoId) {
      if (!partidoId) return [];
      
      this.loading = true;
      this.error = null;
      
      try {
        const response = await axios.get(`/api/partidos/${partidoId}/eventos`);
        this.eventos[partidoId] = response.data.data;
        return this.eventos[partidoId];
      } catch (error) {
        this.error = error.message || 'Error al cargar eventos';
        return [];
      } finally {
        this.loading = false;
      }
    },
    
    async createEvento(eventoData) {
      this.loading = true;
      this.error = null;
      
      try {
        const response = await axios.post('/api/eventos-partido', eventoData);
        const newEvento = response.data.data;
        
        // Add to the eventos list for this partido
        if (newEvento.partido_id) {
          if (!this.eventos[newEvento.partido_id]) {
            this.eventos[newEvento.partido_id] = [];
          }
          this.eventos[newEvento.partido_id].push(newEvento);
        }
        
        return newEvento;
      } catch (error) {
        this.error = error.response?.data?.message || 'Error al crear evento';
        throw error;
      } finally {
        this.loading = false;
      }
    },
    
    async updateEvento(id, eventoData) {
      if (!id) return null;
      
      this.loading = true;
      this.error = null;
      
      try {
        const response = await axios.put(`/api/eventos-partido/${id}`, eventoData);
        const updatedEvento = response.data.data;
        
        // Update in the eventos list
        if (updatedEvento.partido_id && this.eventos[updatedEvento.partido_id]) {
          const index = this.eventos[updatedEvento.partido_id].findIndex(e => e.id === id);
          if (index !== -1) {
            this.eventos[updatedEvento.partido_id][index] = updatedEvento;
          }
        }
        
        return updatedEvento;
      } catch (error) {
        this.error = error.response?.data?.message || 'Error al actualizar evento';
        throw error;
      } finally {
        this.loading = false;
      }
    },
    
    async deleteEvento(id, partidoId) {
      if (!id) return false;
      
      this.loading = true;
      this.error = null;
      
      try {
        await axios.delete(`/api/eventos-partido/${id}`);
        
        // Remove from the eventos list
        if (partidoId && this.eventos[partidoId]) {
          this.eventos[partidoId] = this.eventos[partidoId].filter(e => e.id !== id);
        }
        
        return true;
      } catch (error) {
        this.error = error.response?.data?.message || 'Error al eliminar evento';
        return false;
      } finally {
        this.loading = false;
      }
    }
  }
});
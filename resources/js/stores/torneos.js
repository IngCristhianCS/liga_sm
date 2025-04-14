import { defineStore } from 'pinia';
import axios from 'axios';

export const useTorneoStore = defineStore('torneos', {
  state: () => ({
    torneos: [],
    torneosCatalog: [],
    currentTorneo: null,
    loading: false,
    error: null,
  }),
  
  getters: {
    primerTorneo: (state) => state.torneos[0] || null,
    
    getTorneoById: (state) => (id) => {
      // First check in the detailed torneos
      const torneoDetallado = state.torneos.find(t => t.id === id);
      if (torneoDetallado) return torneoDetallado;
      
      // Then check in the catalog
      return state.torneosCatalog.find(t => t.id === id) || null;
    },
    
    getTorneosCatalog: (state) => state.torneosCatalog
  },
  
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
    },
    
    async fetchTorneosCatalog() {
      try {
        this.loading = true;
        this.error = null;
        const response = await axios.get('/api/torneos/catalog');
        this.torneosCatalog = response.data.data;
        return this.torneosCatalog;
      } catch (error) {
        this.error = 'Error al cargar catálogo de torneos';
        console.error('Error fetching torneos catalog:', error);
        return [];
      } finally {
        this.loading = false;
      }
    },
    
    async fetchTorneoById(id) {
      try {
        this.loading = true;
        this.error = null;
        
        // Check if we already have this tournament in full detail
        const existingTorneo = this.torneos.find(t => t.id === id);
        if (existingTorneo) {
          this.currentTorneo = existingTorneo;
          return this.currentTorneo;
        }
        
        // Otherwise fetch from API
        const response = await axios.get(`/api/torneos/${id}`);
        this.currentTorneo = response.data.data;
        
        // Update the full list if this tournament isn't there
        if (!this.torneos.some(t => t.id === id)) {
          this.torneos.push(this.currentTorneo);
        }
        
        return this.currentTorneo;
      } catch (error) {
        this.error = 'Error al cargar detalles del torneo';
        return null;
      } finally {
        this.loading = false;
      }
    },
    
    async createTorneo(torneoData) {
      try {
        this.loading = true;
        this.error = null;
        const response = await axios.post('/api/torneos', torneoData);
        const newTorneo = response.data.data;
        
        // Add to the full list
        this.torneos.push(newTorneo);
        
        // Add to catalog if it's loaded
        if (this.torneosCatalog.length > 0) {
          this.torneosCatalog.push({
            id: newTorneo.id,
            nombre: newTorneo.nombre
          });
        }
        
        return newTorneo;
      } catch (error) {
        this.error = 'Error al crear torneo';
        throw error;
      } finally {
        this.loading = false;
      }
    },
    
    async updateTorneo(id, torneoData) {
      if (!id) return null;
      
      try {
        this.loading = true;
        this.error = null;
        const response = await axios.put(`/api/torneos/${id}`, torneoData);
        const updatedTorneo = response.data.data;
        
        // Update in the full list
        const index = this.torneos.findIndex(t => t.id === id);
        if (index !== -1) {
          this.torneos[index] = updatedTorneo;
        }
        
        // Update in catalog if it's loaded
        const catalogIndex = this.torneosCatalog.findIndex(t => t.id === id);
        if (catalogIndex !== -1) {
          this.torneosCatalog[catalogIndex] = {
            id: updatedTorneo.id,
            nombre: updatedTorneo.nombre
          };
        }
        
        // Update current torneo if it's the one being edited
        if (this.currentTorneo && this.currentTorneo.id === id) {
          this.currentTorneo = updatedTorneo;
        }
        
        return updatedTorneo;
      } catch (error) {
        this.error = `Error al actualizar torneo ${id}`;
        console.error(`Error updating torneo ${id}:`, error);
        throw error;
      } finally {
        this.loading = false;
      }
    },
    
    async deleteTorneo(id) {
      if (!id) return false;
      
      try {
        this.loading = true;
        this.error = null;
        await axios.delete(`/api/torneos/${id}`);
        
        // Remove from the full list
        this.torneos = this.torneos.filter(t => t.id !== id);
        
        // Remove from catalog if it's loaded
        this.torneosCatalog = this.torneosCatalog.filter(t => t.id !== id);
        
        // Clear current torneo if it's the one being deleted
        if (this.currentTorneo && this.currentTorneo.id === id) {
          this.currentTorneo = null;
        }
        
        return true;
      } catch (error) {
        this.error = `Error al eliminar torneo ${id}`;
        console.error(`Error deleting torneo ${id}:`, error);
        return false;
      } finally {
        this.loading = false;
      }
    },
    
    clearCurrentTorneo() {
      this.currentTorneo = null;
    },
    
    clearTorneos() {
      this.torneos = [];
      this.torneosCatalog = [];
      this.currentTorneo = null;
      this.error = null;
    }
  }
});
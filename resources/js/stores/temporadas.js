import { defineStore } from 'pinia';
import axios from 'axios';

export const useTemporadasStore = defineStore('temporadas', {
  state: () => ({
    temporadas: [],
    temporadasCatalog: [], // For lightweight catalog usage in selects
    currentTemporada: null,
    loading: false,
    error: null
  }),
  
  getters: {
    getTemporadaById: (state) => (id) => {
      return state.temporadas.find(temporada => temporada.id === id);
    }
  },
  
  actions: {
    async loadTemporadas() {
      if (this.temporadas.length > 0) return this.temporadas;
      
      try {
        this.loading = true;
        this.error = null;
        const response = await axios.get('/api/temporadas');
        this.temporadas = response.data.data;
        return this.temporadas;
      } catch (error) {
        this.error = 'Error al cargar temporadas';
        console.error('Error loading temporadas:', error);
        return [];
      } finally {
        this.loading = false;
      }
    },
    
    async fetchTemporadasCatalog() {
      if (this.temporadasCatalog.length > 0) return this.temporadasCatalog;
      
      try {
        this.loading = true;
        this.error = null;
        
        // Try to get from catalog endpoint first
        try {
          const response = await axios.get('/api/temporadas/catalog');
          this.temporadasCatalog = response.data.data;
        } catch (catalogError) {
          // If catalog endpoint fails, use the full temporadas data
          if (this.temporadas.length === 0) {
            await this.loadTemporadas();
          }
          
          // Create a simplified version for the catalog
          this.temporadasCatalog = this.temporadas.map(t => ({
            id: t.id,
            nombre: t.nombre
          }));
        }
        
        return this.temporadasCatalog;
      } catch (error) {
        this.error = 'Error al cargar catálogo de temporadas';
        console.error('Error fetching temporadas catalog:', error);
        return [];
      } finally {
        this.loading = false;
      }
    },
    
    async createTemporada(temporadaData) {
      try {
        this.loading = true;
        this.error = null;
        const response = await axios.post('/api/temporadas', temporadaData);
        const newTemporada = response.data.data;
        
        // Add the new temporada to the list
        this.temporadas.push(newTemporada);
        
        // Add to catalog if it's loaded
        if (this.temporadasCatalog.length > 0) {
          this.temporadasCatalog.push({
            id: newTemporada.id,
            nombre: newTemporada.nombre
          });
        }
        
        return newTemporada;
      } catch (error) {
        this.error = 'Error al crear la temporada';
        console.error('Error creating temporada:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },
    
    async updateTemporada(id, temporadaData) {
      if (!id) return null;
      
      try {
        this.loading = true;
        this.error = null;
        const response = await axios.put(`/api/temporadas/${id}`, temporadaData);
        const updatedTemporada = response.data.data;
        
        // Update the temporada in the list
        const index = this.temporadas.findIndex(t => t.id === id);
        if (index !== -1) {
          this.temporadas[index] = updatedTemporada;
        }
        
        return updatedTemporada;
      } catch (error) {
        this.error = `Error al actualizar la temporada ${id}`;
        throw error;
      } finally {
        this.loading = false;
      }
    },
    
    async deleteTemporada(id) {
      if (!id) return false;
      
      try {
        this.loading = true;
        this.error = null;
        await axios.delete(`/api/temporadas/${id}`);
        
        // Remove the temporada from the list
        this.temporadas = this.temporadas.filter(t => t.id !== id);
        
        return true;
      } catch (error) {
        this.error = `Error al eliminar la temporada ${id}`;
        return false;
      } finally {
        this.loading = false;
      }
    },
    
    clearTemporadas() {
      this.temporadas = [];
      this.currentTemporada = null;
      this.error = null;
    }
  }
});
import { defineStore } from 'pinia';
import axios from 'axios';

export const usePartidoStore = defineStore('partidos', {
  state: () => ({
    partidosByTorneo: {},
    loading: false,
    error: null,
    currentPartido: null
  }),
  
  getters: {
    getPartidoById: (state) => (id) => {
      // Search through all torneos for the partido
      for (const torneoId in state.partidosByTorneo) {
        const found = state.partidosByTorneo[torneoId].find(partido => partido.id === id);
        if (found) return found;
      }
      return null;
    },
    
    getPartidosByTorneo: (state) => (torneoId) => {
      return state.partidosByTorneo[torneoId] || [];
    },
    
    getPartidosByEquipo: (state) => (equipoId) => {
      const result = [];
      // Search through all torneos
      for (const torneoId in state.partidosByTorneo) {
        const matches = state.partidosByTorneo[torneoId].filter(partido => 
          partido.equipo_local_id === equipoId || partido.equipo_visitante_id === equipoId
        );
        result.push(...matches);
      }
      return result;
    },
    
    getPartidosByFecha: (state) => (fecha) => {
      const result = [];
      // Search through all torneos
      for (const torneoId in state.partidosByTorneo) {
        const matches = state.partidosByTorneo[torneoId].filter(partido => partido.fecha === fecha);
        result.push(...matches);
      }
      return result;
    },
    
    // Getter to access all partidos if needed (but use sparingly)
    partidos: (state) => {
      const allPartidos = [];
      for (const torneoId in state.partidosByTorneo) {
        allPartidos.push(...state.partidosByTorneo[torneoId]);
      }
      return allPartidos;
    }
  },
  
  actions: {
    async fetchPartidos() {
      this.loading = true;
      this.error = null;
      
      try {
        const response = await axios.get('/api/partidos');
        const partidosList = response.data.data;
        
        // Organize partidos by torneo_id
        this._organizePartidosByTorneo(partidosList);
      } catch (error) {
        this.error = error.message || 'Error al cargar partidos';
      } finally {
        this.loading = false;
      }
    },
    
    async fetchPartidosByTorneo(torneoId) {
      if (!torneoId) return;
      
      this.loading = true;
      this.error = null;
      
      try {
        const response = await axios.get(`/api/partidos?torneo_id=${torneoId}`);
        const partidosList = response.data.data;
        
        // Update the partidosByTorneo object
        this.partidosByTorneo[torneoId] = partidosList;
        
        return partidosList;
      } catch (error) {
        console.error(`Error fetching partidos for torneo ${torneoId}:`, error);
        this.error = error.message || 'Error al cargar partidos del torneo';
      } finally {
        this.loading = false;
      }
    },
    
    // Helper method to organize partidos by torneo_id
    _organizePartidosByTorneo(partidosList) {
      const byTorneo = {};
      
      for (const partido of partidosList) {
        if (!partido.torneo_id) continue;
        
        if (!byTorneo[partido.torneo_id]) {
          byTorneo[partido.torneo_id] = [];
        }
        
        byTorneo[partido.torneo_id].push(partido);
      }
      
      this.partidosByTorneo = byTorneo;
    },
    
    async fetchPartidoById(id) {
      if (!id) return;
      
      // Check if we already have this partido in our store
      const existingPartido = this.getPartidoById(id);
      if (existingPartido) {
        this.currentPartido = existingPartido;
        return existingPartido;
      }
      
      this.loading = true;
      this.error = null;
      
      try {
        const response = await axios.get(`/api/partidos/${id}`);
        this.currentPartido = response.data.data;
        
        // Update in the partidosByTorneo if applicable
        if (this.currentPartido.torneo_id) {
          if (!this.partidosByTorneo[this.currentPartido.torneo_id]) {
            this.partidosByTorneo[this.currentPartido.torneo_id] = [];
          }
          
          const torneoIndex = this.partidosByTorneo[this.currentPartido.torneo_id]
            .findIndex(p => p.id === id);
            
          if (torneoIndex !== -1) {
            this.partidosByTorneo[this.currentPartido.torneo_id][torneoIndex] = this.currentPartido;
          } else {
            this.partidosByTorneo[this.currentPartido.torneo_id].push(this.currentPartido);
          }
        }
        
        return this.currentPartido;
      } catch (error) {
        console.error(`Error fetching partido ${id}:`, error);
        this.error = error.message || 'Error al cargar el partido';
        return null;
      } finally {
        this.loading = false;
      }
    },
    
    async createPartido(partidoData) {
      this.loading = true;
      this.error = null;
      
      try {
        // Check if partidoData is FormData
        const isFormData = partidoData instanceof FormData;
        
        const response = await axios.post('/api/partidos', partidoData, {
          // Set the correct headers if it's FormData
          headers: isFormData ? {
            'Content-Type': 'multipart/form-data'
          } : {}
        });
        
        const newPartido = response.data.data;
        
        // Add to partidosByTorneo if it has a torneo_id
        if (newPartido.torneo_id) {
          if (!this.partidosByTorneo[newPartido.torneo_id]) {
            this.partidosByTorneo[newPartido.torneo_id] = [];
          }
          this.partidosByTorneo[newPartido.torneo_id].push(newPartido);
        }
        
        return newPartido;
      } catch (error) {
        console.error('Error creating partido:', error);
        this.error = error.response?.data?.errors || error.message || 'Error al crear el partido';
        throw error;
      } finally {
        this.loading = false;
      }
    },
    
    async updatePartido(id, partidoData) {
      if (!id) return null;
      
      this.loading = true;
      this.error = null;
      
      try {
        const isFormData = partidoData instanceof FormData;
        
        let torneoId;
        if (isFormData) {
          torneoId = partidoData.get('torneo_id');
        } else {
          torneoId = partidoData.torneo_id;
        }
        
        const oldPartido = this.getPartidoById(id);
        const oldTorneoId = oldPartido?.torneo_id;
        
        let response;
        if (isFormData) {
          partidoData.append('_method', 'PUT');
          response = await axios.post(`/api/partidos/${id}`, partidoData, {
            headers: {
              'Content-Type': 'multipart/form-data'
            }
          });
        } else {
          response = await axios.put(`/api/partidos/${id}`, partidoData);
        }
        
        const updatedPartido = response.data.data;
        
        // Ensure we have a torneo_id in the updated partido
        if (!updatedPartido.torneo_id) {
          // Use the torneo_id from the request data first
          if (torneoId) {
            updatedPartido.torneo_id = torneoId;
          } 
          // If not available, use the old torneo_id
          else if (oldTorneoId) {
            updatedPartido.torneo_id = oldTorneoId;
          }
        }
        
        // Remove from old torneo array
        if (oldTorneoId && this.partidosByTorneo[oldTorneoId]) {
          this.partidosByTorneo[oldTorneoId] = this.partidosByTorneo[oldTorneoId]
            .filter(p => p.id !== id);
        }
        
        const effectiveTorneoId = updatedPartido.torneo_id || oldTorneoId || torneoId;
        
        if (effectiveTorneoId) {
          if (!this.partidosByTorneo[effectiveTorneoId]) {
            this.partidosByTorneo[effectiveTorneoId] = [];
          }
          this.partidosByTorneo[effectiveTorneoId].push(updatedPartido);
        } else {
          console.warn(`No torneo_id found for partido ${id}, cannot add to any array`);
        }
        
        // Update currentPartido if it's the one being edited
        if (this.currentPartido && this.currentPartido.id === id) {
          this.currentPartido = updatedPartido;
        }
        
        return updatedPartido;
      } catch (error) {
        console.error(`Error updating partido ${id}:`, error);
        this.error = error.response?.data?.errors || error.message || 'Error al actualizar el partido';
        throw error;
      } finally {
        this.loading = false;
      }
    },
    
    async deletePartido(id) {
      if (!id) return false;
      
      this.loading = true;
      this.error = null;
      
      try {
        await axios.delete(`/api/partidos/${id}`);
        
        // Find the partido to get its torneo_id before removing
        const partido = this.getPartidoById(id);
        const torneoId = partido?.torneo_id;
        
        // Remove from partidosByTorneo if applicable
        if (torneoId && this.partidosByTorneo[torneoId]) {
          this.partidosByTorneo[torneoId] = this.partidosByTorneo[torneoId]
            .filter(p => p.id !== id);
        }
        
        return true;
      } catch (error) {
        this.error = error.message || 'Error al eliminar el partido';
        return false;
      } finally {
        this.loading = false;
      }
    },
    
    clearPartidos() {
      this.partidosByTorneo = {};
      this.currentPartido = null;
    }
  }
});
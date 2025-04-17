import { defineStore } from 'pinia';
import axios from 'axios';

export const useEquiposStore = defineStore('equipos', {
    state: () => ({
        equiposByTorneo: {},
        currentEquipo: null,
        loading: false,
        error: null,
        // Add a new state property to store jugadores by equipo
        jugadoresByEquipo: {}
    }),
    
    getters: {
        getEquipoById: (state) => (id, torneoId) => {
            // If we know the torneoId, search in that specific collection first
            if (torneoId && state.equiposByTorneo[torneoId]) {
                const equipo = state.equiposByTorneo[torneoId].find(e => e.id === id);
                if (equipo) return equipo;
            }
            
            // Otherwise search in all collections
            for (const torneoId in state.equiposByTorneo) {
                const equipo = state.equiposByTorneo[torneoId].find(e => e.id === id);
                if (equipo) return equipo;
            }
            
            return null;
        },
        
        // Add a new getter to get jugadores by equipo
        getJugadoresByEquipo: (state) => (equipoId) => {
            return state.jugadoresByEquipo[equipoId] || [];
        }
    },
    
    actions: {
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
        },
        
        async fetchEquipoById(id, torneoId) {
            try {
                this.loading = true;
                this.error = null;
                
                // Check if we already have this equipo
                const existingEquipo = this.getEquipoById(id, torneoId);
                if (existingEquipo) {
                    this.currentEquipo = existingEquipo;
                    return existingEquipo;
                }
                
                // Otherwise fetch from API
                const response = await axios.get(`/api/equipos/${id}`);
                this.currentEquipo = response.data.data;
                
                // Add to the torneo-specific list if it exists
                if (this.currentEquipo.torneo_id) {
                    if (!this.equiposByTorneo[this.currentEquipo.torneo_id]) {
                        this.equiposByTorneo[this.currentEquipo.torneo_id] = [];
                    }
                    
                    const torneoIndex = this.equiposByTorneo[this.currentEquipo.torneo_id].findIndex(e => e.id === id);
                    if (torneoIndex !== -1) {
                        this.equiposByTorneo[this.currentEquipo.torneo_id][torneoIndex] = this.currentEquipo;
                    } else {
                        this.equiposByTorneo[this.currentEquipo.torneo_id].push(this.currentEquipo);
                    }
                }
                
                return this.currentEquipo;
            } catch (error) {
                this.error = `Error al cargar el equipo ${id}`;
                return null;
            } finally {
                this.loading = false;
            }
        },
        
        async createEquipo(equipoData) {
            try {
                this.loading = true;
                this.error = null;
                
                // Check if equipoData is FormData
                const isFormData = equipoData instanceof FormData;
                
                const response = await axios.post('/api/equipos', equipoData, {
                // Set the correct headers if it's FormData
                headers: isFormData ? {
                'Content-Type': 'multipart/form-data'
                } : {}
                });
                
                const newEquipo = response.data.data;
                
                // Add to the torneo-specific list if it exists
                if (newEquipo.torneo_id) {
                    if (!this.equiposByTorneo[newEquipo.torneo_id]) {
                        this.equiposByTorneo[newEquipo.torneo_id] = [];
                    }
                    this.equiposByTorneo[newEquipo.torneo_id].push(newEquipo);
                }
                
                return newEquipo;
            } catch (error) {
                this.error = 'Error al crear el equipo';
                console.error('Error creating equipo:', error);
                throw error;
            } finally {
                this.loading = false;
            }
        },
        
        async updateEquipo(id, equipoData) {
            if (!id) return null;
            
            try {
                this.loading = true;
                this.error = null;
                
                // Check if equipoData is FormData
                const isFormData = equipoData instanceof FormData;
                
                let response;
                if (isFormData) {
                // If it's FormData, we need to use POST with _method=PUT for Laravel
                equipoData.append('_method', 'PUT');
                response = await axios.post(`/api/equipos/${id}`, equipoData, {
                headers: {
                'Content-Type': 'multipart/form-data'
                }
                });
                } else {
                // Regular JSON update
                response = await axios.put(`/api/equipos/${id}`, equipoData);
                }
                
                const updatedEquipo = response.data.data;
                
                // Get the old torneo_id if we have the equipo
                let oldTorneoId = null;
                for (const torneoId in this.equiposByTorneo) {
                    const index = this.equiposByTorneo[torneoId].findIndex(e => e.id === id);
                    if (index !== -1) {
                        oldTorneoId = torneoId;
                        break;
                    }
                }
                
                // Remove from old torneo list if torneo changed
                if (oldTorneoId && oldTorneoId !== updatedEquipo.torneo_id && this.equiposByTorneo[oldTorneoId]) {
                    this.equiposByTorneo[oldTorneoId] = this.equiposByTorneo[oldTorneoId].filter(e => e.id !== id);
                }
                
                // Update in the torneo-specific list
                if (updatedEquipo.torneo_id) {
                    if (!this.equiposByTorneo[updatedEquipo.torneo_id]) {
                        this.equiposByTorneo[updatedEquipo.torneo_id] = [];
                    }
                    
                    const torneoIndex = this.equiposByTorneo[updatedEquipo.torneo_id].findIndex(e => e.id === id);
                    if (torneoIndex !== -1) {
                        this.equiposByTorneo[updatedEquipo.torneo_id][torneoIndex] = updatedEquipo;
                    } else {
                        this.equiposByTorneo[updatedEquipo.torneo_id].push(updatedEquipo);
                    }
                }
                
                // Update current equipo if it's the one being edited
                if (this.currentEquipo && this.currentEquipo.id === id) {
                    this.currentEquipo = updatedEquipo;
                }
                
                return updatedEquipo;
            } catch (error) {
                this.error = `Error al actualizar el equipo ${id}`;
                console.error(`Error updating equipo ${id}:`, error);
                throw error;
            } finally {
                this.loading = false;
            }
        },
        
        async deleteEquipo(id) {
            if (!id) return false;
            
            try {
                this.loading = true;
                this.error = null;
                await axios.delete(`/api/equipos/${id}`);
                
                // Remove from all torneo-specific lists
                for (const torneoId in this.equiposByTorneo) {
                    this.equiposByTorneo[torneoId] = this.equiposByTorneo[torneoId].filter(e => e.id !== id);
                }
                
                // Clear current equipo if it's the one being deleted
                if (this.currentEquipo && this.currentEquipo.id === id) {
                    this.currentEquipo = null;
                }
                
                return true;
            } catch (error) {
                this.error = `Error al eliminar el equipo ${id}`;
                return false;
            } finally {
                this.loading = false;
            }
        },
        
        // Add a new action to load jugadores by equipo
        async loadJugadoresByEquipo(equipoId) {
            // If we already have the jugadores for this equipo, don't fetch again
            if (this.jugadoresByEquipo[equipoId]) {
                return this.jugadoresByEquipo[equipoId];
            }
            
            try {
                this.loading = true;
                this.error = null;
                const response = await axios.get(`/api/equipos/${equipoId}/jugadores`);
                this.jugadoresByEquipo[equipoId] = response.data.data;
                return this.jugadoresByEquipo[equipoId];
            } catch (error) {
                console.error(`Error loading jugadores for equipo ${equipoId}:`, error);
                this.error = 'Error al cargar jugadores del equipo';
                return [];
            } finally {
                this.loading = false;
            }
        },
        
        // Update the clearEquipos method to also clear jugadores
        clearEquipos() {
            this.equiposByTorneo = {};
            this.currentEquipo = null;
            this.jugadoresByEquipo = {}; // Clear jugadores as well
            this.error = null;
        }
    },
});
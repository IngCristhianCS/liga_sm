import { defineStore } from 'pinia';
import axios from 'axios';

export const useJornadasStore = defineStore('jornadas', {
    state: () => ({
        jornadas: [],
        jornadasByTorneo: {},
        currentJornada: null,
        loading: false,
        error: null
    }),
    
    getters: {
        getJornadaById: (state) => (id) => {
            return state.jornadas.find(jornada => jornada.id === id);
        }
    },
    
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
        },
        
        async fetchJornadaById(id) {
            try {
                this.loading = true;
                this.error = null;
                
                // Check if we already have this jornada
                const existingJornada = this.getJornadaById(id);
                if (existingJornada) {
                    this.currentJornada = existingJornada;
                    return existingJornada;
                }
                
                // Otherwise fetch from API
                const response = await axios.get(`/api/jornadas/${id}`);
                this.currentJornada = response.data.data;
                
                // Update the jornada in the list if it exists
                const index = this.jornadas.findIndex(j => j.id === id);
                if (index !== -1) {
                    this.jornadas[index] = this.currentJornada;
                } else {
                    this.jornadas.push(this.currentJornada);
                }
                
                // Update in the torneo-specific list if it exists
                if (this.currentJornada.torneo_id && this.jornadasByTorneo[this.currentJornada.torneo_id]) {
                    const torneoIndex = this.jornadasByTorneo[this.currentJornada.torneo_id].findIndex(j => j.id === id);
                    if (torneoIndex !== -1) {
                        this.jornadasByTorneo[this.currentJornada.torneo_id][torneoIndex] = this.currentJornada;
                    } else {
                        this.jornadasByTorneo[this.currentJornada.torneo_id].push(this.currentJornada);
                    }
                }
                
                return this.currentJornada;
            } catch (error) {
                this.error = `Error al cargar la jornada ${id}`;
                return null;
            } finally {
                this.loading = false;
            }
        },
        
        async createJornada(jornadaData) {
            try {
                this.loading = true;
                this.error = null;
                const response = await axios.post('/api/jornadas', jornadaData);
                const newJornada = response.data.data;
                
                // Add to the main list
                this.jornadas.push(newJornada);
                
                // Add to the torneo-specific list if it exists
                if (newJornada.torneo_id && this.jornadasByTorneo[newJornada.torneo_id]) {
                    this.jornadasByTorneo[newJornada.torneo_id].push(newJornada);
                }
                
                return newJornada;
            } catch (error) {
                this.error = 'Error al crear la jornada';
                throw error;
            } finally {
                this.loading = false;
            }
        },
        
        async updateJornada(id, jornadaData) {
            if (!id) return null;
            
            try {
                this.loading = true;
                this.error = null;
                const response = await axios.put(`/api/jornadas/${id}`, jornadaData);
                const updatedJornada = response.data.data;
                
                // Update in the main list
                const index = this.jornadas.findIndex(j => j.id === id);
                if (index !== -1) {
                    this.jornadas[index] = updatedJornada;
                }
                
                // Update in the torneo-specific list if it exists
                if (updatedJornada.torneo_id && this.jornadasByTorneo[updatedJornada.torneo_id]) {
                    const torneoIndex = this.jornadasByTorneo[updatedJornada.torneo_id].findIndex(j => j.id === id);
                    if (torneoIndex !== -1) {
                        this.jornadasByTorneo[updatedJornada.torneo_id][torneoIndex] = updatedJornada;
                    }
                }
                
                // Update current jornada if it's the one being edited
                if (this.currentJornada && this.currentJornada.id === id) {
                    this.currentJornada = updatedJornada;
                }
                
                return updatedJornada;
            } catch (error) {
                this.error = `Error al actualizar la jornada ${id}`;
                throw error;
            } finally {
                this.loading = false;
            }
        },
        
        async deleteJornada(id) {
            if (!id) return false;
            
            try {
                this.loading = true;
                this.error = null;
                await axios.delete(`/api/jornadas/${id}`);
                
                // Get the torneo_id before removing from lists
                const jornada = this.getJornadaById(id);
                const torneoId = jornada?.torneo_id;
                
                // Remove from the main list
                this.jornadas = this.jornadas.filter(j => j.id !== id);
                
                // Remove from the torneo-specific list if it exists
                if (torneoId && this.jornadasByTorneo[torneoId]) {
                    this.jornadasByTorneo[torneoId] = this.jornadasByTorneo[torneoId].filter(j => j.id !== id);
                }
                
                // Clear current jornada if it's the one being deleted
                if (this.currentJornada && this.currentJornada.id === id) {
                    this.currentJornada = null;
                }
                
                return true;
            } catch (error) {
                this.error = `Error al eliminar la jornada ${id}`;
                return false;
            } finally {
                this.loading = false;
            }
        },
        
        clearJornadas() {
            this.jornadas = [];
            this.jornadasByTorneo = {};
            this.currentJornada = null;
            this.error = null;
        }
    },
});
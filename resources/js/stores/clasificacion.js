// stores/clasificacion.js
import { defineStore } from 'pinia'
import axios from 'axios'

export const useClasificacionStore = defineStore('clasificacion', {
  state: () => ({
    clasificacion: [],
    loading: false,
    error: null
  }),
  actions: {
    async fetchClasificacion() {
      try {
        this.loading = true
        this.error = null
        const response = await axios.get('/api/clasificacion', {
          params: { nocache: new Date().getTime() }
        })
        
        this.clasificacion = response.data
      } catch (error) {
        this.error = 'Error al cargar datos'
      } finally {
        this.loading = false
      }
    }
  }
})
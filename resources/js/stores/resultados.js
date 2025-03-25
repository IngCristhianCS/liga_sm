import { defineStore } from 'pinia'

export const useResultadosStore = defineStore('resultados', {
  state: () => ({
    loading: false,
    error: null,
    jornadas: []
  }),
  actions: {
    async fetchJornadas(torneoId) {
      try {
        this.loading = true
        const response = await fetch(`/api/torneos/${torneoId}/jornadas`, {
          params: { nocache: new Date().getTime() }
        })
        this.jornadas = await response.json()
      } catch (error) {
        this.error = error.message
      } finally {
        this.loading = false
      }
    }
  }
})
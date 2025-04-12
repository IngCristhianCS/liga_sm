import { defineStore } from 'pinia';
import axios from 'axios';

export const useCategoriasStore = defineStore('categorias', {
  state: () => ({
    categorias: [],
    loading: false,
    error: null
  }),
  actions: {
    async loadCategorias() {
      try {
        this.loading = true;
        this.error = null;
        const response = await axios.get('/api/categorias');
        this.categorias = response.data.data;
      } catch (error) {
        this.error = 'Error al cargar categorías';
        console.error('Error loading categorias:', error);
      } finally {
        this.loading = false;
      }
    }
  }
});
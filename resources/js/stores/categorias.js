import { defineStore } from 'pinia';
import axios from 'axios';

export const useCategoriasStore = defineStore('categorias', {
  state: () => ({
    categorias: [],
    categoriasCatalog: [],
    currentCategoria: null,
    loading: false,
    error: null
  }),
  
  getters: {
    getCategoriaById: (state) => (id) => {
      return state.categorias.find(categoria => categoria.id === id);
    }
  },
  
  actions: {
    async loadCategorias() {
      if (this.categorias.length > 0) return this.categorias;
      
      try {
        this.loading = true;
        this.error = null;
        const response = await axios.get('/api/categorias');
        this.categorias = response.data.data;
        return this.categorias;
      } catch (error) {
        this.error = 'Error al cargar categorías';
        console.error('Error loading categorias:', error);
        return [];
      } finally {
        this.loading = false;
      }
    },
    
    async fetchCategoriasCatalog() {
      if (this.categoriasCatalog.length > 0) return this.categoriasCatalog;
      
      try {
        this.loading = true;
        this.error = null;
        const response = await axios.get('/api/categorias/catalog');
        this.categoriasCatalog = response.data.data;
        return this.categoriasCatalog;
      } catch (error) {
        // If catalog endpoint fails, try to use the full endpoint
        if (this.categorias.length === 0) {
          await this.loadCategorias();
        }
        this.categoriasCatalog = this.categorias.map(c => ({
          id: c.id,
          nombre: c.nombre
        }));
        
        this.error = 'Error al cargar catálogo de categorías';
        console.error('Error loading categorias catalog:', error);
        return this.categoriasCatalog;
      } finally {
        this.loading = false;
      }
    },
    
    async createCategoria(categoriaData) {
      try {
        this.loading = true;
        this.error = null;
        const response = await axios.post('/api/categorias', categoriaData);
        const newCategoria = response.data.data;
        
        // Add the new categoria to the lists
        this.categorias.push(newCategoria);
        
        // Update catalog if it's loaded
        if (this.categoriasCatalog.length > 0) {
          this.categoriasCatalog.push({
            id: newCategoria.id,
            nombre: newCategoria.nombre
          });
        }
        
        return newCategoria;
      } catch (error) {
        this.error = 'Error al crear la categoría';
        console.error('Error creating categoria:', error);
        throw error;
      } finally {
        this.loading = false;
      }
    },
    
    async updateCategoria(id, categoriaData) {
      if (!id) return null;
      
      try {
        this.loading = true;
        this.error = null;
        const response = await axios.put(`/api/categorias/${id}`, categoriaData);
        const updatedCategoria = response.data.data;
        
        // Update the categoria in the list
        const index = this.categorias.findIndex(c => c.id === id);
        if (index !== -1) {
          this.categorias[index] = updatedCategoria;
        }
        
        // Update in catalog if it's loaded
        if (this.categoriasCatalog.length > 0) {
          const catalogIndex = this.categoriasCatalog.findIndex(c => c.id === id);
          if (catalogIndex !== -1) {
            this.categoriasCatalog[catalogIndex] = {
              id: updatedCategoria.id,
              nombre: updatedCategoria.nombre
            };
          }
        }
        
        return updatedCategoria;
      } catch (error) {
        this.error = `Error al actualizar la categoría ${id}`;
        console.error(`Error updating categoria ${id}:`, error);
        throw error;
      } finally {
        this.loading = false;
      }
    },
    
    async deleteCategoria(id) {
      if (!id) return false;
      
      try {
        this.loading = true;
        this.error = null;
        await axios.delete(`/api/categorias/${id}`);
        
        // Remove the categoria from the lists
        this.categorias = this.categorias.filter(c => c.id !== id);
        
        // Remove from catalog if it's loaded
        if (this.categoriasCatalog.length > 0) {
          this.categoriasCatalog = this.categoriasCatalog.filter(c => c.id !== id);
        }
        
        return true;
      } catch (error) {
        this.error = `Error al eliminar la categoría ${id}`;
        console.error(`Error deleting categoria ${id}:`, error);
        return false;
      } finally {
        this.loading = false;
      }
    },
    
    clearCategorias() {
      this.categorias = [];
      this.categoriasCatalog = [];
      this.currentCategoria = null;
      this.error = null;
    }
  }
});
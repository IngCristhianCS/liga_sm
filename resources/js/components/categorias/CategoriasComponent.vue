<template>
    <section class="content contact">
      <div class="container-fluid">
        <div class="row clearfix">
          <div class="col-lg-12">
            <div class="tab-content">
              <div class="tab-pane active" id="List">
                <div class="card">
                  <div class="body">
                    <ul class="nav nav-tabs padding-0">
                      <li class="nav-item" v-if="authStore.isAdmin">
                        <a class="btn btn-primary btn-round" href="#largeModal" data-toggle="modal"
                          data-target="#largeModal">
                          {{ mode === 'create' ? 'Nueva' : 'Editar' }} Categoría
                        </a>
                      </li>
                    </ul>
                    <div class="table-responsive">
                      <table id="categorias"
                        class="table table-bordered table-striped table-hover js-basic-example dataTable">
                        <thead>
                          <tr>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Edad Máxima</th>
                            <th>Acciones</th>
                          </tr>
                        </thead>
                        <tbody>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  
    <CategoriaForm 
      ref="categoriaFormRef"
      :mode="mode" 
      :current-categoria="currentCategoria" 
      @submit="handleSubmit" 
      @cancel="resetForm"
    />
  </template>
  
  <script setup>
  import { ref, reactive, onMounted } from 'vue';
  import axios from 'axios';
  import { initializeDataTable, attachTableEvents } from '@/utils/datatables-utils';
  import Notification from '@/utils/Notification';
  import { useAuthStore } from '../../stores/auth';
  import CategoriaForm from './CategoriaForm.vue';
  
  const authStore = useAuthStore();
  const categoriaFormRef = ref(null);
  const mode = ref('create');
  const categorias = ref([]);
  
  /** @type {import('vue').Ref<Categoria>} */
  const currentCategoria = reactive({
    nombre: '',
    tipo: '',
    edad_maxima: null,
  });
  
  /**
   * Columnas para DataTable
   * @type {import('datatables.net').ColumnSettings[]}
   */
  const columns = [
    { data: 'nombre', title: 'Nombre' },
    { data: 'tipo', title: 'Tipo' },
    { data: 'edad_maxima', title: 'Edad Máxima' },
    {
      data: null,
      title: 'Acciones',
      render: (data, type, row) => {
        if (!authStore.isAdmin) return '';
        return `
          <button class="btn btn-icon btn-neutral btn-icon-mini btnEditar"
                  data-id="${row.id}"
                  data-toggle="modal"
                  data-target="#largeModal">
            <i class="zmdi zmdi-edit"></i>
          </button>
          <button class="btn btn-icon btn-neutral btn-icon-mini btnEliminar"
                  data-id="${row.id}">
            <i class="zmdi zmdi-delete"></i>
          </button>`;
      }
    }
  ];
  
  /**
   * Carga las categorías desde la API
   * @returns {Promise<void>}
   */
  const loadCategorias = async () => {
    try {
      const { data } = await axios.get('/api/categorias');
      categorias.value = data.data;
      
      initializeDataTable('categorias', categorias.value, columns);
      attachTableEvents('categorias', editCategoria, deleteCategoria);
  
    } catch (error) {
      Notification.error('Error al cargar categorías');
    }
  };
  
  /**
   * Edita una categoría
   * @param {number} id - ID de la categoría
   */
  const editCategoria = (id) => {
    const categoria = categorias.value.find(c => c.id === id);
    if (categoria) {
      mode.value = 'edit';
      Object.assign(currentCategoria, {
        id: categoria.id,
        nombre: categoria.nombre,
        tipo: categoria.tipo,
        edad_maxima: categoria.edad_maxima
      });
    }
  };
  
  /**
   * Elimina una categoría
   * @param {number} id - ID de la categoría
   * @returns {Promise<void>}
   */
  const deleteCategoria = async (id) => {
    const result = await Notification.confirm(
      '¡No podrás revertir esta acción!',
      '¿Estás seguro?',
      'Sí, eliminar',
      'Cancelar'
    );
  
    if (result.isConfirmed) {
      try {
        await axios.delete(`/api/categorias/${id}`);
        await loadCategorias();
        Notification.success('Categoría eliminada correctamente');
      } catch (error) {
        Notification.error('Error al eliminar categoría');
      }
    }
  };
  
  /**
   * Reinicia el formulario
   */
  const resetForm = () => {
    mode.value = 'create';
    Object.assign(currentCategoria, {
      id: null,
      nombre: '',
      tipo: '',
      edad_maxima: null
    });
  };
  
  const handleSubmit = async (formData) => {
    try {
      const method = mode.value === 'create' ? 'post' : 'put';
      const url = method === 'post' ? '/api/categorias' : `/api/categorias/${currentCategoria.id}`;
      
      const submitData = {
        nombre: formData.nombre,
        tipo: formData.tipo,
        edad_maxima: formData.edad_maxima ? Number(formData.edad_maxima) : null
      };
  
      if (mode.value === 'edit') {
        submitData.id = currentCategoria.id;
      }
      
      await axios[method](url, submitData);
      await loadCategorias();
      Notification.success(
        `Categoría ${mode.value === 'create' ? 'creada' : 'actualizada'} correctamente`
      );
      $('#largeModal').modal('hide');
      resetForm();
      
    } catch (error) {
      if (error.response?.status === 422 && categoriaFormRef.value) {
        categoriaFormRef.value.handleApiErrors(error.response.data.errors);
      } else {
        Notification.error('Error al guardar categoría');
      }
    }
  };

  onMounted(() => {
    loadCategorias();
  });
  </script>
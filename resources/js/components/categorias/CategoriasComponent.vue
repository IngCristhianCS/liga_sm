<template>
  <section class="content contact">
    <div class="container-fluid">
      <div class="block-header">
        <div class="row clearfix">
          <div class="col-lg-5 col-md-5 col-sm-12">
            <h2>Administración de Categorias</h2>
          </div>
          <div class="col-lg-7 col-md-7 col-sm-12">
            <ul class="breadcrumb float-md-right padding-0">
              <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i></a></li>
              <li class="breadcrumb-item"><a href="javascript:void(0);">Administración</a></li>
              <li class="breadcrumb-item active">Categorias</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="row clearfix">
        <div class="col-lg-12">
          <div class="card">
            <div class="header">
              <ul class="header-dropdown">
                <li class="nav-item" v-if="authStore.isAdmin"><a href="#largeModal" data-toggle="modal"
                    data-target="#largeModal" @click="openCreateModal"><i class="zmdi zmdi-plus-circle zmdi-hc-3x"></i></a></li>
              </ul>
            </div>
            <div class="body">
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
  </section>

  <CategoriaForm ref="categoriaFormRef" :mode="mode" :current-categoria="currentCategoria" @submit="handleSubmit"
    @cancel="resetForm" />
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { initializeDataTable, attachTableEvents } from '@/utils/datatables-utils';
import Notification from '@/utils/notification';
import { useAuthStore } from '@/stores/auth';
import { useCategoriasStore } from '@/stores/categorias';
import CategoriaForm from './CategoriaForm.vue';

const authStore = useAuthStore();
const categoriasStore = useCategoriasStore();
const categoriaFormRef = ref(null);
const mode = ref('create');

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
 * Carga las categorías desde el store
 * @returns {Promise<void>}
 */
const loadCategorias = async () => {
  try {
    await categoriasStore.loadCategorias();

    initializeDataTable('categorias', categoriasStore.categorias, columns);
    attachTableEvents('categorias', editCategoria, deleteCategoria);

  } catch (error) {
    Notification.error('Error al cargar categorías');
  }
};

/**
 * Abre el modal para crear una nueva categoría
 */
const openCreateModal = () => {
  mode.value = 'create';
  resetForm();
};

/**
 * Edita una categoría
 * @param {number} id - ID de la categoría
 */
const editCategoria = (id) => {
  const categoria = categoriasStore.getCategoriaById(id);
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
      await categoriasStore.deleteCategoria(id);
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
    const submitData = {
      nombre: formData.nombre,
      tipo: formData.tipo,
      edad_maxima: formData.edad_maxima ? Number(formData.edad_maxima) : null
    };

    if (mode.value === 'create') {
      await categoriasStore.createCategoria(submitData);
    } else {
      await categoriasStore.updateCategoria(currentCategoria.id, submitData);
    }
    
    await loadCategorias();
    Notification.success(
      `Categoría ${mode.value === 'create' ? 'creada' : 'actualizada'} correctamente`
    );
    // Cerrar el modal explícitamente
    document.querySelector('[data-dismiss="modal"]').click();
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
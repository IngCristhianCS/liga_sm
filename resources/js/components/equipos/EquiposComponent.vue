<template>
  <section class="content contact">
    <div class="container-fluid">
      <div class="block-header">
        <div class="row clearfix">
          <div class="col-lg-5 col-md-5 col-sm-12">
            <h2>Administración de Equipos</h2>
          </div>
          <div class="col-lg-7 col-md-7 col-sm-12">
            <ul class="breadcrumb float-md-right padding-0">
              <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> Inicio</a></li>
              <li class="breadcrumb-item active">Administración</li>
              <li class="breadcrumb-item">Equipos</li>
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
                <table id="equipos" class="table table-bordered table-striped table-hover js-basic-example dataTable">
                  <thead>
                    <tr>
                      <th>Imagen</th>
                      <th>Nombre</th>
                      <th>Categoría</th>
                      <th>Entrenador</th>
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

  <EquipoForm 
    :mode="formMode" 
    :current-equipo="currentEquipo" 
    :entrenadores="entrenadores"
    @submit="handleFormSubmit" 
    @cancel="resetForm"
    ref="equipoFormRef"
  />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Notification from '@/utils/notification';
import { initializeDataTable, attachTableEvents } from '@/utils/datatables-utils';
import EquipoForm from './EquipoForm.vue';
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();
const equipos = ref([]);
const entrenadores = ref([]);
const formMode = ref('create');
const currentEquipo = ref({});
const equipoFormRef = ref(null);

/**
 * Columnas para DataTable
 * @type {import('datatables.net').ColumnSettings[]}
 */
const columns = [
  { 
    data: 'imagen', 
    title: 'Imagen',
    render: (data) => {
      if (data) {
        // Ensure the path starts with /storage/
        const imagePath = data.startsWith('/storage/') ? data : `/storage/${data}`;
        return `<img src="${imagePath}" alt="Imagen del equipo" class="img-thumbnail" style="max-height: 50px;">`;
      }
      return 'Sin imagen';
    }
  },
  { data: 'nombre', title: 'Nombre' },
  { 
    data: 'categoria', 
    title: 'Categoría',
    render: (data) => data ? data.nombre : 'N/A'
  },
  { 
    data: 'entrenador', 
    title: 'Entrenador',
    render: (data) => data ? data.name : 'Sin entrenador'
  },
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

const loadEquipos = async () => {
  try {
    const { data } = await axios.get('/api/equipos');
    equipos.value = data.data;
    
    // Inicializar DataTable
    initializeDataTable('equipos', equipos.value, columns);
    attachTableEvents('equipos', openEditModal, deleteEquipo);
    
  } catch (error) {
    console.error('Error cargando equipos:', error);
    Notification.error('No se pudieron cargar los equipos');
  }
};

const loadEntrenadores = async () => {
  try {
    const { data } = await axios.get('/api/users/entrenadores');
    entrenadores.value = data.data;
  } catch (error) {
    console.error('Error cargando entrenadores:', error);
    Notification.error('No se pudieron cargar los entrenadores');
  }
};

const openCreateModal = () => {
  formMode.value = 'create';
  currentEquipo.value = {};
  
  // Configure modal to prevent closing when clicking outside
  setTimeout(() => {
    const modal = $('#largeModal');
    modal.modal({
      backdrop: 'static',
      keyboard: false
    });
  }, 100);
};

const openEditModal = (id) => {
  const equipo = equipos.value.find(e => e.id === id);
  if (equipo) {
    formMode.value = 'edit';
    currentEquipo.value = { ...equipo };
    
    // Configure modal to prevent closing when clicking outside
    setTimeout(() => {
      const modal = $('#largeModal');
      modal.modal({
        backdrop: 'static',
        keyboard: false
      });
    }, 100);
  }
};

const handleFormSubmit = async (formData) => {
  try {
    // Check if we have a base64 image from cropper
    if (formData.get('imagen_base64')) {
      const base64Data = formData.get('imagen_base64');
      
      // Create a new FormData object with the base64 data properly formatted
      const processedFormData = new FormData();
      
      // Copy all other form fields
      for (const [key, value] of formData.entries()) {
        if (key !== 'imagen_base64') {
          processedFormData.append(key, value);
        }
      }
      
      // Add the base64 image as a string field instead of a file
      processedFormData.append('imagen', base64Data);
      
      // Replace the original formData with our processed one
      formData = processedFormData;
    }
    
    if (formMode.value === 'create') {
      await axios.post('/api/equipos', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      });
      Notification.success('Equipo creado correctamente');
    } else {
      await axios.post(`/api/equipos/${currentEquipo.value.id}?_method=PUT`, formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      });
      Notification.success('Equipo actualizado correctamente');
    }
    
    // Cerrar el modal
    document.querySelector('[data-dismiss="modal"]').click();
    
    // Recargar la lista de equipos
    await loadEquipos();
    
    // Resetear el formulario
    resetForm();
  } catch (error) {
    console.error('Error al guardar equipo:', error);
    
    if (error.response && error.response.data && error.response.data.errors) {
      // Manejar errores de validación
      equipoFormRef.value.handleApiErrors(error.response.data.errors);
    } else {
      // Mostrar error genérico
      Notification.error('No se pudo guardar el equipo');
    }
  }
};

const deleteEquipo = async (id) => {
  try {
    const result = await Notification.confirm(
      '¡No podrás revertir esta acción!',
      '¿Estás seguro?',
      'Sí, eliminar',
      'Cancelar'
    );
    
    if (result.isConfirmed) {
      await axios.delete(`/api/equipos/${id}`);
      await loadEquipos();
      Notification.success('El equipo ha sido eliminado');
    }
  } catch (error) {
    console.error('Error eliminando equipo:', error);
    Notification.error('No se pudo eliminar el equipo');
  }
};

const resetForm = () => {
  currentEquipo.value = {};
};

onMounted(async () => {
  await Promise.all([
    loadEquipos(),
    loadEntrenadores()
  ]);
});
</script>
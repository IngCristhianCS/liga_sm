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
      <TorneosMenu @torneoSeleccionado="handleTorneoSeleccionado" />
      <div class="row clearfix">
        <div class="col-lg-12">
          <div class="card">
            <div class="header">
              <ul class="header-dropdown">
                <li class="nav-item" v-if="authStore.isAdmin"><a href="#largeModal" data-toggle="modal"
                    data-target="#largeModal" @click="openCreateModal"><i
                      class="zmdi zmdi-plus-circle zmdi-hc-3x"></i></a></li>
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

  <EquipoForm :mode="formMode" :current-equipo="currentEquipo" :entrenadores="entrenadores" @submit="handleFormSubmit"
    @cancel="resetForm" @torneoSelected="handleFormTorneoSelected" ref="equipoFormRef" />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Notification from '@/utils/notification';
import { initializeDataTable, attachTableEvents } from '@/utils/datatables-utils';
import EquipoForm from './EquipoForm.vue';
import { useAuthStore } from '@/stores/auth';
import { useTorneoStore } from '@/stores/torneos';
import { useEquiposStore } from '@/stores/equipos';
import TorneosMenu from '../global/TorneosMenu.vue';

const authStore = useAuthStore();
const torneoStore = useTorneoStore();
const equiposStore = useEquiposStore();
const entrenadores = ref([]);
const formMode = ref('create');
const currentEquipo = ref({});
const equipoFormRef = ref(null);
const selectedTorneoId = ref(null);

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

const loadEquipos = async (torneoId = null) => {
  try {
    if (torneoId) {
      await equiposStore.loadEquiposByTorneo(torneoId);

      // If DataTable already exists, clear and reload
      if ($.fn.DataTable.isDataTable('#equipos')) {
        const table = $('#equipos').DataTable();
        table.clear().rows.add(equiposStore.equiposByTorneo[torneoId]).draw();
      } else {
        // Initialize DataTable
        initializeDataTable('equipos', equiposStore.equiposByTorneo[torneoId], columns);
        attachTableEvents('equipos', openEditModal, deleteEquipo);
      }
    } else {
      // Handle case when no torneo is selected
      if ($.fn.DataTable.isDataTable('#equipos')) {
        const table = $('#equipos').DataTable();
        table.clear().draw();
      } else {
        initializeDataTable('equipos', [], columns);
        attachTableEvents('equipos', openEditModal, deleteEquipo);
      }
    }
  } catch (error) {
    Notification.error('No se pudieron cargar los equipos');
  }
};

const handleTorneoSeleccionado = (torneoId) => {
  selectedTorneoId.value = torneoId;
  loadEquipos(torneoId);
};

const handleFormTorneoSelected = (torneoId) => {
  if (currentEquipo.value) {
    currentEquipo.value.torneo_id = torneoId;
  }
};

const loadEntrenadores = async () => {
  try {
    const { data } = await axios.get('/api/entrenadores');
    entrenadores.value = data.data;
  } catch (error) {
    Notification.error('No se pudieron cargar los entrenadores');
  }
};

const openCreateModal = () => {
  formMode.value = 'create';
  currentEquipo.value = {
    torneo_id: selectedTorneoId.value
  };
};

const openEditModal = async (id) => {
  try {
    const equipo = await equiposStore.fetchEquipoById(id, selectedTorneoId.value);
    if (equipo) {
      formMode.value = 'edit';
      currentEquipo.value = { ...equipo };
    }
  } catch (error) {
    Notification.error('No se pudo cargar el equipo');
  }
};

const handleFormSubmit = async (formData) => {
  try {
    // Make sure formData is a FormData object
    let processedFormData = formData;

    // If it's not already a FormData object, create one
    if (!(formData instanceof FormData)) {
      processedFormData = new FormData();
      for (const key in formData) {
        processedFormData.append(key, formData[key]);
      }
    }

    // Handle base64 image if present
    if (processedFormData.get('imagen_base64')) {
      const base64Data = processedFormData.get('imagen_base64');

      // Create a new FormData to avoid mutation issues
      const newFormData = new FormData();
      for (const [key, value] of processedFormData.entries()) {
        if (key !== 'imagen_base64') {
          newFormData.append(key, value);
        }
      }

      newFormData.append('imagen', base64Data);
      processedFormData = newFormData;
    }

    // Add torneo_id if needed
    if (selectedTorneoId.value && !processedFormData.get('torneo_id')) {
      processedFormData.append('torneo_id', selectedTorneoId.value);
    }

    if (formMode.value === 'create') {
      await equiposStore.createEquipo(processedFormData);
      Notification.success('Equipo creado correctamente');
    } else {
      await equiposStore.updateEquipo(currentEquipo.value.id, processedFormData);
      Notification.success('Equipo actualizado correctamente');
    }

    // Cerrar el modal
    document.querySelector('[data-dismiss="modal"]').click();
    await loadEquipos(selectedTorneoId.value);

    // Resetear el formulario
    resetForm();
  } catch (error) {
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
      await equiposStore.deleteEquipo(id);
      await loadEquipos(selectedTorneoId.value);
      Notification.success('El equipo ha sido eliminado');
    }
  } catch (error) {
    Notification.error('No se pudo eliminar el equipo');
  }
};

const resetForm = () => {
  currentEquipo.value = {};
};

onMounted(async () => {
  if (torneoStore.torneosCatalog.length === 0) {
    await torneoStore.fetchTorneosCatalog();
  }

  await Promise.all([
    loadEntrenadores()
  ]);
});
</script>
<template>
  <section class="content">
    <div class="container-fluid">
      <div class="block-header">
        <div class="row">
          <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Administración de Partidos</h2>
          </div>
          <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb float-md-right padding-0">
              <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i></a></li>
              <li class="breadcrumb-item"><a href="javascript:void(0);">Administración</a></li>
              <li class="breadcrumb-item active">Partidos</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Componente de selección de torneos -->
      <div class="row clearfix">
        <div class="col-lg-12">
          <div class="card">
            <div class="body">
              <TorneosMenu @torneoSeleccionado="loadPartidosByTorneo" />
            </div>
          </div>
        </div>
      </div>

      <div class="row clearfix">
        <div class="col-lg-12">
          <div class="card">
            <div class="header">
              <ul class="header-dropdown">
                <li class="nav-item"><a href="#largeModal" data-toggle="modal"
                    data-target="#largeModal" @click="openCreateModal"><i
                      class="zmdi zmdi-plus-circle zmdi-hc-3x"></i></a>
                </li>
              </ul>
            </div>
            <div class="body">
              <div class="table-responsive">
                <table id="partidos" class="table table-bordered table-striped table-hover js-basic-example dataTable">
                  <thead>
                    <tr>
                      <th>Fecha</th>
                      <th>Jornada</th>
                      <th>Local</th>
                      <th>Visitante</th>
                      <th>Resultado</th>
                      <th>Ubicación</th>
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
  <!-- Modal para crear/editar partido -->
  <PartidoForm :mode="formMode" :current-partido="currentPartido" @submit="handleFormSubmit" @cancel="resetForm"
    ref="partidoForm" />
</template>

<script setup>
import { ref, onMounted } from 'vue';
import Notification from '@/utils/notification';
import { initializeDataTable, attachTableEvents } from '@/utils/datatables-utils';
import PartidoForm from './PartidoForm.vue';
import TorneosMenu from '../global/TorneosMenu.vue';
import { useJornadasStore } from '@/stores/jornadas';
import { usePartidoStore } from '@/stores/partidos';

const currentTorneoId = ref(null);
const formMode = ref('create');
const currentPartido = ref({});
const partidoForm = ref(null);
const jornadaStore = useJornadasStore();
const partidoStore = usePartidoStore();

/**
 * Columnas para DataTable
 * @type {import('datatables.net').ColumnSettings[]}
 */
const columns = [
  { data: 'fecha', title: 'Fecha', render: (data) => formatDate(data) },
  {
    data: 'jornada_id',
    title: 'Jornada',
    render: (data, type, row) => {
      // Access jornadas the same way as in PartidoForm
      const jornadas = jornadaStore.jornadasByTorneo[row.torneo_id] || [];
      const jornada = jornadas.find(j => j.id === data);
      return jornada ? `Jornada ${jornada.numero}` : 'N/A';
    }
  },
  {
    data: 'equipo_local',
    title: 'Local',
    render: (data) => data ? data.nombre : 'N/A'
  },
  {
    data: 'equipo_visitante',
    title: 'Visitante',
    render: (data) => data ? data.nombre : 'N/A'
  },
  {
    data: null,
    title: 'Resultado',
    render: (data, type, row) => `${row.goles_equipo_local} - ${row.goles_equipo_visitante}`
  },
  {
    data: 'ubicacion',
    title: 'Ubicación',
    render: (data) => data ? data.nombre : 'N/A'
  },
  {
    data: null,
    title: 'Acciones',
    render: (data, type, row) => {
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

const loadPartidosByTorneo = async (torneoId) => {
  currentTorneoId.value = torneoId;

  try {
    // Load jornadas for this torneo first
    await jornadaStore.loadJornadasByTorneo(torneoId);
    
    // Use the partido store to fetch partidos
    await partidoStore.fetchPartidosByTorneo(torneoId);

    // Inicializar DataTable
    initializeDataTable('partidos', partidoStore.partidos, columns);
    attachTableEvents('partidos', openEditModal, deletePartido);

  } catch (error) {
    Notification.error('No se pudieron cargar los partidos');
  }
};

const openCreateModal = () => {
  formMode.value = 'create';
  currentPartido.value = {
    torneo_id: currentTorneoId.value
  };
};

const openEditModal = async (id) => {
  try {
    // Use the store to fetch the partido details
    const partido = await partidoStore.fetchPartidoById(id);
    if (partido) {
      formMode.value = 'edit';
      currentPartido.value = { ...partido };
    }
  } catch (error) {
    Notification.error('No se pudieron cargar los detalles del partido');
  }
};

const handleFormSubmit = async (formData) => {
  try {
    if (formMode.value === 'create') {
      await partidoStore.createPartido(formData);
      Notification.success('Partido creado correctamente');
    } else {
      await partidoStore.updatePartido(currentPartido.value.id, formData);
      Notification.success('Partido actualizado correctamente');
    }

    // Cerrar el modal
    document.querySelector('[data-dismiss="modal"]').click();

    // Recargar la lista de partidos
    await loadPartidosByTorneo(currentTorneoId.value);

    // Resetear el formulario
    resetForm();
  } catch (error) {
    console.error('Error al guardar partido:', error);

    if (error.response && error.response.data && error.response.data.errors) {
      // Manejar errores de validación
      partidoForm.value.handleApiErrors(error.response.data.errors);
    } else {
      // Mostrar error genérico
      Notification.error('No se pudo guardar el partido');
    }
  }
};

const deletePartido = async (id) => {
  try {
    const result = await Notification.confirm(
      '¡No podrás revertir esta acción!',
      '¿Estás seguro?',
      'Sí, eliminar',
      'Cancelar'
    );

    if (result.isConfirmed) {
      await partidoStore.deletePartido(id);
      await loadPartidosByTorneo(currentTorneoId.value);
      Notification.success('El partido ha sido eliminado');
    }
  } catch (error) {
    console.error('Error eliminando partido:', error);
    Notification.error('No se pudo eliminar el partido');
  }
};

const resetForm = () => {
  currentPartido.value = {
    torneo_id: currentTorneoId.value
  };
};

const formatDate = (dateString) => {
  if (!dateString) return 'N/A';

  const date = new Date(dateString);
  return new Intl.DateTimeFormat('es-ES', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(date);
};

// Clear partidos when component is mounted
onMounted(() => {
  partidoStore.clearPartidos();
});
</script>
<template>
  <div class="container-fluid">
    <div class="block-header">
      <div class="row">
        <div class="col-lg-5 col-md-8 col-sm-12">
          <h2>Partidos</h2>
        </div>
        <div class="col-lg-7 col-md-4 col-sm-12 text-right">
          <button type="button" class="btn btn-primary btn-round btn-simple float-right m-l-10" 
                  data-toggle="modal" data-target="#largeModal" @click="openCreateModal">
            <i class="zmdi zmdi-plus"></i> Nuevo Partido
          </button>
        </div>
      </div>
    </div>

    <!-- Componente de selección de torneos -->
    <div class="row clearfix mb-4">
      <div class="col-lg-12">
        <div class="card">
          <div class="header">
            <h2><strong>Seleccionar</strong> Torneo</h2>
          </div>
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
            <h2><strong>Lista de</strong> Partidos</h2>
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

    <!-- Modal para crear/editar partido -->
    <PartidoForm 
      :mode="formMode" 
      :current-partido="currentPartido" 
      @submit="handleFormSubmit" 
      @cancel="resetForm"
      ref="partidoForm"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';
import Notification from '@/utils/notification';
import { initializeDataTable, attachTableEvents } from '@/utils/datatables-utils';
import PartidoForm from './PartidoForm.vue';
import TorneosMenu from '../global/TorneosMenu.vue';
import { useJornadasStore } from '@/stores/jornadas';

const partidos = ref([]);
const currentTorneoId = ref(null);
const formMode = ref('create');
const currentPartido = ref({});
const partidoForm = ref(null);
const jornadaStore = useJornadasStore();

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
    // Load jornadas for this torneo first, just like in PartidoForm
    await jornadaStore.loadJornadasByTorneo(torneoId);
    
    const { data } = await axios.get(`/api/partidos?torneo_id=${torneoId}`);
    partidos.value = data.data;
    
    // Inicializar DataTable
    initializeDataTable('partidos', partidos.value, columns);
    attachTableEvents('partidos', openEditModal, deletePartido);
    
  } catch (error) {
    console.error('Error cargando partidos:', error);
    Notification.error('No se pudieron cargar los partidos');
  }
};

const openCreateModal = () => {
  formMode.value = 'create';
  currentPartido.value = {
    torneo_id: currentTorneoId.value
  };
};

const openEditModal = (id) => {
  const partido = partidos.value.find(p => p.id === id);
  if (partido) {
    formMode.value = 'edit';
    currentPartido.value = { ...partido };
  }
};

const handleFormSubmit = async (formData) => {
  try {
    if (formMode.value === 'create') {
      await axios.post('/api/partidos', formData);
      Notification.success('Partido creado correctamente');
    } else {
      await axios.put(`/api/partidos/${currentPartido.value.id}`, formData);
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
      await axios.delete(`/api/partidos/${id}`);
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
</script>
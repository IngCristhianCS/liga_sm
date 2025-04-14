<template>
  <section class="content">
    <div class="container-fluid">
      <div class="block-header">
        <div class="row clearfix">
          <div class="col-lg-5 col-md-5 col-sm-12">
            <h2>Administración de Jornadas</h2>
          </div>
          <div class="col-lg-7 col-md-7 col-sm-12">
            <ul class="breadcrumb float-md-right padding-0">
              <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i></a></li>
              <li class="breadcrumb-item active">Administración</li>
              <li class="breadcrumb-item">Jornadas</li>
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
              <TorneosMenu @torneoSeleccionado="handleTorneoSeleccionado" />              
              <div class="table-responsive">
                <table id="tablajornadas" class="table table-bordered table-striped table-hover js-basic-example dataTable">
                  <thead>
                    <tr>
                      <th>Número</th>
                      <th>Torneo</th>
                      <th>Fecha Inicio</th>
                      <th>Fecha Fin</th>
                      <th>Partidos</th>
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
  
  <JornadaForm 
      ref="jornadaFormRef"
      :mode="mode" 
      :jornada="currentJornada"
      :torneos="torneos"
      @submit="handleSubmit"
      @cancel="closeModal"
    />
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { initializeDataTable, attachTableEvents } from '@/utils/datatables-utils';
import Notification from '@/utils/notification';
import { useAuthStore } from '@/stores/auth';
import { useJornadasStore } from '@/stores/jornadas';
import { useTorneoStore } from '@/stores/torneos';
import JornadaForm from '@/components/jornadas/jornadaform.vue';
import TorneosMenu from '@/components/global/TorneosMenu.vue';

const authStore = useAuthStore();
const torneoStore = useTorneoStore();
const jornadasStore = useJornadasStore();
const jornadaFormRef = ref(null);
const mode = ref('create');
const selectedTorneoId = ref(null);
const torneos = ref([]);

const currentJornada = reactive({
  id: null,
  numero: '',
  torneo_id: '',
  fecha_inicio: '',
  fecha_fin: ''
});

const columns = [
  { data: 'numero' },
  { 
    data: 'torneo.nombre', 
    defaultContent: 'Sin torneo',
    render: (data, type, row) => row.torneo ? row.torneo.nombre : 'Sin torneo'
  },
  { 
    data: 'fecha_inicio',
    render: (data) => data ? new Date(data).toLocaleDateString() : 'No definida'
  },
  { 
    data: 'fecha_fin',
    render: (data) => data ? new Date(data).toLocaleDateString() : 'No definida'
  },
  {
    data: null,
    render: (data, type, row) => {
      return `<span class="badge badge-info">${row.partidos_count || 0} partidos</span>`;
    }
  },
  {
    data: null,
    render: (data, type, row) => {
      if (!authStore.isAdmin) return '';
      return `
        <button class="btn btn-icon btn-neutral btn-icon-mini btnEditar" data-id="${row.id}" title="Editar">
          <i class="zmdi zmdi-edit"></i>
        </button>
        <button class="btn btn-icon btn-neutral btn-icon-mini btnEliminar" data-id="${row.id}" title="Eliminar">
          <i class="zmdi zmdi-delete"></i>
        </button>`;
    }
  }
];

const loadJornadas = async () => {
  try {
    if (selectedTorneoId.value) {
      await jornadasStore.loadJornadasByTorneo(selectedTorneoId.value);
      
      if ($.fn.DataTable.isDataTable('#tablajornadas')) {
        const table = $('#tablajornadas').DataTable();
        table.clear().rows.add(jornadasStore.jornadasByTorneo[selectedTorneoId.value]).draw();
      } else {
        initializeDataTable('tablajornadas', jornadasStore.jornadasByTorneo[selectedTorneoId.value], columns);
        attachTableEvents('tablajornadas', handleEdit, handleDelete);
      }
    } else {
      await jornadasStore.loadJornadas();
      
      if ($.fn.DataTable.isDataTable('#tablajornadas')) {
        const table = $('#tablajornadas').DataTable();
        table.clear().rows.add(jornadasStore.jornadas).draw();
      } else {
        initializeDataTable('tablajornadas', jornadasStore.jornadas, columns);
        attachTableEvents('tablajornadas', handleEdit, handleDelete);
      }
    }
  } catch (error) {
    console.error('Error al cargar jornadas:', error);
    Notification.error('Error al cargar jornadas');
  }
};

const loadTorneos = async () => {
  try {
    if (torneoStore.torneos.length === 0) {
      await torneoStore.fetchTorneos();
    }
    torneos.value = torneoStore.torneos;
  } catch (error) {
    console.error('Error al cargar torneos:', error);
    Notification.error('Error al cargar torneos');
  }
};

const handleTorneoSeleccionado = (torneoId) => {
  selectedTorneoId.value = torneoId;
  loadJornadas();
};

const openCreateModal = () => {
  mode.value = 'create';
  Object.assign(currentJornada, {
    id: null,
    numero: '',
    torneo_id: selectedTorneoId.value || '',
    fecha_inicio: '',
    fecha_fin: ''
  });
};

const handleEdit = async (id) => {
  try {
    const jornada = await jornadasStore.fetchJornadaById(id);
    if (jornada) {
      mode.value = 'edit';
      Object.assign(currentJornada, {
        id: jornada.id,
        numero: jornada.numero,
        torneo_id: jornada.torneo_id,
        fecha_inicio: jornada.fecha_inicio,
        fecha_fin: jornada.fecha_fin
      });
    }
  } catch (error) {
    console.error('Error al cargar detalles de la jornada:', error);
    Notification.error('Error al cargar detalles de la jornada');
  }
};

const closeModal = () => {
  $('#largeModal').modal('hide');
};

const handleSubmit = async (formData) => {
  try {
    if (mode.value === 'create') {
      await jornadasStore.createJornada(formData);
      Notification.success('Jornada creada correctamente');
    } else {
      await jornadasStore.updateJornada(currentJornada.id, formData);
      Notification.success('Jornada actualizada correctamente');
    }
    
    closeModal();
    await loadJornadas();
  } catch (error) {
    console.error('Error al guardar jornada:', error);
    
    if (error.response?.status === 422 && jornadaFormRef.value) {
      jornadaFormRef.value.handleApiErrors(error.response.data.errors);
    } else {
      Notification.error('Error al guardar jornada');
    }
  }
};

const handleDelete = async (id) => {
  const result = await Notification.confirm(
    '¿Estás seguro de eliminar esta jornada?',
    'Esta acción no se puede deshacer y eliminará todos los partidos asociados.',
    'Sí, eliminar',
    'Cancelar'
  );

  if (result.isConfirmed) {
    try {
      await jornadasStore.deleteJornada(id);
      await loadJornadas();
      Notification.success('Jornada eliminada correctamente');
    } catch (error) {
      console.error('Error al eliminar jornada:', error);
      Notification.error('Error al eliminar jornada');
    }
  }
};

onMounted(async () => {
  await Promise.all([
    loadJornadas(),
    loadTorneos()
  ]);
});
</script>
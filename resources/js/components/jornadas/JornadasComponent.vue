<template>
  <section class="content">
    <div class="container-fluid">
      <div class="row clearfix">
        <div class="col-lg-12">
          <div class="card">
            <div class="header">
              <h2>Jornadas</h2>
              <ul class="header-dropdown">
                <li>
                  <button v-if="authStore.isAdmin" 
                          class="btn btn-primary btn-round" 
                          @click="openCreateModal">
                    Nueva Jornada
                  </button>
                </li>
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

    <JornadaForm 
      ref="jornadaFormRef"
      :mode="mode" 
      :jornada="currentJornada"
      @submit="handleSubmit"
      @cancel="closeModal"
    />
  </section>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import { initializeDataTable, attachTableEvents } from '@/utils/datatables-utils';
import Notification from '@/utils/notification';
import { useAuthStore } from '@/stores/auth';
import JornadaForm from './JornadaForm.vue';
import TorneosMenu from '../global/TorneosMenu.vue';

const authStore = useAuthStore();
const jornadaFormRef = ref(null);
const mode = ref('create');
const jornadas = ref([]);
const selectedTorneoId = ref(null);

const currentJornada = reactive({
  id: null,
  numero: '',
  torneo_id: '',
  fecha_inicio: '',
  fecha_fin: ''
});

const columns = [
  { data: 'numero' },
  { data: 'torneo.nombre', defaultContent: '' },
  { 
    data: 'fecha_inicio',
    render: (data) => new Date(data).toLocaleDateString()
  },
  { 
    data: 'fecha_fin',
    render: (data) => new Date(data).toLocaleDateString()
  },
  {
    data: null,
    render: (data, type, row) => {
      if (!authStore.isAdmin) return '';
      return `
        <button class="btn btn-icon btn-neutral btn-icon-mini btnEditar" data-id="${row.id}">
          <i class="zmdi zmdi-edit"></i>
        </button>
        <button class="btn btn-icon btn-neutral btn-icon-mini btnEliminar" data-id="${row.id}">
          <i class="zmdi zmdi-delete"></i>
        </button>`;
    }
  }
];

const loadJornadas = async () => {
  try {
    const url = selectedTorneoId.value
      ? `/api/jornadas?torneo_id=${selectedTorneoId.value}`
      : '/api/jornadas';
    const { data } = await axios.get(url);
    jornadas.value = data.data;
    initializeDataTable('tablajornadas', jornadas.value, columns);
    attachTableEvents('tablajornadas', handleEdit, handleDelete);
  } catch (error) {
    console.error(error);
    Notification.error('Error al cargar jornadas');
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
  $('#largeModal').modal('show');
};

const handleEdit = (id) => {
  const jornada = jornadas.value.find(j => j.id === id);
  if (jornada) {
    mode.value = 'edit';
    Object.assign(currentJornada, jornada);
    $('#largeModal').modal('show');
  }
};

const closeModal = () => {
  $('#largeModal').modal('hide');
};

const handleSubmit = async (formData) => {
  try {
    const method = mode.value === 'create' ? 'post' : 'put';
    const url = method === 'post' ? '/api/jornadas' : `/api/jornadas/${currentJornada.id}`;
    
    await axios[method](url, formData);
    await loadJornadas();
    
    closeModal();
    Notification.success(
      `Jornada ${mode.value === 'create' ? 'creada' : 'actualizada'} correctamente`
    );
  } catch (error) {
    if (error.response?.status === 422 && jornadaFormRef.value) {
      jornadaFormRef.value.handleApiErrors(error.response.data.errors);
    } else {
      Notification.error('Error al guardar jornada');
    }
  }
};

const handleDelete = async (id) => {
  const result = await Notification.confirm(
    '¿Estás seguro?',
    'Esta acción no se puede deshacer',
    'Sí, eliminar',
    'Cancelar'
  );

  if (result.isConfirmed) {
    try {
      await axios.delete(`/api/jornadas/${id}`);
      await loadJornadas();
      Notification.success('Jornada eliminada correctamente');
    } catch (error) {
      console.error(error);
      Notification.error('Error al eliminar jornada');
    }
  }
};

onMounted(() => {
  loadJornadas();
});
</script>
<template>
  <section class="content contact">
    <div class="container-fluid">
      <!-- Encabezado se mantiene igual -->
      
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
                        {{ mode === 'create' ? 'Nueva' : 'Editar' }} Temporada
                      </a>
                    </li>
                  </ul>
                  <div class="table-responsive">
                    <table id="temporadas"
                      class="table table-bordered table-striped table-hover js-basic-example dataTable">
                      <thead>
                        <tr>
                          <th>Nombre</th>
                          <th>Fecha Inicio</th>
                          <th>Fecha Fin</th>
                          <th>Estado</th>
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

  <TemporadaForm 
    :mode="mode"
    :current-temporada="currentTemporada"
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
import TemporadaForm from './TemporadaForm.vue';

const authStore = useAuthStore();
const temporadas = ref([]);
const mode = ref('create');

/** @type {import('vue').Ref<Temporada>} */
const currentTemporada = reactive({
  nombre: '',
  fecha_inicio: '',
  fecha_fin: '',
  estado: 'planificada',
});

/**
 * Configuración de columnas para DataTable
 * @type {import('datatables.net').ColumnSettings[]}
 */
const columns = [
  { data: 'nombre', title: 'Nombre' },
  { 
    data: 'fecha_inicio', 
    title: 'Fecha Inicio',
    render: (data) => new Date(data).toLocaleDateString()
  },
  { 
    data: 'fecha_fin', 
    title: 'Fecha Fin',
    render: (data) => new Date(data).toLocaleDateString()
  },
  { 
    data: 'estado', 
    title: 'Estado',
    render: (data) => data.charAt(0).toUpperCase() + data.slice(1)
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

/**
 * Carga las temporadas desde la API
 * @returns {Promise<void>}
 */
const loadTemporadas = async () => {
  try {
    const { data } = await axios.get('/api/temporadas');
    temporadas.value = data.data;
    
    initializeDataTable('temporadas', temporadas.value, columns);
    attachTableEvents('temporadas', editTemporada, deleteTemporada);

  } catch (error) {
    console.error('Error cargando temporadas:', error);
    Notification.error('Error al cargar temporadas');
  }
};

/**
 * Edita una temporada
 * @param {number} id - ID de la temporada
 * @returns {void}
 */
const editTemporada = (id) => {
  const temporada = temporadas.value.find(t => t.id === id);
  if (temporada) {
    mode.value = 'edit';
    Object.assign(currentTemporada, temporada);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};

/**
 * Elimina una temporada
 * @param {number} id - ID de la temporada
 * @returns {Promise<void>}
 */
const deleteTemporada = async (id) => {
  const result = await Notification.confirm(
    '¡No podrás revertir esta acción!',
    '¿Estás seguro?',
    'Sí, eliminar',
    'Cancelar'
  );

  if (result.isConfirmed) {
    try {
      await axios.delete(`/api/temporadas/${id}`);
      await loadTemporadas();
      Notification.success('Temporada eliminada correctamente');
    } catch (error) {
      Notification.error('Error al eliminar temporada');
    }
  }
};

/**
 * Maneja el envío del formulario
 * @param {Temporada} formData - Datos del formulario
 * @returns {Promise<void>}
 */
const handleSubmit = async (formData) => {
  try {
    const method = mode.value === 'create' ? 'post' : 'put';
    const url = method === 'post' ? '/api/temporadas' : `/api/temporadas/${formData.id}`;
    
    await axios[method](url, formData);
    await loadTemporadas();
    
    Notification.success(
      `Temporada ${mode.value === 'create' ? 'creada' : 'actualizada'} correctamente`
    );
    $('#largeModal').modal('hide');
    resetForm();
    
  } catch (error) {
    Notification.error('Error al guardar temporada');
  }
};

/**
 * Reinicia el formulario
 * @returns {void}
 */
const resetForm = () => {
  mode.value = 'create';
  Object.assign(currentTemporada, {
    nombre: '',
    fecha_inicio: '',
    fecha_fin: '',
    estado: 'planificada',
  });
};

onMounted(() => {
  loadTemporadas();
});
</script>
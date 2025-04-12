<template>
  <section class="content contact">
    <div class="container-fluid">
      <div class="block-header">
        <div class="row clearfix">
          <div class="col-lg-5 col-md-5 col-sm-12">
            <h2>Administración de Torneos</h2>
          </div>
          <div class="col-lg-7 col-md-7 col-sm-12">
            <ul class="breadcrumb float-md-right padding-0">
              <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> Inicio</a></li>
              <li class="breadcrumb-item active">Administración</li>
              <li class="breadcrumb-item">Torneos</li>
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
                    data-target="#largeModal"><i class="zmdi zmdi-plus-circle zmdi-hc-3x"></i></a></li>
              </ul>
            </div>
            <div class="body">
              <div class="table-responsive">
                <table id="torneos" class="table table-bordered table-striped table-hover js-basic-example dataTable">
                  <thead>
                    <tr>
                      <th>Nombre</th>
                      <th>Categoría</th>
                      <th>Temporada</th>
                      <th>Fecha Inicio</th>
                      <th>Fecha Fin</th>
                      <th>Estado</th>
                      <th>Campeón</th>
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
  <TorneoForm :mode="mode" :current-torneo="currentTorneo" :categorias="categorias" :temporadas="temporadas"
    :equipos="equipos" @submit="handleSubmit" @cancel="resetForm" />
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import { initializeDataTable, attachTableEvents } from '@/utils/datatables-utils';
import Notification from '@/utils/notification';
import { useAuthStore } from '../../stores/auth';
import TorneoForm from './TorneoForm.vue';

const authStore = useAuthStore();
const torneos = ref([]);
const categorias = ref([]);
const temporadas = ref([]);
const equipos = ref([]);
const mode = ref('create');

/** @type {import('vue').Ref<Torneo>} */
const currentTorneo = reactive({
  nombre: '',
  categoria_id: null,
  temporada_id: null,
  fecha_inicio: '',
  fecha_fin: '',
  estado: 'activo',
  campeon_id: null,
});

/**
 * Configuración de columnas para DataTable
 * @type {import('datatables.net').ColumnSettings[]}
 */
const columns = [
  { data: 'nombre', title: 'Nombre' },
  { data: 'categoria.nombre', title: 'Categoría' },
  { data: 'temporada.nombre', title: 'Temporada' },
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
    title: 'Campeón',
    render: (data, type, row) => row.campeon ? row.campeon.nombre : 'Ninguno'
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
 * Carga todos los datos necesarios
 * @returns {Promise<void>}
 */
const loadInitialData = async () => {
  try {
    const [torneosRes, categoriasRes, temporadasRes, equiposRes] = await Promise.all([
      axios.get('/api/torneos'),
      axios.get('/api/categorias'),
      axios.get('/api/temporadas'),
      //axios.get('/api/equipos')
    ]);

    torneos.value = torneosRes.data.data;
    categorias.value = categoriasRes.data.data;
    temporadas.value = temporadasRes.data.data;
    //equipos.value = equiposRes.data.data;

    initializeDataTable('torneos', torneos.value, columns);
    attachTableEvents('torneos', editTorneo, deleteTorneo);

  } catch (error) {
    console.error('Error cargando datos:', error);
    Notification.error('Error al cargar datos iniciales');
  }
};

/**
 * Edita un torneo
 * @param {number} id - ID del torneo
 * @returns {void}
 */
const editTorneo = (id) => {
  const torneo = torneos.value.find(t => t.id === id);
  if (torneo) {
    mode.value = 'edit';
    Object.assign(currentTorneo, torneo);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};

/**
 * Elimina un torneo
 * @param {number} id - ID del torneo
 * @returns {Promise<void>}
 */
const deleteTorneo = async (id) => {
  const result = await Notification.confirm(
    '¡No podrás revertir esta acción!',
    '¿Estás seguro?',
    'Sí, eliminar',
    'Cancelar'
  );

  if (result.isConfirmed) {
    try {
      await axios.delete(`/api/torneos/${id}`);
      await loadInitialData();
      Notification.success('Torneo eliminado correctamente');
    } catch (error) {
      Notification.error('Error al eliminar torneo');
    }
  }
};

/**
 * Maneja el envío del formulario
 * @param {Torneo} formData - Datos del formulario
 * @returns {Promise<void>}
 */
const handleSubmit = async (formData) => {
  try {
    const method = mode.value === 'create' ? 'post' : 'put';
    const url = method === 'post' ? '/api/torneos' : `/api/torneos/${formData.id}`;

    await axios[method](url, formData);
    await loadInitialData();

    Notification.success(
      `Torneo ${mode.value === 'create' ? 'creado' : 'actualizado'} correctamente`
    );
    $('#largeModal').modal('hide');
    resetForm();

  } catch (error) {
    Notification.error('Error al guardar torneo');
  }
};

/**
 * Reinicia el formulario
 * @returns {void}
 */
const resetForm = () => {
  mode.value = 'create';
  Object.assign(currentTorneo, {
    nombre: '',
    categoria_id: null,
    temporada_id: null,
    fecha_inicio: '',
    fecha_fin: '',
    estado: 'activo',
    campeon_id: null,
  });
};

onMounted(() => {
  loadInitialData();
});
</script>
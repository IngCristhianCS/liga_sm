<template>
  <section class="content contact">
    <div class="container-fluid">
      <div class="block-header">
        <div class="row clearfix">
          <div class="col-lg-5 col-md-5 col-sm-12">
            <h2>Gestión de Ingresos</h2>
          </div>
          <div class="col-lg-7 col-md-7 col-sm-12">
            <ul class="breadcrumb float-md-right padding-0">
              <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> Inicio</a></li>
              <li class="breadcrumb-item active">Administración de Ingresos</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="row clearfix">
        <div class="col-lg-4 col-md-12">
          <div class="card project_widget">
            <div class="body">
              <div class="row pw_content">
                <div class="col-12 pw_header">
                  <h6>Monto Cobrado</h6>
                  <small class="text-muted">Total de ingresos cobrados</small>
                </div>
                <div class="col-12 pw_meta">
                  <span>{{ montoCobrado }} MXN</span>
                  <small class="text-success">Total</small>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-12">
          <div class="card project_widget">
            <div class="body">
              <div class="row pw_content">
                <div class="col-12 pw_header">
                  <h6>Monto Pendiente</h6>
                  <small class="text-muted">Total de ingresos pendientes</small>
                </div>
                <div class="col-12 pw_meta">
                  <span>{{ montoPendiente }} MXN</span>
                  <small class="text-danger">Total</small>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-12">
          <div class="card project_widget">
            <div class="body">
              <div class="row pw_content">
                <div class="col-12 pw_header">
                  <h6>Monto Total</h6>
                  <small class="text-muted">Total de todos los ingresos</small>
                </div>
                <div class="col-12 pw_meta">
                  <span>{{ montoTotal }} MXN</span>
                  <small class="text-primary">Total</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row clearfix">
        <div class="col-lg-12">
          <div class="tab-content">
            <div class="tab-pane active" id="List">
              <div class="card">
                <div class="body">
                  <ul class="nav nav-tabs padding-0">
                    <li class="nav-item">
                      <a class="btn btn-primary btn-round" href="#largeModal" data-toggle="modal"
                        data-target="#largeModal">
                        {{ mode === 'create' ? 'Nuevo' : 'Editar' }} Ingreso
                      </a>
                    </li>
                  </ul>
                  <div class="table-responsive">
                    <table id="ingresos"
                      class="table table-bordered table-striped table-hover js-basic-example dataTable">
                      <thead>
                        <tr>
                          <th>Concepto</th>
                          <th>Tipo</th>
                          <th>Monto</th>
                          <th>Torneo</th>
                          <th>Equipo</th>
                          <th>Estatus</th>
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
  <IngresoForm :mode="mode" :current-ingreso="currentIngreso" @submit="handleSubmit" @cancel="resetForm" />
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue';
import axios from 'axios';
import { initializeDataTable, attachTableEvents } from '@/utils/datatables-utils';
import Notification from '@/utils/Notification';
import { useAuthStore } from '../../stores/auth';
import { useTorneoStore } from '../../stores/torneos';
import IngresoForm from './IngresoForm.vue';

/** @typedef {Object} Ingreso
 *  @property {number} id
 *  @property {string} descripcion
 *  @property {string} concepto
 *  @property {number} monto
 *  @property {string} fecha
 *  @property {number|null} torneo_id
 *  @property {number|null} equipo_id
 *  @property {number|null} patrocinador_id
 *  @property {'ok'|'pe'} estatus
 *  @property {'arbitraje'|'patrocinio'} tipo
 */

const authStore = useAuthStore();
const torneoStore = useTorneoStore();

/** @type {import('vue').Ref<Ingreso[]>} */
const ingresos = ref([]);

/** @type {import('vue').Ref<Ingreso[]>} */
const filteredIngresos = ref([]);

/** @type {import('vue').Ref<'create'|'edit'>} */
const mode = ref('create');

/** @type {Ingreso} */
const defaultIngreso = {
  descripcion: '',
  concepto: '',
  monto: 0,
  fecha: '',
  torneo_id: null,
  equipo_id: null,
  patrocinador_id: null,
  estatus: 'ok',
  tipo: 'arbitraje',
};

/** @type {Ingreso} */
const currentIngreso = reactive({ ...defaultIngreso });

/**
 * Columnas configuradas para DataTable
 * @type {import('datatables.net').ColumnSettings[]}
 */
const columns = [
  {
    data: 'descripcion',
    title: 'Concepto',
  },
  {
    data: 'tipo',
    title: 'Tipo',
  },
  {
    data: 'monto',
    title: 'Monto',
    render: (data) => `$${parseFloat(data).toFixed(2)}`,
  },
  {
    data: 'torneo.nombre',
    title: 'Torneo',
  },
  {
    data: 'equipo.nombre',
    title: 'Equipo',
  },
  {
    data: 'estatus',
    title: 'Estatus',
    render: (data) => (data === 'ok'
      ? '<i class="zmdi zmdi-check text-success"></i>'
      : '<i class="zmdi zmdi-close text-danger"></i>'),
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
    },
  },
];

/**
 * Carga los ingresos desde la API y actualiza la tabla
 * @returns {Promise<void>}
 */
const loadIngresos = async () => {
  try {
    const { data } = await axios.get('/api/ingresos');
    ingresos.value = data.data;

    initializeDataTable('ingresos', ingresos.value, columns);
    attachTableEvents('ingresos', editIngreso, deleteIngreso);

    // Actualizar datos filtrados
    const table = $('#ingresos').DataTable();
    const updateFilteredData = () => {
      const filteredData = table.rows({ search: 'applied' }).data().toArray();
      filteredIngresos.value = filteredData.map((item) => ({
        ...item,
        monto: parseFloat(item.monto),
      }));
    };

    table.on('search.dt draw.dt', updateFilteredData);
    updateFilteredData();

  } catch (error) {
    console.error('Error cargando ingresos:', error);
    Notification.error('No se pudieron cargar los ingresos');
  }
};

/**
 * Maneja la edición de un ingreso
 * @param {number} id - ID del ingreso a editar
 */
const editIngreso = (id) => {
  const ingreso = ingresos.value.find((i) => i.id === id);
  if (ingreso) {
    mode.value = 'edit';
    Object.assign(currentIngreso, ingreso);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};

/**
 * Maneja la eliminación de un ingreso
 * @param {number} id - ID del ingreso a eliminar
 * @returns {Promise<void>}
 */
const deleteIngreso = async (id) => {
  const result = await Notification.confirm(
    '¡No podrás revertir esta acción!',
    '¿Estás seguro?',
    'Sí, eliminar',
    'Cancelar',
  );

  if (result.isConfirmed) {
    try {
      await axios.delete(`/api/ingresos/${id}`);
      await loadIngresos();
      Notification.success('Ingreso eliminado correctamente');
    } catch (error) {
      Notification.error('Error al eliminar ingreso');
    }
  }
};

/**
 * Maneja el envío del formulario
 * @param {Ingreso} formData - Datos del formulario
 * @returns {Promise<void>}
 */
const handleSubmit = async (formData) => {
  try {
    const method = mode.value === 'create' ? 'post' : 'put';
    const url = method === 'post' ? '/api/ingresos' : `/api/ingresos/${formData.id}`;

    await axios[method](url, formData);
    await loadIngresos();

    Notification.success(
      `Ingreso ${mode.value === 'create' ? 'creado' : 'actualizado'} correctamente`,
    );
    $('#largeModal').modal('hide');
    resetForm();

  } catch (error) {
    Notification.error('Error al guardar ingreso');
  }
};

/**
 * Reinicia el formulario a los valores por defecto
 */
const resetForm = () => {
  mode.value = 'create';
  Object.assign(currentIngreso, defaultIngreso);
};

/**
 * Calcula el monto total cobrado
 * @type {import('vue').ComputedRef<number>}
 */
const montoCobrado = computed(() => filteredIngresos.value
  .filter((i) => i.estatus === 'ok')
  .reduce((acc, curr) => acc + curr.monto, 0));

/**
 * Calcula el monto pendiente de cobro
 * @type {import('vue').ComputedRef<number>}
 */
const montoPendiente = computed(() => filteredIngresos.value
  .filter((i) => i.estatus === 'pe')
  .reduce((acc, curr) => acc + curr.monto, 0));

/**
 * Calcula el monto total de ingresos
 * @type {import('vue').ComputedRef<number>}
 */
const montoTotal = computed(() => filteredIngresos.value
  .reduce((acc, curr) => acc + curr.monto, 0));

onMounted(async () => {
  await loadIngresos();
  if (torneoStore.torneos.length === 0) {
    await torneoStore.fetchTorneos();
  }
});
</script>
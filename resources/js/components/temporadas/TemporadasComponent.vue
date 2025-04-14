<template>
  <section class="content contact">
    <div class="container-fluid">
      <div class="block-header">
        <div class="row clearfix">
          <div class="col-lg-5 col-md-5 col-sm-12">
            <h2>Administración de Temporadas</h2>
          </div>
          <div class="col-lg-7 col-md-7 col-sm-12">
            <ul class="breadcrumb float-md-right padding-0">
              <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i></a></li>
              <li class="breadcrumb-item"><a href="javascript:void(0);">Administración</a></li>
              <li class="breadcrumb-item active">Temporadas</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="row clearfix">
        <div class="col-lg-12">
          <div class="tab-content">
            <div class="tab-pane active" id="List">
              <div class="card">
                <div class="header">
                  <ul class="header-dropdown">
                    <li class="nav-item" v-if="authStore.isAdmin"><a href="#largeModal" data-toggle="modal"
                        data-target="#largeModal"><i class="zmdi zmdi-plus-circle zmdi-hc-3x"></i></a></li>
                  </ul>
                </div>
                <div class="body">
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

  <TemporadaForm :mode="mode" :current-temporada="currentTemporada" @submit="handleSubmit" @cancel="resetForm" />
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import { initializeDataTable, attachTableEvents } from '@/utils/datatables-utils';
import Notification from '@/utils/notification';
import { useAuthStore } from '@/stores/auth';
import { useTemporadasStore } from '@/stores/temporadas';
import TemporadaForm from './TemporadaForm.vue';

const authStore = useAuthStore();
const temporadasStore = useTemporadasStore();
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
 * Carga las temporadas desde el store
 * @returns {Promise<void>}
 */
const loadTemporadas = async () => {
  try {
    await temporadasStore.loadTemporadas();

    initializeDataTable('temporadas', temporadasStore.temporadas, columns);
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
  const temporada = temporadasStore.getTemporadaById(id);
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
      await temporadasStore.deleteTemporada(id);
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
    if (mode.value === 'create') {
      await temporadasStore.createTemporada(formData);
    } else {
      await temporadasStore.updateTemporada(formData.id, formData);
    }

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
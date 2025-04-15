<template>
  <section class="content contact">
    <div class="container-fluid">
      <div class="block-header">
        <div class="row clearfix">
          <div class="col-lg-5 col-md-5 col-sm-12">
            <h2>Gestión de Egresos</h2>
          </div>
          <div class="col-lg-7 col-md-7 col-sm-12">
            <ul class="breadcrumb float-md-right padding-0">
              <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> Inicio</a></li>
              <li class="breadcrumb-item active">Administración de Egresos</li>
            </ul>
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
                        data-target="#largeModal" @click="openCreateModal">
                        Nuevo Egreso
                      </a>
                    </li>
                  </ul>
                  <div class="table-responsive">
                    <table id="egresos"
                      class="table table-bordered table-striped table-hover js-basic-example dataTable">
                      <thead>
                        <tr>
                          <th>Fecha</th>
                          <th>Monto</th>
                          <th>Tipo</th>
                          <th>Descripción</th>
                          <th>Partido ID</th>
                          <th>Torneo ID</th>
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
  
  <EgresoForm 
    ref="egresoFormRef"
    :mode="mode" 
    :current-egreso="currentEgreso" 
    @submit="handleSubmit" 
    @cancel="resetForm"
  />
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import Notification from '@/utils/notification';
import { initializeDataTable, attachTableEvents } from '@/utils/datatables-utils';
import EgresoForm from '@/components/egresos/EgresoForm.vue';

const egresoFormRef = ref(null);
const egresos = ref([]);
const mode = ref('create');

const defaultEgreso = {
  fecha: '',
  monto: null,
  tipo: '',
  descripcion: '',
  partido_id: null,
  torneo_id: null
};

const currentEgreso = reactive({ ...defaultEgreso });

/**
 * Opens the modal for creating a new egreso
 */
const openCreateModal = () => {
  mode.value = 'create';
  resetForm();
};

const handleSubmit = async (formData) => {
  try {
    const method = mode.value === 'create' ? 'post' : 'put';
    const url = method === 'post' ? '/api/egresos' : `/api/egresos/${formData.id}`;
    
    await axios[method](url, formData);
    await loadEgresos();
    
    Notification.success(
      `Egreso ${mode.value === 'create' ? 'creado' : 'actualizado'} correctamente`
    );
    
    // Explicitly close the modal
    document.querySelector('[data-dismiss="modal"]').click();
    resetForm();
    
  } catch (error) {
    if (error.response?.data?.errors) {
      egresoFormRef.value.handleApiErrors(error.response.data.errors);
    } else {
      Notification.error('Error al guardar egreso');
    }
  }
};

const loadEgresos = async () => {
  try {
    const response = await axios.get('/api/egresos');
    egresos.value = response.data.data;
    setupDataTable();
  } catch (error) {
    console.error(error);
    await Notification.error('Error al cargar egresos');
  }
};

const editEgreso = (id) => {
  const egreso = egresos.value.find(e => e.id === id);
  if (egreso) {
    mode.value = 'edit';
    Object.assign(currentEgreso, egreso);
  }
};

const deleteEgreso = async (id) => {
  const result = await Notification.confirm(
    "¡No podrás revertir esta acción!",
    '¿Estás seguro?',
    'Sí, eliminar',
    'Cancelar'
  );

  if (result.isConfirmed) {
    try {
      await axios.delete(`/api/egresos/${id}`);
      await loadEgresos();
      await Notification.success('Egreso eliminado correctamente', '¡Eliminado!');
    } catch (error) {
      await Notification.error('Error al eliminar egreso');
    }
  }
};

const columns = [
  { data: 'fecha', title: 'Fecha' },
  { data: 'monto', title: 'Monto' },
  { data: 'tipo', title: 'Tipo' },
  { data: 'descripcion', title: 'Descripción' },
  { data: 'partido_id', title: 'Partido ID' },
  { data: 'torneo_id', title: 'Torneo ID' },
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

const setupDataTable = () => {
  initializeDataTable('egresos', egresos.value, columns);
  attachTableEvents('egresos', editEgreso, deleteEgreso);
};

const resetForm = () => {
  mode.value = 'create';
  Object.assign(currentEgreso, defaultEgreso);
};

onMounted(() => {
  loadEgresos();
});
</script>
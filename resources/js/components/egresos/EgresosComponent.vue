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
                        data-target="#largeModal">
                        {{ mode === 'create' ? 'Nuevo' : 'Editar' }} Egreso
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
  <div class="modal fade" id="largeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="title" id="largeModalLabel">
            {{ mode === 'create' ? 'Nuevo Egreso' : 'Editar Egreso' }}
          </h4>
        </div>
        <form @submit.prevent="handleSubmit">
          <div class="modal-body">
            <div class="row clearfix">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Fecha</label>
                  <input type="date" class="form-control" v-model="currentEgreso.fecha" required>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Monto</label>
                  <input type="number" class="form-control" v-model="currentEgreso.monto" required>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Tipo</label>
                  <select class="form-control" v-model="currentEgreso.tipo" required>
                    <option value="arbitraje">Arbitraje</option>
                    <option value="mantenimiento">Mantenimiento</option>
                    <option value="organizacion">Organización</option>
                  </select>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Descripción</label>
                  <input type="text" class="form-control" v-model="currentEgreso.descripcion">
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Partido ID</label>
                  <input type="number" class="form-control" v-model="currentEgreso.partido_id">
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Torneo ID</label>
                  <input type="number" class="form-control" v-model="currentEgreso.torneo_id">
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-default btn-round waves-effect">
              {{ mode === 'create' ? 'Crear Egreso' : 'Actualizar Egreso' }}
            </button>
            <button type="button" class="btn btn-danger btn-simple btn-round waves-effect" data-dismiss="modal"
              @click="resetForm">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { useAuthStore } from '../../stores/auth';

const egresos = ref([]);
const mode = ref('create');
const authStore = useAuthStore();

const defaultEgreso = {
  fecha: '',
  monto: null,
  tipo: 'arbitraje',
  descripcion: '',
  partido_id: null,
  torneo_id: null,
};

const currentEgreso = reactive({ ...defaultEgreso });

const loadEgresos = async () => {
  try {
    const response = await axios.get('/api/egresos');
    egresos.value = response.data.data;
    inicializarDataTable();
  } catch (error) {
    console.error(error);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Error al cargar egresos',
      timer: 3000,
      showConfirmButton: false
    });
  }
};

const editEgreso = (egreso) => {
  mode.value = 'edit';
  Object.assign(currentEgreso, egreso);
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const deleteEgreso = async (id) => {
  const result = await Swal.fire({
    title: '¿Estás seguro?',
    text: "¡No podrás revertir esta acción!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  });

  if (result.isConfirmed) {
    try {
      await axios.delete(`/api/egresos/${id}`);
      await loadEgresos();
      Swal.fire({
        icon: 'success',
        title: '¡Eliminado!',
        text: 'Egreso eliminado correctamente',
        timer: 3000,
        showConfirmButton: false
      });
    } catch (error) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Error al eliminar egreso',
        timer: 3000,
        showConfirmButton: false
      });
    }
  }
};

const handleSubmit = async () => {
  try {
    const url = mode.value === 'create'
      ? '/api/egresos'
      : `/api/egresos/${currentEgreso.id}`;

    const method = mode.value === 'create' ? 'post' : 'put';
    await axios[method](url, currentEgreso);

    await loadEgresos();

    Swal.fire({
      icon: 'success',
      title: '¡Éxito!',
      text: `Egreso ${mode.value === 'create' ? 'creado' : 'actualizado'} correctamente`,
      timer: 3000,
      showConfirmButton: false
    });

    $("#largeModal").modal('hide');
    resetForm();
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Error al guardar egreso',
      timer: 3000,
      showConfirmButton: false
    });
  }
};

const resetForm = () => {
  mode.value = 'create';
  Object.assign(currentEgreso, defaultEgreso);
};

const inicializarDataTable = () => {
  const tabla = "#egresos";

  if ($.fn.DataTable.isDataTable(tabla)) {
    const table = $(tabla).DataTable();
    table.clear().rows.add(egresos.value).draw();
  } else {
    $(tabla).DataTable({
      paging: true,
      pageLength: 10,
      lengthChange: false,
      searching: true,
      ordering: true,
      data: egresos.value,
      columns: [
        { data: 'fecha' },
        { data: 'monto' },
        { data: 'tipo' },
        { data: 'descripcion' },
        { data: 'partido_id' },
        { data: 'torneo_id' },
        {
          data: null,
          render: (data, type, row) => {
            if (authStore.isAdmin) {
              return `<button class="btn btn-icon btn-neutral btn-icon-mini btnEditar"
                                    data-id="${row.id}"
                                    data-toggle="modal"
                                    data-target="#largeModal">
                                <i class="zmdi zmdi-edit"></i>
                            </button>
                            <button class="btn btn-icon btn-neutral btn-icon-mini btnEliminar"
                                    data-id="${row.id}">
                                <i class="zmdi zmdi-delete"></i>
                            </button>`
            } else {
              return ``
            }
          }
        }
      ]
    });
  }
  attachDataTableEvents();
};

const attachDataTableEvents = () => {
  const tabla = "#egresos";
  const tbody = $(tabla + ' tbody');

  tbody.off('click', '.btnEditar');
  tbody.off('click', '.btnEliminar');

  tbody.on('click', '.btnEditar', function () {
    const egresoId = $(this).data('id');
    const egreso = egresos.value.find(e => e.id === egresoId);
    if (egreso) editEgreso(egreso);
  });

  tbody.on('click', '.btnEliminar', function () {
    const egresoId = $(this).data('id');
    deleteEgreso(egresoId);
  });
}

onMounted(() => {
  loadEgresos();
});
</script>
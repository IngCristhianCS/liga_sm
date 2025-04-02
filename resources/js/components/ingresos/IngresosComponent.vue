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
                      <a class="btn btn-primary btn-round" href="#largeModal" data-toggle="modal" data-target="#largeModal">
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
  <div class="modal fade" id="largeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="title" id="largeModalLabel">
            {{ mode === 'create' ? 'Nuevo Ingreso' : 'Editar Ingreso' }}
          </h4>
        </div>
        <form @submit.prevent="handleSubmit">
          <div class="modal-body">
            <div class="row clearfix">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Descripción</label>
                  <input type="text" class="form-control" v-model="currentIngreso.descripcion" required>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Tipo</label>
                  <input type="text" class="form-control" v-model="currentIngreso.tipo" required>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Monto</label>
                  <input type="number" class="form-control" v-model="currentIngreso.monto" required>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Fecha</label>
                  <input type="date" class="form-control" v-model="currentIngreso.fecha" required>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Equipo ID</label>
                  <input type="number" class="form-control" v-model="currentIngreso.equipo_id">
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Torneo ID</label>
                  <input type="number" class="form-control" v-model="currentIngreso.torneo_id">
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Patrocinador ID</label>
                  <input type="number" class="form-control" v-model="currentIngreso.patrocinador_id">
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Estatus</label>
                  <select class="form-control" v-model="currentIngreso.estatus">
                    <option value="ok">ok</option>
                    <option value="pe">pe</option>
                  </select>
                </div>
              </div>

            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-default btn-round waves-effect">
              {{ mode === 'create' ? 'Crear Ingreso' : 'Actualizar Ingreso' }}
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
import { ref, reactive, onMounted, computed } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import { useAuthStore } from '../../stores/auth';

const ingresos = ref([]);
const mode = ref('create');
const authStore = useAuthStore();

const defaultIngreso = {
  concepto: '',
  monto: null,
  fecha: '',
};

const currentIngreso = reactive({ ...defaultIngreso });

// Métodos
const loadIngresos = async () => {
  try {
    const response = await axios.get('/api/ingresos');
    ingresos.value = response.data.data;
    inicializarDataTable();
  } catch (error) {
    console.error(error);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Error al cargar ingresos',
      timer: 3000,
      showConfirmButton: false
    });
  }
};

const editIngreso = (ingreso) => {
  mode.value = 'edit';
  Object.assign(currentIngreso, ingreso);
  window.scrollTo({ top: 0, behavior: 'smooth' });
};

const deleteIngreso = async (id) => {
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
      await axios.delete(`/api/ingresos/${id}`);
      await loadIngresos();
      Swal.fire({
        icon: 'success',
        title: '¡Eliminado!',
        text: 'Ingreso eliminado correctamente',
        timer: 3000,
        showConfirmButton: false
      });
    } catch (error) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Error al eliminar ingreso',
        timer: 3000,
        showConfirmButton: false
      });
    }
  }
};

const handleSubmit = async () => {
  try {
    const url = mode.value === 'create'
      ? '/api/ingresos'
      : `/api/ingresos/${currentIngreso.id}`;

    const method = mode.value === 'create' ? 'post' : 'put';
    await axios[method](url, currentIngreso);

    await loadIngresos();

    Swal.fire({
      icon: 'success',
      title: '¡Éxito!',
      text: `Ingreso ${mode.value === 'create' ? 'creado' : 'actualizado'} correctamente`,
      timer: 3000,
      showConfirmButton: false
    });

    $("#largeModal").modal('hide');
    resetForm();
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'Error al guardar ingreso',
      timer: 3000,
      showConfirmButton: false
    });
  }
};

const resetForm = () => {
  mode.value = 'create';
  Object.assign(currentIngreso, defaultIngreso);
};

const montoCobrado = computed(() => {
  return ingresos.value
    .filter(ingreso => ingreso.estatus === 'ok')
    .reduce((sum, ingreso) => sum + parseFloat(ingreso.monto), 0);
});

const montoPendiente = computed(() => {
  return ingresos.value
    .filter(ingreso => ingreso.estatus === 'pe')
    .reduce((sum, ingreso) => sum + parseFloat(ingreso.monto), 0);
});

const montoTotal = computed(() => {
  return ingresos.value.reduce((sum, ingreso) => sum + parseFloat(ingreso.monto), 0);
});

const inicializarDataTable = () => {
  const tabla = "#ingresos";

  if ($.fn.DataTable.isDataTable(tabla)) {
    const table = $(tabla).DataTable();
    table.clear().rows.add(ingresos.value).draw();
  } else {
    $(tabla).DataTable({
      paging: true,
      pageLength: 10,
      lengthChange: false,
      searching: true,
      ordering: true,
      data: ingresos.value,
      columns: [
        { data: 'descripcion' },
        { data: 'tipo' },
        { data: 'monto' },
        { data: 'equipo.nombre' },
        {
          data: 'estatus',
          render: function (data, type, row) {
            if (data === 'ok') {
              return '<i class="zmdi zmdi-check text-success"></i>'; // Icono de "ok" (check)
            } else if (data === 'pe') {
              return '<i class="zmdi zmdi-close text-danger"></i>'; // Icono de "x" (close)
            } else {
              return data;
            }
          }
        },
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
  const tabla = "#ingresos";
  const tbody = $(tabla + ' tbody');

  // Limpiar eventos previos para evitar duplicados
  tbody.off('click', '.btnEditar');
  tbody.off('click', '.btnEliminar');

  // Evento para editar
  tbody.on('click', '.btnEditar', function () {
    const ingresoId = $(this).data('id');
    const ingreso = ingresos.value.find(i => i.id === ingresoId);
    if (ingreso) editIngreso(ingreso);
  });

  // Evento para eliminar
  tbody.on('click', '.btnEliminar', function () {
    const ingresoId = $(this).data('id');
    deleteIngreso(ingresoId);
  });
}

// Ciclo de vida
onMounted(() => {
  loadIngresos();
});
</script>
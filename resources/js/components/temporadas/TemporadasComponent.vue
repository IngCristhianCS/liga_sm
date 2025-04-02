<template>
    <section class="content contact">
      <div class="container-fluid">
        <div class="block-header">
          <div class="row clearfix">
            <div class="col-lg-5 col-md-5 col-sm-12">
              <h2>Gestión de Temporadas</h2>
            </div>
            <div class="col-lg-7 col-md-7 col-sm-12">
              <ul class="breadcrumb float-md-right padding-0">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> Inicio</a></li>
                <li class="breadcrumb-item active">Administración de Temporadas</li>
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
    <div class="modal fade" id="largeModal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="title" id="largeModalLabel">
              {{ mode === 'create' ? 'Nueva Temporada' : 'Editar Temporada' }}
            </h4>
          </div>
          <form @submit.prevent="handleSubmit">
            <div class="modal-body">
              <div class="row clearfix">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" class="form-control" v-model="currentTemporada.nombre" required>
                  </div>
                </div>
  
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Fecha Inicio</label>
                    <input type="date" class="form-control" v-model="currentTemporada.fecha_inicio" required>
                  </div>
                </div>
  
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Fecha Fin</label>
                    <input type="date" class="form-control" v-model="currentTemporada.fecha_fin" required>
                  </div>
                </div>
  
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Estado</label>
                    <input type="text" class="form-control" v-model="currentTemporada.estado" required>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-default btn-round waves-effect">
                {{ mode === 'create' ? 'Crear Temporada' : 'Actualizar Temporada' }}
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
  
  const temporadas = ref([]);
  const mode = ref('create');
  const authStore = useAuthStore();
  
  const defaultTemporada = {
    nombre: '',
    fecha_inicio: '',
    fecha_fin: '',
    estado: '',
  };
  
  const currentTemporada = reactive({ ...defaultTemporada });
  
  const loadTemporadas = async () => {
    try {
      const response = await axios.get('/api/temporadas');
      temporadas.value = response.data.data;
      inicializarDataTable();
    } catch (error) {
      console.error(error);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Error al cargar temporadas',
        timer: 3000,
        showConfirmButton: false
      });
    }
  };
  
  const editTemporada = (temporada) => {
    mode.value = 'edit';
    Object.assign(currentTemporada, temporada);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  };
  
  const deleteTemporada = async (id) => {
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
        await axios.delete(`/api/temporadas/${id}`);
        await loadTemporadas();
        Swal.fire({
          icon: 'success',
          title: '¡Eliminado!',
          text: 'Temporada eliminada correctamente',
          timer: 3000,
          showConfirmButton: false
        });
      } catch (error) {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Error al eliminar temporada',
          timer: 3000,
          showConfirmButton: false
        });
      }
    }
  };
  
  const handleSubmit = async () => {
    try {
      const url = mode.value === 'create'
        ? '/api/temporadas'
        : `/api/temporadas/${currentTemporada.id}`;
  
      const method = mode.value === 'create' ? 'post' : 'put';
      await axios[method](url, currentTemporada);
  
      await loadTemporadas();
  
      Swal.fire({
        icon: 'success',
        title: '¡Éxito!',
        text: `Temporada ${mode.value === 'create' ? 'creada' : 'actualizada'} correctamente`,
        timer: 3000,
        showConfirmButton: false
      });
  
      $("#largeModal").modal('hide');
      resetForm();
    } catch (error) {
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Error al guardar temporada',
        timer: 3000,
        showConfirmButton: false
      });
    }
  };
  
  const resetForm = () => {
    mode.value = 'create';
    Object.assign(currentTemporada, defaultTemporada);
  };
  
  const inicializarDataTable = () => {
    const tabla = "#temporadas";
  
    if ($.fn.DataTable.isDataTable(tabla)) {
      const table = $(tabla).DataTable();
      table.clear().rows.add(temporadas.value).draw();
    } else {
      $(tabla).DataTable({
        paging: true,
        pageLength: 10,
        lengthChange: false,
        searching: true,
        ordering: true,
        data: temporadas.value,
        columns: [
          { data: 'nombre' },
          { data: 'fecha_inicio' },
          { data: 'fecha_fin' },
          { data: 'estado' },
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
    const tabla = "#temporadas";
    const tbody = $(tabla + ' tbody');
  
    tbody.off('click', '.btnEditar');
    tbody.off('click', '.btnEliminar');
  
    tbody.on('click', '.btnEditar', function () {
      const temporadaId = $(this).data('id');
      const temporada = temporadas.value.find(t => t.id === temporadaId);
      if (temporada) editTemporada(temporada);
    });
  
    tbody.on('click', '.btnEliminar', function () {
      const temporadaId = $(this).data('id');
      deleteTemporada(temporadaId);
    });
  }
  
  onMounted(() => {
    loadTemporadas();
  });
  </script>
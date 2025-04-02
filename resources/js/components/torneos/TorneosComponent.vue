<template>
    <section class="content contact">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row clearfix">
                    <div class="col-lg-5 col-md-5 col-sm-12">
                        <h2>Gestión de Torneos</h2>
                    </div>
                    <div class="col-lg-7 col-md-7 col-sm-12">
                        <ul class="breadcrumb float-md-right padding-0">
                            <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> Inicio</a></li>
                            <li class="breadcrumb-item active">Administración de Torneos</li>
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
                                                {{ mode === 'create' ? 'Nuevo' : 'Editar' }} Torneo
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="table-responsive">
                                        <table id="torneos"
                                            class="table table-bordered table-striped table-hover js-basic-example dataTable">
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
            </div>
        </div>
    </section>
    <div class="modal fade" id="largeModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="largeModalLabel">
                        {{ mode === 'create' ? 'Nuevo Torneo' : 'Editar Torneo' }}
                    </h4>
                </div>
                <form @submit.prevent="handleSubmit">
                    <div class="modal-body">
                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" class="form-control" v-model="currentTorneo.nombre" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Categoría</label>
                                    <select class="form-control" v-model="currentTorneo.categoria_id" required>
                                        <option v-for="categoria in categorias" :value="categoria.id"
                                            :key="categoria.id">
                                            {{ categoria.nombre }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Temporada</label>
                                    <select class="form-control" v-model="currentTorneo.temporada_id" required>
                                        <option v-for="temporada in temporadas" :value="temporada.id"
                                            :key="temporada.id">
                                            {{ temporada.nombre }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha Inicio</label>
                                    <input type="date" class="form-control" v-model="currentTorneo.fecha_inicio"
                                        required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha Fin</label>
                                    <input type="date" class="form-control" v-model="currentTorneo.fecha_fin" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Estado</label>
                                    <select class="form-control" v-model="currentTorneo.estado" required>
                                        <option value="activo">Activo</option>
                                        <option value="finalizado">Finalizado</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Campeón</label>
                                    <select class="form-control" v-model="currentTorneo.campeon_id">
                                        <option value="null">Ninguno</option>
                                        <option v-for="equipo in equipos" :value="equipo.id" :key="equipo.id">
                                            {{ equipo.nombre }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-default btn-round waves-effect">
                            {{ mode === 'create' ? 'Crear Torneo' : 'Actualizar Torneo' }}
                        </button>
                        <button type="button" class="btn btn-danger btn-simple btn-round waves-effect"
                            data-dismiss="modal" @click="resetForm">Cancelar</button>
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

const torneos = ref([]);
const categorias = ref([]);
const temporadas = ref([]);
const equipos = ref([]);
const mode = ref('create');
const authStore = useAuthStore();

const defaultTorneo = {
    nombre: '',
    categoria_id: null,
    temporada_id: null,
    fecha_inicio: '',
    fecha_fin: '',
    estado: 'activo',
    campeon_id: null,
};

const currentTorneo = reactive({ ...defaultTorneo });

const loadTorneos = async () => {
    try {
        const response = await axios.get('/api/torneos');
        torneos.value = response.data.data;
        inicializarDataTable();
    } catch (error) {
        console.error(error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al cargar torneos',
            timer: 3000,
            showConfirmButton: false
        });
    }
};

const loadCategorias = async () => {
    try {
        const response = await axios.get('/api/categorias');
        categorias.value = response.data.data;
    } catch (error) {
        console.error(error);
    }
};

const loadTemporadas = async () => {
    try {
        const response = await axios.get('/api/temporadas');
        temporadas.value = response.data.data;
    } catch (error) {
        console.error(error);
    }
};

const loadEquipos = async () => {
    try {
        const response = await axios.get('/api/equipos');
        equipos.value = response.data.data;
    } catch (error) {
        console.error(error);
    }
};

const editTorneo = (torneo) => {
    mode.value = 'edit';
    Object.assign(currentTorneo, torneo);
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const deleteTorneo = async (id) => {
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
            await axios.delete(`/api/torneos/${id}`);
            await loadTorneos();
            Swal.fire({
                icon: 'success',
                title: '¡Eliminado!',
                text: 'Torneo eliminado correctamente',
                timer: 3000,
                showConfirmButton: false
            });
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al eliminar torneo',
                timer: 3000,
                showConfirmButton: false
            });
        }
    }
};

const handleSubmit = async () => {
    try {
        const url = mode.value === 'create'
            ? '/api/torneos'
            : `/api/torneos/${currentTorneo.id}`;

        const method = mode.value === 'create' ? 'post' : 'put';
        await axios[method](url, currentTorneo);

        await loadTorneos();

        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: `Torneo ${mode.value === 'create' ? 'creado' : 'actualizado'} correctamente`,
            timer: 3000,
            showConfirmButton: false
        });

        $("#largeModal").modal('hide');
        resetForm();
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al guardar torneo',
            timer: 3000,
            showConfirmButton: false
        });
    }
};

const resetForm = () => {
    mode.value = 'create';
    Object.assign(currentTorneo, defaultTorneo);
};

const inicializarDataTable = () => {
    const tabla = "#torneos";

    if ($.fn.DataTable.isDataTable(tabla)) {
        const table = $(tabla).DataTable();
        table.clear().rows.add(torneos.value).draw();
    } else {
        $(tabla).DataTable({
            paging: true,
            pageLength: 10,
            lengthChange: false,
            searching: true,
            ordering: true,
            data: torneos.value,
            columns: [
                { data: 'nombre' },
                { data: 'categoria.nombre' },
                { data: 'temporada.nombre' },
                { data: 'fecha_inicio' },
                { data: 'fecha_fin' },
                { data: 'estado' },
                { data: null, render: (data, type, row) => row.campeon ? row.campeon.nombre : 'Ninguno' },
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
    const tabla = "#torneos";
    const tbody = $(tabla + ' tbody');

    tbody.off('click', '.btnEditar');
    tbody.off('click', '.btnEliminar');

    tbody.on('click', '.btnEditar', function () {
        const torneoId = $(this).data('id');
        const torneo = torneos.value.find(t => t.id === torneoId);
        if (torneo) editTorneo(torneo);
    });

    tbody.on('click', '.btnEliminar', function () {
        const torneoId = $(this).data('id');
        deleteTorneo(torneoId);
    });
}

onMounted(() => {
    Promise.all([loadTorneos(), loadCategorias(), loadTemporadas(), loadEquipos()]);
});
</script>
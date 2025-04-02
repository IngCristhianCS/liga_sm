<template>
    <section class="content contact">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row clearfix">
                    <div class="col-lg-5 col-md-5 col-sm-12">
                        <h2>Gestión de Categorías</h2>
                    </div>
                    <div class="col-lg-7 col-md-7 col-sm-12">
                        <ul class="breadcrumb float-md-right padding-0">
                            <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> Inicio</a></li>
                            <li class="breadcrumb-item active">Administración de Categorías</li>
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
                                                {{ mode === 'create' ? 'Nueva' : 'Editar' }} Categoría
                                            </a>
                                        </li>
                                    </ul>
                                    <div class="table-responsive">
                                        <table id="categorias"
                                            class="table table-bordered table-striped table-hover js-basic-example dataTable">
                                            <thead>
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Tipo</th>
                                                    <th>Edad Máxima</th>
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
                        {{ mode === 'create' ? 'Nueva Categoría' : 'Editar Categoría' }}
                    </h4>
                </div>
                <form @submit.prevent="handleSubmit">
                    <div class="modal-body">
                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nombre</label>
                                    <input type="text" class="form-control" v-model="currentCategoria.nombre" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tipo</label>
                                    <input type="text" class="form-control" v-model="currentCategoria.tipo" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Edad Máxima</label>
                                    <input type="number" class="form-control" v-model="currentCategoria.edad_maxima"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-default btn-round waves-effect">
                            {{ mode === 'create' ? 'Crear Categoría' : 'Actualizar Categoría' }}
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

const categorias = ref([]);
const mode = ref('create');
const authStore = useAuthStore();

const defaultCategoria = {
    nombre: '',
    tipo: '',
    edad_maxima: null,
};

const currentCategoria = reactive({ ...defaultCategoria });

const loadCategorias = async () => {
    try {
        const response = await axios.get('/api/categorias');
        categorias.value = response.data.data;
        inicializarDataTable();
    } catch (error) {
        console.error(error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al cargar categorías',
            timer: 3000,
            showConfirmButton: false
        });
    }
};

const editCategoria = (categoria) => {
    mode.value = 'edit';
    Object.assign(currentCategoria, categoria);
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const deleteCategoria = async (id) => {
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
            await axios.delete(`/api/categorias/${id}`);
            await loadCategorias();
            Swal.fire({
                icon: 'success',
                title: '¡Eliminado!',
                text: 'Categoría eliminada correctamente',
                timer: 3000,
                showConfirmButton: false
            });
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al eliminar categoría',
                timer: 3000,
                showConfirmButton: false
            });
        }
    }
};

const handleSubmit = async () => {
    try {
        const url = mode.value === 'create'
            ? '/api/categorias'
            : `/api/categorias/${currentCategoria.id}`;

        const method = mode.value === 'create' ? 'post' : 'put';
        await axios[method](url, currentCategoria);

        await loadCategorias();

        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: `Categoría ${mode.value === 'create' ? 'creada' : 'actualizada'} correctamente`,
            timer: 3000,
            showConfirmButton: false
        });

        $("#largeModal").modal('hide');
        resetForm();
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al guardar categoría',
            timer: 3000,
            showConfirmButton: false
        });
    }
};

const resetForm = () => {
    mode.value = 'create';
    Object.assign(currentCategoria, defaultCategoria);
};

const inicializarDataTable = () => {
    const tabla = "#categorias";

    if ($.fn.DataTable.isDataTable(tabla)) {
        const table = $(tabla).DataTable();
        table.clear().rows.add(categorias.value).draw();
    } else {
        $(tabla).DataTable({
            paging: true,
            pageLength: 10,
            lengthChange: false,
            searching: true,
            ordering: true,
            data: categorias.value,
            columns: [
                { data: 'nombre' },
                { data: 'tipo' },
                { data: 'edad_maxima' },
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
    const tabla = "#categorias";
    const tbody = $(tabla + ' tbody');

    tbody.off('click', '.btnEditar');
    tbody.off('click', '.btnEliminar');

    tbody.on('click', '.btnEditar', function () {
        const categoriaId = $(this).data('id');
        const categoria = categorias.value.find(c => c.id === categoriaId);
        if (categoria) editCategoria(categoria);
    });

    tbody.on('click', '.btnEliminar', function () {
        const categoriaId = $(this).data('id');
        deleteCategoria(categoriaId);
    });
}

onMounted(() => {
    loadCategorias();
});
</script>
<template>
    <section class="content contact">
        <div class="container-fluid">
            <div class="block-header">
                <div class="row clearfix">
                    <div class="col-lg-5 col-md-5 col-sm-12">
                        <h2>Administración de Usuarios</h2>
                    </div>
                    <div class="col-lg-7 col-md-7 col-sm-12">
                        <ul class="breadcrumb float-md-right padding-0">
                            <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Administración</a></li>
                            <li class="breadcrumb-item active">Usuarios</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="row clearfix">
                <div class="col-lg-12">
                    <div class="tab-content">
                        <!-- Listado de Usuarios -->
                        <div class="tab-pane active" id="List">
                            <div class="card">
                                <div class="header">
                                    <ul class="header-dropdown">
                                        <li class="nav-item" v-if="authStore.isAdmin"><a href="#largeModal"
                                                data-toggle="modal" data-target="#largeModal"><i
                                                    class="zmdi zmdi-plus-circle zmdi-hc-3x"></i></a></li>
                                    </ul>
                                </div>
                                <div class="body">
                                    <div class="table-responsive">
                                        <table id="usuarios"
                                            class="table table-bordered table-striped table-hover js-basic-example dataTable">
                                            <thead>
                                                <tr>
                                                    <th>Nombre</th>
                                                    <th>Email</th>
                                                    <th>Rol</th>
                                                    <th>Fecha Nacimiento</th>
                                                    <th>Género</th>
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
    <div class="modal fade" id="largeModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="largeModalLabel">{{ mode === 'create' ? 'Nuevo Usuario' : 'Editar Usuario' }}
                    </h4>
                </div>
                <form @submit.prevent="handleSubmit">
                    <div class="modal-body">
                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Nombre Completo</label>
                                    <input type="text" class="form-control" v-model="currentUser.name" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" v-model="currentUser.email" required>
                                </div>
                            </div>

                            <div class="col-md-6" v-if="mode === 'create'">
                                <div class="form-group">
                                    <label>Contraseña</label>
                                    <input type="password" class="form-control" v-model="currentUser.password"
                                        :required="mode === 'create'">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Rol</label>
                                    <select class="form-control" v-model="currentUser.role.id" required>
                                        <option v-for="role in roles" :value="role.id" :key="role.id">
                                            {{ role.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha de Nacimiento</label>
                                    <input type="date" class="form-control" v-model="currentUser.fecha_nacimiento">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Género</label>
                                    <select class="form-control" v-model="currentUser.genero">
                                        <option value="masculino">Masculino</option>
                                        <option value="femenino">Femenino</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6" v-if="currentUser.role.id === 3">
                                <div class="form-group">
                                    <label>Equipo</label>
                                    <select class="form-control" v-model="currentUser.equipo.id">
                                        <option v-for="equipo in equipos" :value="equipo.id" :key="equipo.id">
                                            {{ equipo.nombre }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6" v-if="currentUser.role.id === 4">
                                <div class="form-group">
                                    <label>Licencia</label>
                                    <input type="text" class="form-control" v-model="currentUser.licencia">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-default btn-round waves-effect">{{ mode === 'create' ?
                            'Crear Usuario' : 'Actualizar Usuario' }}</button>
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

const users = ref([]);
const roles = ref([]);
const equipos = ref([]);
const mode = ref('create');
const authStore = useAuthStore();

const defaultUser = {
    name: '',
    email: '',
    password: '',
    role: {},
    fecha_nacimiento: '',
    genero: '',
    equipo: {},

};

const currentUser = reactive({ ...defaultUser });

// Métodos
const loadUsers = async () => {
    try {
        const response = await axios.get('/api/users');
        users.value = response.data.data;
        inicializarDataTable();
    } catch (error) {
        console.error(error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al cargar usuarios',
            timer: 3000,
            showConfirmButton: false
        });
    }
};

const loadRoles = async () => {
    try {
        const response = await axios.get('/api/roles');
        roles.value = response.data.data;
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al cargar roles',
            timer: 3000,
            showConfirmButton: false
        });
    }
};

const loadEquipos = async () => {
    try {
        const response = await axios.get('/api/equipo');
        equipos.value = response.data.data;
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al cargar equipos',
            timer: 3000,
            showConfirmButton: false
        });
    }
};

const editUser = (user) => {
    mode.value = 'edit';
    Object.assign(currentUser, user);
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const deleteUser = async (id) => {
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
            await axios.delete(`/api/users/${id}`);
            await loadUsers();
            Swal.fire({
                icon: 'success',
                title: '¡Eliminado!',
                text: 'Usuario eliminado correctamente',
                timer: 3000,
                showConfirmButton: false
            });
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al eliminar usuario',
                timer: 3000,
                showConfirmButton: false
            });
        }
    }
};

const handleSubmit = async () => {
    try {
        const url = mode.value === 'create'
            ? '/api/users'
            : `/api/users/${currentUser.id}`;

        const method = mode.value === 'create' ? 'post' : 'put';
        await axios[method](url, {
            ...currentUser,
            role_id: currentUser.role.id,
            equipo_id: currentUser.equipo.id,
            password_confirmation: currentUser.password
        });

        await loadUsers();

        Swal.fire({
            icon: 'success',
            title: '¡Éxito!',
            text: `Usuario ${mode.value === 'create' ? 'creado' : 'actualizado'} correctamente`,
            timer: 3000,
            showConfirmButton: false
        });

        $("#largeModal").modal('hide');
        resetForm();
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error al guardar usuario',
            timer: 3000,
            showConfirmButton: false
        });
    }
};

const resetForm = () => {
    mode.value = 'create';
    Object.assign(currentUser, defaultUser);
};

const getRoleName = (roleId) => {
    const role = roles.value.find(r => r.id === roleId);
    return role ? role.name : 'Desconocido';
};

const getRoleBadge = (roleId) => {
    const badges = {
        1: 'primary',   // Admin
        2: 'success',   // Entrenador
        3: 'info',      // Jugador
        4: 'warning'    // Arbitro
    };
    return badges[roleId] || 'secondary';
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-ES');
};

const inicializarDataTable = () => {
    const tabla = "#usuarios";

    if ($.fn.DataTable.isDataTable(tabla)) {
        const table = $(tabla).DataTable();
        table.clear().rows.add(users.value).draw();
    } else {
        $(tabla).DataTable({
            paging: true,
            pageLength: 10,
            lengthChange: false,
            searching: true,
            ordering: true,
            data: users.value,
            columns: [
                {
                    data: null,
                    render: (data, type, row) => `<img src="${row.avatar || '/assets/images/xs/avatar1.jpg'}" class="rounded-circle avatar" alt="Avatar"> ${row.name}`
                },
                { data: 'email' },
                {
                    data: null,
                    render: (data, type, row) => `<span class="badge badge-${getRoleBadge(row.role.id)}">${getRoleName(row.role.id)}</span>`
                },
                {
                    data: 'fecha_nacimiento',
                    render: (data, type, row) => formatDate(data)
                },
                { data: 'genero', defaultContent: 'N/A' },
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
                        }
                        else {
                            return '';
                        }
                    }
                }
            ]
        });
    }
    attachDataTableEvents();
};

const attachDataTableEvents = () => {
    const tabla = "#usuarios";
    const tbody = $(tabla + ' tbody');

    // Limpiar eventos previos para evitar duplicados
    tbody.off('click', '.btnEditar');
    tbody.off('click', '.btnEliminar');

    // Evento para editar
    tbody.on('click', '.btnEditar', function () {
        const userId = $(this).data('id');
        const user = users.value.find(u => u.id === userId);
        if (user) editUser(user);
    });

    // Evento para eliminar
    tbody.on('click', '.btnEliminar', function () {
        const userId = $(this).data('id');
        deleteUser(userId);
    });
}

// Ciclo de vida
onMounted(() => {
    loadRoles();
    loadEquipos();
    loadUsers();
});
</script>
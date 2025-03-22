<template>
  <div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-lg bg-white rounded-xl shadow-md p-8">
      <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-2">Registro de Usuario</h1>
        <p class="text-gray-600">Complete los campos requeridos</p>
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-5">
        <!-- Fila 1: Nombre Completo -->
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-2">Nombre completo *</label>
          <input v-model="form.name" class="input-field" :class="{ 'input-error': errors.name }"
            placeholder="Ej: Juan Pérez">
          <span v-if="errors.name" class="error-message">{{ errors.name[0] }}</span>
        </div>

        <!-- Fila 2: Correo y Fecha Nacimiento -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Correo *</label>
            <input v-model="form.email" type="email" class="input-field" :class="{ 'input-error': errors.email }"
              placeholder="ejemplo@email.com">
            <span v-if="errors.email" class="error-message">{{ errors.email[0] }}</span>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Nacimiento *</label>
            <input v-model="form.fecha_nacimiento" type="date" class="input-field"
              :class="{ 'input-error': errors.fecha_nacimiento }">
            <span v-if="errors.fecha_nacimiento" class="error-message">{{ errors.fecha_nacimiento[0] }}</span>
          </div>
        </div>

        <!-- Fila 3: Género y Rol -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Género</label>
            <select v-model="form.genero" class="input-field">
              <option value="masculino">Masculino</option>
              <option value="femenino">Femenino</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Rol *</label>
            <select v-model="form.role_id" class="input-field" :class="{ 'input-error': errors.role_id }">
              <option v-for="role in roles" :value="role.id">{{ role.name }}</option>
            </select>
            <span v-if="errors.role_id" class="error-message">{{ errors.role_id[0] }}</span>
          </div>
        </div>

        <!-- Fila 4: Contraseñas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Contraseña *</label>
            <input v-model="form.password" type="password" class="input-field"
              :class="{ 'input-error': errors.password }" placeholder="Mín. 8 caracteres">
            <span v-if="errors.password" class="error-message">{{ errors.password[0] }}</span>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Confirmar *</label>
            <input v-model="form.password_confirmation" type="password" class="input-field"
              placeholder="Repite tu contraseña">
          </div>
        </div>

        <!-- Botón de Registro -->
        <button type="submit"
          class="w-full bg-blue-600 text-white py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors"
          :disabled="loading">
          <span v-if="!loading">Crear Cuenta</span>
          <span v-else class="flex items-center justify-center gap-2">
            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
              </path>
            </svg>
            Procesando...
          </span>
        </button>

        <!-- Enlace Login -->
        <p class="text-center text-sm text-gray-600 mt-4">
          ¿Ya tienes cuenta?
          <router-link to="/login" class="text-blue-600 hover:text-blue-800 font-medium">
            Inicia sesión aquí
          </router-link>
        </p>
      </form>
    </div>
  </div>
</template>

<script>
import Swal from 'sweetalert2';
import axios from 'axios';

export default {
  data() {
    return {
      form: {
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
        role_id: '',
        fecha_nacimiento: '',
        genero: 'masculino'
      },
      roles: [],
      errors: {},
      loading: false
    }
  },
  async created() {
    await this.fetchRoles();
  },
  methods: {
    async fetchRoles() {
      try {
        const response = await axios.get('/api/roles');
        this.roles = response.data.data;
        if (this.roles.length > 0) {
          this.form.role_id = this.roles[0].id;
        }
      } catch (error) {
        await Swal.fire({
            icon: 'error',
            title: "Oops...",
            text: 'Roles no se pudieron obtener',
            showConfirmButton: false,
            timer: 2000
          });
      }
    },
    methods: {
      async handleSubmit() {
        this.loading = true;
        this.errors = {};

        try {
          const response = await axios.post('/api/register', this.form);

          await Swal.fire({
            icon: 'success',
            title: '¡Registro exitoso!',
            text: 'Tu cuenta ha sido creada correctamente',
            showConfirmButton: false,
            timer: 2000
          });

          if (response.data.redirect) {
            this.$router.push(response.data.redirect);
          }

        } catch (error) {
          if (error.response?.status === 422) {
            this.errors = error.response.data.errors;
          } else {
            await Swal.fire({
              icon: 'error',
              title: 'Error en el registro',
              text: error.response?.data.message || 'Ocurrió un error inesperado',
              confirmButtonColor: '#3085d6',
            });
          }
        } finally {
          this.loading = false;
        }
      }
    }
  }
}
</script>
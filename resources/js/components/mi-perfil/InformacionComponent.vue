<template>
    <section class="content">
      <div class="container">
        <div class="block-header">
          <div class="row clearfix">
            <div class="col-lg-5 col-md-5 col-sm-12">
              <h2>Mi Perfil <small>Información de Cuenta</small></h2>
            </div>
            <div class="col-lg-7 col-md-7 col-sm-12">
              <ul class="breadcrumb float-md-right padding-0">
                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i></a></li>
                <li class="breadcrumb-item"><a href="javascript:void(0);">Mi Perfil</a></li>
                <li class="breadcrumb-item active">Información de Cuenta</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="row clearfix">
          <div class="col-lg-12">
            <div class="card">
              <div class="header">Actualizar Información</div>
              <div class="body">
                <div class="row clearfix">
                  <div class="col-sm-12">
                    <form v-if="authStore.isAuthenticated" @submit.prevent="updateProfile" class="mt-4">
                      <div class="form-group">
                        <label for="name">Nombre</label>
                        <input id="name" name="name" type="text" class="form-control" v-model="profileData.name" required
                          autofocus autocomplete="name" />
                      </div>
  
                      <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email" class="form-control" v-model="profileData.email" required
                          autocomplete="username" />
                      </div>
  
                      <div class="d-flex align-items-center">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row clearfix">
          <div class="col-lg-12">
            <div class="card">
              <div class="header">Cambiar Contraseña</div>
              <div class="body">
                <div class="row clearfix">
                  <div class="col-sm-12">
                    <form v-if="authStore.isAuthenticated" @submit.prevent="updatePassword" class="mt-4">
                      <div class="form-group">
                        <label for="update_password_current_password">Contraseña Actual</label>
                        <input id="update_password_current_password" name="current_password" type="password"
                          class="form-control" v-model="passwordData.current_password" autocomplete="current-password" />
                      </div>
  
                      <div class="form-group">
                        <label for="update_password_password">Nueva Contraseña</label>
                        <input id="update_password_password" name="password" type="password" class="form-control"
                          v-model="passwordData.password" autocomplete="new-password" />
                      </div>
  
                      <div class="form-group">
                        <label for="update_password_password_confirmation">Confirmar Contraseña</label>
                        <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                          class="form-control" v-model="passwordData.password_confirmation" autocomplete="new-password" />
                      </div>
  
                      <div class="d-flex align-items-center">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import { useAuthStore } from '@/stores/auth';
  import axios from 'axios';
  import Swal from 'sweetalert2';
  
  const authStore = useAuthStore();
  
  const profileData = ref({ name: authStore.user.name, email: authStore.user.email });
  const passwordData = ref({ current_password: '', password: '', password_confirmation: '' });
  
  const updateProfile = async () => {
    try {
      const response = await axios.patch('/api/profile', profileData.value);
  
      if (response.status === 200) {
        authStore.user = { ...authStore.user, ...profileData.value };
        Swal.fire({
          icon: 'success',
          title: 'Éxito',
          text: 'Perfil actualizado correctamente.',
        });
      }
    } catch (error) {
      console.error('Error updating profile:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Error al actualizar el perfil.',
      });
    }
  };
  
  const updatePassword = async () => {
    try {
      const response = await axios.put('/api/password', passwordData.value);
  
      if (response.status === 200) {
        Swal.fire({
          icon: 'success',
          title: 'Éxito',
          text: 'Contraseña actualizada correctamente.',
        });
        // Resetea el formulario de contraseña
        passwordData.value = { current_password: '', password: '', password_confirmation: '' };
      }
    } catch (error) {
      console.error('Error updating password:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Error al actualizar la contraseña.',
      });
    }
  };
  </script>
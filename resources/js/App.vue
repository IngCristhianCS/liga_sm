<template>
    
    <AppNavigation />
    <div>
      <router-view />
    </div>
  </template>
  
  <script>
  import { inject } from 'vue' // Añadir esta línea
  
  export default {
    name: 'App',
    setup() {
      const isLoading = inject('isLoading') // Ahora funcionará correctamente
      return { isLoading }
    }
  }
  </script>
  
  <style>
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.3s;
  }
  
  .fade-enter-from,
  .fade-leave-to {
    opacity: 0;
  }
  </style>
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'
import Navigation from './components/NavegacionComponent.vue'

// Configuración de rutas con carga perezosa
const router = createRouter({
  history: createWebHistory(),
  routes: [
    {
      path: '/clasificacion',
      name: 'Clasificacion',
      component: () => import(/* webpackChunkName: "clasificacion" */ './components/clasificacion/ClasificacionComponent.vue')
    },
    {
      path: '/registro',
      name: 'Registro',
      component: () => import(/* webpackChunkName: "registro" */ './components/RegisterComponent.vue')
    },
    {
      path: '/resultados',
      name: 'Resultados',
      component: () => import(/* webpackChunkName: "resultados" */ './components/resultados/ResultadosComponent.vue')
    },
    { 
      path: '/', 
      redirect: '/clasificacion' 
    },

  ]
})

// Crear instancia de Vue
const app = createApp(App)
const pinia = createPinia()

app.use(pinia)
// Registrar componente de navegación globalmente
app.component('AppNavigation', Navigation)

app.use(router)
app.mount('#app')
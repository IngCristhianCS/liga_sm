import './bootstrap.js'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'
import Navigation from './components/global/NavegacionComponent.vue'
import Loader from './components/global/LoaderComponent.vue'
import { useAuthStore } from './stores/auth'

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
      path: '/usuarios',
      name: 'Usuarios',
      component: () => import(/* webpackChunkName: "usuarios" */ './components/usuarios/UsuariosComponent.vue')
    },{
      path: '/calendario',
      name: 'Calendario',
      component: () => import(/* webpackChunkName: "calendario" */ './components/calendario/CalendarioComponent.vue')
    },        
    {
      path: '/mi-equipo',
      name: 'Mi equipo',
      component: () => import(/* webpackChunkName: "mi-equipo" */ './components/usuarios/UsuariosComponent.vue')
    },
    {
      path: '/mi-perfil',
      name: 'Mi perfil',
      component: () => import(/* webpackChunkName: "mi-perfil" */ './components/mi-perfil/InformacionComponent.vue')
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
app.component('AppLoader', Loader)

// Inicialización del store
const authStore = useAuthStore()
authStore.checkAuth()

app.use(router)
app.mount('#app')
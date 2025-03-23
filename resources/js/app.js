import { createApp, ref} from 'vue'
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
      component: () => import(/* webpackChunkName: "clasificacion" */ './components/ClasificacionComponent.vue')
    },
    {
      path: '/registro',
      name: 'Registro',
      component: () => import(/* webpackChunkName: "registro" */ './components/RegisterComponent.vue')
    },
    {
      path: '/resultados',
      name: 'Resultados',
      component: () => import(/* webpackChunkName: "resultados" */ './components/ResultadosComponent.vue')
    },
    { 
      path: '/', 
      redirect: '/clasificacion' 
    },

  ]
})

// Crear instancia de Vue
const app = createApp(App)

// Registrar componente de navegación globalmente
app.component('AppNavigation', Navigation)

// Manejo de errores de carga de componentes
router.onError((error) => {
  console.error('Error loading component:', error)
  router.push({ name: 'NotFound' })
})

// Estado de carga global
app.config.globalProperties.$isLoading = ref(false)

router.beforeEach(() => {
  app.config.globalProperties.$isLoading.value = true
})

router.afterEach(() => {
  app.config.globalProperties.$isLoading.value = false
})

app.use(router)
app.mount('#app')
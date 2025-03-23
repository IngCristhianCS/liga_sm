import "/resources/js/bootstrap.js";

import { createApp } from 'vue'
import ClasificacionComponent from "/resources/js/components/ClasificacionComponent.vue";
import RegisterComponent from "/resources/js/components/RegisterComponent.vue";
import ResultadosComponent from "/resources/js/components/ResultadosComponent.vue";

// Crear instancia de Vue
const app = createApp({})

// Registrar componentes
app.component('clasificacion-component', ClasificacionComponent);
app.component('register-component', RegisterComponent);
app.component('resultados-component', ResultadosComponent)
app.mount('#app')


import "/resources/js/bootstrap.js";

import { createApp } from 'vue'
import ClasificacionComponent from "/resources/js/components/ClasificacionComponent.vue";
import RegisterComponent from "/resources/js/components/RegisterComponent.vue";
import Swal from "/node_modules/.vite/deps/sweetalert2.js?v=4969aabe";

// Configuración de SweetAlert
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer)
        toast.addEventListener('mouseleave', Swal.resumeTimer)
    }
})



// Crear instancia de Vue
const app = createApp({})

// Registrar componentes
app.component('clasificacion-component', ClasificacionComponent);
app.component('register-component', RegisterComponent);

app.mount('#app')

console.log("se monto app")


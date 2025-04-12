<template>
  <div class="modal fade" id="equipoModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title">{{ titulo }}</h4>
        </div>
        <form @submit.prevent="handleSubmit">
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Nombre</label>
                  <input type="text" class="form-control" v-model="formData.nombre" required>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Categoría</label>
                  <select class="form-control" v-model="formData.categoria_id" required>
                    <option v-for="categoria in categorias" :key="categoria.id" :value="categoria.id">
                      {{ categoria.nombre }}
                    </option>
                  </select>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Entrenador</label>
                  <select class="form-control" v-model="formData.entrenador_id" required>
                    <option v-for="entrenador in entrenadores" :key="entrenador.id" :value="entrenador.id">
                      {{ entrenador.name }}
                    </option>
                  </select>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Imagen</label>
                  <input type="file" class="form-control" @change="cargarImagen">
                </div>
              </div>

              <div class="col-md-12" v-if="formData.imagen">
                <img :src="formData.imagen" class="img-thumbnail" width="100" alt="Vista previa">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="cancelar">Cancelar</button>
            <button type="submit" class="btn btn-primary">
              {{ textoBoton }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, computed, watch } from 'vue';

/**
 * @typedef {Object} Equipo
 * @property {number} [id] - ID único del equipo
 * @property {string} nombre - Nombre del equipo
 * @property {string|null} imagen - Imagen en formato Base64
 * @property {number} categoria_id - ID de la categoría asociada
 * @property {number} entrenador_id - ID del entrenador asignado
 */

/**
 * @typedef {Object} Categoria
 * @property {number} id - ID único de la categoría
 * @property {string} nombre - Nombre de la categoría
 */

/**
 * @typedef {Object} Entrenador
 * @property {number} id - ID único del entrenador
 * @property {string} name - Nombre completo del entrenador
 */

const props = defineProps({
  /** 
   * Modo de operación del formulario
   * @type {'create'|'edit'}
   * @default 'create'
   */
  mode: {
    type: String,
    default: 'create',
    validator: (value) => ['create', 'edit'].includes(value)
  },

  /** 
   * Datos del equipo a editar
   * @type {Equipo}
   * @example
   * {
   *   id: 1,
   *   nombre: 'Equipo Ejemplo',
   *   imagen: 'data:image/png;base64,iVBORw0KGg...',
   *   categoria_id: 2,
   *   entrenador_id: 3
   * }
   */
  currentEquipo: {
    type: Object,
    default: () => ({
      id: null,
      nombre: '',
      imagen: null,
      categoria_id: null,
      entrenador_id: null
    })
  },

  /** 
   * Lista de categorías disponibles
   * @type {Array<Categoria>}
   */
  categorias: {
    type: Array,
    default: () => []
  },

  /** 
   * Lista de entrenadores disponibles
   * @type {Array<Entrenador>}
   */
  entrenadores: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits([
  /** 
   * Evento al enviar el formulario
   * @param {Equipo} formData - Datos del equipo en formato válido
   */
  'submit',

  /** 
   * Evento al cancelar la operación
   */
  'cancel'
]);

/** 
 * Datos del formulario reactivo
 * @type {Equipo}
 */
const formData = reactive({ ...props.currentEquipo });

/** 
 * Título del modal basado en el modo
 * @type {import('vue').ComputedRef<string>}
 */
const titulo = computed(() =>
  props.mode === 'create' ? 'Nuevo Equipo' : 'Editar Equipo'
);

/** 
 * Texto del botón de acción principal
 * @type {import('vue').ComputedRef<string>}
 */
const textoBoton = computed(() =>
  props.mode === 'create' ? 'Guardar Equipo' : 'Actualizar Equipo'
);

/**
 * Maneja la carga y conversión de imágenes a Base64
 * @param {Event} event - Evento de cambio del input file
 * @returns {void}
 */
const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (!file) return;

  /** 
   * Valida el tipo de archivo
   * @type {RegExp}
   */
  const validTypes = /image\/(jpeg|png|gif|webp)/;
  if (!validTypes.test(file.type)) {
    Notification.error('Formato de imagen no válido');
    return;
  }

  /** 
   * Límite de tamaño en MB
   * @constant {number}
   */
  const MAX_SIZE_MB = 2;
  if (file.size > MAX_SIZE_MB * 1024 * 1024) {
    Notification.error(`El tamaño máximo permitido es ${MAX_SIZE_MB}MB`);
    return;
  }

  const reader = new FileReader();
  reader.onload = (e) => {
    /** 
     * Resultado en Base64
     * @type {string}
     */
    formData.imagen = e.target.result;
  };
  reader.readAsDataURL(file);
};

/**
 * Envía los datos del formulario
 * @returns {void}
 */
const handleSubmit = () => {
  /** 
   * Validación básica de campos requeridos
   */
  if (!formData.nombre || !formData.categoria_id || !formData.entrenador_id) {
    Notification.error('Complete los campos requeridos');
    return;
  }

  emit('submit', formData);
};

/**
 * Cancela la operación y resetea el formulario
 * @returns {void}
 */
const handleCancel = () => {
  emit('cancel');
  resetForm();
};

/**
 * Restablece los valores del formulario
 * @returns {void}
 */
const resetForm = () => {
  Object.assign(formData, props.currentEquipo);
};

// Watchers
watch(() => props.currentEquipo, (newVal) => {
  Object.assign(formData, newVal);
}, { deep: true, immediate: true });
</script>
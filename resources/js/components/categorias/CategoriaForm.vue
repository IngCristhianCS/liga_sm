<!-- components/CategoriaForm.vue -->
<template>
  <div class="modal fade" id="largeModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="title" id="largeModalLabel">
            {{ mode === 'create' ? 'Nueva Categoría' : 'Editar Categoría' }}
          </h4>
        </div>
        <form @submit.prevent="handleSubmit">
          <div class="modal-body">
            <div class="row clearfix">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Nombre</label>
                  <input type="text" id="nombre" class="form-control" v-model="formData.nombre"
                    @input="validateSingleField('nombre')" required>
                  <div class="invalid-feedback">
                    El nombre debe tener entre 1 y 50 caracteres
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Tipo</label>
                  <select id="tipo" class="form-control" v-model="formData.tipo" @change="validateSingleField('tipo')"
                    required>
                    <option value="">Seleccione un tipo</option>
                    <option value="femenil">Femenil</option>
                    <option value="varonil">Varonil</option>
                    <option value="infantil">Infantil</option>
                  </select>
                  <div class="invalid-feedback">
                    Debe seleccionar un tipo válido
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Edad Máxima</label>
                  <input type="number" id="edad_maxima" class="form-control"
                    :class="{ 'is-invalid': apiErrors.edad_maxima }" v-model="formData.edad_maxima"
                    @input="validateSingleField('edad_maxima')">
                  <div class="invalid-feedback">
                    {{ apiErrors.edad_maxima ? apiErrors.edad_maxima[0] : 'La edad debe ser un número positivo' }}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-default btn-round waves-effect">
              {{ mode === 'create' ? 'Crear Categoría' : 'Actualizar Categoría' }}
            </button>
            <button type="button" class="btn btn-danger btn-simple btn-round waves-effect" data-dismiss="modal"
              @click="resetForm">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch, ref } from 'vue';
import { RegexUtils, validateForm, validateField } from '@/utils/regex-util';

/**
 * @typedef {Object} Categoria
 * @property {number} [id]
 * @property {string} nombre
 * @property {string} tipo
 * @property {number} edad_maxima
 */

const props = defineProps({
  /** @type {'create'|'edit'} */
  mode: {
    type: String,
    default: 'create'
  },
  /** @type {Categoria} */
  currentCategoria: {
    type: Object,
    default: () => ({}),
  }
});

const emit = defineEmits([
  /** @param {Categoria} formData */
  'submit',
  'cancel'
]);

const validation = {
  nombre: {
    valid: null,
    regex: /^[A-Za-zá-úÁ-ÚñÑ0-9 ]{1,50}$/
  },
  tipo: {
    valid: null,
    regex: /^(femenil|varonil|infantil)$/
  },
  edad_maxima: {
    valid: null,
    regex: RegexUtils.integer,
    optional: true
  }
};

/** @type {Categoria} */
const formData = reactive({ ...props.currentCategoria });

// Add watch for currentCategoria changes
watch(() => props.currentCategoria, (newVal) => {
  Object.assign(formData, newVal);
}, { deep: true });

const apiErrors = ref({});

const handleSubmit = () => {
  apiErrors.value = {};
  if (validateForm(validation, formData)) {
    emit('submit', formData);
  }
};

// Add error handler method
const handleApiErrors = (errors) => {
  apiErrors.value = errors;
};

defineExpose({
  handleApiErrors
});

const validateSingleField = (field) => {
  validateField(field, validation, formData);
};
</script>
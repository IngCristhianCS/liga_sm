<template>
  <div class="modal fade" id="largeModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="title" id="largeModalLabel">
            {{ mode === 'create' ? 'Nueva Jornada' : 'Editar Jornada' }}
          </h4>
        </div>
        <form @submit.prevent="handleSubmit">
          <div class="modal-body">
            <div class="row clearfix">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Número de Jornada</label>
                  <input type="number" class="form-control"
                    :class="{ 'is-invalid': !validation.numero.valid && validation.numero.valid !== null }"
                    v-model="formData.numero" @input="validateSingleField('numero')" min="1" required>
                  <div class="invalid-feedback">
                    {{ apiErrors.numero?.[0] || 'Ingrese un número válido' }}
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Torneo</label>
                  <select class="form-control"
                    :class="{ 'is-invalid': !validation.torneo_id.valid && validation.torneo_id.valid !== null }"
                    v-model="formData.torneo_id" @change="validateSingleField('torneo_id')" required>
                    <option value="">-- Seleccionar Torneo --</option>
                    <option v-for="torneo in torneos" :key="torneo.id" :value="torneo.id">
                      {{ torneo.nombre }}
                    </option>
                  </select>
                  <div class="invalid-feedback">
                    {{ apiErrors.torneo_id?.[0] || 'Seleccione un torneo válido' }}
                  </div>
                </div>
              </div>
            </div>
            <div class="row clearfix">
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Fecha de Inicio</label>
                  <input type="date" class="form-control"
                    :class="{ 'is-invalid': !validation.fecha_inicio.valid && validation.fecha_inicio.valid !== null }"
                    v-model="formData.fecha_inicio" @input="validateSingleField('fecha_inicio')" required>
                  <div class="invalid-feedback">
                    {{ apiErrors.fecha_inicio?.[0] || 'Ingrese una fecha válida' }}
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="form-group">
                  <label>Fecha de Fin</label>
                  <input type="date" class="form-control"
                    :class="{ 'is-invalid': !validation.fecha_fin.valid && validation.fecha_fin.valid !== null }"
                    v-model="formData.fecha_fin" @input="validateSingleField('fecha_fin')" required>
                  <div class="invalid-feedback">
                    {{ apiErrors.fecha_fin?.[0] || 'Ingrese una fecha válida' }}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-default btn-round waves-effect">
              {{ mode === 'create' ? 'Crear Jornada' : 'Actualizar Jornada' }}
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
import { ref, reactive, watch } from 'vue';
import { RegexUtils, validateField as validateFieldUtil } from '@/utils/regex-util';

const props = defineProps({
  mode: {
    type: String,
    default: 'create'
  },
  jornada: {
    type: Object,
    default: () => ({})
  },
  torneos: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['submit', 'cancel']);

// Form data
const formData = reactive({
  numero: '',
  torneo_id: '',
  fecha_inicio: '',
  fecha_fin: ''
});

// Validation state
const validation = reactive({
  numero: { valid: null, regex: RegexUtils.number, optional: false },
  torneo_id: { valid: null, optional: false },
  fecha_inicio: { valid: null, optional: false },
  fecha_fin: { valid: null, optional: false }
});

const apiErrors = ref({});

// Watch for changes in the jornada prop to update the form
watch(() => props.jornada, (newJornada) => {
  if (newJornada) {
    formData.numero = newJornada.numero || '';
    formData.torneo_id = newJornada.torneo_id || '';
    formData.fecha_inicio = newJornada.fecha_inicio || '';
    formData.fecha_fin = newJornada.fecha_fin || '';
  }
}, { deep: true, immediate: true });

// Validation using RegexUtils
const validateSingleField = (field) => {
  return validateFieldUtil(field, validation, formData);
};

const validateAllFields = () => {
  let isValid = true;

  Object.keys(validation).forEach(field => {
    const fieldValid = validateSingleField(field);
    if (!fieldValid) {
      isValid = false;
    }
  });

  // Additional validation for dates
  if (formData.fecha_inicio && formData.fecha_fin &&
    new Date(formData.fecha_fin) < new Date(formData.fecha_inicio)) {
    validation.fecha_fin.valid = false;
    apiErrors.value.fecha_fin = ['La fecha de fin debe ser posterior a la fecha de inicio'];
    isValid = false;
  }

  return isValid;
};

const handleSubmit = () => {
  apiErrors.value = {};

  if (validateAllFields()) {
    emit('submit', { ...formData });
  }
};

const handleApiErrors = (errors) => {
  apiErrors.value = errors;

  // Mark fields with API errors as invalid
  Object.keys(errors).forEach(field => {
    if (validation[field]) {
      validation[field].valid = false;
    }
  });
};

// Reset form
const resetForm = () => {
  Object.keys(formData).forEach(key => {
    formData[key] = '';
  });

  Object.keys(validation).forEach(key => {
    validation[key].valid = null;
  });

  apiErrors.value = {};
  emit('cancel');
};

// Expose methods to parent component
defineExpose({
  handleApiErrors
});
</script>
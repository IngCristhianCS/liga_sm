<template>
  <div class="modal fade" id="largeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="title" id="largeModalLabel">
            {{ mode === 'create' ? 'Nuevo Egreso' : 'Editar Egreso' }}
          </h4>
        </div>
        <form @submit.prevent="handleSubmit">
          <div class="modal-body">
            <div class="row clearfix">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Fecha</label>
                  <input 
                    type="date" 
                    id="fecha"
                    class="form-control" 
                    v-model="formData.fecha"
                    @input="validateSingleField('fecha')"
                    required
                  >
                  <div class="invalid-feedback">
                    {{ apiErrors?.fecha ? apiErrors.fecha[0] : 'La fecha es requerida' }}
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Monto</label>
                  <input 
                    type="number" 
                    id="monto"
                    class="form-control" 
                    v-model="formData.monto"
                    @input="validateSingleField('monto')"
                    required
                  >
                  <div class="invalid-feedback">
                    {{ apiErrors?.monto ? apiErrors.monto[0] : 'El monto debe ser un número positivo' }}
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Tipo</label>
                  <select 
                    id="tipo"
                    class="form-control" 
                    v-model="formData.tipo"
                    @change="validateSingleField('tipo')"
                    required
                  >
                    <option value="">Seleccione un tipo</option>
                    <option value="arbitraje">Arbitraje</option>
                    <option value="mantenimiento">Mantenimiento</option>
                    <option value="organizacion">Organización</option>
                  </select>
                  <div class="invalid-feedback">
                    {{ apiErrors?.tipo ? apiErrors.tipo[0] : 'Debe seleccionar un tipo válido' }}
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Descripción</label>
                  <input 
                    type="text" 
                    id="descripcion"
                    class="form-control" 
                    v-model="formData.descripcion"
                    @input="validateSingleField('descripcion')"
                  >
                  <div class="invalid-feedback">
                    {{ apiErrors?.descripcion ? apiErrors.descripcion[0] : 'La descripción no es válida' }}
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Partido ID</label>
                  <input 
                    type="number" 
                    id="partido_id"
                    class="form-control" 
                    v-model="formData.partido_id"
                    @input="validateSingleField('partido_id')"
                  >
                  <div class="invalid-feedback">
                    {{ apiErrors?.partido_id ? apiErrors.partido_id[0] : 'El ID del partido debe ser un número válido' }}
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Torneo ID</label>
                  <input 
                    type="number" 
                    id="torneo_id"
                    class="form-control" 
                    v-model="formData.torneo_id"
                    @input="validateSingleField('torneo_id')"
                  >
                  <div class="invalid-feedback">
                    {{ apiErrors?.torneo_id ? apiErrors.torneo_id[0] : 'El ID del torneo debe ser un número válido' }}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-default btn-round waves-effect">
              {{ mode === 'create' ? 'Crear Egreso' : 'Actualizar Egreso' }}
            </button>
            <button type="button" class="btn btn-danger btn-simple btn-round waves-effect"
              data-dismiss="modal" @click="resetForm">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, watch, ref } from 'vue';
import { RegexUtils, validateForm, validateField } from '@/utils/regex-util';

const props = defineProps({
  mode: {
    type: String,
    default: 'create'
  },
  currentEgreso: {
    type: Object,
    default: () => ({})
  }
});

const emit = defineEmits(['submit', 'cancel']);

const validation = {
  fecha: { 
    valid: null, 
    regex: RegexUtils.date 
  },
  monto: { 
    valid: null, 
    regex: RegexUtils.numberPositivo 
  },
  tipo: { 
    valid: null, 
    regex: /^(arbitraje|mantenimiento|organizacion)$/ 
  },
  descripcion: { 
    valid: null, 
    regex: RegexUtils.textExtendido,
    optional: true 
  },
  partido_id: { 
    valid: null, 
    regex: RegexUtils.numberPositivo,
    optional: true 
  },
  torneo_id: { 
    valid: null, 
    regex: RegexUtils.numberPositivo,
    optional: true 
  }
};

const formData = reactive({
  fecha: '',
  monto: '',
  tipo: '',
  descripcion: '',
  partido_id: '',
  torneo_id: '',
  ...props.currentEgreso
});

const handleSubmit = () => {
  apiErrors.value = {};
  if (validateForm(validation, formData)) {
    const submitData = {
      fecha: formData.fecha,
      monto: Number(formData.monto),
      tipo: formData.tipo,
      descripcion: formData.descripcion || null,
      partido_id: formData.partido_id ? Number(formData.partido_id) : null,
      torneo_id: formData.torneo_id ? Number(formData.torneo_id) : null
    };
    emit('submit', submitData);
  }
};

const resetForm = () => emit('cancel');

const validateSingleField = (field) => {
  validateField(field, validation, formData);
};

const handleApiErrors = (errors) => {
  apiErrors.value = errors;
};

watch(() => props.currentEgreso, (newVal) => {
  Object.assign(formData, newVal);
}, { deep: true });

defineExpose({
  handleApiErrors
});
</script>
<template>
  <div class="modal fade" id="largeModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
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
                  <input type="date" id="fecha" class="form-control" v-model="formData.fecha"
                    @input="validateSingleField('fecha')" required>
                  <div class="invalid-feedback">
                    {{ apiErrors?.fecha ? apiErrors.fecha[0] : 'La fecha es requerida' }}
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Monto</label>
                  <input type="number" id="monto" class="form-control" v-model="formData.monto"
                    @input="validateSingleField('monto')" required>
                  <div class="invalid-feedback">
                    {{ apiErrors?.monto ? apiErrors.monto[0] : 'El monto debe ser un número positivo' }}
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Tipo</label>
                  <select id="tipo" class="form-control" v-model="formData.tipo" @change="validateSingleField('tipo')"
                    required>
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
                  <input type="text" id="descripcion" class="form-control" v-model="formData.descripcion"
                    @input="validateSingleField('descripcion')">
                  <div class="invalid-feedback">
                    {{ apiErrors?.descripcion ? apiErrors.descripcion[0] : 'La descripción no es válida' }}
                  </div>
                </div>
              </div>

              <!-- Torneo Selector -->
              <div class="col-md-6">
                <div class="form-group">
                  <label>Torneo</label>
                  <select id="torneo_id" class="form-control" v-model="formData.torneo_id"
                    @change="handleTorneoChange">
                    <option value="">Seleccione un torneo</option>
                    <option v-for="torneo in torneoStore.torneosCatalog" :key="torneo.id" :value="torneo.id">
                      {{ torneo.nombre }}
                    </option>
                  </select>
                  <div class="invalid-feedback">
                    {{ apiErrors?.torneo_id ? apiErrors.torneo_id[0] : 'Debe seleccionar un torneo válido' }}
                  </div>
                </div>
              </div>

              <!-- Partido Selector -->
              <div class="col-md-6">
                <div class="form-group">
                  <label>Partido</label>
                  <select id="partido_id" class="form-control" v-model="formData.partido_id"
                    @change="validateSingleField('partido_id')" :disabled="!formData.torneo_id">
                    <option value="">Seleccione un partido</option>
                    <option v-for="partido in filteredPartidos" :key="partido.id" :value="partido.id">
                      {{ formatPartidoLabel(partido) }}
                    </option>
                  </select>
                  <div class="invalid-feedback">
                    {{ apiErrors?.partido_id ? apiErrors.partido_id[0] : 'Debe seleccionar un partido válido' }}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-default btn-round waves-effect">
              {{ mode === 'create' ? 'Crear Egreso' : 'Actualizar Egreso' }}
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
import { reactive, watch, ref, computed, onMounted } from 'vue';
import { RegexUtils, validateForm, validateField } from '@/utils/regex-util';
import { useTorneoStore } from '@/stores/torneos';
import { usePartidoStore } from '@/stores/partidos';

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
const apiErrors = ref({});

// Use stores
const torneoStore = useTorneoStore();
const partidoStore = usePartidoStore();

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

// Filter partidos based on selected torneo
const filteredPartidos = computed(() => {
  if (!formData.torneo_id) return [];
  return partidoStore.partidos.filter(partido => 
    partido.torneo_id === formData.torneo_id
  );
});

// Format partido label for dropdown
const formatPartidoLabel = (partido) => {
  // Check if we have the equipo_local and equipo_visitante objects
  const localName = partido.equipo_local?.nombre || 'Equipo Local';
  const visitanteName = partido.equipo_visitante?.nombre || 'Equipo Visitante';
 
  return `${localName} vs ${visitanteName}`;
};

// Handle torneo change
const handleTorneoChange = async () => {
  validateSingleField('torneo_id');
  formData.partido_id = '';
  
  if (formData.torneo_id) {
    await partidoStore.fetchPartidosByTorneo(formData.torneo_id);
  }
};

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
    
    if (props.mode === 'edit' && props.currentEgreso.id) {
      submitData.id = props.currentEgreso.id;
    }
    
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
  
  // If we have a torneo_id, load the partidos for that torneo
  if (newVal.torneo_id) {
    partidoStore.fetchPartidosByTorneo(newVal.torneo_id);
  }
}, { deep: true });

onMounted(async () => {
  // Load torneos catalog if not already loaded
  if (torneoStore.torneosCatalog.length === 0) {
    await torneoStore.fetchTorneosCatalog();
  }
  
  // If we have a torneo_id in the form data, load partidos for that torneo
  if (formData.torneo_id) {
    await partidoStore.fetchPartidosByTorneo(formData.torneo_id);
  }
});

defineExpose({
  handleApiErrors
});
</script>
<template>
  <div class="modal fade" id="largeModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="title" id="largeModalLabel">
            {{ mode === 'create' ? 'Nuevo Partido' : 'Editar Partido' }}
          </h4>
        </div>
        <form @submit.prevent="handleSubmit">
          <div class="modal-body">
            <div class="row clearfix">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Fecha</label>
                  <input 
                    id="fecha"
                    type="date" 
                    class="form-control"
                    :class="{ 'is-invalid': !validation.fecha.valid && validation.fecha.valid !== null }" 
                    v-model="formData.fecha"
                    @input="validateSingleField('fecha')"
                    required
                  >
                  <div class="invalid-feedback">
                    {{ apiErrors.fecha?.[0] || 'La fecha es requerida' }}
                  </div>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label>Hora de inicio</label>
                  <input 
                    id="hora_inicio"
                    type="time" 
                    class="form-control"
                    :class="{ 'is-invalid': !validation.hora_inicio.valid && validation.hora_inicio.valid !== null }" 
                    v-model="formData.hora_inicio"
                    @input="validateSingleField('hora_inicio')"
                    required
                  >
                  <div class="invalid-feedback">
                    {{ apiErrors.hora_inicio?.[0] || 'La hora es requerida' }}
                  </div>
                </div>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Torneo</label>
                  <select 
                    id="torneo_id"
                    class="form-control"
                    :class="{ 'is-invalid': !validation.torneo_id.valid && validation.torneo_id.valid !== null }" 
                    v-model="formData.torneo_id"
                    @change="onTorneoChange"
                    required
                    :disabled="mode === 'update'"
                  >
                    <option value="">Seleccione un torneo</option>
                    <option v-for="torneo in torneoStore.torneos" :key="torneo.id" :value="torneo.id">
                      {{ torneo.nombre }}
                    </option>
                  </select>
                  <div class="invalid-feedback">
                    {{ apiErrors.torneo_id?.[0] || 'Debe seleccionar un torneo' }}
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Jornada</label>
                  <select 
                    id="jornada_id"
                    class="form-control"
                    :class="{ 'is-invalid': !validation.jornada_id.valid && validation.jornada_id.valid !== null }" 
                    v-model="formData.jornada_id"
                    @change="validateSingleField('jornada_id')"
                    required
                    :disabled="mode === 'update'"
                  >
                    <option value="">Seleccione una jornada</option>
                    <option v-for="jornada in jornadasByTorneo" :key="jornada.id" :value="jornada.id">
                      Jornada {{ jornada.numero }}
                    </option>
                  </select>
                  <div class="invalid-feedback">
                    {{ apiErrors.jornada_id?.[0] || 'Debe seleccionar una jornada' }}
                  </div>
                </div>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Equipo Local</label>
                  <select 
                    id="equipo_local_id"
                    class="form-control"
                    :class="{ 'is-invalid': !validation.equipo_local_id.valid && validation.equipo_local_id.valid !== null }" 
                    v-model="formData.equipo_local_id"
                    @change="validateSingleField('equipo_local_id')"
                    required
                  >
                    <option value="">Seleccione un equipo</option>
                    <option v-for="equipo in equiposByTorneo" :key="equipo.id" :value="equipo.id">
                      {{ equipo.nombre }}
                    </option>
                  </select>
                  <div class="invalid-feedback">
                    {{ apiErrors.equipo_local_id?.[0] || 'Debe seleccionar un equipo local' }}
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Equipo Visitante</label>
                  <select 
                    id="equipo_visitante_id"
                    class="form-control"
                    :class="{ 'is-invalid': !validation.equipo_visitante_id.valid && validation.equipo_visitante_id.valid !== null }" 
                    v-model="formData.equipo_visitante_id"
                    @change="validateSingleField('equipo_visitante_id')"
                    required
                  >
                    <option value="">Seleccione un equipo</option>
                    <option v-for="equipo in equiposByTorneo" :key="equipo.id" :value="equipo.id">
                      {{ equipo.nombre }}
                    </option>
                  </select>
                  <div class="invalid-feedback">
                    {{ apiErrors.equipo_visitante_id?.[0] || 'Debe seleccionar un equipo visitante diferente al local' }}
                  </div>
                </div>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Goles Equipo Local</label>
                  <input 
                    id="goles_equipo_local"
                    type="number" 
                    class="form-control"
                    :class="{ 'is-invalid': !validation.goles_equipo_local.valid && validation.goles_equipo_local.valid !== null }" 
                    v-model="formData.goles_equipo_local"
                    @input="validateSingleField('goles_equipo_local')"
                    min="0"
                  >
                  <div class="invalid-feedback">
                    {{ apiErrors.goles_equipo_local?.[0] || 'El número de goles debe ser un número válido' }}
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Goles Equipo Visitante</label>
                  <input 
                    id="goles_equipo_visitante"
                    type="number" 
                    class="form-control"
                    :class="{ 'is-invalid': !validation.goles_equipo_visitante.valid && validation.goles_equipo_visitante.valid !== null }" 
                    v-model="formData.goles_equipo_visitante"
                    @input="validateSingleField('goles_equipo_visitante')"
                    min="0"
                  >
                  <div class="invalid-feedback">
                    {{ apiErrors.goles_equipo_visitante?.[0] || 'El número de goles debe ser un número válido' }}
                  </div>
                </div>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Ubicación</label>
                  <select 
                    id="ubicacion_id"
                    class="form-control"
                    :class="{ 'is-invalid': !validation.ubicacion_id.valid && validation.ubicacion_id.valid !== null }" 
                    v-model="formData.ubicacion_id"
                    @change="validateSingleField('ubicacion_id')"
                    required
                  >
                    <option value="">Seleccione una ubicación</option>
                    <option v-for="ubicacion in ubicacionStore.ubicaciones" :key="ubicacion.id" :value="ubicacion.id">
                      {{ ubicacion.nombre }}
                    </option>
                  </select>
                  <div class="invalid-feedback">
                    {{ apiErrors.ubicacion_id?.[0] || 'Debe seleccionar una ubicación' }}
                  </div>
                </div>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Árbitro Principal</label>
                  <select 
                    id="arbitro_principal_id"
                    class="form-control"
                    :class="{ 'is-invalid': !validation.arbitro_principal_id.valid && validation.arbitro_principal_id.valid !== null }" 
                    v-model="formData.arbitro_principal_id"
                    @change="validateSingleField('arbitro_principal_id')"
                  >
                    <option value="">Seleccione un árbitro</option>
                    <option v-for="arbitro in arbitroStore.arbitros" :key="arbitro.id" :value="arbitro.id">
                      {{ arbitro.nombre }}
                    </option>
                  </select>
                  <div class="invalid-feedback">
                    {{ apiErrors.arbitro_principal_id?.[0] || 'Seleccione un árbitro válido' }}
                  </div>
                </div>
              </div>

              <div class="col-md-6">
                <div class="form-group">
                  <label>Árbitro Asistente 1</label>
                  <select 
                    id="arbitro_asistente1_id"
                    class="form-control"
                    :class="{ 'is-invalid': !validation.arbitro_asistente1_id.valid && validation.arbitro_asistente1_id.valid !== null }" 
                    v-model="formData.arbitro_asistente1_id"
                    @change="validateSingleField('arbitro_asistente1_id')"
                  >
                    <option value="">Seleccione un árbitro</option>
                    <option v-for="arbitro in arbitroStore.arbitros" :key="arbitro.id" :value="arbitro.id">
                      {{ arbitro.nombre }}
                    </option>
                  </select>
                  <div class="invalid-feedback">
                    {{ apiErrors.arbitro_asistente1_id?.[0] || 'Seleccione un árbitro válido' }}
                  </div>
                </div>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Árbitro Asistente 2</label>
                  <select 
                    id="arbitro_asistente2_id"
                    class="form-control"
                    :class="{ 'is-invalid': !validation.arbitro_asistente2_id.valid && validation.arbitro_asistente2_id.valid !== null }" 
                    v-model="formData.arbitro_asistente2_id"
                    @change="validateSingleField('arbitro_asistente2_id')"
                  >
                    <option value="">Seleccione un árbitro</option>
                    <option v-for="arbitro in arbitroStore.arbitros" :key="arbitro.id" :value="arbitro.id">
                      {{ arbitro.nombre }}
                    </option>
                  </select>
                  <div class="invalid-feedback">
                    {{ apiErrors.arbitro_asistente2_id?.[0] || 'Seleccione un árbitro válido' }}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-default btn-round waves-effect">
              {{ mode === 'create' ? 'Crear Partido' : 'Actualizar Partido' }}
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
import { ref, reactive, onMounted, watch, computed } from 'vue';
import { useTorneoStore } from '@/stores/torneos';
import { useJornadasStore } from '@/stores/jornadas';
import { useEquiposStore } from '@/stores/equipos';
import { useUbicacionesStore } from '@/stores/ubicaciones';
import { useArbitrosStore } from '@/stores/arbitros';
import { RegexUtils, validateField as validateFieldUtil } from '@/utils/regex-util';

const props = defineProps({
  mode: {
    type: String,
    default: 'create'
  },
  currentPartido: {
    type: Object,
    default: () => ({})
  }
});

const emit = defineEmits(['submit', 'cancel']);

// Use the stores
const torneoStore = useTorneoStore();
const jornadaStore = useJornadasStore();
const equipoStore = useEquiposStore();
const ubicacionStore = useUbicacionesStore();
const arbitroStore = useArbitrosStore();

// Form data
const formData = reactive({
  fecha: '',
  hora_inicio: '',
  torneo_id: '',
  jornada_id: '',
  equipo_local_id: '',
  equipo_visitante_id: '',
  goles_equipo_local: null,
  goles_equipo_visitante: null,
  ubicacion_id: '',
  arbitro_principal_id: '',
  arbitro_asistente1_id: '',
  arbitro_asistente2_id: '',
});

// Validation state with RegexUtils
const validation = reactive({
  fecha: { valid: null, regex: RegexUtils.date, optional: false },
  hora_inicio: { valid: null, regex: RegexUtils.time, optional: false },
  torneo_id: { valid: null, optional: false },
  jornada_id: { valid: null, optional: false },
  equipo_local_id: { valid: null, optional: false },
  equipo_visitante_id: { valid: null, optional: false },
  goles_equipo_local: { valid: null, regex: RegexUtils.integer, optional: true },
  goles_equipo_visitante: { valid: null, regex: RegexUtils.integer, optional: true },
  ubicacion_id: { valid: null, optional: false },
  arbitro_principal_id: { valid: null, optional: true },
  arbitro_asistente1_id: { valid: null, optional: true },
  arbitro_asistente2_id: { valid: null, optional: true },
});

const apiErrors = ref({});

// Computed properties for filtered data
const jornadasByTorneo = computed(() => {
  if (!formData.torneo_id) return [];
  return jornadaStore.jornadasByTorneo[formData.torneo_id] || [];
});

const equiposByTorneo = computed(() => {
  if (!formData.torneo_id) return [];
  return equipoStore.equiposByTorneo[formData.torneo_id] || [];
});

// Watch for changes in props.currentPartido
watch(() => props.currentPartido, async (newValue) => {
  if (newValue && Object.keys(newValue).length > 0) {
    // Parse date and time from the datetime string
    if (newValue.fecha) {
      // Check if fecha contains a 'T' (ISO format)
      if (newValue.fecha.includes('T')) {
        const [date, time] = newValue.fecha.split('T');
        formData.fecha = date;
        formData.hora_inicio = time.substring(0, 5); // Extract HH:MM
      } else if (newValue.fecha.includes(' ')) {
        // Handle MySQL datetime format (YYYY-MM-DD HH:MM:SS)
        const [date, time] = newValue.fecha.split(' ');
        formData.fecha = date;
        formData.hora_inicio = time.substring(0, 5); // Extract HH:MM
      } else {
        // Just a date without time
        formData.fecha = newValue.fecha;
        formData.hora_inicio = newValue.hora_inicio || '';
      }
    }
    
    // Set other form fields
    formData.torneo_id = newValue.torneo_id || '';
    formData.jornada_id = newValue.jornada_id || '';
    formData.equipo_local_id = newValue.equipo_local_id || '';
    formData.equipo_visitante_id = newValue.equipo_visitante_id || '';
    formData.goles_equipo_local = newValue.goles_equipo_local;
    formData.goles_equipo_visitante = newValue.goles_equipo_visitante;
    formData.ubicacion_id = newValue.ubicacion_id || '';
    formData.arbitro_principal_id = newValue.arbitro_principal_id || '';
    formData.arbitro_asistente1_id = newValue.arbitro_asistente1_id || '';
    formData.arbitro_asistente2_id = newValue.arbitro_asistente2_id || '';
    
    // If we have a torneo_id, load related data
    if (formData.torneo_id) {
      await loadJornadasByTorneo();
      await loadEquiposByTorneo();
    }
  }
}, { immediate: true, deep: true });

// Handle torneo change
const onTorneoChange = async () => {
  // Reset dependent fields
  formData.jornada_id = '';
  formData.equipo_local_id = '';
  formData.equipo_visitante_id = '';
  
  // Load related data
  await loadJornadasByTorneo();
  await loadEquiposByTorneo();
  
  // Validate the torneo field
  validateSingleField('torneo_id');
};

// Load data from stores
const loadJornadasByTorneo = async () => {
  if (!formData.torneo_id) return;
  await jornadaStore.loadJornadasByTorneo(formData.torneo_id);
};

const loadEquiposByTorneo = async () => {
  if (!formData.torneo_id) return;
  await equipoStore.loadEquiposByTorneo(formData.torneo_id);
};

// Validation using RegexUtils
const validateSingleField = (field) => {
  // Special validation for equipo_visitante_id
  if (field === 'equipo_visitante_id' && formData.equipo_visitante_id === formData.equipo_local_id) {
    validation[field].valid = false;
    return false;
  }
  
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
    if (typeof formData[key] === 'string') {
      formData[key] = '';
    } else if (typeof formData[key] === 'number') {
      formData[key] = null;
    }
  });
  
  Object.keys(validation).forEach(key => {
    validation[key].valid = null;
  });
  
  apiErrors.value = {};
  emit('cancel');
};

// Load initial data
onMounted(async () => {
  await torneoStore.fetchTorneos();
  await ubicacionStore.loadUbicaciones();
  await arbitroStore.loadArbitros();
  
  // If we have a torneo_id from props, load related data
  if (formData.torneo_id) {
    await loadJornadasByTorneo();
    await loadEquiposByTorneo();
  }
});

// Expose methods to parent component
defineExpose({
  handleApiErrors
});
</script>
<template>
  <div class="modal fade" id="eventoPartidoModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="title" id="eventoPartidoModalLabel">
            {{ mode === 'create' ? 'Nuevo Evento' : 'Editar Evento' }}
          </h4>
        </div>
        <form @submit.prevent="handleSubmit">
          <div class="modal-body">
            <div class="row clearfix">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Minuto</label>
                  <input 
                    type="number" 
                    class="form-control"
                    :class="{ 'is-invalid': !validation.minuto.valid && validation.minuto.valid !== null }" 
                    v-model="formData.minuto"
                    @input="validateSingleField('minuto')"
                    required
                    min="0"
                    max="120"
                  >
                  <div class="invalid-feedback">
                    {{ apiErrors.minuto?.[0] || 'El minuto es requerido (0-120)' }}
                  </div>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label>Tipo de Evento</label>
                  <select 
                    class="form-control show-tick"
                    :class="{ 'is-invalid': !validation.tipo_evento.valid && validation.tipo_evento.valid !== null }" 
                    v-model="formData.tipo_evento"
                    @change="validateSingleField('tipo_evento')"
                    required
                  >
                    <option value="">-- Seleccionar Tipo --</option>
                    <option value="gol">Gol</option>
                    <option value="tarjeta_amarilla">Tarjeta Amarilla</option>
                    <option value="tarjeta_roja">Tarjeta Roja</option>
                    <option value="tarjeta_azul">Tarjeta Azul</option>
                    <option value="falta">Falta</option>
                    <option value="lesion">Lesión</option>
                    <option value="penal">Penal</option>
                  </select>
                  <div class="invalid-feedback">
                    {{ apiErrors.tipo_evento?.[0] || 'El tipo de evento es requerido' }}
                  </div>
                </div>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Equipo</label>
                  <select 
                    class="form-control show-tick"
                    v-model="selectedEquipoId"
                    @change="handleEquipoChange"
                    required
                  >
                    <option value="">-- Seleccionar Equipo --</option>
                    <option v-if="partido && partido.equipo_local" :value="partido.equipo_local_id">
                      {{ partido.equipo_local.nombre }}
                    </option>
                    <option v-if="partido && partido.equipo_visitante" :value="partido.equipo_visitante_id">
                      {{ partido.equipo_visitante.nombre }}
                    </option>
                  </select>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="form-group">
                  <label>Jugador</label>
                  <select 
                    class="form-control show-tick"
                    :class="{ 'is-invalid': !validation.jugador_id.valid && validation.jugador_id.valid !== null }" 
                    v-model="formData.jugador_id"
                    @change="validateSingleField('jugador_id')"
                    required
                    :disabled="!selectedEquipoId"
                  >
                    <option value="">-- Seleccionar Jugador --</option>
                    <option 
                      v-for="jugador in jugadores" 
                      :key="jugador.id" 
                      :value="jugador.id"
                    >
                      {{ jugador.nombre }}
                    </option>
                  </select>
                  <div class="invalid-feedback">
                    {{ apiErrors.jugador_id?.[0] || 'El jugador es requerido' }}
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger btn-simple btn-round waves-effect" data-dismiss="modal" @click="cancel">Cancelar</button>
            <button type="submit" class="btn btn-default btn-round waves-effect">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue';
import { useEquiposStore } from '@/stores/equipos';

const equiposStore = useEquiposStore();

const props = defineProps({
  mode: {
    type: String,
    default: 'create'
  },
  partido: {
    type: Object,
    default: () => ({})
  },
  currentEvento: {
    type: Object,
    default: () => ({})
  }
});

const emit = defineEmits(['submit', 'cancel']);

const formData = reactive({
  partido_id: props.partido?.id || null,
  minuto: '',
  tipo_evento: '',
  jugador_id: ''
});

const selectedEquipoId = ref('');
const jugadores = ref([]);
const apiErrors = ref({});

// Define resetForm before using it in watch
const resetForm = () => {
  Object.assign(formData, {
    partido_id: props.partido?.id || null,
    minuto: '',
    tipo_evento: '',
    jugador_id: ''
  });
  
  selectedEquipoId.value = '';
  jugadores.value = [];
  apiErrors.value = {};
  
  // Reset validation
  Object.keys(validation).forEach(key => {
    validation[key].valid = null;
  });
};

const validation = reactive({
  minuto: { valid: null },
  tipo_evento: { valid: null },
  jugador_id: { valid: null }
});

// Load partido data when partido changes
watch(() => props.partido, async (newPartido) => {
  if (newPartido && newPartido.id) {
    formData.partido_id = newPartido.id;
    selectedEquipoId.value = '';
    jugadores.value = [];
  }
}, { immediate: true });

// Update form data when currentEvento changes
watch(() => props.currentEvento, async (newEvento) => {
  if (newEvento && Object.keys(newEvento).length > 0) {
    Object.assign(formData, {
      partido_id: newEvento.partido_id || props.partido?.id,
      minuto: newEvento.minuto || '',
      tipo_evento: newEvento.tipo_evento || '',
      jugador_id: newEvento.jugador_id || ''
    });
    
    // If we have a jugador_id, we need to determine which equipo it belongs to
    if (newEvento.jugador_id && props.partido) {
      try {
        const response = await axios.get(`/api/jugadores/${newEvento.jugador_id}`);
        const jugador = response.data.data;
        
        if (jugador && jugador.equipo_id) {
          selectedEquipoId.value = jugador.equipo_id;
          await loadJugadoresByEquipo(jugador.equipo_id);
        }
      } catch (error) {
        console.error('Error loading jugador details:', error);
      }
    }
  } else {
    resetForm();
  }
}, { immediate: true });

const handleEquipoChange = async () => {
  formData.jugador_id = '';
  
  if (selectedEquipoId.value) {
    await loadJugadoresByEquipo(selectedEquipoId.value);
  } else {
    jugadores.value = [];
  }
};

const loadJugadoresByEquipo = async (equipoId) => {
  if (!equipoId) return;
  
  try {
    // Use the store instead of direct API call
    const jugadoresData = await equiposStore.loadJugadoresByEquipo(equipoId);
    jugadores.value = jugadoresData;
  } catch (error) {
    console.error('Error loading jugadores:', error);
    jugadores.value = [];
  }
};

const validateSingleField = (field) => {
  switch (field) {
    case 'minuto':
      validation.minuto.valid = formData.minuto !== '' && 
                               formData.minuto >= 0 && 
                               formData.minuto <= 120;
      break;
    case 'tipo_evento':
      validation.tipo_evento.valid = formData.tipo_evento !== '';
      break;
    case 'jugador_id':
      validation.jugador_id.valid = formData.jugador_id !== '';
      break;
  }
};

const validateForm = () => {
  validateSingleField('minuto');
  validateSingleField('tipo_evento');
  validateSingleField('jugador_id');
  
  return validation.minuto.valid && 
         validation.tipo_evento.valid && 
         validation.jugador_id.valid;
};

const handleSubmit = () => {
  if (!validateForm()) return;
  
  emit('submit', { ...formData });
};

const cancel = () => {
  emit('cancel');
};

// Method to handle API validation errors
const handleApiErrors = (errors) => {
  apiErrors.value = errors;
  
  // Mark fields as invalid
  Object.keys(errors).forEach(field => {
    if (validation[field]) {
      validation[field].valid = false;
    }
  });
};

// Expose methods to parent component
defineExpose({
  resetForm,
  handleApiErrors
});
</script>
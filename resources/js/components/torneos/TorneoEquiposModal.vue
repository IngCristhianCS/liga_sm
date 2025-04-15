<template>
  <div class="modal fade" id="torneoEquiposModal" tabindex="-1" role="dialog" aria-labelledby="torneoEquiposModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="torneoEquiposModalLabel">Asignar Equipos al Torneo</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div v-if="loading" class="text-center">
            <div class="spinner-border text-primary" role="status">
              <span class="sr-only">Cargando...</span>
            </div>
          </div>
          <div v-else>
            <div class="row mb-3">
              <div class="col-md-12">
                <h6>Torneo: {{ torneo?.nombre || 'Seleccione un torneo' }}</h6>
              </div>
            </div>
            
            <div class="row mb-3">
              <div class="col-md-12">
                <div class="form-group">
                  <label>Buscar Equipos</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    v-model="searchTerm" 
                    placeholder="Buscar por nombre de equipo..."
                  >
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <h6>Equipos Disponibles</h6>
                <div class="list-group" style="max-height: 300px; overflow-y: auto;">
                  <a 
                    v-for="equipo in filteredAvailableEquipos" 
                    :key="equipo.id" 
                    href="#" 
                    class="list-group-item list-group-item-action"
                    @click.prevent="addEquipo(equipo)"
                  >
                    <div class="d-flex align-items-center">
                      <img 
                        v-if="equipo.imagen" 
                        :src="`/storage/${equipo.imagen}`" 
                        alt="Logo" 
                        class="mr-2" 
                        style="width: 30px; height: 30px; object-fit: contain;"
                      >
                      <span>{{ equipo.nombre }}</span>
                    </div>
                  </a>
                  <div v-if="filteredAvailableEquipos.length === 0" class="text-center p-3">
                    No hay equipos disponibles
                  </div>
                </div>
              </div>
              
              <div class="col-md-6">
                <h6>Equipos Asignados</h6>
                <div class="list-group" style="max-height: 300px; overflow-y: auto;">
                  <a 
                    v-for="equipo in filteredAssignedEquipos" 
                    :key="equipo.id" 
                    href="#" 
                    class="list-group-item list-group-item-action"
                    @click.prevent="removeEquipo(equipo)"
                  >
                    <div class="d-flex align-items-center justify-content-between">
                      <div class="d-flex align-items-center">
                        <img 
                          v-if="equipo.imagen" 
                          :src="`/storage/${equipo.imagen}`" 
                          alt="Logo" 
                          class="mr-2" 
                          style="width: 30px; height: 30px; object-fit: contain;"
                        >
                        <span>{{ equipo.nombre }}</span>
                      </div>
                      <button class="btn btn-sm btn-danger">
                        <i class="fa fa-times"></i>
                      </button>
                    </div>
                  </a>
                  <div v-if="filteredAssignedEquipos.length === 0" class="text-center p-3">
                    No hay equipos asignados
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button 
            type="button" 
            class="btn btn-primary" 
            @click="saveChanges" 
            :disabled="loading || !hasChanges"
          >
            Guardar Cambios
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import { useTorneoStore } from '@/stores/torneos';
import Notification from '@/utils/notification';
const torneoStore = useTorneoStore();

const props = defineProps({
  torneoId: {
    type: [Number, String],
    default: null
  }
});

const emit = defineEmits(['updated']);

const loading = ref(false);
const torneo = ref(null);
const allEquipos = ref([]);
const assignedEquipos = ref([]);
const originalAssignedIds = ref([]);
const searchTerm = ref('');

// Computed properties for filtered lists
const filteredAvailableEquipos = computed(() => {
  const assignedIds = assignedEquipos.value.map(e => e.id);
  return allEquipos.value
    .filter(e => !assignedIds.includes(e.id))
    .filter(e => e.nombre.toLowerCase().includes(searchTerm.value.toLowerCase()));
});

const filteredAssignedEquipos = computed(() => {
  return assignedEquipos.value
    .filter(e => e.nombre.toLowerCase().includes(searchTerm.value.toLowerCase()));
});

const hasChanges = computed(() => {
  const currentIds = assignedEquipos.value.map(e => e.id).sort();
  const originalIds = [...originalAssignedIds.value].sort();
  
  if (currentIds.length !== originalIds.length) return true;
  
  for (let i = 0; i < currentIds.length; i++) {
    if (currentIds[i] !== originalIds[i]) return true;
  }
  
  return false;
});

// Methods
const loadTorneo = async (id) => {
  if (!id) return;
  
  try {
    loading.value = true;
    torneo.value = await torneoStore.fetchTorneoById(id);
  } catch (error) {
    Notification.error('Error al cargar el torneo');
    console.error(error);
  } finally {
    loading.value = false;
  }
};

const loadEquipos = async () => {
  try {
    loading.value = true;
    const response = await axios.get('/api/equipos');
    allEquipos.value = response.data.data;
  } catch (error) {
    Notification.error('Error al cargar los equipos');
    console.error(error);
  } finally {
    loading.value = false;
  }
};

const loadAssignedEquipos = async (torneoId) => {
  if (!torneoId) return;
  
  try {
    loading.value = true;
    const response = await axios.get(`/api/torneos/${torneoId}/equipos`);
    assignedEquipos.value = response.data.data;
    originalAssignedIds.value = assignedEquipos.value.map(e => e.id);
  } catch (error) {
    Notification.error('Error al cargar los equipos asignados');
    console.error(error);
  } finally {
    loading.value = false;
  }
};

const addEquipo = (equipo) => {
  assignedEquipos.value.push(equipo);
};

const removeEquipo = (equipo) => {
  assignedEquipos.value = assignedEquipos.value.filter(e => e.id !== equipo.id);
};

const saveChanges = async () => {
  if (!torneo.value?.id) return;
  
  try {
    loading.value = true;
    const equipoIds = assignedEquipos.value.map(e => e.id);
    
    await axios.post(`/api/torneos/${torneo.value.id}/equipos`, {
      equipos: equipoIds
    });
    
    originalAssignedIds.value = [...equipoIds];
    Notification.success('Equipos asignados correctamente');
    emit('updated', torneo.value.id);
  } catch (error) {
    Notification.error('Error al guardar los cambios');
    console.error(error);
  } finally {
    loading.value = false;
  }
};

const initialize = async (torneoId) => {
  if (!torneoId) return;
  
  await Promise.all([
    loadTorneo(torneoId),
    loadEquipos(),
    loadAssignedEquipos(torneoId)
  ]);
};

// Watch for torneoId changes
watch(() => props.torneoId, (newId) => {
  if (newId) {
    initialize(newId);
  }
});

// Expose methods to parent component
defineExpose({
  initialize
});
</script>
<template>
  <div v-if="cargando">
    <p>Cargando torneos...</p>
  </div>
  
  <div v-else-if="error">
    <p>Error al cargar torneos: {{ error }}</p>
  </div>
  
  <div v-else-if="torneos && torneos.length > 0">
    <ul class="nav nav-tabs padding-0">
      <li class="nav-item inlineblock" v-for="torneo in torneos" :key="torneo.id">
        <a class="nav-link" href="#" 
           @click.prevent="seleccionarTorneo(torneo.id)"
           :class="{ 'active': torneoSeleccionado === torneo.id }">
          {{ torneo.nombre }}
        </a>
      </li>
    </ul>
  </div>
  
  <div v-else>
    <p>No hay torneos disponibles.</p>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';

const emit = defineEmits(['torneoSeleccionado']);
const torneos = ref(null);
const error = ref(null);
const cargando = ref(true);
const torneoSeleccionado = ref(null);
const hasEmitted = ref(false);

const obtenerTorneos = async () => {
  try {
    const response = await axios.get('/api/torneos');
    torneos.value = response.data.data;
    
    if (torneos.value.length > 0 && !hasEmitted.value) {
      hasEmitted.value = true;
      torneoSeleccionado.value = torneos.value[0].id;
      emit('torneoSeleccionado', torneos.value[0].id);
    }
    
  } catch (err) {
    error.value = err.message || 'Error al obtener torneos';
  } finally {
    cargando.value = false;
  }
};

const seleccionarTorneo = (id) => {
  if (torneoSeleccionado.value !== id) {
    torneoSeleccionado.value = id;
    emit('torneoSeleccionado', id);
  }
};

obtenerTorneos();
</script>
<template>
  <div v-if="store.loading">
    <p>Cargando torneos...</p>
  </div>
  
  <div v-else-if="store.error">
    <p>Error al cargar torneos: {{ store.error }}</p>
  </div>
  
  <div v-else-if="store.torneos.length > 0">
    <ul class="nav nav-tabs padding-0">
      <li class="nav-item inlineblock" v-for="torneo in store.torneos" :key="torneo.id">
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
import { ref, onMounted } from 'vue';
import { useTorneoStore } from '@/stores/torneos';

const emit = defineEmits(['torneoSeleccionado']);
const store = useTorneoStore();
const torneoSeleccionado = ref(null);
const hasEmitted = ref(false);

onMounted(async () => {  
  if (store.torneos.length > 0 && !hasEmitted.value) {
    hasEmitted.value = true;
    torneoSeleccionado.value = store.torneos[0].id;
    emit('torneoSeleccionado', store.torneos[0].id);
  }else{
    await store.fetchTorneos();
    if (store.torneos.length > 0 && !hasEmitted.value) {
      hasEmitted.value = true;
      torneoSeleccionado.value = store.torneos[0].id;
      emit('torneoSeleccionado', store.torneos[0].id);
    }
  }
});

const seleccionarTorneo = (id) => {
  if (torneoSeleccionado.value !== id) {
    torneoSeleccionado.value = id;
    emit('torneoSeleccionado', id);
  }
};
</script>
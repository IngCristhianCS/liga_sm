<template>
  <section class="content">
    <div v-show="useClasificacionStore.loading">
      <AppLoader />
    </div>

    <div v-show="useClasificacionStore.error" class="text-red-500">
      Error: {{ useClasificacionStore.error }}
    </div>

    <div v-show="!useClasificacionStore.loading && !useClasificacionStore.error" class="container">
      <div class="block-header">
        <div class="row clearfix">
          <div class="col-lg-5 col-md-5 col-sm-12">
            <h2>Tabla General</h2>
          </div>
          <div class="col-lg-7 col-md-7 col-sm-12">
            <ul class="breadcrumb float-md-right padding-0">
              <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i></a></li>
              <li class="breadcrumb-item"><a href="javascript:void(0);">Clasificación</a></li>
              <li class="breadcrumb-item active">Tabla General</li>
            </ul>
          </div>
        </div>
      </div>
      <TorneosMenu @torneoSeleccionado="actualizarClasificacion" />
      <div class="row clearfix">
        <div class="col-lg-12">
          <div class="card" id="details">
            <div class="body">
              <div class="row">
                <div class="col-md-12">
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th>#</th>
                          <th>Equipo</th>
                          <th>PJ</th>
                          <th>PG</th>
                          <th>PP</th>
                          <th>GF</th>
                          <th>GC</th>
                          <th>DIF</th>
                          <th>PTS</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="(equipo, index) in clasificacionData" :key="equipo.equipo"
                          class="border-b">
                          <td>{{ index + 1 }}</td>
                          <td>
                            <img v-if="equipo.imagen" :src="`/storage/${equipo.imagen}`" alt="Escudo del equipo"
                              width="30" height="30" class="rounded-circle" />
                            <img v-else src="/assets/images/xs/avatar1.jpg" alt="Escudo predeterminado" width="30"
                              height="30" class="rounded-circle bg-light" />
                            {{ equipo.equipo }}
                          </td>
                          <td>{{ equipo.PJ }}</td>
                          <td>{{ equipo.PG }}</td>
                          <td>{{ equipo.PP }}</td>
                          <td>{{ equipo.GF }}</td>
                          <td>{{ equipo.GC }}</td>
                          <td>{{ equipo.DIF }}</td>
                          <td>{{ equipo.PTS }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { onMounted, ref, computed } from 'vue';
import { useClasificacionStore } from '@/stores/clasificacion';
import { useTorneoStore } from '@/stores/torneos';
import TorneosMenu from '../global/TorneosMenu.vue';

const clasificacionStore = useClasificacionStore();
const torneoStore = useTorneoStore();
const fetching = ref(false);
const currentTorneoId = ref(null);

// Computed property to get classification data for the current tournament
const clasificacionData = computed(() => {
  return clasificacionStore.getClasificacion(currentTorneoId.value) || [];
});

const actualizarClasificacion = async (idTorneo) => {
  if (!fetching.value) {
    fetching.value = true;
    currentTorneoId.value = idTorneo;
    try {
      await clasificacionStore.getClasificacionByTorneo(idTorneo);
    } catch (error) {
      console.error('Error actualizando clasificación:', error);
    } finally {
      fetching.value = false;
    }
  }
};

onMounted(async () => {
  if (torneoStore.torneosCatalog.length > 0) {
    await actualizarClasificacion(torneoStore.torneosCatalog[0].id);
  } else {
    await torneoStore.fetchTorneosCatalog();
    if (torneoStore.torneosCatalog.length > 0) {
      await actualizarClasificacion(torneoStore.torneosCatalog[0].id);
    }
  }
});
</script>
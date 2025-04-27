<template>
  <section class="content blog-page">
    <div class="container">
      <div class="block-header">
        <div class="row clearfix">
          <div class="col-lg-5 col-md-5 col-sm-12">
            <h2>Resultados</h2>
          </div>
          <div class="col-lg-7 col-md-7 col-sm-12">
            <ul class="breadcrumb float-md-right padding-0">
              <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i></a></li>
              <li class="breadcrumb-item"><a href="javascript:void(0);">Resultados</a></li>
              <li class="breadcrumb-item active">Jornadas</li>
            </ul>
          </div>
        </div>
      </div>
      <TorneosMenu @torneoSeleccionado="handleTorneoSeleccionado"/>      
      <div class="row clearfix">
        <div class="col-lg-6 col-md-12" v-for="jornada in store.jornadas" :key="jornada.jornada_id">
          <div class="card single_post">
            <div class="body">
              <h3 class="m-t-0 m-b-5"><a href="javascript:void(0);">Jornada {{ jornada.jornada_id }}</a></h3>
              <div class="img-post m-b-15">
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>Equipo Local</th>
                        <th>Goles Local</th>
                        <th>Equipo Visitante</th>
                        <th>Goles Visitante</th>
                        <th></th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="resultado in jornada.resultados"
                        :key="resultado.equipo_local + resultado.equipo_visitante">
                        <td>
                          <img v-if="resultado.imagen_local" :src="`/storage/${resultado.imagen_local}`" 
                               alt="Escudo del equipo" width="30" height="30" class="rounded-circle" />
                          <img v-else src="/assets/images/xs/avatar1.jpg" 
                               alt="Escudo predeterminado" width="30" height="30" class="rounded-circle bg-light" /> 
                          {{ resultado.equipo_local }}
                        </td>
                        <td>{{ resultado.goles_equipo_local }}</td>
                        <td>
                          <img v-if="resultado.imagen_visitante" :src="`/storage/${resultado.imagen_visitante}`" 
                               alt="Escudo del equipo" width="30" height="30" class="rounded-circle" />
                          <img v-else src="/assets/images/xs/avatar1.jpg" 
                               alt="Escudo predeterminado" width="30" height="30" class="rounded-circle bg-light" />
                          {{ resultado.equipo_visitante }}
                        </td>
                        <td>{{ resultado.goles_equipo_visitante }}</td>
                        <td>
                          <router-link :to="`/partidos/${resultado.id}/detalles`" class="nav-link">
                            <i class="zmdi zmdi-calendar-note"></i>
                          </router-link>
                        </td>
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
  </section>
</template>

<script setup>
import { onMounted, ref } from 'vue'
import { useResultadosStore } from '@/stores/resultados'
import { useTorneoStore } from '@/stores/torneos'
import TorneosMenu from '@/components/global/TorneosMenu.vue';

const store = useResultadosStore()
const torneoStore = useTorneoStore()
const selectedTorneoId = ref(null)

const loadResultados = async (torneoId) => {
  if (torneoId) {
    await store.fetchJornadas(torneoId)
  }
}

const handleTorneoSeleccionado = (torneoId) => {
  selectedTorneoId.value = torneoId
  loadResultados(torneoId)
}

onMounted(async () => {
  if (torneoStore.torneosCatalog.length === 0) {
    await torneoStore.fetchTorneosCatalog()
  }
  
  if (torneoStore.torneosCatalog.length > 0) {
    selectedTorneoId.value = torneoStore.torneosCatalog[0].id
    await loadResultados(selectedTorneoId.value)
  }
})
</script>
<template>
  <section class="content">
    <div v-if="store.loading">
        <AppLoader/>
    </div>
    <div v-else-if="store.error" class="text-red-500">
      Error: {{ store.error }}
    </div>
    <div v-else class="container">
      <div class="block-header">
        <div class="row clearfix">
          <div class="col-lg-5 col-md-5 col-sm-12">
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
      <div class="row clearfix">
        <div class="col-lg-12">
          <div class="card">
            <div class="body">
              <h3 class="m-b-10">Tabla General</h3>
              <ul class="nav nav-tabs padding-0">
                <li class="nav-item inlineblock"><a class="nav-link active" data-toggle="tab" href="#details"
                    aria-expanded="true">Varonil Libre</a></li>
                <li class="nav-item inlineblock"><a class="nav-link" data-toggle="tab" href="#notes"
                    aria-expanded="false">Femenil Libre</a></li>
                <li class="nav-item inlineblock"><a class="nav-link" data-toggle="tab" href="#history"
                    aria-expanded="false">Juvenil Sub##</a></li>
              </ul>
            </div>
          </div>
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane in active" id="details" aria-expanded="true">
              <div class="card" id="details">
                <div class="body">
                  <h4 class="margin-0"></h4>
                  <div class="mt-40"></div>
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
                            <tr v-for="(equipo, index) in store.clasificacion" :key="equipo.equipo" class="border-b">
                              <td>{{ index + 1 }}</td>
                              <td>
                                  <!-- Imagen del equipo si existe -->
                                  <img v-if="equipo.imagen" :src="`data:image/png;base64,${equipo.imagen}`"
                                    alt="Escudo del equipo" width="30" height="30" class="rounded-circle">

                                  <!-- Imagen por defecto si no existe -->
                                  <img v-else src="/assets/images/xs/avatar1.jpg" alt="Escudo predeterminado" width="30"
                                    height="30" class="rounded-circle bg-light">
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
            <div role="tabpanel" class="tab-pane" id="notes" aria-expanded="false">
              <div class="card" id="details">
                <div class="body">
                  <div class="row">
                    en construcción
                  </div>
                  <div class="mt-40"></div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="table-responsive">
                        <table class="table table-striped">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th>Equipo</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td>1</td>
                              <td>chelsea</td>
                            </tr>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="history" aria-expanded="false">
              <div class="card" id="details">
                <div class="body">
                  <div class="row">
                    en construcción
                  </div>
                  <div class="mt-40"></div>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="table-responsive">
                        <table class="table table-striped">
                          <thead>
                            <tr>
                              <th>#</th>
                              <th>Equipo</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <td>1</td>
                              <td>chelsea</td>
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
      </div>
    </div>
  </section>

</template>
<script setup>
import { onMounted } from 'vue'
import { useClasificacionStore } from '@/stores/clasificacion'

const store = useClasificacionStore()

onMounted(() => {
  if (store.clasificacion.length === 0) {
    store.fetchClasificacion()
  }
})
</script>
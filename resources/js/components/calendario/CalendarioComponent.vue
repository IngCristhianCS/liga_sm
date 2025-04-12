<template>
  <section class="content contact">
    <div v-if="store.loading">
      <AppLoader />
    </div>
    <div v-else-if="store.error" class="text-red-500">
      Error: {{ store.error }}
    </div>
    <div v-else class="container-fluid">
      <div class="block-header">
        <div class="row clearfix">
          <div class="col-lg-5 col-md-5 col-sm-12">
            <h2>Jornadas de mi Equipo</h2>
          </div>
          <div class="col-lg-7 col-md-7 col-sm-12">
            <ul class="breadcrumb float-md-right padding-0">
              <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> Inicio</a></li>
              <li class="breadcrumb-item active">Jornadas de mi Equipo</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="row clearfix">
        <div class="col-lg-12">
          <div class="card">
            <div class="body">
              <div class="table-responsive">
                <table id="jornadasEquipo"
                  class="table table-bordered table-striped table-hover js-basic-example dataTable">
                  <thead>
                    <tr>
                      <th>Jornada</th>
                      <th>Equipo Local</th>
                      <th>Goles Local</th>
                      <th>Equipo Visitante</th>
                      <th>Goles Visitante</th>
                      <th>Torneo</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { onMounted } from 'vue';
import { useJornadasStore } from '@/stores/jornadas';

const store = useJornadasStore();

const inicializarDataTable = () => {
  const tabla = "#jornadasEquipo";

  if ($.fn.DataTable.isDataTable(tabla)) {
    const table = $(tabla).DataTable();
    table.clear().rows.add(store.jornadas).draw();
  } else {
    $(tabla).DataTable({
      paging: false,
      pageLength: 10,
      lengthChange: false,
      searching: false,
      ordering: false,
      data: store.jornadas,
      columns: [
        { data: 'jornada_id' },
        {
          data: null,
          render: (data, type, row) => {
            if (row.imagen_local == null) {
              return `<img src="/assets/images/xs/avatar1.jpg" width="30" height="30" class="rounded-circle avatar" alt="Avatar"> ${row.equipo_local}`;
            }
            return `<img src="data:image/png;base64,${row.imagen_local}" width="30" height="30" class="rounded-circle avatar" alt="Avatar"> ${row.equipo_local}`;
          }
        },
        { data: 'goles_equipo_local' },
        {
          data: null,
          render: (data, type, row) => {
            if (row.imagen_visitante == null) {
              return `<img src="/assets/images/xs/avatar1.jpg" width="30" height="30" class="rounded-circle avatar" alt="Avatar"> ${row.equipo_visitante}`;
            }
            return `<img src="data:image/png;base64,${row.imagen_visitante}" width="30" height="30" class="rounded-circle avatar" alt="Avatar"> ${row.equipo_visitante}`;
          }
        },
        { data: 'goles_equipo_visitante' },
        { data: 'torneo_nombre' }
      ],
    });
  }
};

onMounted(() => {
  store.loadJornadas().then(() => {
    inicializarDataTable();
  });
});
</script>
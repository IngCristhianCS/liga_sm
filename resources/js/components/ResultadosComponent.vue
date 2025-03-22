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
              <li class="breadcrumb-item"><a href="index.html"><i class="zmdi zmdi-home"></i></a></li>
              <li class="breadcrumb-item"><a href="blog-dashboard.html">Resultados</a></li>
              <li class="breadcrumb-item active">Jornadas</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="row clearfix">
        <div class="col-lg-6 col-md-12" v-for="jornada in jornadas" :key="jornada.jornada_id">
          <div class="card single_post">
            <div class="body">
              <h3 class="m-t-0 m-b-5"><a href="blog-details.html">Jornada {{ jornada.jornada_id }}</a></h3>
              <div class="img-post m-b-15">
                <table>
                  <thead>
                    <tr>
                      <th>Equipo Local</th>
                      <th>Goles Local</th>
                      <th>Equipo Visitante</th>
                      <th>Goles Visitante</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="resultado in jornada.resultados"
                      :key="resultado.equipo_local + resultado.equipo_visitante">
                      <td>{{ resultado.equipo_local }}</td>
                      <td>{{ resultado.goles_equipo_local }}</td>
                      <td>{{ resultado.equipo_visitante }}</td>
                      <td>{{ resultado.goles_equipo_visitante }}</td>
                    </tr>
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

<script>
import axios from 'axios';

export default {
  data() {
    return {
      jornadas: [],
    };
  },
  mounted() {
    this.obtenerResultadosJornadas();
  },
  methods: {
    obtenerResultadosJornadas() {
      axios.get('/api/torneos/2/jornadas') // Reemplaza 1 con el ID del torneo
        .then(response => {
          this.jornadas = response.data;
        })
        .catch(error => {
          console.error('Error al obtener los resultados:', error);
        });
    },
  },
};
</script>

<style scoped>
table {
  width: 100%;
  border-collapse: collapse;
  margin-bottom: 20px;
}

th,
td {
  border: 1px solid #ddd;
  padding: 8px;
  text-align: left;
}

th {
  background-color: #f2f2f2;
}
</style>
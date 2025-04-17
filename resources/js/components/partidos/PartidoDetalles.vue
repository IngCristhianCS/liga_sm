<template>
  <section class="content profile-page">
    <div class="container">
      <!-- Page header -->
      <div class="block-header">
        <div class="row">
          <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Detalles del Partido</h2>
            <ul class="breadcrumb">
              <li class="breadcrumb-item"><router-link to="/"><i class="icon-home"></i></router-link></li>
              <li class="breadcrumb-item"><router-link to="/partidos">Partidos</router-link></li>
              <li class="breadcrumb-item active">Detalles</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Loading indicator -->
      <div v-if="loading" class="text-center p-5">
        <div class="spinner-border text-primary" role="status">
          <span class="sr-only">Cargando...</span>
        </div>
      </div>

      <!-- Error message -->
      <div v-else-if="error" class="alert alert-danger">
        {{ error }}
      </div>

      <!-- Main content -->
      <div v-else class="row clearfix">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Información del Partido</h3>
            </div>
            <div class="card-body">
              <!-- Información básica del partido -->
              <div class="row mb-4">
                <div class="col-md-12">
                  <div class="partido-header d-flex justify-content-between align-items-center">
                    <div class="equipo-local text-center">
                      <img v-if="partido?.equipo_local?.imagen" :src="`/storage/${partido.equipo_local.imagen}`"
                        alt="Logo Local" class="equipo-logo">
                      <h4>{{ partido?.equipo_local?.nombre || 'Equipo Local' }}</h4>
                    </div>

                    <div class="marcador">
                      <h3>{{ partido?.goles_equipo_local || 0 }} - {{ partido?.goles_equipo_visitante || 0 }}</h3>
                      <div class="estado-partido">{{ getEstadoPartido }}</div>
                    </div>

                    <div class="equipo-visitante text-center">
                      <img v-if="partido?.equipo_visitante?.imagen" :src="`/storage/${partido.equipo_visitante.imagen}`"
                        alt="Logo Visitante" class="equipo-logo">
                      <h4>{{ partido?.equipo_visitante?.nombre || 'Equipo Visitante' }}</h4>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Información de fecha y ubicación -->
              <div class="row mb-4">
                <div class="col-md-6">
                  <div class="info-item">
                    <strong>Fecha:</strong> {{ formatFecha }}
                  </div>
                  <div class="info-item">
                    <strong>Hora:</strong> {{ formatHora }}
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="info-item">
                    <strong>Ubicación:</strong> {{ partido?.ubicacion?.nombre || 'No especificada' }}
                  </div>
                  <div class="info-item">
                    <strong>Jornada:</strong> {{ partido?.jornada?.numero || 'No especificada' }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div role="tabpanel" class="tab-pane" id="overview">
                        <div class="row">
                            <div class="col-lg-3 col-md-6 col-6">
                                <div class="card text-center">
                                    <div class="body">
                                        <i class="zmdi zmdi-thumb-up zmdi-hc-2x"></i>
                                        <h5 class="m-b-0 number count-to" data-from="0" data-to="1203" data-speed="1000" data-fresh-interval="700">1203</h5>
                                        <small>Likes</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-6">
                                <div class="card text-center">
                                    <div class="body">                            
                                        <i class="zmdi zmdi-comment-text zmdi-hc-2x"></i>
                                        <h5 class="m-b-0 number count-to" data-from="0" data-to="324" data-speed="1000" data-fresh-interval="700">324</h5>
                                        <small>Comments</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-6">
                                <div class="card text-center">
                                    <div class="body">
                                        <i class="zmdi zmdi-eye zmdi-hc-2x"></i>
                                        <h5 class="m-b-0 number count-to" data-from="0" data-to="1980" data-speed="1000" data-fresh-interval="700">1980</h5>
                                        <small>Views</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6 col-6">
                                <div class="card text-center">
                                    <div class="body">
                                        <i class="zmdi zmdi-attachment zmdi-hc-2x"></i>
                                        <h5 class="m-b-0 number count-to" data-from="0" data-to="52" data-speed="1000" data-fresh-interval="700">52</h5>
                                        <small>Attachment</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-4 col-md-12">
                                <div class="card">
                                    <div class="header">
                                        <h2><strong>Info</strong></h2>
                                        <ul class="header-dropdown">
                                            <li class="dropdown"> <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <i class="zmdi zmdi-more"></i> </a>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <li><a href="javascript:void(0);">Action</a></li>
                                                    <li><a href="javascript:void(0);">Another action</a></li>
                                                    <li><a href="javascript:void(0);">Something else</a></li>
                                                    <li><a href="javascript:void(0);" class="boxs-close">Delete</a></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="body">
                                        <small class="text-muted">Address: </small>
                                        <p>795 Folsom Ave, Suite 600 San Francisco, 94107</p>
                                        <div>
                                                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1923731.7533500232!2d-120.39098936853455!3d37.63767091877441!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80859a6d00690021%3A0x4a501367f076adff!2sSan+Francisco%2C+CA%2C+USA!5e0!3m2!1sen!2sin!4v1522391841133" width="100%" height="150" frameborder="0" style="border:0" allowfullscreen></iframe>
                                        </div>
                                        <hr>
                                        <small class="text-muted">Email address: </small>
                                        <p>michael@gmail.com</p>
                                        <hr>
                                        <small class="text-muted">Phone: </small>
                                        <p>+ 202-555-9191</p>
                                        <hr>
                                        <small class="text-muted">Mobile: </small>
                                        <p>+ 202-555-2828</p>
                                        <hr>
                                        <small class="text-muted">Birth Date: </small>
                                        <p class="m-b-0">October 22th, 1990</p>
                                    </div>
                                </div>
                                <div class="card">
                                    <div class="header">
                                        <h2><strong>Who</strong> to follow</h2>
                                        <ul class="header-dropdown">
                                            <li class="dropdown"> <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <i class="zmdi zmdi-more"></i> </a>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <li><a href="javascript:void(0);">Action</a></li>
                                                    <li><a href="javascript:void(0);">Another action</a></li>
                                                    <li><a href="javascript:void(0);">Something else</a></li>
                                                    <li><a href="javascript:void(0);" class="boxs-close">Delete</a></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="body">
                                        <ul class="follow_us list-unstyled m-b-0">
                                            <li class="online">
                                                <a href="javascript:void(0);">
                                                    <div class="media">
                                                        <img class="media-object " src="assets/images/xs/avatar4.jpg" alt="">
                                                        <div class="media-body">
                                                            <span class="name">Chris Fox</span>
                                                            <span class="message">Designer, Blogger</span>
                                                            <span class="badge badge-outline status"></span>
                                                        </div>
                                                    </div>
                                                </a>                            
                                            </li>
                                            <li class="online">
                                                <a href="javascript:void(0);">
                                                    <div class="media">
                                                        <img class="media-object " src="assets/images/xs/avatar5.jpg" alt="">
                                                        <div class="media-body">
                                                            <span class="name">Joge Lucky</span>
                                                            <span class="message">Java Developer</span>
                                                            <span class="badge badge-outline status"></span>
                                                        </div>
                                                    </div>
                                                </a>                            
                                            </li>
                                            <li class="offline">
                                                <a href="javascript:void(0);">
                                                    <div class="media">
                                                        <img class="media-object " src="assets/images/xs/avatar2.jpg" alt="">
                                                        <div class="media-body">
                                                            <span class="name">Isabella</span>
                                                            <span class="message">CEO, Thememakker</span>
                                                            <span class="badge badge-outline status"></span>
                                                        </div>
                                                    </div>
                                                </a>                            
                                            </li>
                                            <li class="offline">
                                                <a href="javascript:void(0);">
                                                    <div class="media">
                                                        <img class="media-object " src="assets/images/xs/avatar1.jpg" alt="">
                                                        <div class="media-body">
                                                            <span class="name">Folisise Chosielie</span>
                                                            <span class="message">Art director, Movie Cut</span>
                                                            <span class="badge badge-outline status"></span>
                                                        </div>
                                                    </div>
                                                </a>                            
                                            </li>
                                            <li class="online">
                                                <a href="javascript:void(0);">
                                                    <div class="media">
                                                        <img class="media-object " src="assets/images/xs/avatar3.jpg" alt="">
                                                        <div class="media-body">
                                                            <span class="name">Alexander</span>
                                                            <span class="message">Writter, Mag Editor</span>
                                                            <span class="badge badge-outline status"></span>
                                                        </div>
                                                    </div>
                                                </a>                            
                                            </li>                        
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8 col-md-12">
                                <div class="card">
                                    <div class="header">
                                        <h2><strong>Add</strong> Post</h2>
                                        <ul class="header-dropdown">
                                            <li class="dropdown"> <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> <i class="zmdi zmdi-more"></i> </a>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                    <li><a href="blog-post.html">New Post</a></li>
                                                    <li><a href="blog-details.html">Post Detail</a></li>
                                                    <li><a href="blog-dashboard.html">Dashboard</a></li>
                                                    <li><a href="javascript:void(0);" class="boxs-close">Delete</a></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="body m-b-10">
                                        <div class="form-group">
                                            <textarea rows="4" class="form-control no-resize" placeholder="Please type what you want..."></textarea>
                                        </div>
                                        <div class="post-toolbar-b">
                                            <button class="btn btn-warning btn-icon btn-icon-mini btn-round"><i class="zmdi zmdi-attachment"></i></button>
                                            <button class="btn btn-warning btn-icon btn-icon-mini btn-round"><i class="zmdi zmdi-camera"></i></button>
                                            <button class="btn btn-primary btn-round">Post</button>
                                        </div>
                                    </div>
                                    <div class="body">
                                        <div class="post-img">
                                            <img src="assets/images/image1.jpg" class="img-fluid" alt="">
                                        </div>                                
                                        <h5 class="m-t-20 m-b-0 post_title">It is a long established fact that a be distracted</h5>                                
                                        <p>If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text</p>                                
                                        <div class="form-group m-b-0">
                                            <button class="btn btn-info btn-round">Like 5</button>
                                            <button class="btn btn-primary btn-simple btn-round">Reply</button>
                                            <span class="date m-l-20"><i class="zmdi zmdi-alarm"></i> 7min ago</span>
                                        </div>                                
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
        <!-- Estadísticas del partido -->
        <div class="col-lg-6 col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Estadísticas</h3>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Estadística</th>
                      <th>{{ partido?.equipo_local?.nombre || 'Local' }}</th>
                      <th>{{ partido?.equipo_visitante?.nombre || 'Visitante' }}</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>Goles</td>
                      <td>{{ partido?.goles_equipo_local || 0 }}</td>
                      <td>{{ partido?.goles_equipo_visitante || 0 }}</td>
                    </tr>
                    <tr>
                      <td>Faltas</td>
                      <td>{{ estadisticas?.faltas_local || 0 }}</td>
                      <td>{{ estadisticas?.faltas_visitante || 0 }}</td>
                    </tr>
                    <tr>
                      <td>Tarjetas Amarillas</td>
                      <td>{{ estadisticas?.tarjetas_amarillas_local || 0 }}</td>
                      <td>{{ estadisticas?.tarjetas_amarillas_visitante || 0 }}</td>
                    </tr>
                    <tr>
                      <td>Tarjetas Rojas</td>
                      <td>{{ estadisticas?.tarjetas_rojas_local || 0 }}</td>
                      <td>{{ estadisticas?.tarjetas_rojas_visitante || 0 }}</td>
                    </tr>
                    <tr>
                      <td>Tarjetas Azules</td>
                      <td>{{ estadisticas?.tarjetas_azules_local || 0 }}</td>
                      <td>{{ estadisticas?.tarjetas_azules_visitante || 0 }}</td>
                    </tr>
                    <tr>
                      <td>Penales</td>
                      <td>{{ estadisticas?.penales_local || 0 }}</td>
                      <td>{{ estadisticas?.penales_visitante || 0 }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Eventos del partido -->
        <div class="col-lg-6 col-md-12">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title">Cronología de Eventos</h3>
              <div class="card-options">
                <button v-if="canManageEventos" class="btn btn-primary btn-sm" @click="openEventoModal">
                  <i class="fa fa-plus"></i> Nuevo Evento
                </button>
              </div>
            </div>
            <div class="card-body">
              <div class="timeline-eventos">
                <div v-if="!eventos.length" class="text-center py-3">
                  No hay eventos registrados para este partido
                </div>
                <div v-for="evento in sortedEventos" :key="evento.id" class="evento-item">
                  <div class="minuto">{{ evento.minuto }}'</div>
                  <div class="tipo-evento">
                    <i :class="getIconoEvento(evento.tipo_evento)"></i>
                  </div>
                  <div class="detalle-evento">
                    <strong>{{ evento.jugador?.nombre || 'Jugador' }}</strong>
                    <span class="equipo-nombre">{{ getEquipoNombre(evento.jugador?.equipo_id) }}</span>
                    <span class="tipo-texto">{{ getTipoEventoTexto(evento.tipo_evento) }}</span>
                  </div>
                  <div v-if="canManageEventos" class="acciones">
                    <button class="btn btn-sm btn-outline-primary" @click="editarEvento(evento)">
                      <i class="fa fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger ml-1" @click="eliminarEvento(evento)">
                      <i class="fa fa-trash"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Modal para eventos -->
  <EventoEquipoForm v-if="showEventoModal" :mode="eventoMode" :partido="partido" :currentEvento="currentEvento"
    @submit="handleEventoSubmit" @cancel="closeEventoModal" />
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { useRoute } from 'vue-router';
// Fix the import name from usePartidosStore to usePartidoStore
import { usePartidoStore } from '@/stores/partidos';
import { useEventoPartidoStore } from '@/stores/eventoPartido';
import { useAuthStore } from '@/stores/auth';
import EventoEquipoForm from '@/components/partidos/EventoEquipoForm.vue';
import Swal from 'sweetalert2';

const route = useRoute();
const partidoStore = usePartidoStore();
const eventoPartidoStore = useEventoPartidoStore();
const authStore = useAuthStore();

const loading = ref(true);
const error = ref(null);
const partido = ref({});
const eventos = ref([]);
const estadisticas = ref({
  faltas_local: 0,
  faltas_visitante: 0,
  tarjetas_amarillas_local: 0,
  tarjetas_amarillas_visitante: 0,
  tarjetas_rojas_local: 0,
  tarjetas_rojas_visitante: 0,
  tarjetas_azules_local: 0,
  tarjetas_azules_visitante: 0,
  penales_local: 0,
  penales_visitante: 0
});

// Modal state
const showEventoModal = ref(false);
const eventoMode = ref('create');
const currentEvento = ref({});

// Computed properties
const formatFecha = computed(() => {
  if (!partido.value?.fecha) return 'Fecha no disponible';
  return new Date(partido.value.fecha).toLocaleDateString('es-ES', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  });
});

const formatHora = computed(() => {
  if (!partido.value?.hora_inicio) return 'Hora no disponible';
  return partido.value.hora_inicio;
});

const getEstadoPartido = computed(() => {
  if (!partido.value?.estado) return 'No iniciado';

  switch (partido.value.estado) {
    case 'programado': return 'Programado';
    case 'en_curso': return 'En curso';
    case 'finalizado': return 'Finalizado';
    case 'suspendido': return 'Suspendido';
    default: return partido.value.estado;
  }
});

const sortedEventos = computed(() => {
  return [...eventos.value].sort((a, b) => a.minuto - b.minuto);
});

const canManageEventos = computed(() => {
  return authStore.user && authStore.user.isAdmin;
});

// Methods
const getEquipoNombre = (equipoId) => {
  if (equipoId === partido.value?.equipo_local_id) {
    return partido.value?.equipo_local?.nombre || 'Local';
  } else if (equipoId === partido.value?.equipo_visitante_id) {
    return partido.value?.equipo_visitante?.nombre || 'Visitante';
  }
  return 'Equipo desconocido';
};

const getTipoEventoTexto = (tipo) => {
  switch (tipo) {
    case 'gol': return 'Gol';
    case 'tarjeta_amarilla': return 'Tarjeta Amarilla';
    case 'tarjeta_roja': return 'Tarjeta Roja';
    case 'tarjeta_azul': return 'Tarjeta Azul';
    case 'falta': return 'Falta';
    case 'lesion': return 'Lesión';
    case 'penal': return 'Penal';
    default: return tipo;
  }
};

const getIconoEvento = (tipo) => {
  switch (tipo) {
    case 'gol': return 'fa fa-futbol-o';
    case 'tarjeta_amarilla': return 'fa fa-square text-warning';
    case 'tarjeta_roja': return 'fa fa-square text-danger';
    case 'tarjeta_azul': return 'fa fa-square text-primary';
    case 'falta': return 'fa fa-exclamation-triangle';
    case 'lesion': return 'fa fa-medkit';
    case 'penal': return 'fa fa-bullseye';
    default: return 'fa fa-circle';
  }
};

const cargarPartido = async () => {
  const partidoId = route.params.id;
  if (!partidoId) {
    error.value = 'ID de partido no proporcionado';
    loading.value = false;
    return;
  }

  try {
    loading.value = true;
    error.value = null;
    // Change partidosStore to partidoStore (singular)
    partido.value = await partidoStore.fetchPartidoById(partidoId);
    await cargarEventos();
  } catch (err) {
    console.error('Error al cargar partido:', err);
    error.value = 'Error al cargar los datos del partido';
  } finally {
    loading.value = false;
  }
};

const cargarEventos = async () => {
  if (partido.value?.id) {
    try {
      eventos.value = await eventoPartidoStore.fetchEventosByPartido(partido.value.id);
      calcularEstadisticas();
    } catch (err) {
      console.error('Error al cargar eventos:', err);
    }
  }
};

const calcularEstadisticas = () => {
  // Reiniciar estadísticas
  Object.keys(estadisticas.value).forEach(key => {
    estadisticas.value[key] = 0;
  });

  // Calcular estadísticas basadas en eventos
  eventos.value.forEach(evento => {
    const esLocal = evento.jugador?.equipo_id === partido.value?.equipo_local_id;
    const equipoKey = esLocal ? 'local' : 'visitante';

    switch (evento.tipo_evento) {
      case 'falta':
        estadisticas.value[`faltas_${equipoKey}`]++;
        break;
      case 'tarjeta_amarilla':
        estadisticas.value[`tarjetas_amarillas_${equipoKey}`]++;
        break;
      case 'tarjeta_roja':
        estadisticas.value[`tarjetas_rojas_${equipoKey}`]++;
        break;
      case 'tarjeta_azul':
        estadisticas.value[`tarjetas_azules_${equipoKey}`]++;
        break;
      case 'penal':
        estadisticas.value[`penales_${equipoKey}`]++;
        break;
    }
  });
};

// Gestión de eventos
const openEventoModal = () => {
  eventoMode.value = 'create';
  currentEvento.value = {};
  showEventoModal.value = true;
};

const closeEventoModal = () => {
  showEventoModal.value = false;
};

const editarEvento = (evento) => {
  currentEvento.value = { ...evento };
  eventoMode.value = 'edit';
  showEventoModal.value = true;
};

const eliminarEvento = (evento) => {
  Swal.fire({
    title: '¿Estás seguro?',
    text: "Esta acción no se puede revertir",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Sí, eliminar',
    cancelButtonText: 'Cancelar'
  }).then(async (result) => {
    if (result.isConfirmed) {
      try {
        await eventoPartidoStore.deleteEvento(evento.id, partido.value.id);
        await cargarEventos();
        Swal.fire(
          'Eliminado',
          'El evento ha sido eliminado correctamente',
          'success'
        );
      } catch (error) {
        Swal.fire(
          'Error',
          'No se pudo eliminar el evento',
          'error'
        );
      }
    }
  });
};

const handleEventoSubmit = async (formData) => {
  try {
    if (eventoMode.value === 'create') {
      await eventoPartidoStore.createEvento(formData);
    } else {
      await eventoPartidoStore.updateEvento(currentEvento.value.id, formData);
    }

    closeEventoModal();
    await cargarEventos();

    Swal.fire({
      icon: 'success',
      title: eventoMode.value === 'create' ? 'Evento creado' : 'Evento actualizado',
      showConfirmButton: false,
      timer: 1500
    });
  } catch (error) {
    console.error('Error al guardar evento:', error);
    Swal.fire({
      icon: 'error',
      title: 'Error',
      text: 'No se pudo guardar el evento'
    });
  }
};

// Watch for route changes
watch(() => route.params.id, (newId) => {
  if (newId) {
    cargarPartido();
  }
}, { immediate: true });

onMounted(() => {
  cargarPartido();
});
</script>

<style scoped>
.equipo-logo {
  max-width: 80px;
  max-height: 80px;
  margin-bottom: 10px;
}

.marcador {
  text-align: center;
  padding: 0 20px;
}

.estado-partido {
  font-size: 14px;
  color: #666;
  margin-top: 5px;
}

.info-item {
  margin-bottom: 8px;
}

.timeline-eventos {
  margin-top: 15px;
  max-height: 500px;
  overflow-y: auto;
}

.evento-item {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
  padding: 8px;
  border-bottom: 1px solid #f0f0f0;
}

.minuto {
  font-weight: bold;
  min-width: 40px;
}

.tipo-evento {
  margin: 0 15px;
  font-size: 18px;
}

.detalle-evento {
  flex-grow: 1;
}

.equipo-nombre {
  margin-left: 8px;
  color: #666;
  font-size: 0.9em;
}

.tipo-texto {
  display: block;
  font-size: 0.85em;
  color: #777;
}

.acciones {
  margin-left: 10px;
}
</style>
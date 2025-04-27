<template>
  <section class="content profile-page">
    <div class="container">
      <div class="block-header">
        <div class="row">
          <div class="col-lg-5 col-md-8 col-sm-12">
            <h2>Detalles del Partido</h2>
          </div>
          <div class="col-lg-7 col-md-4 col-sm-12 text-right">
            <ul class="breadcrumb float-md-right padding-0">
              <li class="breadcrumb-item"><router-link to="/"><i class="zmdi zmdi-home"></i></router-link></li>
              <li class="breadcrumb-item"><router-link to="/partidos">Partidos</router-link></li>
              <li class="breadcrumb-item active">Detalles</li>
            </ul>
          </div>
        </div>
      </div>
      <!-- Loading indicator -->
      <div v-if="loading">
        <AppLoader />
      </div>

      <!-- Error message -->
      <div v-else-if="error" class="alert alert-danger">
        {{ error }}
      </div>

      <!-- Main content -->
      <div class="row clearfix">
        <div class="col-lg-12">
          <div class="card">
            <div class="body text-center">
              <div class="row">
                <div class="col">
                  <div class="chart easy-pie-chart-1" data-percent="100"> <span><img
                        v-if="partido?.equipo_local?.imagen" :src="`/storage/${partido.equipo_local.imagen}`"
                        alt="Logo Local" class="rounded-circle img-raised"></span></div>
                  <h6>{{ partido?.equipo_local?.nombre || 'Equipo Local' }}</h6>
                </div>
                <div class="col  text-center">
                  <h3>{{ partido?.goles_equipo_local || 0 }} - {{ partido?.goles_equipo_visitante || 0 }}</h3>
                  <small>{{ formatFecha.value }} - {{ formatHora.value }}</small>
                </div>
                <div class="col">
                  <div class="chart easy-pie-chart-1" data-percent="100"> <span><img
                        v-if="partido?.equipo_visitante?.imagen" :src="`/storage/${partido.equipo_visitante.imagen}`"
                        alt="Logo Visitante" class="rounded-circle img-raised"></span></div>
                  <h6>{{ partido?.equipo_visitante?.nombre || 'Equipo Visitante' }}</h6>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="col-lg-3 col-md-6 col-6">
            <div class="card text-center">
              <div class="body">
                <i class="zmdi zmdi-card zmdi-hc-2x text-warning"></i>
                <h5 class="m-b-0 number count-to">{{ estadisticas?.tarjetas_amarillas_local +
                  estadisticas?.tarjetas_amarillas_visitante }}</h5>
                <small>Tarjetas Amarillas</small>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 col-6">
            <div class="card text-center">
              <div class="body">
                <i class="zmdi zmdi-card zmdi-hc-2x text-danger"></i>
                <h5 class="m-b-0 number count-to">{{ estadisticas?.tarjetas_rojas_local +
                  estadisticas?.tarjetas_rojas_visitante }}</h5>
                <small>Tarjetas Rojas</small>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 col-6">
            <div class="card text-center">
              <div class="body">
                <i class="zmdi zmdi-card zmdi-hc-2x text-info"></i>
                <h5 class="m-b-0 number count-to">{{ estadisticas?.tarjetas_azules_local +
                  estadisticas?.tarjetas_azules_visitante }}</h5>
                <small>Tarjetas Azules</small>
              </div>
            </div>
          </div>
          <div class="col-lg-3 col-md-6 col-6">
            <div class="card text-center">
              <div class="body">
                <i class="zmdi zmdi-alert-triangle zmdi-hc-2x text-warning"></i>
                <h5 class="m-b-0 number count-to">{{ estadisticas?.faltas_local + estadisticas?.faltas_visitante }}</h5>
                <small>Faltas Totales</small>
              </div>
            </div>
          </div>
          <div class="col-12 text-right mt-3 mb-3" v-if="authStore.isAdmin">
            <button class="btn btn-primary" @click="openEventoModal">
              <i class="zmdi zmdi-plus-circle"></i> Agregar Evento
            </button>
          </div>
          <div class="col-lg-4 col-md-12">
            <div class="card">
              <div class="header">
                <h2><strong>Ubicación</strong></h2>
              </div>
              <div class="body">
                <small class="text-muted">Dirección: </small>
                <p>{{ partido?.ubicacion?.direccion || 'No disponible' }}</p>
                <div>
                  <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d942.9334011270495!2d-98.78326208709375!3d19.03145689540873!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x85ce3f0000a5d2f7%3A0x7a094fc3edd120fa!2sCANCHA%20DE%20FUTBOL%20R%C3%81PIDO!5e0!3m2!1ses!2smx!4v1744878503941!5m2!1ses!2smx"
                    title="Ubicación de la Cancha de Fútbol Rápido" width="100%" height="100%" style="border:0;"
                    allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
                <hr>
                <small class="text-muted">Ciudad: </small>
                <p>{{ partido?.ubicacion?.ciudad || 'No disponible' }}</p>
                <hr>
                <small class="text-muted">Código Postal: </small>
                <p>{{ partido?.ubicacion?.codigo_postal || 'No disponible' }}</p>
                <hr>
                <small class="text-muted">Capacidad: </small>
                <p class="m-b-0">{{ partido?.ubicacion?.capacidad || 'No especificada' }}</p>
              </div>
            </div>
          </div>
          <div class="col-lg-8 col-md-12">
            <div class="card">
              <div class="header">
                <h2><strong>Eventos</strong></h2>
              </div>
              <div class="body">
                <div class="table-responsive">
                  <table id="tabla-eventos" class="table table-hover dashboard-task-infos">
                    <thead>
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
    </div>
  </section>

  <!-- Modal para eventos - solo visible para administradores -->
  <EventoEquipoForm v-if="authStore.isAdmin" :mode="eventoMode" :partido="partido" :currentEvento="currentEvento"
    @submit="handleEventoSubmit" @cancel="closeEventoModal" />
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { usePartidoStore } from '@/stores/partidos';
import { useEventoPartidoStore } from '@/stores/eventoPartido';
import { useAuthStore } from '@/stores/auth';
import EventoEquipoForm from '@/components/partidos/EventoEquipoForm.vue';
import Notification from '@/utils/notification';
import { initializeDataTable, attachTableEvents } from '@/utils/datatables-utils';


const route = useRoute();
const partidoStore = usePartidoStore();
const eventoPartidoStore = useEventoPartidoStore();
const authStore = useAuthStore();
// Configuración de columnas para DataTable
const columns = [
  { data: 'minuto', title: 'Minuto' },
  { 
    data: 'jugador', 
    title: 'Jugador',
    render: function(data) {
      return data?.user?.name || 'Desconocido';
    }
  },
  { 
    data: 'tipo_evento', 
    title: 'Tipo de Evento',
    render: function(data) {
      return getIconoEvento(data) + getTipoEventoTexto(data);
    }
  }
];

if(authStore.isAdmin) {
  columns.push(
    
  { 
    data: null, 
    title: 'Acciones',
    orderable: false,
    render: function(data) {
      if (authStore.isAdmin) {
        return `
          <button class="btn btn-icon btn-neutral btn-icon-mini btnEditar" data-id="${data.id}">
            <i class="zmdi zmdi-edit"></i>
          </button>
          <button class="btn btn-icon btn-neutral btn-icon-mini btnEliminar" data-id="${data.id}">
            <i class="zmdi zmdi-delete"></i>
          </button>
        `;
      }
      return '';
    }
  })
}

const loading = ref(true);
const error = ref(null);
const partido = ref({});
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
    case 'gol': return 'zmdi zmdi-soccer zmdi-hc-2x';
    case 'tarjeta_amarilla': return 'zmdi zmdi-card zmdi-hc-2x text-warning';
    case 'tarjeta_roja': return 'zmdi zmdi-card zmdi-hc-2x text-danger';
    case 'tarjeta_azul': return 'zmdi zmdi-card zmdi-hc-2x text-primary';
    case 'falta': return 'zmdi zmdi-alert-triangle zmdi-hc-2x text-warning';
    case 'lesion': return 'zmdi zmdi-hospital zmdi-hc-2x';
    case 'penal': return 'zmdi zmdi-dot-circle zmdi-hc-2x';
    default: return 'zmdi zmdi-circle zmdi-hc-2x';
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
      await eventoPartidoStore.fetchEventosByPartido(partido.value.id);
      calcularEstadisticas();
      
      inicializarTabla();
    } catch (err) {
      console.error('Error al cargar eventos:', err);
    }
  }
};

const inicializarTabla = () =>{
  if ($.fn.DataTable.isDataTable('#tabla-eventos')) {
    $('#tabla-eventos').DataTable().destroy();
  }

  const sortedEventos = eventoPartidoStore.getEventosByPartidoId(partido.value.id).sort((a, b) => a.minuto - b.minuto);
  console.log('Eventos ordenados:', sortedEventos);
  initializeDataTable('tabla-eventos', sortedEventos, columns);
  attachTableEvents('tabla-eventos', editarEvento, eliminarEvento);
}

const calcularEstadisticas = () => {
  Object.keys(estadisticas.value).forEach(key => {
    estadisticas.value[key] = 0;
  });
  eventoPartidoStore.getEventosByPartidoId(partido.value.id).forEach(evento => {
    const esLocal = evento.jugador?.user?.equipo_id === partido.value?.equipo_local_id;
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

const openEventoModal = () => {
  eventoMode.value = 'create';
  currentEvento.value = {};
  $('#eventoPartidoModal').modal('show');
};

const closeEventoModal = () => {
  $('#eventoPartidoModal').modal('hide');
};

const editarEvento = (eventoId) => {
  // Buscar el evento completo usando el ID
  const eventos = eventoPartidoStore.getEventosByPartidoId(partido.value.id);
  const evento = eventos.find(e => e.id === eventoId);
  
  if (!evento) {
    console.error('No se encontró el evento con ID:', eventoId);
    Notification.error('No se pudo encontrar el evento para editar', 'Error');
    return;
  }
  
  console.log('Editando evento:', evento);
  currentEvento.value = { ...evento };
  eventoMode.value = 'edit';
  $('#eventoPartidoModal').modal('show');
};

const eliminarEvento = (eventoId) => {
  // Buscar el evento completo usando el ID
  const eventos = eventoPartidoStore.getEventosByPartidoId(partido.value.id);
  const evento = eventos.find(e => e.id === eventoId);
  
  if (!evento) {
    console.error('No se encontró el evento con ID:', eventoId);
    Notification.error('No se pudo encontrar el evento para eliminar', 'Error');
    return;
  }
  
  console.log('Eliminando evento:', evento);
  
  Notification.confirm(
    "Esta acción no se puede revertir",
    '¿Estás seguro?',
    'Sí, eliminar',
    'Cancelar'
  ).then(async (result) => {
    if (result.isConfirmed) {
      try {
        await eventoPartidoStore.deleteEvento(evento.id, partido.value.id);
        // El store ya actualiza el array de eventos internamente
        calcularEstadisticas();
        
        inicializarTabla();
        
        await Notification.success('El evento ha sido eliminado correctamente', 'Eliminado');
      } catch (error) {
        console.error('Error al eliminar evento:', error);
        // Verificar si es un error de autorización
        if (error.response?.status === 403 || 
            error.response?.data?.message === 'This action is unauthorized.' ||
            eventoPartidoStore.error === 'No tienes permiso para eliminar este evento') {
          await Notification.error('No tienes permiso para eliminar este evento. Solo los administradores pueden realizar esta acción.', 'Error de autorización');
        } else {
          await Notification.error(error.response?.data?.message || eventoPartidoStore.error || 'No se pudo eliminar el evento', 'Error');
        }
      }
    }
  });
}

const handleEventoSubmit = async (formData) => {
  try {
    if (eventoMode.value === 'create') {
      await eventoPartidoStore.createEvento(formData);
      // El store ya actualiza el array de eventos internamente
    } else {
      await eventoPartidoStore.updateEvento(currentEvento.value.id, formData);
      // El store ya actualiza el evento en su estado interno
    }

    closeEventoModal();
    calcularEstadisticas();    
    inicializarTabla();

    await Notification.success(
      eventoMode.value === 'create' ? 'Evento creado' : 'Evento actualizado'
    );
  } catch (error) {
    console.error('Error al guardar evento:', error);
    await Notification.error('No se pudo guardar el evento', 'Error');
  }
}

// Cargar partido cuando se monta el componente
onMounted(() => {
  const partidoId = route.params.id;
  if (partidoId) {
    cargarPartido();
  }
});
</script>

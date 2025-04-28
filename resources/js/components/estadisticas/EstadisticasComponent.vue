<template>
    <section class="content contact">

        <div class="container">
            <div v-if="loading">
                <AppLoader />
            </div>
            <div v-else-if="error" class="text-red-500">
                Error: {{ error }}
            </div>
            <div v-else class="container-fluid">
                <div class="block-header">
                    <div class="row clearfix">
                        <div class="col-lg-5 col-md-5 col-sm-12">
                            <h2>Estadísticas</h2>
                        </div>
                        <div class="col-lg-7 col-md-7 col-sm-12">
                            <ul class="breadcrumb float-md-right padding-0">
                                <li class="breadcrumb-item"><a href="/"><i class="zmdi zmdi-home"></i> Inicio</a></li>
                                <li class="breadcrumb-item active">Estadísticas</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <TorneosMenu @torneoSeleccionado="handleTorneoSeleccionado" />
                <div class="row clearfix">
                    <div class="col-lg-6">
                        <div class="card">
                            <div class="header">
                                <h2><strong>Tabla</strong> de Goleadores</h2>
                            </div>
                            <div class="body">
                                <div class="table-responsive earning-report">
                                    <table id="tabla-goleadores" class="table m-b-0 table-hover">
                                        <thead>
                                            <tr>
                                                <th>Posición</th>
                                                <th colspan="2">Jugador</th>
                                                <th>Equipo</th>
                                                <th>Goles</th>
                                                <th>Partidos</th>
                                                <th>Promedio</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(goleador, index) in goleadores" :key="index">
                                                <td>{{ index + 1 }}</td>
                                                <td style="width:60px;">
                                                    <span class="rounded">
                                                        <img src="assets/images/xs/avatar1.jpg" alt="jugador" width="50">
                                                    </span>
                                                </td>
                                                <td>
                                                    <h6>{{ goleador.jugador?.user?.name || 'Desconocido' }}</h6>
                                                    <small class="text-muted">{{ goleador.equipo?.nombre || 'Sin equipo' }}</small>
                                                </td>
                                                <td>{{ goleador.equipo?.nombre || 'Desconocido' }}</td>
                                                <td><span class="badge badge-success">{{ goleador.goles }}</span></td>
                                                <td>{{ goleador.partidos }}</td>
                                                <td>{{ goleador.partidos > 0 ? (goleador.goles / goleador.partidos).toFixed(2) : '0.00' }}</td>
                                            </tr>
                                            <tr v-if="goleadores.length === 0">
                                                <td colspan="7" class="text-center">No hay datos de goleadores disponibles</td>
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
import { ref, onMounted } from 'vue';
import { useEventoPartidoStore } from '@/stores/eventoPartido';
import { useTorneoStore } from '@/stores/torneos';
import { useEquiposStore } from '@/stores/equipos';
import TorneosMenu from '@/components/global/TorneosMenu.vue';

// Estado
const loading = ref(true);
const error = ref(null);
const goleadores = ref([]);
const selectedTorneoId = ref(null);

// Stores
const eventoPartidoStore = useEventoPartidoStore();
const torneoStore = useTorneoStore();
const equipoStore = useEquiposStore();

// Métodos
const cargarGoleadores = async (torneoId) => {
    try {
        loading.value = true;
        error.value = null;

        // Cargar eventos de partidos para el torneo seleccionado
        await eventoPartidoStore.fetchEventosByTorneo(torneoId);

        // Procesar eventos para obtener estadísticas de goleadores
        const eventos = eventoPartidoStore.getEventosByTorneoId(torneoId);
        const goleadoresMap = {};

        // Filtrar solo eventos de gol
        const eventosGol = eventos.filter(evento => evento.tipo_evento === 'gol');

        // Agrupar goles por jugador
        for (const evento of eventosGol) {
            if (!evento.jugador || !evento.jugador.id) continue;

            const jugadorId = evento.jugador.id;
            const equipoId = evento.jugador.equipo_id;

            if (!goleadoresMap[jugadorId]) {
                // Cargar datos del equipo si es necesario
                let equipo = null;
                try {
                    if (equipoId) {
                        const equipos = await equipoStore.loadEquiposByTorneo(torneoId);
                        equipo = equipos.find(e => e.id === equipoId);
                    }
                } catch (err) {
                    console.error('Error al cargar equipo:', err);
                }

                goleadoresMap[jugadorId] = {
                    jugador: evento.jugador,
                    equipo: equipo,
                    goles: 0,
                    partidos: new Set(), // Usamos Set para contar partidos únicos
                };
            }

            goleadoresMap[jugadorId].goles++;
            if (evento.partido_id) {
                goleadoresMap[jugadorId].partidos.add(evento.partido_id);
            }
        }

        // Convertir el mapa a un array y ordenar por goles (descendente)
        const goleadoresArray = Object.values(goleadoresMap).map(g => ({
            ...g,
            partidos: g.partidos.size // Convertir el Set a un número
        }));

        goleadoresArray.sort((a, b) => b.goles - a.goles);
        goleadores.value = goleadoresArray;

        // Los datos ya están listos para mostrar en la tabla HTML

    } catch (err) {
        console.error('Error al cargar goleadores:', err);
        error.value = 'Error al cargar los datos de goleadores';
    } finally {
        loading.value = false;
    }
};

const handleTorneoSeleccionado = async (torneoId) => {
    selectedTorneoId.value = torneoId;
    await cargarGoleadores(torneoId);
};

onMounted(async () => {
    try {
        // Cargar torneos si es necesario
        if (torneoStore.torneosCatalog.length === 0) {
            await torneoStore.fetchTorneosCatalog();
        }

        // Seleccionar el primer torneo por defecto
        if (torneoStore.torneosCatalog.length > 0) {
            selectedTorneoId.value = torneoStore.torneosCatalog[0].id;
            await cargarGoleadores(selectedTorneoId.value);
        } else {
            loading.value = false;
            error.value = 'No hay torneos disponibles';
        }
    } catch (err) {
        console.error('Error en la inicialización:', err);
        error.value = 'Error al cargar los datos iniciales';
        loading.value = false;
    }
});
</script>
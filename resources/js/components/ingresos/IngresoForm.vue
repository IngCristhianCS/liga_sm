<!-- components/IngresoForm.vue -->
<template>
    <div class="modal fade" id="largeModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="largeModalLabel">
                        {{ mode === 'create' ? 'Nuevo Ingreso' : 'Editar Ingreso' }}
                    </h4>
                </div>
                <form @submit.prevent="handleSubmit">
                    <div class="modal-body">
                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Descripción</label>
                                    <input type="text" class="form-control" v-model="formData.descripcion" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tipo</label>
                                    <input type="text" class="form-control" v-model="formData.tipo" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Monto</label>
                                    <input type="number" class="form-control" v-model="formData.monto" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha</label>
                                    <input type="date" class="form-control" v-model="formData.fecha" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Torneo</label>
                                    <select class="form-control" v-model="formData.torneo_id"
                                        @change="actualizarEquipos" required>
                                        <option v-for="torneo in torneoStore.torneos" :key="torneo.id"
                                            :value="torneo.id">
                                            {{ torneo.nombre }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Equipo</label>
                                    <select class="form-control" v-model="formData.equipo_id">
                                        <option v-for="equipo in equiposTorneo" :key="equipo.id" :value="equipo.id">
                                            {{ equipo.nombre }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Patrocinador ID</label>
                                    <input type="number" class="form-control" v-model="formData.patrocinador_id">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Estatus</label>
                                    <select class="form-control" v-model="formData.estatus">
                                        <option value="ok">ok</option>
                                        <option value="pe">pe</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-default btn-round waves-effect">
                            {{ mode === 'create' ? 'Crear Ingreso' : 'Actualizar Ingreso' }}
                        </button>
                        <button type="button" class="btn btn-danger btn-simple btn-round waves-effect"
                            data-dismiss="modal" @click="resetForm">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, reactive } from 'vue';
import { useTorneoStore } from '@/stores/torneos';

/**
 * @typedef {Object} Ingreso
 * @property {number} [id]
 * @property {string} descripcion
 * @property {string} tipo
 * @property {number} monto
 * @property {string} fecha
 * @property {number|null} torneo_id
 * @property {number|null} equipo_id
 * @property {number|null} patrocinador_id
 * @property {'ok'|'pe'} estatus
 */

const props = defineProps({
    /** 
     * Modo de operación del formulario 
     * @type {'create'|'edit'}
     */
    mode: {
        type: String,
        default: 'create',
    },

    /** 
     * Datos del ingreso a editar 
     * @type {Ingreso}
     */
    currentIngreso: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits([
    /** Evento al enviar el formulario @param {Ingreso} formData */
    'submit',

    /** Evento al cancelar la operación */
    'cancel',
]);

const torneoStore = useTorneoStore();

/** @type {import('vue').Ref<Array<import('@/stores/torneos').Equipo>>} */
const equiposTorneo = ref([]);

/** @type {Ingreso} */
const formData = reactive({ ...props.currentIngreso });

/**
 * Actualiza la lista de equipos según el torneo seleccionado
 * @returns {void}
 */
const actualizarEquipos = () => {
    const torneoSeleccionado = torneoStore.torneos.find(
        (torneo) => torneo.id === formData.torneo_id
    );
    equiposTorneo.value = torneoSeleccionado ? torneoSeleccionado.equipos : [];
};

/**
 * Maneja el envío del formulario
 * @returns {void}
 */
const handleSubmit = () => {
    emit('submit', formData);
};

/**
 * Reinicia el formulario y emite evento de cancelación
 * @returns {void}
 */
const resetForm = () => {
    emit('cancel');
};

// Watcher para sincronizar cambios en el prop currentIngreso
watch(
    () => props.currentIngreso,
    (newVal) => {
        Object.assign(formData, newVal);
        actualizarEquipos();
    },
    { deep: true }
);
</script>
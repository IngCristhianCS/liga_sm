<template>
    <div class="modal fade" id="largeModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="title" id="defaultModalLabel">
                        {{ mode === 'create' ? 'Nueva Jornada' : 'Editar Jornada' }}
                    </h4>
                </div>
                <form @submit.prevent="handleSubmit">
                    <div class="modal-body">
                        <div class="row clearfix">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Número de Jornada</label>
                                    <input type="number" class="form-control"
                                        :class="{ 'is-invalid': apiErrors.numero }" v-model="formData.numero" min="1"
                                        required>
                                    <div class="invalid-feedback">
                                        {{ apiErrors.numero?.[0] || 'El número es requerido' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Torneo</label>
                                    <select class="form-control" :class="{ 'is-invalid': apiErrors.torneo_id }"
                                        v-model="formData.torneo_id" required>
                                        <option value="">Seleccione un torneo</option>
                                        <option v-for="torneo in torneos" :key="torneo.id" :value="torneo.id">
                                            {{ torneo.nombre }}
                                        </option>
                                    </select>
                                    <div class="invalid-feedback">
                                        {{ apiErrors.torneo_id?.[0] || 'Debe seleccionar un torneo' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha Inicio</label>
                                    <input type="date" class="form-control"
                                        :class="{ 'is-invalid': apiErrors.fecha_inicio }"
                                        v-model="formData.fecha_inicio" required>
                                    <div class="invalid-feedback">
                                        {{ apiErrors.fecha_inicio?.[0] || 'La fecha de inicio es requerida' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fecha Fin</label>
                                    <input type="date" class="form-control"
                                        :class="{ 'is-invalid': apiErrors.fecha_fin }" v-model="formData.fecha_fin"
                                        required>
                                    <div class="invalid-feedback">
                                        {{ apiErrors.fecha_fin?.[0] || 'La fecha de fin es requerida' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-default btn-round waves-effect">
                            {{ mode === 'create' ? 'CREAR' : 'ACTUALIZAR' }}
                        </button>
                        <button type="button" class="btn btn-danger waves-effect"
                            @click="$emit('cancel')">CERRAR</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted } from 'vue';
import axios from 'axios';

const props = defineProps({
    mode: {
        type: String,
        default: 'create'
    },
    jornada: {
        type: Object,
        default: () => ({})
    }
});

const emit = defineEmits(['submit', 'cancel']);

const torneos = ref([]);
const apiErrors = ref({});

const formData = reactive({
    numero: '',
    torneo_id: '',
    fecha_inicio: '',
    fecha_fin: ''
});

const loadTorneos = async () => {
    try {
        const { data } = await axios.get('/api/torneos');
        torneos.value = data.data;
    } catch (error) {
        console.error('Error cargando torneos:', error);
    }
};

const handleSubmit = () => {
    apiErrors.value = {};
    emit('submit', { ...formData });
};

watch(() => props.jornada, (newVal) => {
    Object.assign(formData, newVal);
}, { deep: true });

onMounted(() => {
    loadTorneos();
});

const handleApiErrors = (errors) => {
    apiErrors.value = errors;
};

defineExpose({
    handleApiErrors
});
</script>
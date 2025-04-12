<template>
    <div class="modal fade" id="largeModal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="title" id="largeModalLabel">
              {{ mode === 'create' ? 'Nuevo Torneo' : 'Editar Torneo' }}
            </h4>
          </div>
          <form @submit.prevent="handleSubmit">
            <div class="modal-body">
              <div class="row clearfix">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" class="form-control" v-model="formData.nombre" required>
                  </div>
                </div>
  
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Categoría</label>
                    <select class="form-control" v-model="formData.categoria_id" required>
                      <option v-for="categoria in categorias" :value="categoria.id" :key="categoria.id">
                        {{ categoria.nombre }}
                      </option>
                    </select>
                  </div>
                </div>
  
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Temporada</label>
                    <select class="form-control" v-model="formData.temporada_id" required>
                      <option v-for="temporada in temporadas" :value="temporada.id" :key="temporada.id">
                        {{ temporada.nombre }}
                      </option>
                    </select>
                  </div>
                </div>
  
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Fecha Inicio</label>
                    <input type="date" class="form-control" v-model="formData.fecha_inicio" required>
                  </div>
                </div>
  
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Fecha Fin</label>
                    <input type="date" class="form-control" v-model="formData.fecha_fin" required>
                  </div>
                </div>
  
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Estado</label>
                    <select class="form-control" v-model="formData.estado" required>
                      <option value="activo">Activo</option>
                      <option value="finalizado">Finalizado</option>
                    </select>
                  </div>
                </div>
  
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Campeón</label>
                    <select class="form-control" v-model="formData.campeon_id">
                      <option :value="null">Ninguno</option>
                      <option v-for="equipo in equipos" :value="equipo.id" :key="equipo.id">
                        {{ equipo.nombre }}
                      </option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-default btn-round waves-effect">
                {{ mode === 'create' ? 'Crear Torneo' : 'Actualizar Torneo' }}
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
  import { reactive, watch } from 'vue';
  
  /**
   * @typedef {Object} Torneo
   * @property {number} [id]
   * @property {string} nombre
   * @property {number} categoria_id
   * @property {number} temporada_id
   * @property {string} fecha_inicio
   * @property {string} fecha_fin
   * @property {'activo'|'finalizado'} estado
   * @property {number|null} campeon_id
   */
  
  const props = defineProps({
    /** @type {'create'|'edit'} */
    mode: {
      type: String,
      default: 'create'
    },
    /** @type {Torneo} */
    currentTorneo: {
      type: Object,
      default: () => ({}),
    },
    /** @type {Array<import('@/stores/categorias').Categoria>} */
    categorias: {
      type: Array,
      default: () => [],
    },
    /** @type {Array<import('@/stores/temporadas').Temporada>} */
    temporadas: {
      type: Array,
      default: () => [],
    },
    /** @type {Array<import('@/stores/equipos').Equipo>} */
    equipos: {
      type: Array,
      default: () => [],
    }
  });
  
  const emit = defineEmits([
    /** @param {Torneo} formData */
    'submit',
    'cancel'
  ]);
  
  /** @type {Torneo} */
  const defaultTorneo = {
    nombre: '',
    categoria_id: null,
    temporada_id: null,
    fecha_inicio: '',
    fecha_fin: '',
    estado: 'activo',
    campeon_id: null,
  };
  
  /** @type {Torneo} */
  const formData = reactive({ ...props.currentTorneo });
  
  /**
   * Maneja el envío del formulario
   * @returns {void}
   */
  const handleSubmit = () => emit('submit', formData);
  
  /**
   * Reinicia el formulario
   * @returns {void}
   */
  const resetForm = () => emit('cancel');
  
  // Actualizar formData cuando cambia el prop
  watch(() => props.currentTorneo, (newVal) => {
    Object.assign(formData, newVal);
  }, { deep: true });
  </script>
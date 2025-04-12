<template>
    <div class="modal fade" id="largeModal" tabindex="-1" role="dialog">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="title" id="largeModalLabel">
              {{ mode === 'create' ? 'Nueva Temporada' : 'Editar Temporada' }}
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
                      <option value="activa">Activa</option>
                      <option value="finalizada">Finalizada</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-default btn-round waves-effect">
                {{ mode === 'create' ? 'Crear Temporada' : 'Actualizar Temporada' }}
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
   * @typedef {Object} Temporada
   * @property {number} [id]
   * @property {string} nombre
   * @property {string} fecha_inicio
   * @property {string} fecha_fin
   * @property {'activa'|'finalizada'} estado
   */
  
  const props = defineProps({
    /** @type {'create'|'edit'} */
    mode: {
      type: String,
      default: 'create'
    },
    /** @type {Temporada} */
    currentTemporada: {
      type: Object,
      default: () => ({}),
    }
  });
  
  const emit = defineEmits([
    /** @param {Temporada} formData */
    'submit',
    'cancel'
  ]);
  
  /** @type {Temporada} */
  const defaultTemporada = {
    nombre: '',
    fecha_inicio: '',
    fecha_fin: '',
    estado: 'activa',
  };
  
  /** @type {Temporada} */
  const formData = reactive({ ...props.currentTemporada });
  
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
  watch(() => props.currentTemporada, (newVal) => {
    Object.assign(formData, newVal);
  }, { deep: true });
  </script>
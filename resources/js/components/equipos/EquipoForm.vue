<template>
  <div class="modal fade" id="largeModal" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <form @submit.prevent="handleSubmit">
          <div class="modal-header">
            <h4 class="title" id="largeModalLabel">{{ mode === 'create' ? 'Crear Equipo' : 'Editar Equipo' }}</h4>
          </div>
          <div class="modal-body">
            <div class="row clearfix">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Nombre</label>
                  <input 
                    type="text" 
                    class="form-control" 
                    :class="{ 'is-invalid': !validation.nombre.valid && validation.nombre.valid !== null }"
                    v-model="formData.nombre"
                    @input="validateSingleField('nombre')"
                    placeholder="Nombre del equipo"
                  >
                  <div class="invalid-feedback">
                    {{ apiErrors.nombre?.[0] || 'Ingrese un nombre válido' }}
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Categoría</label>
                  <select 
                    class="form-control"
                    :class="{ 'is-invalid': !validation.categoria_id.valid && validation.categoria_id.valid !== null }"
                    v-model="formData.categoria_id"
                    @change="validateSingleField('categoria_id')"
                  >
                    <option value="">Seleccione una categoría</option>
                    <option v-for="categoria in categoriaStore.categorias" :key="categoria.id" :value="categoria.id">
                      {{ categoria.nombre }}
                    </option>
                  </select>
                  <div class="invalid-feedback">
                    {{ apiErrors.categoria_id?.[0] || 'Seleccione una categoría válida' }}
                  </div>
                </div>
              </div>
            </div>

            <div class="row clearfix">
              <div class="col-md-6">
                <div class="form-group">
                  <label>Entrenador</label>
                  <select 
                    class="form-control"
                    :class="{ 'is-invalid': !validation.entrenador_id.valid && validation.entrenador_id.valid !== null }"
                    v-model="formData.entrenador_id"
                    @change="validateSingleField('entrenador_id')"
                  >
                    <option value="">Seleccione un entrenador</option>
                    <option v-for="entrenador in entrenadores" :key="entrenador.id" :value="entrenador.id">
                      {{ entrenador.name }}
                    </option>
                  </select>
                  <div class="invalid-feedback">
                    {{ apiErrors.entrenador_id?.[0] || 'Seleccione un entrenador válido' }}
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-group">
                  <label>Imagen</label>
                  <input 
                    type="file" 
                    class="form-control" 
                    :class="{ 'is-invalid': !validation.imagen.valid && validation.imagen.valid !== null }"
                    @change="handleImageUpload"
                    accept="image/*"
                  >
                  <div class="invalid-feedback">
                    {{ apiErrors.imagen?.[0] || 'Seleccione una imagen válida' }}
                  </div>
                  <div v-if="formData.imagen && typeof formData.imagen === 'string'" class="mt-2">
                    <img :src="'/storage/' + formData.imagen" alt="equipo" class="img-thumbnail" style="max-height: 100px;">
                  </div>
                </div>
              </div>
            </div>

            <!-- Image Cropper Section -->
            <div class="row clearfix" v-if="imgSrc">
              <div class="col-md-8">
                <div style="max-height: 400px; border: 1px solid #ddd; overflow: hidden;">
                  <vue-cropper
                    ref="cropper"
                    :guides="true"
                    :view-mode="2"
                    drag-mode="crop"
                    :auto-crop-area="0.8"
                    :min-container-width="250"
                    :min-container-height="180"
                    :background="true"
                    :rotatable="true"
                    :src="imgSrc"
                    :aspect-ratio="1"
                    alt="Source Image"
                    :img-style="{ 'max-width': '100%', 'max-height': '400px' }"
                    style="max-height: 400px; width: 100%;">
                  </vue-cropper>
                </div>
                <div class="mt-2 mb-3">
                  <button type="button" class="btn btn-primary btn-sm mr-2" @click="cropImage">Recortar</button>
                  <button type="button" class="btn btn-info btn-sm mr-2" @click="rotate">Rotar</button>
                  <div class="btn-group">
                    <button type="button" class="btn btn-secondary btn-sm" @click="zoomIn">
                      <i class="zmdi zmdi-zoom-in"></i>
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" @click="zoomOut">
                      <i class="zmdi zmdi-zoom-out"></i>
                    </button>
                  </div>
                </div>
              </div>
              <div class="col-md-4">
                <div v-if="cropImg" class="text-center">
                  <h6>Vista previa</h6>
                  <img :src="cropImg" class="img-fluid img-thumbnail" alt="recortada" />
                </div>
                <div v-else-if="formData.imagen && typeof formData.imagen === 'string' && !imgSrc.includes(formData.imagen)" class="text-center">
                  <h6>Imagen actual</h6>
                  <img :src="'/storage/' + formData.imagen" class="img-fluid img-thumbnail" alt="actual" />
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-default btn-round waves-effect">
              {{ mode === 'create' ? 'Crear Equipo' : 'Actualizar Equipo' }}
            </button>
            <button type="button" class="btn btn-danger btn-simple btn-round waves-effect" data-dismiss="modal"
              @click="resetForm">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, watch } from 'vue';
import { RegexUtils, validateField as validateFieldUtil } from '@/utils/regex-util';
import Notification from '@/utils/notification';
import { useCategoriasStore } from '@/stores/categorias';
import VueCropper from 'vue-cropperjs';
import 'vue-cropperjs/node_modules/cropperjs/dist/cropper.min.css';

const props = defineProps({
  mode: {
    type: String,
    default: 'create'
  },
  currentEquipo: {
    type: Object,
    default: () => ({})
  },
  entrenadores: {
    type: Array,
    default: () => []
  }
});

const emit = defineEmits(['submit', 'cancel']);

// Use the stores
const categoriaStore = useCategoriasStore();

// Form data
const formData = reactive({
  nombre: '',
  categoria_id: '',
  entrenador_id: '',
  imagen: null
});

// Image cropper data
const imgSrc = ref('');
const cropImg = ref('');
const cropper = ref(null);

// Validation state with RegexUtils
const validation = reactive({
  nombre: { valid: null, regex: RegexUtils.name, optional: false },
  categoria_id: { valid: null, optional: false },
  entrenador_id: { valid: null, optional: false },
  imagen: { valid: null, optional: true }
});

const apiErrors = ref({});

// Watch for changes in props.currentEquipo
watch(() => props.currentEquipo, (newValue) => {
  if (newValue && Object.keys(newValue).length > 0) {
    formData.nombre = newValue.nombre || '';
    formData.categoria_id = newValue.categoria_id || '';
    formData.entrenador_id = newValue.entrenador_id || '';
    formData.imagen = newValue.imagen || null;
  }
}, { immediate: true, deep: true });

// Handle image upload
const handleImageUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    if (!file.type.includes('image/')) {
      Notification.error('Por favor seleccione un archivo de imagen');
      return;
    }
    
    if (typeof FileReader === 'function') {
      const reader = new FileReader();
      reader.onload = (event) => {
        imgSrc.value = event.target.result;
        // Reset cropped image
        cropImg.value = '';
      };
      reader.readAsDataURL(file);
    } else {
      Notification.error('Lo sentimos, FileReader API no es soportada en este navegador');
    }
  }
};

// Crop the image
const cropImage = () => {
  if (!cropper.value) return;
  
  cropImg.value = cropper.value.getCroppedCanvas().toDataURL();
  formData.imagen = cropImg.value;
  validation.imagen.valid = true;
};

// Rotate the image
const rotate = () => {
  if (!cropper.value) return;
  cropper.value.rotate(90);
};

// Validation using RegexUtils
const validateSingleField = (field) => {
  return validateFieldUtil(field, validation, formData);
};

const validateAllFields = () => {
  let isValid = true;
  
  Object.keys(validation).forEach(field => {
    const fieldValid = validateSingleField(field);
    if (!fieldValid) {
      isValid = false;
    }
  });
  
  return isValid;
};

const handleSubmit = () => {
  apiErrors.value = {};
  
  if (validateAllFields()) {
    // Create FormData object for file upload
    const submitData = new FormData();
    submitData.append('nombre', formData.nombre);
    submitData.append('categoria_id', formData.categoria_id);
    submitData.append('entrenador_id', formData.entrenador_id);
    
    // If we have a cropped image (base64), use it
    if (cropImg.value) {
      submitData.append('imagen_base64', cropImg.value);
    } 
    // Only append image if it's a File object (new upload) and no cropped image
    else if (formData.imagen instanceof File) {
      submitData.append('imagen', formData.imagen);
    }
    
    // If editing, include the ID
    if (props.mode === 'edit' && props.currentEquipo.id) {
      submitData.append('id', props.currentEquipo.id);
    }
    
    emit('submit', submitData);
  }
};

const handleApiErrors = (errors) => {
  apiErrors.value = errors;
  
  // Mark fields with API errors as invalid
  Object.keys(errors).forEach(field => {
    if (validation[field]) {
      validation[field].valid = false;
    }
  });
};

// Reset form
const resetForm = () => {
  Object.keys(formData).forEach(key => {
    if (typeof formData[key] === 'string') {
      formData[key] = '';
    } else {
      formData[key] = null;
    }
  });
  
  Object.keys(validation).forEach(key => {
    validation[key].valid = null;
  });
  
  // Reset image cropper
  imgSrc.value = '';
  cropImg.value = '';
  
  apiErrors.value = {};
  emit('cancel');
};

// Load initial data
onMounted(async () => {
  await categoriaStore.loadCategorias();
});

// Expose methods to parent component
defineExpose({
  handleApiErrors
});

// Add zoom functions
// Replace the zoom functions with these compatible versions
const zoomIn = () => {
  if (!cropper.value) return;
  // Use scaleX and scaleY instead of zoom
  const currentScaleX = cropper.value.getImageData().scaleX;
  const currentScaleY = cropper.value.getImageData().scaleY;
  cropper.value.scale(currentScaleX + 0.1, currentScaleY + 0.1);
};

const zoomOut = () => {
  if (!cropper.value) return;
  // Use scaleX and scaleY instead of zoom
  const currentScaleX = cropper.value.getImageData().scaleX;
  const currentScaleY = cropper.value.getImageData().scaleY;
  // Prevent scaling to zero or negative
  if (currentScaleX > 0.2 && currentScaleY > 0.2) {
    cropper.value.scale(currentScaleX - 0.1, currentScaleY - 0.1);
  }
};
</script>

<style scoped>
.modal-body {
  max-height: 70vh;
  overflow-y: auto;
}
</style>
import axios from 'axios';
import { ref, onMounted } from 'vue';

export default {
  setup() {
    const clasificacion = ref([]);
    const loading = ref(true);
    const error = ref(null);

    const fetchClasificacion = async () => {
      try {
        await axios.get('/sanctum/csrf-cookie');
        const response = await axios.get('/api/clasificacion');
        clasificacion.value = response.data;
      } catch (err) {
        error.value = 'Error al cargar los datos';
        console.error(err);
      } finally {
        loading.value = false;
      }
    };

    onMounted(() => {
      fetchClasificacion();
    });

    return {
      clasificacion,
      loading,
      error,
    };
  },
};
<template>
  <div class="container-fluid">
    <div class="block-header">
      <h2>Gestión de Equipos</h2>
    </div>

    <div class="row clearfix">
      <div class="col-lg-12">
        <div class="card">
          <div class="body">
            <button class="btn btn-primary mb-3" @click="abrirModalCrear">
              <i class="zmdi zmdi-plus"></i> Nuevo Equipo
            </button>

            <div class="table-responsive">
              <table id="equiposTable" class="table table-bordered table-striped table-hover">
                <thead>
                  <tr>
                    <th>Nombre</th>
                    <th>Imagen</th>
                    <th>Categoría</th>
                    <th>Entrenador</th>
                    <th>Acciones</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <EquipoForm 
      :editando="modoEdicion"
      :equipo="equipoSeleccionado"
      :categorias="categorias"
      :entrenadores="entrenadores"
      @guardar="guardarEquipo"
      @cancelar="cerrarModal"
    />
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue';
import axios from 'axios';
import { initializeDataTable, attachTableEvents } from '@/utils/datatables-utils';
import Notification from '@/utils/notification';
import EquipoForm from './EquipoForm.vue';

/** 
 * Lista de equipos
 * @type {import('vue').Ref<Array<Equipo>>}
 */
const equipos = ref([]);

/** 
 * Lista de categorías disponibles
 * @type {import('vue').Ref<Array<Categoria>>}
 */
const categorias = ref([]);

/** 
 * Lista de entrenadores disponibles
 * @type {import('vue').Ref<Array<Entrenador>>}
 */
const entrenadores = ref([]);

/** 
 * Modo actual de operación
 * @type {import('vue').Ref<'create'|'edit'>}
 */
const mode = ref('create');

/** 
 * Equipo actual seleccionado para edición
 * @type {Equipo}
 */
const currentEquipo = reactive({
  id: null,
  nombre: '',
  imagen: null,
  categoria_id: null,
  entrenador_id: null
});

/** 
 * Configuración de columnas para DataTable
 * @type {Array<import('datatables.net').ColumnSettings>}
 */
const tableColumns = [
  { 
    data: 'nombre', 
    title: 'Nombre',
    className: 'align-middle'
  },
  { 
    data: 'imagen', 
    title: 'Escudo',
    className: 'text-center align-middle',
    /** 
     * Renderiza la imagen Base64 o mensaje de ausencia
     * @param {string|null} data - Cadena Base64 de la imagen
     * @returns {string}
     */
    render: (data) => data 
      ? `<img:src="data:image/png;base64,${data}" alt="Escudo del equipo" width="30" height="30" class="rounded-circle" />`
      : '<div class="text-muted">Sin imagen</div>'
  },
  { 
    data: 'categoria.nombre', 
    title: 'Categoría',
    className: 'align-middle',
    /** 
     * Renderiza el nombre de la categoría o 'N/A'
     * @param {string|null} data - Nombre de la categoría
     * @returns {string}
     */
    render: (data) => data || 'N/A'
  },
  { 
    data: 'entrenador.name', 
    title: 'Entrenador',
    className: 'align-middle',
    /** 
     * Renderiza el nombre del entrenador o 'N/A'
     * @param {string|null} data - Nombre del entrenador
     * @returns {string}
     */
    render: (data) => data || 'N/A'
  },
  {
    data: null,
    title: 'Acciones',
    className: 'text-center align-middle',
    /** 
     * Genera los botones de acciones
     * @param {Equipo} data - Datos completos del equipo
     * @returns {string}
     */
    render: (data, type, row) => `
      <button class="btn btn-warning btn-sm btnEditar" 
              data-id="${row.id}"
              title="Editar">
        <i class="zmdi zmdi-edit"></i>
      </button>
      <button class="btn btn-danger btn-sm btnEliminar ml-2" 
              data-id="${row.id}"
              title="Eliminar">
        <i class="zmdi zmdi-delete"></i>
      </button>`
  }
];

/** 
 * Hook de ciclo de vida al montar el componente
 */
onMounted(async () => {
  await Promise.all([loadEquipos(), loadCategorias(), loadEntrenadores()]);
  initializeDataTable('equiposTable', equipos.value, tableColumns);
  attachTableEvents('equiposTable', handleEdit, handleDelete);
});

/** 
 * Carga los equipos desde la API
 * @returns {Promise<void>}
 */
const loadEquipos = async () => {
  try {
    const { data } = await axios.get('/api/equipos');
    equipos.value = data.data;
  } catch (error) {
    Notification.error('Error al cargar equipos');
    console.error('Detalles del error:', error.response?.data || error.message);
  }
};

/** 
 * Carga las categorías desde la API
 * @returns {Promise<void>}
 */
const loadCategorias = async () => {
  try {
    const { data } = await axios.get('/api/categorias');
    categorias.value = data.data;
  } catch (error) {
    Notification.error('Error al cargar categorías');
    console.error('Detalles del error:', error.response?.data || error.message);
  }
};

/** 
 * Carga los entrenadores desde la API
 * @returns {Promise<void>}
 */
const loadEntrenadores = async () => {
  try {
    const { data } = await axios.get('/api/entrenadores');
    entrenadores.value = data.data;
  } catch (error) {
    Notification.error('Error al cargar entrenadores');
    console.error('Detalles del error:', error.response?.data || error.message);
  }
};

/** 
 * Abre el modal en modo creación
 * @returns {void}
 */
const openCreateModal = () => {
  mode.value = 'create';
  resetForm();
  $('#equipoModal').modal('show');
};

/** 
 * Maneja la edición de un equipo
 * @param {number} id - ID del equipo a editar
 * @returns {void}
 */
const handleEdit = (id) => {
  const equipo = equipos.value.find(e => e.id === id);
  if (equipo) {
    mode.value = 'edit';
    Object.assign(currentEquipo, equipo);
    $('#equipoModal').modal('show');
  }
};

/** 
 * Maneja el envío del formulario
 * @param {Equipo} formData - Datos del equipo a guardar
 * @returns {Promise<void>}
 */
const handleSubmit = async (formData) => {
  try {
    const method = mode.value === 'create' ? 'post' : 'put';
    const url = method === 'post' 
      ? '/api/equipos' 
      : `/api/equipos/${formData.id}`;

    // Enviar solo campos necesarios
    const payload = {
      nombre: formData.nombre,
      imagen: formData.imagen,
      categoria_id: formData.categoria_id,
      entrenador_id: formData.entrenador_id
    };

    await axios[method](url, payload);
    await loadEquipos();
    Notification.success(`Equipo ${method === 'post' ? 'creado' : 'actualizado'} correctamente`);
    closeModal();
  } catch (error) {
    Notification.error('Error al guardar el equipo');
    console.error('Detalles del error:', error.response?.data || error.message);
  }
};

/** 
 * Maneja la eliminación de un equipo
 * @param {number} id - ID del equipo a eliminar
 * @returns {Promise<void>}
 */
const handleDelete = async (id) => {
  const confirmed = await Notification.confirm(
    'Confirmar eliminación',
    '¿Estás seguro de eliminar este equipo?',
    'Eliminar',
    'Cancelar'
  );

  if (confirmed) {
    try {
      await axios.delete(`/api/equipos/${id}`);
      await loadEquipos();
      Notification.success('Equipo eliminado correctamente');
    } catch (error) {
      Notification.error('Error al eliminar el equipo');
      console.error('Detalles del error:', error.response?.data || error.message);
    }
  }
};

/** 
 * Cierra el modal y resetea el formulario
 * @returns {void}
 */
const closeModal = () => {
  $('#equipoModal').modal('hide');
  resetForm();
};

/** 
 * Restablece los valores del formulario
 * @returns {void}
 */
const resetForm = () => {
  Object.assign(currentEquipo, {
    id: null,
    nombre: '',
    imagen: null,
    categoria_id: null,
    entrenador_id: null
  });
};
</script>
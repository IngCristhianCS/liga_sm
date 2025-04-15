// datatables-utils.js

/**
 * Inicializa una tabla DataTable con configuraciones genéricas.
 * @param tableId - El ID del elemento HTML de la tabla.
 * @param data - Los datos que se mostrarán en la tabla.
 * @param columns - Las columnas de la tabla con sus configuraciones.
 */
export const initializeDataTable = (tableId, data, columns) => {
    const tabla = `#${tableId}`;
    if ($.fn.DataTable.isDataTable(tabla)) {
        const table = $(tabla).DataTable();
        table.clear().rows.add(data).draw();
    } else {
        $(tabla).DataTable({
            paging: true,
            pageLength: 10,
            lengthChange: false,
            searching: true,
            ordering: true,
            data,
            columns,
        });
    }
};

/**
 * Attach event handlers to table buttons
 * @param {string} tableId - ID of the table
 * @param {Function} editHandler - Handler for edit button
 * @param {Function} deleteHandler - Handler for delete button
 * @param {Function} [assignEquiposHandler] - Optional handler for assign equipos button
 */
export const attachTableEvents = (tableId, editHandler, deleteHandler, assignEquiposHandler = null) => {
  const table = $(`#${tableId}`);
  
  // Edit button handler
  table.on('click', '.btnEditar', function() {
    const id = $(this).data('id');
    editHandler(id);
  });
  
  // Delete button handler
  table.on('click', '.btnEliminar', function() {
    const id = $(this).data('id');
    deleteHandler(id);
  });
  
  // Assign equipos button handler (if provided)
  if (assignEquiposHandler) {
    table.on('click', '.btnAsignarEquipos', function() {
      const id = $(this).data('id');
      assignEquiposHandler(id);
    });
  }
};
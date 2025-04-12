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
 * Asigna eventos de clic a los botones de edición y eliminación en una tabla DataTable.
 * @param tableId - El ID del elemento HTML de la tabla.
 * @param onEdit - Función de callback para manejar la edición.
 * @param onDelete - Función de callback para manejar la eliminación.
 */
export const attachTableEvents = (tableId, onEdit, onDelete) => {
    const tabla = `#${tableId}`;
    const tbody = $(tabla + ' tbody');
    tbody.off('click', '.btnEditar');
    tbody.off('click', '.btnEliminar');

    tbody.on('click', '.btnEditar', function () {
        const id = $(this).data('id');
        onEdit(id);
    });

    tbody.on('click', '.btnEliminar', function () {
        const id = $(this).data('id');
        onDelete(id);
    });
};
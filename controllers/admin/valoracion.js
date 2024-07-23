// Constante para completar la ruta de la API.
const VALORACION_API = 'services/admin/valoracion.php';
const CLIENTE_API = 'services/admin/cliente.php';
const PRODUCTO_API = 'services/admin/producto.php';
// Constante para establecer el formulario de buscar.
const SEARCH_FORM = document.getElementById('searchForm');
// Constantes para establecer los elementos de la tabla.
const TABLE_BODY = document.getElementById('tableBody'),
    ROWS_FOUND = document.getElementById('rowsFound');
// Constantes para establecer los elementos del componente Modal.
const SAVE_MODAL = new bootstrap.Modal('#saveModal'),
    MODAL_TITLE = document.getElementById('modalTitle');
// Constantes para establecer los elementos del formulario de guardar.
const SAVE_FORM = document.getElementById('saveForm'),
    ID_VALORACION = document.getElementById('idValoracion'),
    CALIFICACION_VALORACION = document.getElementById('calificacionValoracion');
COMENTARIO_VALORACION = document.getElementById('comentarioValoracion'),
    FECHA_VALORACION = document.getElementById('fechaValoracion'),
    CLIENTE_VALORACION = document.getElementById('clienteValoracion'),
    PRODUCTO_VALORACION = document.getElementById('productoValoracion'),
    ESTADO_VALORACION =  document.getElementById('estadoValoracion');

document.addEventListener('DOMContentLoaded', async () => {
    loadTemplate();
    MAIN_TITLE.textContent = 'Gestionar valoración';
    await fillTable();

});

// Método del evento para cuando se envía el formulario de buscar.
SEARCH_FORM.addEventListener('submit', (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SEARCH_FORM);
    // Convertir las fechas al formato MySQL antes de enviar el formulario

    // Llamada a la función para llenar la tabla con los resultados de la búsqueda.
    fillTable(FORM);
});

// Método del evento para cuando se envía el formulario de guardar.
SAVE_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Se verifica la acción a realizar.
    (ID_VALORACION.value) ? action = 'updateRow' : action = 'createRow';
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SAVE_FORM);
    // Petición para guardar los datos del formulario.
    const DATA = await fetchData(VALORACION_API, action, FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se cierra la caja de diálogo.
        SAVE_MODAL.hide();
        // Se muestra un mensaje de éxito.
        sweetAlert(1, DATA.message, true);
        // Se carga nuevamente la tabla para visualizar los cambios.
        fillTable();
    } else {
        sweetAlert(2, DATA.error, false);
    }
});

/*
*   Función asíncrona para llenar la tabla con los registros disponibles.
*   Parámetros: form (objeto opcional con los datos de búsqueda).
*   Retorno: ninguno.
*/
const fillTable = async (form = null) => {
    // Se inicializa el contenido de la tabla.
    ROWS_FOUND.textContent = '';
    TABLE_BODY.innerHTML = '';
    // Se verifica la acción a realizar.
    (form) ? action = 'searchRows' : action = 'readAll';
    // Petición para obtener los registros disponibles.
    const DATA = await fetchData(VALORACION_API, action, form);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se recorre el conjunto de registros fila por fila.
        DATA.dataset.forEach(row => {
            // Se crean y concatenan las filas de la tabla con los datos de cada registro.
            icon = (row.estado_valoracion == 1) ? 'bi bi-eye-fill' : 'bi bi-eye-slash-fill';
            TABLE_BODY.innerHTML += `
                <tr>
                    <td>${row.nombre_producto}</td>
                    <td>${row.nombre_cliente}</td>
                    <td>${row.calificacion}</td>
                    <td>${row.comentario}</td>
                    <td>${row.fecha_valoracion}</td>
                    <td><i class="${icon}" style="color: black;"></i></td>
                    <td>
                        <button type="button" class="btn btn-info" onclick="openUpdate(${row.id_valoracion})">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <button type="button" class="btn btn-danger" onclick="openDelete(${row.id_valoracion})">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        // Se muestra el número de registros encontrados.
        ROWS_FOUND.textContent = DATA.message;
    } else {
        sweetAlert(4, DATA.error, true);
    }
}

// Función para preparar el formulario al momento de insertar un registro.
const openCreate = () => {

    // Se muestra la caja de diálogo con su título.
    SAVE_MODAL.show();
    // Se coloca el título para el formulario.
    MODAL_TITLE.textContent = 'Crear valoración';
    // Se restauran los elementos del formulario.
    SAVE_FORM.reset();
    ID_VALORACION.value = '';
    fillSelect(CLIENTE_API, 'readAll', 'clienteValoracion');
    fillSelect(PRODUCTO_API, 'readAll', 'productoValoracion');
    const fechaActual = new Date();
    const fechaFormateada = fechaActual.toISOString().split('T')[0];
    FECHA_VALORACION.value = fechaFormateada;
}

/*
*   Función asíncrona para preparar el formulario al momento de actualizar un registro.
*   Parámetros: id (identificador del registro seleccionado).
*   Retorno: ninguno.
*/
const openUpdate = async (id) => {
    // Se define una constante tipo objeto con los datos del registro seleccionado.
    const FORM = new FormData();
    FORM.append('idValoracion', id);
    // Petición para obtener los datos del registro solicitado.
    const DATA = await fetchData(VALORACION_API, 'readOne', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se muestra la caja de diálogo con su título.
        SAVE_MODAL.show();
        MODAL_TITLE.textContent = 'Actualizar valoracion';
        // Se prepara el formulario.
        SAVE_FORM.reset();
        // Se inicializan los campos con los datos.
        const ROW = DATA.dataset;
        ID_VALORACION.value = ROW.id_valoracion;
        CALIFICACION_VALORACION.value = ROW.calificacion;
        COMENTARIO_VALORACION.value = ROW.comentario;
        FECHA_VALORACION.value = ROW.fecha_valoracion;
        if (ROW.estado_valoracion == 0) {
            ESTADO_VALORACION.checked = false;
        } else if (ROW.estado_valoracion == 1) {
            ESTADO_VALORACION.checked = true;
        } else (
            console.log(ROW.estado_valoracion)
        )
        ESTADO_VALORACION.value =  ROW.estado_valoracion;
        fillSelect(CLIENTE_API, 'readAll', 'clienteValoracion', ROW.id_cliente);
        fillSelect(PRODUCTO_API, 'readAll', 'productoValoracion', ROW.id_producto);
    } else {
        sweetAlert(2, DATA.error, false);
    }
}

/*
*   Función asíncrona para eliminar un registro.
*   Parámetros: id (identificador del registro seleccionado).
*   Retorno: ninguno.
*/
const openDelete = async (id) => {
    // Llamada a la función para mostrar un mensaje de confirmación, capturando la respuesta en una constante.
    const RESPONSE = await confirmAction('¿Desea eliminar el administrador de forma permanente?');
    // Se verifica la respuesta del mensaje.
    if (RESPONSE) {
        // Se define una constante tipo objeto con los datos del registro seleccionado.
        const FORM = new FormData();
        FORM.append('idValoracion', id);
        // Petición para eliminar el registro seleccionado.
        const DATA = await fetchData(VALORACION_API, 'deleteRow', FORM);
        // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
        if (DATA.status) {
            // Se muestra un mensaje de éxito.
            await sweetAlert(1, DATA.message, true);
            // Se carga nuevamente la tabla para visualizar los cambios.
            fillTable();
        } else {
            sweetAlert(2, DATA.error, false);
        }
    }
}
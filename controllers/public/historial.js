// Constantes para completar la ruta de la API.
const PEDIDO_API = 'services/public/pedido.php';
const VALORACION_API = 'services/admin/valoracion.php';
const PRODUCTO_API = 'services/public/producto.php';
// Constante para establecer el cuerpo de la tabla.
const TABLE_BODY = document.getElementById('tableBody');
// Constantes para establecer los elementos del componente Modal.
const SAVE_MODAL = new bootstrap.Modal('#itemModal'),
    MODAL_TITLE = document.getElementById('modalTitle');
const SAVE_FORM = document.getElementById('itemForm'),
    ID_VALORACION = document.getElementById('idValoracion'),
    PRODUCTO = document.getElementById('productoValoracion'),
    CALIFICACION = document.getElementsByName('calificacionValoracion'),
    COMENTARIO = document.getElementById('comentarioValoracion'),
    IMAGEN_PRODUCTO = document.getElementById('imagenProducto');
MAIN_TITLE.textContent = 'Historial de compra';
MAIN.style.paddingTop = '100px';

// Método del evento para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', async () => {
    // Llamada a la función para mostrar el encabezado y pie del documento.
    loadTemplate();
    await fillTable();
    addProductChangeListener();
});

// Método del evento para cuando se envía el formulario de guardar.
SAVE_FORM.addEventListener('submit', async (event) => {
    event.preventDefault();

    try {
        const productoId = PRODUCTO.value;
        const calificacion = getSelectedCalificacion();
        const comentario = COMENTARIO.value;

        // Enviar datos como FormData para asegurar que se envíen como POST
        const formData = new FormData();
        formData.append('productoValoracion', productoId);
        formData.append('calificacionValoracion', calificacion);
        formData.append('comentarioValoracion', comentario);

        const valoracionResponse = await fetchData(PEDIDO_API, 'getValoracionByProducto', formData);

        if (!valoracionResponse.status) {
            const action = ID_VALORACION.value ? 'updateValoracion' : 'createValoracion';
            const DATA = await fetchData(PEDIDO_API, action, formData);

            if (DATA.status) {
                SAVE_MODAL.hide();
                sweetAlert(1, DATA.message, true);
                fillTable();
            } else {
                sweetAlert(2, DATA.error, false);
            }
        } else {
            sweetAlert(2, 'Ya has valorado este producto.', false);
        }
    } catch (error) {
        console.error('Error during form submission:', error);
        sweetAlert(3, 'An error occurred while processing your request.', false);
    }
});

/*
 *   Función para obtener el detalle de los pedidos realizados
 *   Parámetros: ninguno.
 *   Retorno: ninguno.
 */
async function fillTable() {
    const DATA = await fetchData(PEDIDO_API, 'getHistory');
    await fillSelect(PEDIDO_API, 'getProductosComprados', 'productoValoracion');

    const productoValoracion = document.getElementById('productoValoracion');
    const productosData = await fetchData(PEDIDO_API, 'getProductosComprados');
    if (productosData.status) {
        const options = productoValoracion.options;
        for (let i = 0; i < options.length; i++) {
            const option = options[i];
            const producto = productosData.dataset.find(p => p.id_producto == option.value);
            if (producto) {
                option.setAttribute('data-image', producto.imagen_producto);
            }
        }
    }

    if (DATA.status) {
        // Se inicializa el cuerpo de la tabla.
        TABLE_BODY.innerHTML = '';
        // Se recorre el conjunto de registros fila por fila a través del objeto row.
        DATA.dataset.forEach(row => {
            // Se crean y concatenan las filas de la tabla con los datos de cada registro.
            TABLE_BODY.innerHTML += `
                <tr>
                    <td>${row.nombre_producto}</td>
                    <td>${row.precio_producto}</td>
                    <td>${row.cantidad_producto}</td>
                    <td>${row.estado_pedido}</td>
                </tr>
            `;
        });
    } else {
        sweetAlert(4, DATA.error, false, 'index.html');
    }
}

const openCreate = () => {
    SAVE_MODAL.show();
    // Se prepara el formulario.
    SAVE_FORM.reset();
}

/*
 *   Función para añadir el evento change al ComboBox productoValoracion
 *   Parámetros: ninguno.
 *   Retorno: ninguno.
 */
function addProductChangeListener() {
    const productoValoracion = document.getElementById('productoValoracion');
    const imagenProducto = document.getElementById('imagenProducto');

    productoValoracion.addEventListener('change', () => {
        const selectedOption = productoValoracion.options[productoValoracion.selectedIndex];
        const imageUrl = selectedOption.getAttribute('data-image');

        if (imageUrl) {
            imagenProducto.src = `../../api/images/productos/${imageUrl}`;
        } else {
            imagenProducto.src = '../../api/images/productos/default.png';
        }
    });
}

/*
 *   Función auxiliar para obtener la calificación seleccionada.
 *   Parámetros: ninguno.
 *   Retorno: el valor de la calificación seleccionada.
 */
function getSelectedCalificacion() {
    const calificacionElement = document.querySelector('input[name="calificacionValoracion"]:checked');
    return calificacionElement ? calificacionElement.value : null;
}

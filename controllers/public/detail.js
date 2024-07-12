// Constantes para completar la ruta de la API.
const PRODUCTO_API = 'services/public/producto.php';
const PEDIDO_API = 'services/public/pedido.php';
const VALORACIONES_API = 'services/public/resena.php'
// Constante tipo objeto para obtener los parámetros disponibles en la URL.
const PARAMS = new URLSearchParams(location.search);
// Constante para establecer el formulario de agregar un producto al carrito de compras.
const SHOPPING_FORM = document.getElementById('shoppingForm');
const COMENTARIO_FORM = document.getElementById('comentarioForm');

// Método del eventos para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', async () => {
    // Llamada a la función para mostrar el encabezado y pie del documento.
    loadTemplate();
    // Se establece el título del contenido principal.
    MAIN_TITLE.textContent = 'Detalles del producto';
    // Constante tipo objeto con los datos del producto seleccionado.
    const FORM = new FormData();
    FORM.append('idProducto', PARAMS.get('id'));
    // Petición para solicitar los datos del producto seleccionado.
    const DATA = await fetchData(PRODUCTO_API, 'readOne', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se colocan los datos en la página web de acuerdo con el producto seleccionado previamente.
        document.getElementById('imagenProducto').src = SERVER_URL.concat('images/productos/', DATA.dataset.imagen_producto);
        document.getElementById('nombreProducto').textContent = DATA.dataset.nombre_producto;
        document.getElementById('descripcionProducto').textContent = DATA.dataset.descripcion_producto;
        document.getElementById('precioProducto').textContent = DATA.dataset.precio_producto;
        document.getElementById('existenciasProducto').textContent = DATA.dataset.existencias_producto;
        document.getElementById('idProducto').value = DATA.dataset.id_producto;
    } else {
        // Se presenta un mensaje de error cuando no existen datos para mostrar.
        document.getElementById('mainTitle').textContent = DATA.error;
        // Se limpia el contenido cuando no hay datos para mostrar.
        document.getElementById('detalle').innerHTML = '';
    }
    cargarComentarios();
});

// Método del evento para cuando se envía el formulario de agregar un producto al carrito.
SHOPPING_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SHOPPING_FORM);
    // Petición para guardar los datos del formulario.
    const DATA = await fetchData(PEDIDO_API, 'insertarDetalle', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se constata si el cliente ha iniciado sesión.
    if (DATA.status) {
        sweetAlert(1, DATA.message, false, 'cart.html');
    } else if (DATA.session) {
        sweetAlert(2, DATA.error, false);
    } else {
        sweetAlert(3, DATA.error, true, 'login.html');
    }
});


// Método del evento para cuando se envía el formulario de agregar un producto al carrito.
COMENTARIO_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();

    const radios = document.getElementsByName('star');
    let seleccion = null;

    for (const radio of radios) {
        if (radio.checked) {
            seleccion = radio.value;
            break;
        }
    }
    console.log(seleccion);
    console.log(PARAMS.get('id'))
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(COMENTARIO_FORM);
    FORM.append('valoracion', seleccion);
    FORM.append('producto', PARAMS.get('id'));
    // Petición para guardar los datos del formulario.
    const DATA = await fetchData(VALORACIONES_API, 'createRow', FORM);
    try {
        // Se comprueba si la respuesta es satisfactoria, de lo contrario se constata si el cliente ha iniciado sesión.
        if (DATA.status) {
            sweetAlert(1, DATA.message, false);
            cargarComentarios();
            COMENTARIO_FORM.reset();
        }
        else if (!DATA.exception) {
            sweetAlert(3, DATA.error, true);
        } else {
            sweetAlert(3, DATA.exception, true);
        }
    } catch (error) {
        sweetAlert(3, "Debe iniciar sesión para hacer un comentario al producto", true);
    }
});

async function cargarComentarios(listacomentarios = null) {
    const contenedorComentarios = document.getElementById('comentarios');
    try {
        const FORM = new FormData();
        FORM.append('idProducto', PARAMS.get('id'));
        const data = await fetchData(VALORACIONES_API, 'readOne', FORM);
        listacomentarios = data.dataset;
        console.log(listacomentarios);

        contenedorComentarios.innerHTML = '';
        listacomentarios.forEach((valoracion, index) => {
            const isActive = index === 0 ? 'active' : '';
            const valoracionHtml = `
            <div class="carousel-item ${isActive}">
                <div class="carta-comentario d-flex align-items-start">
                    <div class="comentario-contenido ms-3">
                        <div class="d-flex align-items-center mb-2">
                            <h5 class="card-title mb-0 me-3">${valoracion.NOMBRE}</h5>
                            <div class="rating">
                                <input type="radio" id="star5_${index}" name="rating_${index}" value="5"><label for="star5_${index}"></label>
                                <input type="radio" id="star4_${index}" name="rating_${index}" value="4"><label for="star4_${index}"></label>
                                <input type="radio" id="star3_${index}" name="rating_${index}" value="3"><label for="star3_${index}"></label>
                                <input type="radio" id="star2_${index}" name="rating_${index}" value="2"><label for="star2_${index}"></label>
                                <input type="radio" id="star1_${index}" name="rating_${index}" value="1"><label for="star1_${index}"></label>
                            </div>
                        </div>
                        <p class="card-text">${valoracion.COMENTARIO}</p>
                    </div>
                </div>
            </div>
            `;
            contenedorComentarios.innerHTML += valoracionHtml;
        });

        // Marcar las estrellas según la calificación
        listacomentarios.forEach((valoracion, index) => {
            const nota = valoracion.CALIFICACIÓN;
            if (nota) {
                document.getElementById(`star${nota}_${index}`).checked = true;
                // Desactivar las estrellas para que no sean interactivas
                for (let i = 1; i <= 5; i++) {
                    document.getElementById(`star${i}_${index}`).disabled = true;
                }
            }
        });
    } catch (error) {
        console.error('Error al obtener datos de la API:', error);
    }
}
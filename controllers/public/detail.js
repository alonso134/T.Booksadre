// Constantes para completar la ruta de la API.
const PRODUCTO_API = 'services/public/producto.php';
const PEDIDO_API = 'services/public/pedido.php';
// Constante tipo objeto para obtener los parámetros disponibles en la URL.
const PARAMS = new URLSearchParams(location.search);
// Constante para establecer el formulario de agregar un producto al carrito de compras.
const SHOPPING_FORM = document.getElementById('shoppingForm');

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

      // Llamar función para cargar comentarios y valoraciones
     await fillCommentsAndRatings();
    } else {
        // Se presenta un mensaje de error cuando no existen datos para mostrar.
        document.getElementById('mainTitle').textContent = DATA.error;
        // Se limpia el contenido cuando no hay datos para mostrar.
        document.getElementById('detalle').innerHTML = '';
    }
});

// Método para llenar los comentarios y valoraciones del producto.
async function fillCommentsAndRatings() {
    // Obtener el ID del producto actual
    const productId = document.getElementById('idProducto').value;

    // Llamada al backend para obtener los comentarios y valoraciones
    const formData = new FormData();
    formData.append('idProducto', productId);

    const comentariosValoraciones = document.getElementById('comentariosValoraciones');

    const comentariosValoracionesData = await fetchData(PRODUCTO_API, 'getCommentsAndRatings', formData);

    if (comentariosValoracionesData.status && Array.isArray(comentariosValoracionesData.dataset)) {
        comentariosValoraciones.innerHTML = '';  

        comentariosValoracionesData.dataset.forEach(comment => {
            const formattedDate = formatDate(comment.fecha_valoracion);

            comentariosValoraciones.innerHTML += `
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">${comment.nombre_cliente} ${comment.apellido_cliente}</h5>
                        <div class="mb-2">
                            ${generateStars(comment.calificacion)}
                        </div>
                        <h6 class="card-subtitle mb-2 text-muted">${formattedDate}</h6>
                        <p class="card-text">${comment.comentario}</p>
                    </div>
                </div>
            `;
        });
    } else {
        comentariosValoraciones.innerHTML = '<p>No hay comentarios ni valoraciones disponibles.</p>';
    }
}

// Función para generar estrellas basadas en la calificación
function generateStars(calificacion) {
    let stars = '';
    for (let i = 0; i < 5; i++) {
        if (i < calificacion) {
            stars += '<i class="bi bi-star-fill" style="color: gold;"></i>';
        } else {
            stars += '<i class="bi bi-star" style="color: gold;"></i>';
        }
    }
    return stars;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    date.setMinutes(date.getMinutes() + date.getTimezoneOffset());
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

// Método del evento para cuando se envía el formulario de agregar un producto al carrito.
SHOPPING_FORM.addEventListener('submit', async (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SHOPPING_FORM);
    // Petición para guardar los datos del formulario.
    const DATA = await fetchData(PEDIDO_API, 'createDetail', FORM);
    // Se comprueba si la respuesta es satisfactoria, de lo contrario se constata si el cliente ha iniciado sesión.
    if (DATA.status) {
        sweetAlert(1, DATA.message, false, 'cart.html');
    } else if (DATA.session) {
        sweetAlert(2, DATA.error, false);
    } else {
        sweetAlert(3, DATA.error, true, 'login.html');
    }
});
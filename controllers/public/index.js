// Constante para completar la ruta de la API.
const CATEGORIA_API = 'services/public/categoria.php';
// Constante para establecer el formulario de buscar.
const SEARCH_FORM = document.getElementById('searchForm');
// Constante para establecer el contenedor de categorías.
const CATEGORIAS = document.getElementById('categorias');

// Método del evento para cuando el documento ha cargado.
document.addEventListener('DOMContentLoaded', async () => {
    // Llamada a la función para mostrar el encabezado y pie del documento.
    loadTemplate();
    // Se establece el título del contenido principal.
    MAIN_TITLE.textContent = 'Productos por categoría';
    // Petición para obtener las categorías disponibles.
    const DATA = await fetchData(CATEGORIA_API, 'readAll');
    // Llamada a la función para llenar la tabla con los registros existentes.

    // Se comprueba si la respuesta es satisfactoria, de lo contrario se muestra un mensaje con la excepción.
    if (DATA.status) {
        // Se inicializa el contenedor de categorías.
        CATEGORIAS.innerHTML = '';
        // Se recorre el conjunto de registros fila por fila a través del objeto row.
        DATA.dataset.forEach(row => {
            // Se establece la página web de destino con los parámetros.
            let url = `products.html?id=${row.id_categoria}&nombre=${row.nombre_categoria}`;
            // Se crean y concatenan las tarjetas con los datos de cada categoría.
            CATEGORIAS.innerHTML += `
                <div class="col-sm-12 col-md-6 col-lg-3">
                    <div class="card mb-3">
                        <img src="${SERVER_URL}images/categorias/${row.imagen_categoria}" class="card-img-top" alt="${row.nombre_categoria}">
                        <div class="card-body text-center">
                            <h5 class="card-title">${row.nombre_categoria}</h5>
                            <p class="card-text">${row.descripcion_categoria}</p>
                            <a href="${url}" class="btn btn-primary">Ver productos</a>
                        </div>
                    </div>
                </div>
            `;
        });
    } else {
        // Se asigna al título del contenido de la excepción cuando no existen datos para mostrar.
        document.getElementById('mainTitle').textContent = DATA.error;
    }
});

// Método del evento para cuando se envía el formulario de buscar.
SEARCH_FORM.addEventListener('submit', (event) => {
    // Se evita recargar la página web después de enviar el formulario.
    event.preventDefault();
    // Constante tipo objeto con los datos del formulario.
    const FORM = new FormData(SEARCH_FORM);
    // Llamada a la función para llenar la tabla con los resultados de la búsqueda.
    fillTable(FORM);
});

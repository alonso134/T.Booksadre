// Constantes para las rutas de la API.
const PEDIDO_API = 'services/admin/pedido.php';
const CLIENTE_API = 'services/admin/cliente.php';
const DETALLE_PEDIDO_API = 'services/admin/detalle_pedido.php';
const PRODUCTO_API = 'services/admin/producto.php';

// Constantes para los elementos del formulario de búsqueda y la tabla.
const SEARCH_FORM = document.getElementById('searchForm');
const TABLE_BODY = document.getElementById('tableBody');
const ROWS_FOUND = document.getElementById('rowsFound');
const CRUD_BODY = document.getElementById('crudBody');
const CRUD_FOUND = document.getElementById('crudFound');

// Constantes para los elementos del componente Modal.
const SAVE_MODAL = new bootstrap.Modal('#saveModal');
const MODAL_TITLE = document.getElementById('modalTitle');
const CRUD_MODAL = new bootstrap.Modal('#crudModal');
const CRUD_TITLE = document.getElementById('crudTitle');

// Constantes para los elementos del componente CRUD.
const SAVE_CRUD = document.getElementById('saveCrud');
const ID_PEDIDO_CRUD = document.getElementById('idPedidoCRUD');
const ID_DETALLE_PEDIDO = document.getElementById('idDetallePedido');
const PRODUCTO = document.getElementById('productoDetalleP');
const CANTIDAD = document.getElementById('cantidadDetalleP');

// Constantes para los elementos del formulario de guardar.
const SAVE_FORM = document.getElementById('saveForm');
const ID_PEDIDO = document.getElementById('idPedido');
const CLIENTE_PEDIDO = document.getElementById('clientePedido');
const FECHA_PEDIDO = document.getElementById('fechaPedido');
const ESTADO_PEDIDO = document.getElementById('estadoPedido');
const DIRECCION_PEDIDO = document.getElementById('direccionPedido');

// Constantes para manejar los contenedores de los modales.
const SAVE_MODAL_CONTAINER = document.getElementById('saveModalContainer');
const SAVE_MODAL_CONTENT = document.getElementById('saveModal');
const CRUD_MODAL_CONTAINER = document.getElementById('crudModalContainer');
const CRUD_MODAL_CONTENT = document.getElementById('crudModal');

// Event listener para cuando el contenido del DOM está completamente cargado.
document.addEventListener('DOMContentLoaded', async () => {
    loadTemplate();
    MAIN_TITLE.textContent = 'Gestionar pedidos';
    await fillTable();
});

// Manejo del modal para guardar
SAVE_MODAL._element.addEventListener('show.bs.modal', () => {
    document.body.appendChild(SAVE_MODAL_CONTENT);
});

SAVE_MODAL._element.addEventListener('hidden.bs.modal', () => {
    SAVE_MODAL_CONTAINER.appendChild(SAVE_MODAL_CONTENT);
});

// Manejo del modal CRUD
CRUD_MODAL._element.addEventListener('show.bs.modal', () => {
    document.body.appendChild(CRUD_MODAL_CONTENT);
});

CRUD_MODAL._element.addEventListener('hidden.bs.modal', () => {
    CRUD_MODAL_CONTAINER.appendChild(CRUD_MODAL_CONTENT);
});

// Método del evento para cuando se envía el formulario de buscar.
SEARCH_FORM.addEventListener('submit', (event) => {
    event.preventDefault();
    const FORM = new FormData(SEARCH_FORM);
    fillTable(FORM);
});

// Método del evento para cuando se envía el formulario de guardar.
SAVE_FORM.addEventListener('submit', async (event) => {
    event.preventDefault();
    const action = ID_PEDIDO.value ? 'updateRow' : 'createRow';
    const FORM = new FormData(SAVE_FORM);
    const DATA = await fetchData(PEDIDO_API, action, FORM);

    if (DATA.status) {
        SAVE_MODAL.hide();
        sweetAlert(1, DATA.message, true);
        fillTable();
    } else {
        sweetAlert(2, DATA.error, false);
    }
});

// Método del evento para cuando se envía el formulario CRUD.
SAVE_CRUD.addEventListener('submit', async (event) => {
    event.preventDefault();
    const action = ID_DETALLE_PEDIDO.value ? 'updateRow' : 'createRow';
    const FORM = new FormData(SAVE_CRUD);
    const DATA = await fetchData(DETALLE_PEDIDO_API, action, FORM);

    if (DATA.status) {
        CRUD_MODAL.hide();
        sweetAlert(1, DATA.message, true);
        fillTable();
    } else {
        sweetAlert(2, DATA.error, false);
    }
});

// Función asíncrona para llenar la tabla con los registros disponibles.
const fillTable = async (form = null) => {
    ROWS_FOUND.textContent = '';
    TABLE_BODY.innerHTML = '';
    const action = form ? 'searchRows' : 'readAll';
    const DATA = await fetchData(PEDIDO_API, action, form);

    if (DATA.status) {
        DATA.dataset.forEach(row => {
            TABLE_BODY.innerHTML += `
                <tr>
                    <td>${row.fecha_registro}</td>
                    <td>${row.nombre_cliente}</td>
                    <td>${row.direccion_pedido}</td>
                    <td>${row.estado_pedido}</td>
                    <td>
                        <button type="button" class="btn btn-info" onclick="openUpdate(${row.id_pedido})">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <button type="button" class="btn btn-danger" onclick="openDelete(${row.id_pedido})">
                            <i class="bi bi-trash3"></i>
                        </button>
                        <button type="button" class="btn btn-warning" onclick="openDetails(${row.id_pedido})">
                            <i class="bi bi-info-circle"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        ROWS_FOUND.textContent = DATA.message;
    } else {
        sweetAlert(4, DATA.error, true);
    }
};

// Función asíncrona para llenar el CRUD con los registros disponibles.
const fillCrud = async (id) => {
    fillSelect(PRODUCTO_API, 'productoDetalleP');
    CRUD_FOUND.textContent = '';
    CRUD_BODY.innerHTML = '';

    const FORM = new FormData();
    FORM.append('idPedidoCRUD', id);
    const DATA = await fetchData(DETALLE_PEDIDO_API, 'readAll', FORM);

    if (DATA.status) {
        DATA.dataset.forEach(row => {
            CRUD_BODY.innerHTML += `
                <tr>
                    <td>${row.nombre_producto}</td>
                    <td>${row.cantidad_producto}</td>
                    <td>$${row.precio_producto}</td>
                    <td>
                        <button type="button" class="btn btn-info" onclick="openUpdateCrud(${row.id_detalle})">
                            <i class="bi bi-pencil-fill"></i>
                        </button>
                        <button type="button" class="btn btn-danger" onclick="openDeleteCrud(${row.id_detalle})">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        CRUD_FOUND.textContent = DATA.message;
    } else {
        sweetAlert(4, DATA.error, true);
    }
};

// Función para preparar el formulario al momento de insertar un registro.
const openDetails = async (id) => {
    ID_PEDIDO_CRUD.value = id;
    await fillCrud(ID_PEDIDO_CRUD.value);
    CRUD_MODAL.show();
    CRUD_TITLE.textContent = 'Información detalle del pedido';
    fillSelect(PRODUCTO_API, 'readAll', 'productoDetalleP');
    SAVE_CRUD.reset();
};

const openDeleteCrud = async (id) => {
    const RESPONSE = await confirmAction('¿Desea eliminar el detalle pedido de forma permanente?');

    if (RESPONSE) {
        ID_PEDIDO_CRUD.value = id;
        const FORM = new FormData();
        FORM.append('idDetallePedido', id);
        const DATA = await fetchData(DETALLE_PEDIDO_API, 'deleteRow', FORM);

        if (DATA.status) {
            await sweetAlert(1, DATA.message, true);
            await fillCrud(ID_PEDIDO_CRUD.value);
        } else {
            sweetAlert(2, DATA.error, false);
        }
    }
};

const openUpdateCrud = async (id) => {
    const FORM = new FormData();
    FORM.append('idDetallePedido', id);
    const DATA = await fetchData(DETALLE_PEDIDO_API, 'readOne', FORM);

    if (DATA.status) {
        CRUD_MODAL.show();
        MODAL_TITLE.textContent = 'Actualizar detalle';
        SAVE_CRUD.reset();
        const ROW = DATA.dataset;
        ID_DETALLE_PEDIDO.value = ROW.id_detalle;
        CANTIDAD.value = ROW.cantidad_producto;
        fillSelect(PRODUCTO_API, 'readAll', 'productoDetalleP', ROW.id_producto);
    } else {
        sweetAlert(2, DATA.error, false);
    }
};

const openCreate = () => {
    SAVE_MODAL.show();
    MODAL_TITLE.textContent = 'Crear pedido';
    SAVE_FORM.reset();
    const fechaActual = new Date();
    const fechaFormateada = fechaActual.toISOString().split('T')[0];
    fillSelect(CLIENTE_API, 'readAll', 'clientePedido');
    fillSelect(PEDIDO_API, 'getEstados', 'estadoPedido');
    FECHA_PEDIDO.value = fechaFormateada;
};

const openUpdate = async (id_pedido) => {
    const FORM = new FormData();
    FORM.append('idPedido', id_pedido);
    const DATA = await fetchData(PEDIDO_API, 'readOne', FORM);

    if (DATA.status) {
        SAVE_MODAL.show();
        MODAL_TITLE.textContent = 'Actualizar pedido';
        SAVE_FORM.reset();
        const ROW = DATA.dataset;
        ID_PEDIDO.value = ROW.id_pedido;
        DIRECCION_PEDIDO.value = ROW.direccion_pedido;
        FECHA_PEDIDO.value = ROW.fecha_registro;
        fillSelect(CLIENTE_API, 'readAll', 'clientePedido', ROW.id_cliente);
        fillSelect(PEDIDO_API, 'getEstados', 'estadoPedido', ROW.estado_pedido);
    } else {
        sweetAlert(2, DATA.error, false);
    }
};

const openDelete = async (id_pedido) => {
    const RESPONSE = await confirmAction('¿Desea eliminar el administrador de forma permanente?');

    if (RESPONSE) {
        const FORM = new FormData();
        FORM.append('idPedido', id_pedido);
        const DATA = await fetchData(PEDIDO_API, 'deleteRow', FORM);

        if (DATA.status) {
            await sweetAlert(1, DATA.message, true);
            fillTable();
        } else {
            sweetAlert(2, DATA.error, false);
        }
    }
};

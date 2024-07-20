<?php
// Se incluye la clase del modelo.
require_once('../../models/data/detalle_pedido_data.php');

// Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
if (isset($_GET['action'])) {
    // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
    session_start();
    // Se instancia la clase correspondiente.
    $venta = new DetallePedidosData;
    // Se declara e inicializa un arreglo para guardar el resultado que retorna la API.
    $result = array('status' => 0, 'message' => null, 'dataset' => null, 'error' => null, 'exception' => null, 'fileStatus' => null);
    // Se verifica si existe una sesión iniciada como administrador, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_SESSION['idAdministrador'])) {
        // Se compara la acción a realizar cuando un administrador ha iniciado sesión.
        switch ($_GET['action']) {
            case 'searchRows':
                if (!Validator::validateSearch($_POST['search'])) {
                    $result['error'] = Validator::getSearchError();
                } else {
                    $result['dataset'] = $venta->searchRows();
                    if ($result['dataset']) {
                        $result['status'] = 1;
                        $result['message'] = 'Existen ' . count($result['dataset']) . ' coincidencias';
                    } else {
                        $result['error'] = 'No hay coincidencias';
                    }
                }
                break;
            case 'createRow':
                // Validar y registrar los datos del formulario
                $_POST = Validator::validateForm($_POST);
                // Validar los datos y procesar la creación de la venta
                if (
                    !$venta->setPedidoId($_POST['idPedidoCRUD']) or
                    !$venta->setProductoId($_POST['productoDetalleP']) or
                    !$venta->setCantidad($_POST['cantidadDetalleP'])
                ) {
                    // Error de validación
                    $result['error'] = $venta->getDataError();
                    error_log('Error de validación: ' . $venta->getDataError());
                } elseif ($venta->createRow()) {
                    // Venta creada correctamente
                    $result['status'] = 1;
                    $result['message'] = 'Venta creada correctamente';
                } else {
                    // Error general al crear la venta
                    $result['error'] = 'Ocurrió un problema al crear el detalle de la venta';
                    error_log('Error al crear la venta: Ocurrió un problema al crear la venta');
                }
                break;
                /*     case 'readAll':
                if ($result['dataset'] = $venta->readAll()) {
                    $result['status'] = 1;
                    $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                } else {
                    $result['error'] = 'No existen detalles de ventas registradas';
                }
                break;*/
            case 'readAll':
                if (!$venta->setPedidoId($_POST['idPedidoCRUD'])) {
                    $result['error'] = $venta->getDataError();
                } elseif ($result['dataset'] = $venta->readAll()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Detalle de ventas inexistente';
                }
                break;
            case 'readOne':
                if (!$venta->setId($_POST['idDetallePedido'])) {
                    $result['error'] = $venta->getDataError();
                } elseif ($result['dataset'] = $venta->readOne()) {
                    $result['status'] = 1;
                } else {
                    $result['error'] = 'Detalle de ventas inexistente';
                }
                break;
            case 'updateRow':
                $_POST = Validator::validateForm($_POST);
                if (
                    !$venta->setProductoId($_POST['productoDetalleP']) or
                    !$venta->setCantidad($_POST['cantidadDetalleP']) or
                    !$venta->setId($_POST['idDetallePedido'])
                ) {
                    $result['error'] = $venta->getDataError();
                } elseif ($venta->updateRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Detalle del pedido modificado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al modificar la venta';
                }
                break;
            case 'deleteRow':
                if (
                    !$venta->setId($_POST['idDetallePedido'])
                ) {
                    $result['error'] = $venta->getDataError();
                } elseif ($venta->deleteRow()) {
                    $result['status'] = 1;
                    $result['message'] = 'Detalle de pedido eliminado correctamente';
                } else {
                    $result['error'] = 'Ocurrió un problema al eliminar el detalle del pedido';
                }
                break;
            default:
                $result['error'] = 'Acción no disponible dentro de la sesión';
        }
        // Se obtiene la excepción del servidor de base de datos por si ocurrió un problema.
        $result['exception'] = Database::getException();
        // Se indica el tipo de contenido a mostrar y su respectivo conjunto de caracteres.
        header('Content-type: application/json; charset=utf-8');
        // Se imprime el resultado en formato JSON y se retorna al controlador.
        print(json_encode($result));
    } else {
        print(json_encode('Acceso denegado'));
    }
} else {
    print(json_encode('Recurso no disponible'));
}

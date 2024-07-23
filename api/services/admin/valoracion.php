<?php
    // Se incluye la clase del modelo.
    require_once('../../models/data/valoracion_data.php');

    // Se comprueba si existe una acción a realizar, de lo contrario se finaliza el script con un mensaje de error.
    if (isset($_GET['action'])) {
        // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en el script.
        session_start();
        // Se instancia la clase correspondiente.
        $valoracion = new ValoracionData;
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
                        $result['dataset'] = $valoracion->searchRows();
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
                    if (
                        !$valoracion->setProducto($_POST['productoValoracion']) or
                        !$valoracion->setCliente($_POST['clienteValoracion']) or
                        !$valoracion->setCalificacion($_POST['calificacionValoracion']) or
                        !$valoracion->setComentario($_POST['comentarioValoracion']) or
                        !$valoracion->setFechaValoracion($_POST['fechaValoracion'])
                    ) {
                        // Error de validación
                        $result['error'] = $valoracion->getDataError();
                        error_log('Error de validación: ' . $valoracion->getDataError());
                    } elseif ($valoracion->createRow()) {
                        // Venta creada correctamente
                        $result['status'] = 1;
                        $result['message'] = 'Valoracion creada correctamente';
                    } else {
                        // Error general al crear la venta
                        $result['error'] = 'Ocurrió un problema al crear la valoracion';
                        error_log('Error al crear la venta: Ocurrió un problema al crear la valoracion');
                    }
                    break;
                case 'readAll':
                    if ($result['dataset'] = $valoracion->readAll()) {
                        $result['status'] = 1;
                        $result['message'] = 'Existen ' . count($result['dataset']) . ' registros';
                    } else {
                        $result['error'] = 'No existen valoraciones registradas';
                    }
                    break;
                case 'readOne':
                    if (!$valoracion->setId($_POST['idValoracion'])) {
                        $result['error'] = $valoracion->getDataError();
                    } elseif ($result['dataset'] = $valoracion->readOne()) {
                        $result['status'] = 1;
                    } else {
                        $result['error'] = "Valoracion inexistente";
                    }
                    break;
                case 'updateRow':
                    $_POST = Validator::validateForm($_POST);
                    if (
                        !$valoracion->setProducto($_POST['productoValoracion']) or
                        !$valoracion->setCliente($_POST['clienteValoracion']) or
                        !$valoracion->setCalificacion($_POST['calificacionValoracion']) or
                        !$valoracion->setComentario($_POST['comentarioValoracion']) or
                        !$valoracion->setFechaValoracion($_POST['fechaValoracion']) or
                        !$valoracion->setEstado(isset($_POST['estadoValoracion']) ? 1 : 0) or 
                        !$valoracion->setId($_POST['idValoracion'])
                    ) {
                        $result['error'] = $valoracion->getDataError();
                    } elseif ($valoracion->updateRow()) {
                        $result['status'] = 1;
                        $result['message'] = 'Valoración modificada correctamente';
                    } else {
                        $result['error'] = 'Ocurrió un problema al modificar la venta';
                    }
                    break;
                case 'deleteRow':
                    if (
                        !$valoracion->setId($_POST['idValoracion'])
                    ) {
                        $result['error'] = $valoracion->getDataError();
                    } elseif ($valoracion->deleteRow()) {
                        $result['status'] = 1;
                        $result['message'] = 'Valoración eliminada correctamente';
                    } else {
                        $result['error'] = 'Ocurrió un problema al eliminar la valoración';
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

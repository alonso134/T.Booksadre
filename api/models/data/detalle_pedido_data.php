<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/detalle_pedido_handler.php');
/*
 *	Clase para manejar el encapsulamiento de los datos de la tabla PRODUCTO.
 */
class DetallePedidosData extends DetallesPedidosHandler
{
    /*
     *  Atributos adicionales.
     */
    private $data_error = null;
    private $filename = null;

    /*
     *   Métodos para validar y establecer los datos.
     */
    public function setId($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del id es incorrecto';
            return false;
        }
    }

    public function setPedidoId($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->pedido = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del pedido es incorrecto';
            return false;
        }
    }

    public function setProductoId($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->producto = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del producto es incorrecto';
            return false;
        }
    }

    public function setNombre($value, $min = 2, $max = 50)
    {
        if (!Validator::validateAlphanumeric($value)) {
            $this->data_error = 'El nombre debe ser un valor alfanumérico';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            //      $this->nombre = $value;
            return true;
        } else {
            $this->data_error = 'El nombre debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }

    public function setDescripcion($value, $min = 2, $max = 250)
    {
        if (!Validator::validateString($value)) {
            $this->data_error = 'La descripción contiene caracteres prohibidos';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            //      $this->descripcion = $value;
            return true;
        } else {
            $this->data_error = 'La descripción debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }

    public function setPrecio($value)
    {
        if (Validator::validateMoney($value)) {
            $this->precio = $value;
            return true;
        } else {
            $this->data_error = 'El precio debe ser un valor numérico';
            return false;
        }
    }

    public function setSubTotal($value)
    {
        if (Validator::validateMoney($value)) {
            $this->subtotal = $value;
            return true;
        } else {
            $this->data_error = 'El precio debe ser un valor numérico';
            return false;
        }
    }
    public function setCantidad($value)
    {
        if (Validator::validateMoney($value)) {
            $this->cantidad = $value;
            if ($this->setExistenciasDisponibles($value) == true) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->data_error = 'La cantidad debe ser un valor numérico';
            return false;
        }
    }

    public function setExistenciasDisponibles($value)
    {
        if ($data = $this->validarExistencias()) {
            if ($value > $data['existencias_producto']) {
                $this->data_error = 'La cantidad solicitada supera las existencias disponibles del producto';
                return false;
            } else {
                return true;
            }
        } else {
            $this->data_error = 'El producto que intentas comprar no está disponible en nuestro inventario';
            return false;
        }
    }
    public function setDireccion($value, $min = 2, $max = 250)
    {
        if (!Validator::validateString($value)) {
            $this->data_error = 'La dirección contiene caracteres prohibidos';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            //     $this->direccion = $value;
            return true;
        } else {
            $this->data_error = 'La dirección debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }

    public function setExistencias($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            //    $this->existencias = $value;
            return true;
        } else {
            $this->data_error = 'El valor de las existencias debe ser numérico entero';
            return false;
        }
    }

    public function setImagen($file, $filename = null)
    {
        if (Validator::validateImageFile($file, 1000)) {
            //   $this->imagen = Validator::getFileName();
            return true;
        } elseif (Validator::getFileError()) {
            $this->data_error = Validator::getFileError();
            return false;
        } elseif ($filename) {
            //    $this->imagen = $filename;
            return true;
        } else {
            //     $this->imagen = 'default.png';
            return true;
        }
    }

    // Nuevo método para establecer la fecha de la venta
    public function setFechaVenta($value)
    {
        // Validar el formato de la fecha
        $date = date('Y-m-d', strtotime($value));
        if ($date && $date == $value) {
            //    $this->fecha = $date;
            return true;
        } else {
            $this->data_error = 'Fecha inválida';
            return false;
        }
    }

    public function setCategoria($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            //     $this->categoria = $value;
            return true;
        } else {
            $this->data_error = 'El identificador de la categoría es incorrecto';
            return false;
        }
    }

    public function setProveedor($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            //    $this->proveedor = $value;
            return true;
        } else {
            $this->data_error = 'El identificador de el proveedor es incorrecto';
            return false;
        }
    }

    public function setMarca($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            //   $this->marca = $value;
            return true;
        } else {
            $this->data_error = 'El identificador de la marca es incorrecto';
            return false;
        }
    }
    public function setEstado($value)
    {
        if (Validator::validateBoolean($value)) {
            //   $this->estado = $value;
            return true;
        } else {
            $this->data_error = 'Estado incorrecto';
            return false;
        }
    }

    public function setFilename()
    {
        if ($data = $this->readFilename()) {
            $this->filename = $data['foto'];
            return true;
        } else {
            $this->data_error = 'Producto inexistente';
            return false;
        }
    }

    /*
     *  Métodos para obtener los atributos adicionales.
     */
    public function getDataError()
    {
        return $this->data_error;
    }

    public function getFilename()
    {
        return $this->filename;
    }
}
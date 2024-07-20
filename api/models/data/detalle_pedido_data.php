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

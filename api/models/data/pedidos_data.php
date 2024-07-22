<?php
// Se incluye la clase para validar los datos de entrada.
require_once('../../helpers/validator.php');
// Se incluye la clase padre.
require_once('../../models/handler/pedidos_handler.php');
/*
*	Clase para manejar el encapsulamiento de los datos de las tablas PEDIDO y DETALLE_PEDIDO.
*/
class PedidosData extends PedidosHandler
{
    // Atributo genérico para manejo de errores.
    private $data_error = null;

    /*
    *   Métodos para validar y establecer los datos.
    */
    public function setIdPedido($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_pedido = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del pedido es incorrecto';
            return false;
        }
    }

    public function setIdDetalle($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->id_detalle = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del detalle pedido es incorrecto';
            return false;
        }
    }

    public function setCliente($value)
    {
        if (Validator::validateNaturalNumber($value)) {
            $this->cliente = $value;
            return true;
        } else {
            $this->data_error = 'El identificador del cliente es incorrecto';
            return false;
        }
    }


    public function setComentario($value, $min = 2, $max = 250)
    {
        if (!Validator::validateString($value)) {
            $this->data_error = 'La descripción contiene caracteres prohibidos';
            return false;
        } elseif (Validator::validateLength($value, $min, $max)) {
            $this->comentario = $value;
            return true;
        } else {
            $this->data_error = 'La descripción debe tener una longitud entre ' . $min . ' y ' . $max;
            return false;
        }
    }
    
    public function setCalificacion($value)
    {
        if (Validator::validateMoney($value)) {
            $this->calificacion = $value;
            return true;
        } else {
            $this->data_error = 'La calificación debe ser un valor numérico';
            return false;
        }
    }

    public function setProducto($value)
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
        $existencias = $this->getCantidad($this->producto);

        if (!Validator::validateNaturalNumber($value) || $value < 1) {
            $this->data_error = 'La cantidad del producto debe ser mayor o igual a 1';
            return false;
        } else if ($existencias === false) {
            $this->data_error = 'No hay existencias disponibles del producto';
            return false;
        } else if ($value > $existencias) {
            $this->data_error = 'La cantidad solicitada sobrepasa las existencias del producto';
            return false;
        }
        $this->cantidad = $value;
        return true;
    }


    public function getCantidad($value)
    {
        $data = $this->readCantidad($value);
        if ($data) {
            return $data['existencias_producto'];
        } else {
            return false;
        }
    }

    // Método para obtener el error de los datos.
    public function getDataError()
    {
        return $this->data_error;
    }
}

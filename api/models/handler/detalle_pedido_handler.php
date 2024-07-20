<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
*	Clase para manejar el comportamiento de los datos de la tabla PRODUCTO.
*/
class DetallesPedidosHandler
{
    /*
    *   Declaración de atributos para el manejo de datos.
    */
    protected $id = null;
    protected $pedido = null;
    protected $producto = null;
    protected $cantidad = null;
    protected $precio = null;
    protected $subtotal = null;

    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT c, pedido.id_cliente,  nombre_cliente, nombre_producto, cantidad_producto, 
        detalle_pedido.precio_producto, direccion_pedido, detalle_pedido.id_pedido, detalle_pedido.id_producto
        FROM detalle_pedido
        INNER JOIN pedido ON detalle_pedido.id_pedido = pedido.id_pedido
        INNER JOIN producto ON detalle_pedido.id_producto = producto.id_producto
        INNER JOIN cliente ON pedido.id_cliente = cliente.id_cliente
        WHERE nombre_producto LIKE ? OR nombre_cliente LIKE ?';
        $params = array($value, $value);
        return Database::getRows($sql, $params);
    }

    public function createRow()
    {
        $sql = 'INSERT INTO detalle_pedido(id_pedido, id_producto, cantidad_producto)
                VALUES(?, ?, ?)';

        $params = array($this->pedido, $this->producto, $this->cantidad);
        return Database::executeRow($sql, $params);
    }


    public function readAll()
    {
        $sql = 'SELECT id_detalle, pedido.id_cliente,  nombre_cliente, nombre_producto, cantidad_producto, 
        detalle_pedido.precio_producto, direccion_pedido, pedido.id_pedido AS id_pedido
        FROM detalle_pedido
        INNER JOIN pedido ON detalle_pedido.id_pedido = pedido.id_pedido
        INNER JOIN producto ON detalle_pedido.id_producto = producto.id_producto
        INNER JOIN cliente ON pedido.id_cliente = cliente.id_cliente WHERE pedido.id_cliente = ?
        ';
        $params = array($this->pedido);
        return Database::getRows($sql, $params);
    }

    public function readOne()
    {
        $sql = 'SELECT detalle_pedido_id, pedido.cliente_id,  nombre_cliente, nombre_producto, cantidad, 
        detalle_pedido.precio_producto, subtotal, direccion_pedido, pedido.pedido_id AS pedido_id, detalle_pedido.producto_id
        FROM detalle_pedido
        INNER JOIN pedido ON detalle_pedido.pedido_id = pedido.pedido_id
        INNER JOIN producto ON detalle_pedido.producto_id = producto.producto_id
        INNER JOIN cliente ON pedido.cliente_id = cliente.cliente_id
                WHERE detalle_pedido_id = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }


    public function readFilename()
    {
        $sql = 'SELECT imagen_producto
                FROM producto
                WHERE id_producto = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }

    public function updateRow()
    {
        $sql = 'UPDATE detalle_pedido
                SET id_producto = ?, cantidad_producto = ?
                WHERE id_detalle = ?';
        $params = array(
           $this->producto, $this->cantidad,$this->id
        );
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM detalle_pedido
                WHERE id_detalle = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

    public function validarExistencias()
    {
        $sql = 'SELECT existencias_producto
                FROM producto
                WHERE id_producto = ?';
        $params = array($this->producto);
        return Database::getRow($sql, $params);
    }

}
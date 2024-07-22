<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
*	Clase para manejar el comportamiento de los datos de las tablas PEDIDO y DETALLE_PEDIDO.
*/
class PedidosHandler
{
    /*
    *   Declaración de atributos para el manejo de datos.
    */
    protected $id = null;
    protected $id_pedido = null;
    protected $id_detalle = null;
    protected $cliente = null;
    protected $producto = null;
    protected $cantidad = null;
    protected $estado = null;
    protected $categoria = null;
    protected $calificacion = null;
    protected $comentario = null;
    /*
    *   ESTADOS DEL PEDIDO
    *   Pendiente (valor por defecto en la base de datos). Pedido en proceso y se puede modificar el detalle.
    *   Finalizado. Pedido terminado por el cliente y ya no es posible modificar el detalle.
    *   Entregado. Pedido enviado al cliente.
    *   Anulado. Pedido cancelado por el cliente después de ser finalizado.
    */

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
    */
    // Método para verificar si existe un pedido en proceso con el fin de iniciar o continuar una compra.


    public function getOrder()
    {
        $this->estado = 'Pendiente';
        $sql = 'SELECT id_pedido 
                FROM pedido
                WHERE estado_pedido = ? AND cliente_id = ?';
        $params = array($this->estado, $_SESSION['idCliente']);
        if ($data = Database::getRow($sql, $params)) {
            $_SESSION['idPedido'] = $data['id_pedido'];
            return true;
        } else {
            return false;
        }
    }
    // Método para iniciar un pedido en proceso.
    public function startOrder()
    {
        if ($this->getOrder()) {
            return true;
        } else {
            $sql = 'INSERT INTO pedido(direccion_pedido, id_pedido)
                    VALUES((SELECT direccion_cliente FROM cliente WHERE id_pedido = ?), ?)';
            $params = array($_SESSION['idCliente'], $_SESSION['idCliente']);
            // Se obtiene el ultimo valor insertado de la llave primaria en la tabla pedido.
            if ($_SESSION['idPedido'] = Database::getLastRow($sql, $params)) {
                return true;
            } else {
                return false;
            }
        }
    }
    public function getHistory()
    {
        $sql = 'SELECT id_detalle , nombre_producto, detalle_pedido.precio_producto, cantidad_producto, estado_pedido
                FROM detalle_pedido
                INNER JOIN pedido USING(id_pedido)
                INNER JOIN producto USING(id_producto)
                WHERE id_cliente = ?';
        $params = array($_SESSION['idCliente']);
        return Database::getRows($sql, $params);
    }
    public function readDetail()
    {
        $sql = 'SELECT id_detalle, nombre_producto, detalle_pedido.precio_producto, detalle_pedido.cantidad_producto AS cantidad_producto , detalle_pedido.id_producto AS id_producto
                FROM detalle_pedido
                INNER JOIN pedido USING(id_pedido)
                INNER JOIN producto USING(id_pedido)
                WHERE id_pedido = ?';
        $params = array($_SESSION['idPedido']);
        return Database::getRows($sql, $params);
    }
    public function readOne()
    {
        $sql = 'SELECT id_detalle, nombre_producto, detalle_pedido.precio_producto, cantidad_producto, id_producto
                FROM detalle_pedido
                INNER JOIN producto USING(id_producto)
                WHERE id_detalle = ? AND id_pedido = ?';
    
        $params = array($this->id_detalle, $_SESSION['idPedido']);
        return Database::getRow($sql, $params);
    }
    

    public function readCantidad()
    {
        $sql = 'SELECT existencias_producto
                FROM producto
                WHERE id_producto = ?';
        $params = array($this->producto);
        return Database::getRow($sql, $params);
    }

    // Método para agregar un producto al carrito de compras.

    public function createDetail()
    {
        $sql = 'INSERT INTO detalle_pedido(id_producto, precio_producto, cantidad_producto, id_pedido)
                VALUES(?, (SELECT precio_producto FROM producto WHERE id_producto = ?), ?, ?)';

        $params = array($this->producto, $this->producto, $this->cantidad, $_SESSION['idPedido']);
        return Database::executeRow($sql, $params);
    }

    // Método para actualizar la cantidad de un producto agregado al carrito de compras.
    public function updateDetail()
    {
        $sql = 'UPDATE detalle_pedido
                SET cantidad = ?
                WHERE id_detalle = ? AND id_pedido = ?';
    
        $params = array($this->cantidad, $this->id_detalle, $_SESSION['idPedido']);
        return Database::executeRow($sql, $params);
    }
    

    // Método para finalizar un pedido por parte del cliente.
    public function finishOrder()
    {
        $this->estado = 'Finalizado';

        $sql = 'SELECT id_producto, cantidad FROM detalle_pedido WHERE id_pedido = ?';
        $params = array($_SESSION['idPedido']);
        $orderDetails = Database::getRows($sql, $params);

        foreach ($orderDetails as $detail) {
            // Fetch current stock
            $sql = 'SELECT existencias_producto FROM producto WHERE id_producto = ?';
            $params = array($detail['id_producto']);
            $currentStock = Database::getRow($sql, $params);

            $newStock = $currentStock['existencias_producto'] - $detail['cantidad'];

            $sql = 'UPDATE producto SET existencias_producto = ? WHERE id_producto = ?';
            $params = array($newStock, $detail['id_producto']);
            Database::executeRow($sql, $params);
        }

        $sql = 'UPDATE pedido SET estado_pedido = ? WHERE id_pedido = ?';
        $params = array($this->estado, $_SESSION['idPedido']);
        return Database::executeRow($sql, $params);
    }




    // Método para eliminar un producto que se encuentra en el carrito de compras.
    public function deleteDetail()
    {
        $sql = 'DELETE FROM detalle_pedido
                WHERE id_detalle = ? AND id_pedido = ?';
        $params = array($this->id_detalle, $_SESSION['idPedido']);
        return Database::executeRow($sql, $params);
    }
    public function readProductosCategoria()
    {
        $sql = 'SELECT id_producto, imagen_producto, nombre_producto, descripcion_producto, precio_producto, existencias_producto
                FROM producto
                INNER JOIN categoria USING(id_categoria)
                WHERE id_categoria = ? AND estado = true
                ORDER BY nombre_producto';
        $params = array($this->categoria);
        return Database::getRows($sql, $params);
    }
    public function getProductosComprados()
    {
        $sql = 'SELECT DISTINCT dp.id_producto, producto.nombre_producto, producto.imagen_producto
                FROM pedido
                INNER JOIN detalle_pedido dp USING(id_pedido)
                INNER JOIN producto USING(id_producto)
                LEFT JOIN valoracion USING(id_producto)
                WHERE pedido.id_cliente  = ? AND pedido.estado_pedido = "Finalizado"';
        $params = array($_SESSION['idCliente']);
        return Database::GetRows($sql, $params);
    }


    public function createValoracion()
    {
        $sql = 'INSERT INTO valoracion(id_producto, id_cliente, calificacion, comentario, fecha_valoracion, estado_valoracion)
                VALUES(?, ?, ?, ?, ?, 1)';

        $params = array($this->producto, $_SESSION['idCliente'], $this->calificacion, $this->comentario, date('Y-m-d'));

        return Database::executeRow($sql, $params);
    }



    public function getValoracionByProducto($producto_id)
    {
        $sql = 'SELECT id_valoracion  FROM valoracion WHERE id_producto = ? AND id_cliente = ?';
        $params = array($producto_id, $_SESSION['idCliente']);
        return Database::getRow($sql, $params);
    }
}

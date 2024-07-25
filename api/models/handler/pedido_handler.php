<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
*	Clase para manejar el comportamiento de los datos de las tablas PEDIDO y DETALLE_PEDIDO.
*/
class PedidoHandler
{
    /*
    *   Declaración de atributos para el manejo de datos.
    */
    protected $id_pedido = null;
    protected $id_detalle = null;
    protected $cliente = null;
    protected $producto = null;
    protected $fecha = null;
    protected $direccion = null;
    protected $cantidad = null;
    protected $estado = null;


    /*
    pedido sitio privado
    */
        // Constante para establecer la ruta de las imágenes.

        public function searchRows($startDate, $endDate)
        {
            $sql = 'SELECT pedido.id_pedido, cliente.nombre_cliente, pedido.fecha_registro, pedido.direccion_pedido, pedido.estado_pedido
                    FROM pedido
                    INNER JOIN cliente USING(id_cliente)
                    WHERE pedido.fecha_registro BETWEEN ? AND ?';
            $params = array($startDate, $endDate);
            return Database::getRows($sql, $params);
        }
        
        public function createRow()
        {
            $sql = 'INSERT INTO pedido(id_cliente, direccion_pedido, estado_pedido, fecha_registro)
                    VALUES(?, ?, ?, ?)';
            $params = array($this->cliente, $this->direccion, $this->estado, $this->fecha);
            return Database::executeRow($sql, $params);
        }
        
        public function readAll()
        {
            $sql = 'SELECT pedido.id_pedido, cliente.nombre_cliente, pedido.fecha_registro, pedido.direccion_pedido, pedido.estado_pedido
                    FROM pedido
                    INNER JOIN cliente USING(id_cliente)
                    ORDER BY cliente.nombre_cliente';
            return Database::getRows($sql);
        }
        
        public function getEstado()
        {
            $sql = "SELECT COLUMN_TYPE
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_NAME = 'pedido'
                      AND COLUMN_NAME = 'estado_pedido'";
            $result = Database::getRows($sql);
        
            if (!empty($result)) {
                $enumValues = explode(",", str_replace("'", "", $result[0]['COLUMN_TYPE']));
                $options = [];
        
                foreach ($enumValues as $value) {
                    $options[] = [
                        'value' => $value,
                        'text' => ucfirst($value), // Puedes personalizar el texto según tus necesidades
                    ];
                }
        
                return json_encode($options); // Devuelve los valores como JSON
            }
        
            return '[]'; // En caso de que no se obtengan valores del enum
        }
        
        public function readOne()
        {
            $sql = 'SELECT id_pedido, id_cliente, fecha_registro, direccion_pedido, estado_pedido
                    FROM pedido
                    WHERE id_pedido = ?';
            $params = array($this->id_pedido);
            return Database::getRow($sql, $params);
        }
        
        //El this sirve para metodos y funciones estaticas
        public function updateRow()
        {
            $sql = 'UPDATE pedido
                    SET id_cliente = ?, direccion_pedido = ?, estado_pedido = ?, fecha_registro = ?
                    WHERE id_pedido = ?';
            $params = array(
                $this->cliente, $this->direccion, $this->estado, $this->fecha, $this->id_pedido
            );
            return Database::executeRow($sql, $params);
        }
        
        public function deleteRow()
        {
            $sql = 'DELETE FROM pedido
                    WHERE id_pedido = ?';
            $params = array($this->id_pedido);
            return Database::executeRow($sql, $params);
        }



    /*
     *  Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */
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
                WHERE estado_pedido = ? AND id_cliente = ?';
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
            $sql = 'INSERT INTO pedido(direccion_pedido, id_cliente)
                    VALUES((SELECT direccion_cliente FROM cliente WHERE id_cliente = ?), ?)';
            $params = array($_SESSION['idCliente'], $_SESSION['idCliente']);
            // Se obtiene el ultimo valor insertado de la llave primaria en la tabla pedido.
            if ($_SESSION['idPedido'] = Database::getLastRow($sql, $params)) {
                return true;
            } else {
                return false;
            }
        }
    }

    // Método para agregar un producto al carrito de compras.
    public function createDetail()
    {
        // Se realiza una subconsulta para obtener el precio del producto.
        $sql = 'INSERT INTO detalle_pedido(id_producto, precio_producto, cantidad_producto, id_pedido)
                VALUES(?, (SELECT precio_producto FROM producto WHERE id_producto = ?), ?, ?)';
        $params = array($this->producto, $this->producto, $this->cantidad, $_SESSION['idPedido']);
        return Database::executeRow($sql, $params);
    }

    // Método para obtener los productos que se encuentran en el carrito de compras.
    public function readDetail()
    {
        $sql = 'SELECT id_detalle, nombre_producto, detalle_pedido.precio_producto, detalle_pedido.cantidad_producto
                FROM detalle_pedido
                INNER JOIN pedido USING(id_pedido)
                INNER JOIN producto USING(id_producto)
                WHERE id_pedido = ?';
        $params = array($_SESSION['idPedido']);
        return Database::getRows($sql, $params);
    }

    // Método para finalizar un pedido por parte del cliente.
    public function finishOrder()
    {
        $this->estado = 'Finalizado';
        $sql = 'UPDATE pedido
                SET estado_pedido = ?
                WHERE id_pedido = ?';
        $params = array($this->estado, $_SESSION['idPedido']);
        return Database::executeRow($sql, $params);
    }

    // Método para actualizar la cantidad de un producto agregado al carrito de compras.
    public function updateDetail()
    {
        $sql = 'UPDATE detalle_pedido
        SET cantidad_producto = ?
        WHERE id_detalle = ? AND id_pedido = ?';
        $params = array($this->cantidad, $this->id_detalle, $_SESSION['idPedido']);
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

    // Método en procedimiento, para manipular el detalle de pedido y simplificar el paso a paso
    public function manipulateDetail()
    {
        // Se realiza una subconsulta para obtener el precio del producto.
        $sql = 'CALL insertar_orden_validado(?, ?, ?)';
        $params = array($_SESSION['idCliente'], $this->cantidad, $this->producto);
        return Database::executeRow($sql, $params);
    }

    public function top5ClientesConMasPedidos()
    {
        $sql = 'SELECT c.nombre_cliente, COUNT(p.id_pedido) as total_pedidos
                FROM cliente c
                JOIN pedido p ON c.id_cliente = p.id_cliente
                GROUP BY c.id_cliente
                ORDER BY total_pedidos DESC
                LIMIT 5';
        return Database::getRows($sql);
    }

    
// Método para actualizar la cantidad de un producto agregado al carrito de compras.
public function actualizarDetalle()
{
    $sql = 'CALL actualizar_orden_validado(?,?,?)';
    $params = array($this->cantidad, $this->id_detalle, $_SESSION['idCliente']);
    return Database::executeRow($sql, $params);
}

 // Método para eliminar un producto que se encuentra en el carrito de compras.
 public function eliminarDetalle()
 {
     $sql = 'CALL eliminar_orden_validado(?,?)';
     $params = array($this->id_detalle, $_SESSION['idCliente']);
     return Database::executeRow($sql, $params);
 }

}

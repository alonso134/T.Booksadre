<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
 *  Clase para manejar el comportamiento de los datos de la tabla administrador.
 */
class ResenasHandler
{
    /*
     *  Declaración de atributos para el manejo de datos.
     */
    protected $id = null;
    protected $producto = null;
    protected $comentario = null;
    protected $calificacion = null;


    protected $estado = null;

    /*
     *  Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
     */

    //Función para leer los comentarios de un producto
    //Función para leer los comentarios de un producto
    public function readOne()
    {
        $sql = 'SELECT r.id_resena AS "ID",
        c.id_cliente AS "IDENTIFICADOR",
        CONCAT(c.nombre_cliente, " ", c.apellido_cliente) AS "NOMBRE",
        p.nombre_producto AS "PRODUCTO",
        r.comentario_producto AS "COMENTARIO", 
        r.calificacion_producto AS "CALIFICACIÓN",
        r.fecha_valoracion AS "FECHA",
        CASE 
            WHEN r.estado_comentario = 1 THEN "Activo"
            WHEN r.estado_comentario = 0 THEN "Bloqueado"
        END AS "ESTADO"
    FROM resena r
    INNER JOIN detalle_pedido dp ON dp.id_detalle = r.id_detalle
    INNER JOIN producto p ON p.id_producto = dp.id_producto
    INNER JOIN pedido pe ON pe.id_pedido = dp.id_pedido
    INNER JOIN cliente c ON c.id_cliente = pe.id_cliente 
    WHERE p.id_producto = ? AND r.estado_comentario = 1';
        $params = array($this->producto);
        return Database::getRows($sql, $params);
    }

    /*
    *   Métodos para realizar las operaciones SCRUD (search, create, read, update, and delete).
    */
    public function createRow()
    {
        $sql = 'CALL insertar_comentario(?, ?, ?, ?)';
        $params = array($_SESSION['idCliente'], $this->calificacion, $this->comentario, $this->producto);
        return Database::executeRow($sql, $params);
    }
}

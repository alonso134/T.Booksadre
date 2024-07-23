<?php
// Se incluye la clase para trabajar con la base de datos.
require_once('../../helpers/database.php');
/*
*	Clase para manejar el comportamiento de los datos de la tabla PRODUCTO.
*/
class ValoracionHandler
{
    /*
    *   Declaración de atributos para el manejo de datos.
    */
    protected $id = null;
    protected $nombre = null;
    protected $fecha = null;
    protected $cliente = null;
    protected $producto = null;
    protected $calificacion = null;
    protected $comentario = null;
    protected $total = null;
    protected $estado = null;
    // Constante para establecer la ruta de las imágenes.


    public function searchRows()
    {
        $value = '%' . Validator::getSearchValue() . '%';
        $sql = 'SELECT id_valoracion, nombre_cliente, nombre_producto, calificacion, comentario, fecha_valoracion, estado_producto
                FROM valoracion
                 INNER JOIN cliente USING(id_cliente)
		         INNER JOIN producto USING(id_producto)
                WHERE nombre_cliente LIKE ? OR nombre_producto LIKE ?
                ORDER BY nombre_cliente';
        $params = array($value, $value);
        return Database::getRows($sql, $params);
    }


    public function createRow()
    {
        $sql = 'INSERT INTO valoracion(id_valoracion, id_cliente, id_producto, calificacion, comentario, fecha_valoracion, estado_valoracion)
                VALUES(?, ?, ?, ?, ?, 1)';

        $params = array($this->producto, $this->cliente, $this->calificacion, $this->comentario, $this->fecha, $this->estado);
        return Database::executeRow($sql, $params);
    }

    public function readAll()
    {
        $sql = 'SELECT id_valoracion, nombre_cliente, nombre_producto, calificacion, comentario, fecha_valoracion, estado_valoracion
                FROM valoracion
               INNER JOIN cliente USING(id_cliente)
		       INNER JOIN producto USING(id_producto)';
        return Database::getRows($sql);
    }

    public function readOne()
    {
        $sql = 'SELECT id_valoracion, id_cliente, id_producto, calificacion, comentario, fecha_valoracion, estado_valoracion
                FROM valoracion
                WHERE id_valoracion = ?';
        $params = array($this->id);
        return Database::getRow($sql, $params);
    }


    public function updateRow()
    {
        $sql = 'UPDATE valoracion
                SET id_producto = ?, id_cliente = ?, calificacion = ?, comentario = ?, fecha_valoracion = ?, estado_valoracion = ?
                WHERE id_valoracion = ?';
        $params = array(
            $this->producto, $this->cliente, $this->calificacion, $this->comentario, $this->fecha, $this->estado, $this->id
        );
        return Database::executeRow($sql, $params);
    }

    public function deleteRow()
    {
        $sql = 'DELETE FROM valoracion
                WHERE id_valoracion = ?';
        $params = array($this->id);
        return Database::executeRow($sql, $params);
    }

   
}

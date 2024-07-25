<?php
// Se incluye la clase con las plantillas para generar reportes.
require_once('../../helpers/report.php');

// Se instancia la clase para crear el reporte.
$pdf = new PublicReport;
// Se inicia el reporte con el encabezado del documento.
$pdf->startReport('Comprobante de Compra');
// Se incluyen las clases para la transferencia y acceso a datos.
require_once('../../models/data/pedido_data.php');
require_once('../../models/data/cliente_data.php');

// Se instancian las entidades correspondientes.
$producto = new PedidoHandler();
$cliente = new ClienteHandler(); // Asegúrate de que esta clase existe y tiene el método readOneCorreo

// Obtener detalles del pedido
$detalle_pedido = $producto->readDetail();

// Obtener información del cliente
$usuario_info = $cliente->readProfile();

// Verificar si hay detalles del pedido
if ($detalle_pedido) {
    
    // Información del cliente
    $pdf->setFont('Arial', 'B', 14);

    $pdf->setFont('Arial', 'B', 12);
    $pdf->cell(0, 10, $pdf->encodeString('Información del Cliente'), 0, 1, 'L');
    $pdf->setFont('Arial', '', 12);
    $pdf->cell(0, 10, 'Correo: ' . $usuario_info['correo_cliente'], 0, 1, 'L');
    $pdf->cell(0, 10, 'Nombre: ' . $usuario_info['nombre_cliente'], 0, 1, 'L');
    $pdf->ln(10); // Espacio de línea

    // Encabezado de la tabla de detalles de pedido
    $pdf->setFillColor(13, 27, 42);
    $pdf->setFont('Arial', 'B', 12);
    $pdf->setTextColor(255, 255, 255); // Color de texto blanco para los encabezados
    $pdf->cell(10, 10, 'No.', 1, 0, 'C', 1);
    $pdf->cell(80, 10, 'Producto', 1, 0, 'C', 1);
    $pdf->cell(30, 10, 'Precio', 1, 0, 'C', 1);
    $pdf->cell(30, 10, 'Cantidad', 1, 0, 'C', 1);
    $pdf->cell(30, 10, 'Subtotal', 1, 1, 'C', 1);

    // Datos del detalle del pedido
    $pdf->setFont('Arial', '', 12);
    $pdf->setTextColor(0, 0, 0); // Color de texto negro
    $no = 1;
    foreach ($detalle_pedido as $item) {
        $subtotal = $item['precio_producto'] * $item['cantidad_producto'];
        $pdf->cell(10, 10, $no++, 1, 0, 'C');
        $pdf->cell(80, 10, $pdf->encodeString($item['nombre_producto']), 1, 0, 'L');
        $pdf->cell(30, 10, number_format($item['precio_producto'], 2), 1, 0, 'R');
        $pdf->cell(30, 10, $item['cantidad_producto'], 1, 0, 'R');
        $pdf->cell(30, 10, number_format($subtotal, 2), 1, 1, 'R');
    }

    // Total de la compra (calcular el total en base a los detalles del pedido)
    $total = array_reduce($detalle_pedido, function($carry, $item) {
        return $carry + ($item['precio_producto'] * $item['cantidad_producto']);
    }, 0);

    $pdf->setFillColor(13, 27, 42);
    $pdf->setFont('Arial', 'B', 12);
    $pdf->setTextColor(255, 255, 255); // Color de texto blanco para la celda de Total
    $pdf->cell(150, 10, 'Total', 1, 0, 'C', 1);
    $pdf->cell(30, 10, number_format($total, 2), 1, 1, 'R', 1);

    // Se llama implícitamente al método footer() y se envía el documento al navegador web.
    $pdf->output('I', 'comprobante_compra.pdf');
} else {
    print('No hay detalles para mostrar');
}
?>

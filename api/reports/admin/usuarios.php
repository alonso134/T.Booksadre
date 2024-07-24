<?php
// Se incluye la clase con las plantillas para generar reportes.
require_once('../../helpers/report.php');

// Se instancia la clase para crear el reporte.
$pdf = new Report;

// Se incluyen las clases para la transferencia y acceso a datos.
require_once('../../models/data/cliente_data.php');

// Se instancian las entidades correspondientes.
$cliente = new ClienteHandler;

// Se verifica si existen registros para mostrar, de lo contrario se imprime un mensaje.
if ($dataClientes = $cliente->readAll()) {
    // Se inicia el reporte con el encabezado del documento.
    $pdf->startReport('Listado de Clientes');

    // Se establece un color de relleno para los encabezados.
    $pdf->setFillColor(13, 27, 42);
    // Se establece la fuente para los encabezados.
    $pdf->setFont('Arial', 'B', 11);
    $pdf->setTextColor(255, 255, 255); // Color de texto blanco para los encabezados

    // Se imprimen las celdas con los encabezados.
    $pdf->cell(30, 10, 'Nombre', 1, 0, 'C', 1);
    $pdf->cell(30, 10, 'Apellido', 1, 0, 'C', 1);
    $pdf->cell(50, 10, 'Correo', 1, 0, 'C', 1);
    $pdf->cell(30, 10, 'DUI', 1, 0, 'C', 1);
    $pdf->cell(30, 10, 'Estado', 1, 1, 'C', 1);

    // Se establece la fuente para los datos de los clientes.
    $pdf->setFont('Arial', '', 11);
    $pdf->setTextColor(0, 0, 0); // Color de texto negro

    // Se recorren los registros fila por fila.
    $fill = false; // Alternancia de color de relleno
    foreach ($dataClientes as $rowCliente) {
        // Se imprimen las celdas con los datos de los clientes.
        $pdf->setFillColor($fill ? 230 : 255); // Color de relleno gris más claro y blanco alternante
        $pdf->cell(30, 10, $pdf->encodeString($rowCliente['nombre_cliente']), 1, 0, '', $fill);
        $pdf->cell(30, 10, $pdf->encodeString($rowCliente['apellido_cliente']), 1, 0, '', $fill);
        $pdf->cell(50, 10, $pdf->encodeString($rowCliente['correo_cliente']), 1, 0, '', $fill);
        $pdf->cell(30, 10, $pdf->encodeString($rowCliente['dui_cliente']), 1, 0, '', $fill);
        $pdf->cell(30, 10, $pdf->encodeString($rowCliente['estado_cliente']), 1, 1, '', $fill);
        // Alternar color de relleno
        $fill = !$fill;
    }

    // Se llama implícitamente al método footer() y se envía el documento al navegador web.
    $pdf->output('I', 'clientes.pdf');
} else {
    print('No hay clientes para mostrar');
}

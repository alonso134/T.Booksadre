<?php
// Se incluye la clase con las plantillas para generar reportes.
require_once('../../helpers/report.php');

// Se instancia la clase para crear el reporte.
$pdf = new Report;

// Se incluyen las clases para la transferencia y acceso a datos.
require_once('../../models/data/administrador_data.php');

// Se instancian las entidades correspondientes.
$administrador = new AdministradorHandler;

// Se verifica si existen registros para mostrar, de lo contrario se imprime un mensaje.
if ($dataAdministradores = $administrador->readAll()) {
    // Se inicia el reporte con el encabezado del documento.
    $pdf->startReport('Listado de Administradores');

    // Se establece un color de relleno para los encabezados.
    $pdf->setFillColor(13, 27, 42);
    // Se establece la fuente para los encabezados.
    $pdf->setFont('Arial', 'B', 11);
    $pdf->setTextColor(255, 255, 255); // Color de texto blanco para los encabezados

    // Se imprimen las celdas con los encabezados.
    $pdf->cell(40, 10, 'Nombre', 1, 0, 'C', 1);
    $pdf->cell(40, 10, 'Apellido', 1, 0, 'C', 1);
    $pdf->cell(60, 10, 'Correo', 1, 0, 'C', 1);
    $pdf->cell(40, 10, 'Alias', 1, 1, 'C', 1);

    // Se establece la fuente para los datos de los administradores.
    $pdf->setFont('Arial', '', 11);
    $pdf->setTextColor(0, 0, 0); // Color de texto negro

    // Se recorren los registros fila por fila.
    $fill = false; // Alternancia de color de relleno
    foreach ($dataAdministradores as $rowAdministrador) {
        // Se imprimen las celdas con los datos de los administradores.
        $pdf->setFillColor($fill ? 230 : 255); // Color de relleno gris más claro y blanco alternante
        $pdf->cell(40, 10, $pdf->encodeString($rowAdministrador['nombre_administrador']), 1, 0, '', $fill);
        $pdf->cell(40, 10, $pdf->encodeString($rowAdministrador['apellido_administrador']), 1, 0, '', $fill);
        $pdf->cell(60, 10, $pdf->encodeString($rowAdministrador['correo_administrador']), 1, 0, '', $fill);
        $pdf->cell(40, 10, $pdf->encodeString($rowAdministrador['alias_administrador']), 1, 1, '', $fill);
        // Alternar color de relleno
        $fill = !$fill;
    }

    // Se llama implícitamente al método footer() y se envía el documento al navegador web.
    $pdf->output('I', 'administradores.pdf');
} else {
    print('No hay administradores para mostrar');
}



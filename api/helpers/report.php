<?php
// Se incluye la clase para generar archivos PDF.
require_once('../../libraries/fpdf185/fpdf.php');

/*
*   Clase para definir las plantillas de los reportes del sitio privado.
*   Para más información http://www.fpdf.org/
*/
class Report extends FPDF
{
    // Constante para definir la ruta de las vistas del sitio privado.
    const CLIENT_URL = 'http://localhost/T.Booksadre/views/admin/';
    // Propiedad para guardar el título del reporte.
    private $title = null;

    /*
    *   Método para iniciar el reporte con el encabezado del documento.
    *   Parámetros: $title (título del reporte).
    *   Retorno: ninguno.
    */
    public function startReport($title)
    {
        // Se crea una sesión o se reanuda la actual para poder utilizar variables de sesión en los reportes.
        session_start();
        // Se verifica si un administrador ha iniciado sesión para generar el documento, de lo contrario se direcciona a la página web principal.
        if (isset($_SESSION['idAdministrador'])) {
            // Se asigna el título del documento a la propiedad de la clase.
            $this->title = $title;
            // Se establece el título del documento (true = utf-8).
            $this->setTitle('Booksadre - Reporte', true);
            // Se establecen los margenes del documento (izquierdo, superior y derecho).
            $this->setMargins(15, 15, 15);
            // Se añade una nueva página al documento con orientación vertical y formato carta, llamando implícitamente al método header()
            $this->addPage('p', 'letter');
            // Se define un alias para el número total de páginas que se muestra en el pie del documento.
            $this->aliasNbPages();
        } else {
            header('location:' . self::CLIENT_URL);
        }
    }

    /*
    *   Método para codificar una cadena de alfabeto español a UTF-8.
    *   Parámetros: $string (cadena).
    *   Retorno: cadena convertida.
    */
    public function encodeString($string)
    {
        return mb_convert_encoding($string, 'ISO-8859-1', 'utf-8');
    }

    /*
    *   Se sobrescribe el método de la librería para establecer la plantilla del encabezado de los reportes.
    *   Se llama automáticamente en el método addPage()
    */
    public function header()
    {   
        require_once('../../models/data/administrador_data.php');
    
        $administrador = new AdministradorHandler();
        $usuario_info = $administrador->readProfile();
    
        // Logo
        $this->image('../../images/logo.png', 15, 15, 20);
    
        // Título
        $this->setFont('Arial', 'B', 15);
        $this->cell(0, 10, $this->encodeString($this->title), 0, 1, 'C');
    
        // Fecha y hora
        $this->setFont('Arial', '', 10);
        $this->cell(0, 5, 'Fecha/Hora: ' . date('d-m-Y H:i:s'), 0, 1, 'C');
    
        // Línea separadora
        $this->Line(10, 35, 200, 35);
    
        // Información del usuario
        $this->ln(5);
        $this->setFont('Arial', 'B', 12);
        $this->cell(0, 10, $this->encodeString('Información del Usuario'), 0, 1, 'L');
        $this->setFont('Arial', '', 12);
        $this->cell(0, 7, 'Correo: ' . $usuario_info['correo_administrador'], 0, 1, 'L');
        $this->cell(0, 7, 'Nombre: ' . $usuario_info['nombre_administrador'], 0, 1, 'L');
    
        // Espacio adicional antes del contenido principal
        $this->ln(10);
    }

    /*
    *   Se sobrescribe el método de la librería para establecer la plantilla del pie de los reportes.
    *   Se llama automáticamente en el método output()
    */
    public function footer()
    {
        // Se establece la posición para el número de página (a 15 milímetros del final).
        $this->setY(-15);
        // Se establece la fuente para el número de página.
        $this->setFont('Arial', 'I', 8);
        // Se imprime una celda con el número de página.
        $this->cell(0, 10, $this->encodeString('Página ') . $this->pageNo() . '/{nb}', 0, 0, 'C');
    }
}

class PublicReport extends FPDF
{
    const CLIENT2_URL = 'http://localhost/T.Booksadre/views/public/';

    // Propiedad para guardar el título del reporte.
    private $title = null;
    

    /*
    *   Método para iniciar el reporte con el encabezado del documento.
    *   Parámetros: $title (título del reporte).
    *   Retorno: ninguno.
    */
    public function startReport($title)
    {

        // Se inicia o se reanuda la sesión para utilizar variables de sesión.
        session_start();
        
        // Verificar si el usuario está logueado
        if (isset($_SESSION['idCliente'])) {
            // Se asigna el título del documento a la propiedad de la clase.
            $this->title = $title;
            // Se establece el título del documento (true = utf-8).
            $this->setTitle('Booksadre - Reporte', true);
            // Se establecen los margenes del documento (izquierdo, superior y derecho).
            $this->setMargins(15, 15, 15);
            // Se añade una nueva página al documento con orientación vertical y formato carta, llamando implícitamente al método header()
            $this->addPage('p', 'letter');
            // Se define un alias para el número total de páginas que se muestra en el pie del documento.
            $this->aliasNbPages();
        } else {
            header('location:' . self::CLIENT2_URL);
        }
    }

    /*
    *   Método para codificar una cadena de alfabeto español a UTF-8.
    *   Parámetros: $string (cadena).
    *   Retorno: cadena convertida.
    */
    public function encodeString($string)
    {
        return mb_convert_encoding($string, 'ISO-8859-1', 'utf-8');
    }

    /*
    *   Se sobrescribe el método de la librería para establecer la plantilla del encabezado de los reportes.
    *   Se llama automáticamente en el método addPage()
    */
    
    public function header()
    {   


        $this->image('../../../resources/img/logo.png', 15, 15, 20);
        // Se ubica el título.
        $this->cell(20);
        $this->setFont('Arial', 'B', 15);
        $this->cell(166, 10, $this->encodeString($this->title), 0, 1, 'C');
        // Se ubica la fecha y hora del servidor.
        $this->cell(20);
        $this->setFont('Arial', '', 10);
        $this->cell(166, 10, 'Fecha/Hora: ' . date('d-m-Y H:i:s'), 0, 1, 'C');
        // Se agrega un salto de línea para mostrar el contenido principal del documento.
        $this->ln(10);
    }

    /*
    *   Se sobrescribe el método de la librería para establecer la plantilla del pie de los reportes.
    *   Se llama automáticamente en el método output()
    */
    public function footer()
    {
        // Se establece la posición para el número de página (a 15 milímetros del final).
        $this->setY(-15);
        // Se establece la fuente para el número de página.
        $this->setFont('Arial', 'I', 8);
        // Se imprime una celda con el número de página.
        $this->cell(0, 10, $this->encodeString('Página ') . $this->pageNo() . '/{nb}', 0, 0, 'C');
    }
}


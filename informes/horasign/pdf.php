<?php
ini_set('max_execution_time', 600); //180 seconds = 3 minutes
require_once __DIR__ . '/../../vendor/autoload.php';

if (session_status() === PHP_SESSION_ACTIVE) {
    session_write_close();
}

$start_time = microtime(true);

$_destino = 'F'; // I: Muestra PDF en pantalla; D: Descarga el archivo; F: Descarga el Archivo; V: Abre otra Pestaña
$_SaltoPag = "1";
$_PlanillaSemanal = $payload['PlanillaSemanal'] ?? 0;

$_titulo = $_PlanillaSemanal === 0 ? 'Horarios Asignados' : 'Planilla Semanal Horarios';
$_nombre = $_titulo;

$_nombre = $_nombre ?: strtoupper($_titulo);

$_path = $_SERVER['DOCUMENT_ROOT'] . '/' . HOMEHOST . '/app-data/archivos';

// Crear el directorio si no existe
if (!file_exists($_path)) {
    mkdir($_path, 0777, true);
}

$fecha = new DateTime();
$MicroTime = microtime(true);
$NombreArchivo = $_path . '/' . $_nombre . "_" . $MicroTime . ".pdf";
$NombreArchivo = str_replace(' ', '_', $NombreArchivo);
$NombreArchivo2 = $_nombre . "_" . $MicroTime . ".pdf";
$NombreArchivo2 = str_replace(' ', '_', $NombreArchivo2);

$FechaIni = $payload['FechaDesde'] ?? '';
$FechaFin = $payload['FechaHasta'] ?? '';

$data = $Datos['DATA'] ?? [];

// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');

try {

    BorrarArchivosPDF($_path . '/*.pdf');
    /** Borra los archivos anteriores a la fecha actual */
    ini_set("pcre.backtrack_limit", "5000000");

    ob_start();

    // Seleccionar el archivo de reporte según el tipo de planilla
    if ($_PlanillaSemanal === 1) {
        require_once 'reporte_pdf_planilla.php';
    } else {
        require_once 'reporte_pdf.php';
    }

    $buffer = ob_get_clean();
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'default_font_size' => 10,
        'margin_left' => 5,
        'margin_right' => 5,
        'margin_top' => 10,
        'margin_bottom' => 12,
        'margin_header' => 5,
        'margin_footer' => 5,
        'orientation' => 'P'
    ]);
    $mpdf->SetDisplayMode('fullpage');
    $mpdf->shrink_tables_to_fit = 0;
    $mpdf->SetAuthor('Portal WEB - ' . ($_SESSION['NOMBRE_SESION'] ?? ''));
    $mpdf->SetTitle($_titulo); // Corregido: usar $_titulo en lugar de $title
    $mpdf->SetSubject($_titulo);
    $mpdf->SetCreator('Portal WEB');
    $mpdf->SetCompression(true);
    $mpdf->simpleTables = true;
    $mpdf->useSubstitutions = false;

    $mpdf->SetHTMLHeader('
            <table width="100%">
                <tr>
                    <td style="width: 55%;">
                        <p class="left bold">' . strtoupper($_titulo) . '</p>
                    </td>
                    <td style="width: 45%;" align="right">
                        <p class="right bold">Desde: ' . $FechaDesde . ' hasta: ' . $FechaHasta . '</p>
                    </td>
                </tr>
            </table>
        ');
    $mpdf->SetHTMLFooter('
            <hr>
            <table width="100%">
                <tr>
                    <td width="33%">Fecha de Impresión: {DATE j/m/Y}</td>
                    <td width="33%" align="center">Hoja: {PAGENO}/{nbpg}</td>
                    <td width="33%" style="text-align: right;">' . strtoupper($_titulo) . '</td>
                </tr>
            </table>');

    $mpdf->list_indent_first_level = 1; // 1 or 0 - whether to indent the first level of a list

    // Load a stylesheet
    $stylesheet = file_get_contents(__DIR__ . '/../../css/stylepdf/mpdfstyletables.css');

    $mpdf->WriteHTML($stylesheet, 1); // The parameter 1 tells that this is css/style only and no body/html/text

    // Escribir el contenido HTML directamente
    $mpdf->WriteHTML($buffer, 2);

    ob_end_clean();

    $mpdf->Output(strtolower($NombreArchivo), \Mpdf\Output\Destination::FILE);

    $data = [
        'status' => 'ok',
        'Mensaje' => 'Reporte Creado. ',
        'archivo' => 'archivos/' . strtolower($NombreArchivo2),
        'destino' => $_destino,
        'data' => $data,
        'payloadEstructura' => $payloadEstructura ?? [],
        'estructuras' => $estructuras ?? [],
    ];

    echo json_encode($data);

} catch (\Mpdf\MpdfException $e) {
    error_log('Error mPDF: ' . $e->getMessage());
    $errorData = [
        'status' => 'error',
        'Mensaje' => 'Error al generar el PDF: ' . $e->getMessage(),
        'tipo' => 'MpdfException',
        'archivo' => '',
        'linea' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    echo json_encode($errorData);
} catch (\Exception $e) {
    error_log('Error general: ' . $e->getMessage());
    $errorData = [
        'status' => 'error',
        'Mensaje' => 'Error general: ' . $e->getMessage(),
        'tipo' => get_class($e),
        'archivo' => $e->getFile(),
        'linea' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    echo json_encode($errorData);
} catch (\Throwable $e) {
    error_log('Error crítico: ' . $e->getMessage());
    $errorData = [
        'status' => 'error',
        'Mensaje' => 'Error crítico: ' . $e->getMessage(),
        'tipo' => get_class($e),
        'archivo' => $e->getFile(),
        'linea' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    echo json_encode($errorData);
}

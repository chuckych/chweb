<?php
ini_set('max_execution_time', 600); //180 seconds = 3 minutes
// Solo establecer headers si no se han enviado ya
if (!headers_sent()) {
    header('Content-type: text/html; charset=utf-8');
    header("Content-Type: application/json");
}
require_once __DIR__ . '/../../../vendor/autoload.php';
// E_ALL(); // Comentado ya que no existe esta función
// ocultar errores
error_reporting(0);


$start_time = microtime(true);
$titulo = 'REPORTE DE LIQUIDACION';

// Los datos reales vienen de la variable $result del archivo prysmian3.php
$datos = $result ?? [];

// Obtener fechas del payload
$fechaInicio = date('d/m/Y', strtotime($payload['FechIni']));
$fechaFin = date('d/m/Y', strtotime($payload['FechFin']));

// Generar hash único para el archivo (usar el hash del payload si existe)
$hash = microtime(true);

// Usar el directorio de archivos de app-data
$nameFile = "archivos/reporte-liquidacion-{$hash}.pdf";
$fecha = new DateTime();
$MicroTime = microtime(true);
try {

    // La limpieza de archivos se hace desde app-data/index.php con clean_files('archivos/', 1, 'pdf');
    ini_set("pcre.backtrack_limit", "5000000");

    ob_start();
    require_once __DIR__ . '/pdf3render.php';
    $buffer = ob_get_clean();
    // formato oficio 
    $format = [216, 340]; // Tamaño oficio en mm (ancho x alto)
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'c',
        'format' => $format,
        'default_font_size' => '10pt',
        'margin_left' => 5,
        'margin_right' => 5,
        'margin_top' => 10,
        'margin_bottom' => 12,
        'margin_header' => 5,
        'margin_footer' => 5,
        'orientation' => "L"
    ]);
    $mpdf->SetDisplayMode('fullpage');
    $mpdf->shrink_tables_to_fit = 0;
    $mpdf->SetAuthor('Portal Control Horario WEB');
    $mpdf->SetTitle($titulo);
    $mpdf->SetSubject($titulo);
    $mpdf->SetCreator('Portal Control Horario WEB');
    $mpdf->SetCompression(true);
    $mpdf->simpleTables = true;
    $mpdf->useSubstitutions = false;

    $mpdf->SetHTMLHeader('
            <table width="100%" style="border-bottom: 0.5pt solid #333; color:#333">
                <tr>
                    <td style="width: 65%;">
                        <p class="left bold">' . $titulo . '</p>
                    </td>
                    <td style="width: 35%;" align="right">
                        <p class="right">Período: ' . $fechaInicio . ' a ' . $fechaFin . '</p>
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
                    <td width="33%" style="text-align: right;">' . strtoupper($titulo) . '</td>
                </tr>
            </table>');

    $mpdf->list_indent_first_level = 1; // 1 or 0 - whether to indent the first level of a list

    // Load a stylesheet
    $stylesheet = file_get_contents(__DIR__ . '/../../../css/stylepdf/mpdfstyletables.css');

    $mpdf->WriteHTML($stylesheet, 1); // The parameter 1 tells that this is css/style only and no body/html/text
    $chunks = explode("chunk", $buffer);
    // foreach ($chunks as $key => $val) {
    //     $mpdf->WriteHTML($val, 2);
    // }
    foreach ($chunks as $key => $val) {
        if (!empty(trim($val))) {
            // Dividir chunks grandes en pedazos más pequeños si es necesario
            if (strlen($val) > 1000000) { // Si el chunk es mayor a 1MB
                $smallChunks = str_split($val, 500000); // Dividir en pedazos de 500KB
                foreach ($smallChunks as $smallChunk) {
                    $mpdf->WriteHTML($smallChunk, 2);
                }
            } else {
                $mpdf->WriteHTML($val, 2);
            }
        }
    }

    // Limpiar buffer solo si existe
    if (ob_get_level()) {
        ob_end_clean();
    }

    // Guardar el PDF en el directorio app-data/archivos
    $outputPath = __DIR__ . '/../../../app-data/' . $nameFile;
    $mpdf->Output($outputPath, \Mpdf\Output\Destination::FILE);

    $data = ['status' => 'ok', 'Mensaje' => 'Reporte Creado', 'archivo' => $nameFile];

    // Verificar si Flight está disponible, sino usar echo json_encode
    if (class_exists('Flight')) {
        Flight::json($data);
    } else {
        echo json_encode($data);
    }

} catch (\Mpdf\MpdfException $e) {
    $error_data = ['status' => 'error', 'Mensaje' => 'Error al generar PDF: ' . $e->getMessage()];
    file_put_contents(__DIR__ . "/error_reporte_liquidacion.log", date('Y-m-d H:i:s') . " - " . $e->getMessage() . PHP_EOL, FILE_APPEND | LOCK_EX);

    if (class_exists('Flight')) {
        Flight::json($error_data);
    } else {
        echo json_encode($error_data);
    }
} catch (\Exception $e) {
    $error_data = ['status' => 'error', 'Mensaje' => 'Error general: ' . $e->getMessage()];
    file_put_contents(__DIR__ . "/error_reporte_liquidacion.log", date('Y-m-d H:i:s') . " - " . $e->getMessage() . PHP_EOL, FILE_APPEND | LOCK_EX);

    if (class_exists('Flight')) {
        Flight::json($error_data);
    } else {
        echo json_encode($error_data);
    }
}
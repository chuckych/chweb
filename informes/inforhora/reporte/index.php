<?php
ini_set('max_execution_time', 600); //180 seconds = 3 minutes
session_start();
header('Content-type: text/html; charset=utf-8');
header("Content-Type: application/json");
require __DIR__ . '/../../../config/index.php';
ultimoacc();
secure_auth_ch();
$Modulo = '24';
ExisteModRol($Modulo);
E_ALL();

require_once __DIR__ . '/../../../vendor/autoload.php';
if (($_SERVER["REQUEST_METHOD"] == "POST")) {
    $start_time = microtime(true);

    $_format = FusNuloPOST('_format', 'A4'); /** Tipo de Hoja */
    $_orientation = FusNuloPOST('_orientation', 'L'); /** L: Horizontal; P: Vertical */
    $_destino = FusNuloPOST('_destino', 'F'); /** I: Muestra PDF en pantalla; D: Descarga el archivo; F: Descarga el Archivo; V: Abre otra Pestaña */
    $_password = FusNuloPOST('_password', ''); /** Password de apertura del archivo */
    $_print = FusNuloPOST('_print', ''); /**  Bloquea la impresion del archivo */
    $_modify = FusNuloPOST('_modify', ''); /**  Bloquea la modificacion archivo */
    $_copy = FusNuloPOST('_copy', ''); /**  Bloquea la copiar datos del archivo */
    $_annotforms = FusNuloPOST('_annotforms', ''); /**  Bloquea la Anotaciones del archivo 'annot-forms'*/
    $_nombre = FusNuloPOST('_nombre', "InforHora"); /**  Nombre del archivo*/
    $_titulo = FusNuloPOST('_titulo', ""); /**  titulo del reporte*/
    $_SaltoPag = FusNuloPOST('_SaltoPag', "0"); /** salto de pagina por legajo */
    $_TotHoras = 1;
    $_TotNove = FusNuloPOST('_TotNove', "0"); /** Mostrar Total de Novedades por legajos */
    $_VerHoras = FusNuloPOST('_VerHoras', "0"); /** Mostrar Horas */
    $_VerNove = FusNuloPOST('_VerNove', "0"); /** Mostrar Novedades */
    $_VerFic = FusNuloPOST('_VerFic', "0"); /** Mostrar Fichadas */
    $_Por = FusNuloPOST("_Por", 'Leg'); /** Agrupar por: Fech,Lega, ApNo */
    $_agrupar_thcolu = $_POST['agrupar_thcolu'] ?? '0';
    $_agrupar_thcolu = ($_agrupar_thcolu == '1') ? true : false;

    $_titulo = $_titulo == '' ? 'INFORME DE HORAS' : $_titulo;
    $_nombre = $_nombre == '' ? strtoupper($_titulo) : $_nombre;
    // h1($_nombre); exit;
    $title = ($_nombre == 'InforHora') ? strtoupper($_titulo) : $_nombre;

    $_path = $_SERVER['DOCUMENT_ROOT'] . '/' . HOMEHOST . '/informes/inforhora/reporte/archivos/';
    $fecha = new DateTime();
    $MicroTime = microtime(true);
    $NombreArchivo = $_path . $_nombre . "_" . $MicroTime . ".pdf";
    $NombreArchivo = str_replace(' ', '_', $NombreArchivo);
    $NombreArchivo2 = $_nombre . "_" . $MicroTime . ".pdf";
    $NombreArchivo2 = str_replace(' ', '_', $NombreArchivo2);


    $DateRange = explode(' al ', $_POST['_dr']);
    $FechaIni = test_input(dr_fecha($DateRange[0]));
    $FechaFin = test_input(dr_fecha($DateRange[1]));

    $fecha1 = new DateTime($FechaIni);
    $fecha2 = new DateTime($FechaFin);
    $diff = $fecha1->diff($fecha2);
    // echo $diff->days . ' dias'; exit;

    if ($diff->days > '31') {
        $data = array('status' => 'error', 'dato' => '<b>Rango de fechas supierior a 31 d&iacute;as</b>');
        echo json_encode($data);
        exit;
    }
    try {

        // ============ LOGGING DE PERFORMANCE ============
        $perf_log = [];
        $perf_start = microtime(true);
        
        function perf_log($label, $start = null) {
            global $perf_log, $perf_start;
            $now = microtime(true);
            if ($start) {
                $perf_log[] = sprintf("%s: %.2f ms", $label, ($now - $start) * 1000);
            } else {
                $perf_log[] = sprintf("[%.2f ms] %s", ($now - $perf_start) * 1000, $label);
            }
            return $now;
        }
        
        perf_log("INICIO index.php");
        
        $t1 = microtime(true);
        BorrarArchivosPDF('archivos/*.pdf'); /** Borra los archivos anteriores a la fecha actual */
        perf_log("BorrarArchivosPDF", $t1);
        
        ini_set("pcre.backtrack_limit", "5000000");

        $t2 = microtime(true);
        ob_start();
        require_once 'InforHora.php';
        $buffer = ob_get_clean();
        perf_log("require InforHora.php + ob_get_clean", $t2);
        perf_log("Tamaño del buffer HTML: " . strlen($buffer) . " bytes");
        
        $t3 = microtime(true);
        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'c',
            'format' => $_format,
            'default_font_size' => '10pt',
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 10,
            'margin_bottom' => 12,
            'margin_header' => 5,
            'margin_footer' => 5,
            'orientation' => $_orientation,
            // OPTIMIZACIONES DE PERFORMANCE
            'autoScriptToLang' => false,
            'autoLangToFont' => false
        ]);
        perf_log("new Mpdf() - Inicialización", $t3);
        
        $t4 = microtime(true);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->shrink_tables_to_fit = 1;
        $mpdf->SetAuthor('Control Horario WEB - ' . $_SESSION['NOMBRE_SESION']);
        $mpdf->SetTitle($title);
        $mpdf->SetSubject($_titulo);
        $mpdf->SetCreator('Control Horario WEB');
        $mpdf->SetCompression(true);
        
        // OPTIMIZACIONES DE PERFORMANCE
        $mpdf->simpleTables = true;
        $mpdf->useSubstitutions = false;
        $mpdf->packTableData = true;
        $mpdf->keep_table_proportions = false;

        $_watermark = FusNuloPOST("_watermark", ''); /** Marca de Agua */
        if (($_watermark)) {
            $mpdf->SetWatermarkText($_watermark); /** MARCA DE AGUA */
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'Arial';
            $mpdf->watermarkTextAlpha = 0.1;
        }

        $mpdf->SetHTMLHeader('
            <table width="100%">
                <tr>
                    <td style="width: 65%;">
                        <p class="left bold">' . strtoupper($_titulo) . '</p>
                    </td>
                    <td style="width: 35%;" align="right">
                        <p class="right">Desde: ' . FechaFormatVar($FechaIni, 'd/m/Y') . ' hasta: ' . FechaFormatVar($FechaFin, 'd/m/Y') . '</p>
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
        perf_log("Configuración de mPDF (headers, footers, etc)", $t4);

        $mpdf->list_indent_first_level = 1; // 1 or 0 - whether to indent the first level of a list

        // Load a stylesheet
        $t5 = microtime(true);
        $stylesheet = file_get_contents('../../../css/stylepdf/mpdfstyletables.css');
        $mpdf->WriteHTML($stylesheet, 1); // The parameter 1 tells that this is css/style only and no body/html/text
        perf_log("WriteHTML CSS", $t5);

        $t6 = microtime(true);
        $chunks = explode("chunk", $buffer);
        perf_log("Chunks totales: " . count($chunks));
        
        $t7 = microtime(true);
        foreach ($chunks as $key => $val) {
            $mpdf->WriteHTML($val, 2);
        }
        perf_log("WriteHTML - Renderizado de todos los chunks", $t7);
        
        ob_end_clean();

        $t8 = microtime(true);
        $mpdf->Output($NombreArchivo, \Mpdf\Output\Destination::FILE);
        perf_log("Output PDF a archivo", $t8);
        
        perf_log("TOTAL index.php", $perf_start);
        
        // Guardar log de performance
        $log_content = "\n========================================\n";
        $log_content .= "LOG DE PERFORMANCE index.php - " . date('Y-m-d H:i:s') . "\n";
        $log_content .= "========================================\n";
        $log_content .= "Archivo: $NombreArchivo2\n";
        $log_content .= "Páginas estimadas: " . count($chunks) . "\n";
        $log_content .= "Formato: $_format | Orientación: $_orientation\n";
        $log_content .= "========================================\n";
        $log_content .= implode("\n", $perf_log) . "\n";
        $log_content .= "========================================\n";
        $log_content .= "TIEMPO TOTAL: " . sprintf("%.2f", (microtime(true) - $perf_start) * 1000) . " ms\n";
        $log_content .= "========================================\n\n";
        
        file_put_contents(__DIR__ . '/performance_log.txt', $log_content, FILE_APPEND);

        $data = array('status' => 'ok', 'dato' => 'Reporte Creado.' . $_nombre, 'archivo' => $NombreArchivo2, 'destino' => $_destino, 'x' => getmypid());
        echo json_encode($data);
        exit();
    } catch (\Mpdf\MpdfException $e) {
        echo $e->getMessage();
        // echo $formatter->getHtmlMessage();
        file_put_contents("../../../logs/error/Error_PDF_InforHora_" . date('Ymd') . ".log", $e->getMessage(), FILE_APPEND | LOCK_EX);
        exit();
    }
}
<?php
ini_set('max_execution_time', 600); //180 seconds = 3 minutes
session_start();
header('Content-type: text/html; charset=utf-8');
header("Content-Type: application/json");
require __DIR__ . '/../../../config/index.php';
ultimoacc();
secure_auth_ch_json();
$Modulo = '19';
timeZone();
timeZone_lang();
ExisteModRol($Modulo);
E_ALL();
$request = Flight::request();
$method = $request->method;
$params = $request->data;
$authBasic = base64_encode('chweb:' . HOMEHOST);
$token = sha1($_SESSION['RECID_CLIENTE']);

if (($method == "POST")) {
    $start_time = microtime(true);

    $_format = FusNuloPOST('_format', 'A4');
    /** Tipo de Hoja */
    $_orientation = FusNuloPOST('_orientation', 'P');
    /** L: Horizontal; P: Vertical */
    $_destino = FusNuloPOST('_destino', 'D');
    /** I: Muestra PDF en pantalla; D: Descarga el archivo; F: Descarga el Archivo; V: Abre otra Pestaña */
    $_password = FusNuloPOST('_password', '');
    /** Password de apertura del archivo */
    $_print = FusNuloPOST('_print', '');
    /**  Bloquea la impresion del archivo */
    $_modify = FusNuloPOST('_modify', '');
    /**  Bloquea la modificacion archivo */
    $_copy = FusNuloPOST('_copy', '');
    /**  Bloquea la copiar datos del archivo */
    $_annotforms = FusNuloPOST('_annotforms', '');
    /**  Bloquea la Anotaciones del archivo 'annot-forms'*/
    $_nombre = FusNuloPOST('_nombre', "HorAsign");
    /**  Nombre del archivo*/
    $_titulo = FusNuloPOST('_titulo', "");
    /**  titulo del reporte*/
    $_SaltoPag = FusNuloPOST('_SaltoPag', "0");
    /** salto de pagina por legajo */

    $_titulo = $_titulo == '' ? 'HORARIOS ASIGNADOS' : $_titulo;
    $_nombre = $_nombre == '' ? strtoupper($_titulo) : $_nombre;
    // h1($_nombre); exit;
    $title = ($_nombre == 'HorAsign') ? strtoupper($_titulo) : $_nombre;

    $_path = $_SERVER['DOCUMENT_ROOT'] . '/' . HOMEHOST . '/informes/horasign/archivos/';
    $fecha = new DateTime();
    $MicroTime = microtime(true);
    $NombreArchivo = $_path . $_nombre . "_" . $MicroTime . ".pdf";
    $NombreArchivo = str_replace(' ', '_', $NombreArchivo);
    $NombreArchivo2 = $_nombre . "_" . $MicroTime . ".pdf";
    $NombreArchivo2 = str_replace(' ', '_', $NombreArchivo2);

    $DateRange = explode(' al ', $_POST['_drhorarios']);
    $FechaIni = test_input(dr_fecha($DateRange[0]));
    $FechaFin = test_input(dr_fecha($DateRange[1]));

    $fecha1 = new DateTime($FechaIni);
    $fecha2 = new DateTime($FechaFin);
    $diff = $fecha1->diff($fecha2);
    // echo $diff->days . ' dias'; exit;

    if ($diff->days > '31') {
        $data = array('status' => 'error', 'Mensaje' => 'Error: Rango de fechas supierior a 31 d&iacute;as');
        echo json_encode($data);
        exit;
    }
    try {

        borrarLogs($_path, 1, '.pdf');
        // ini_set("pcre.backtrack_limit", "5000000");
        ob_start();
        require_once 'toPdf.php';
        $buffer = ob_get_clean();
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
            'orientation' => $_orientation
        ]);
        $mpdf->SetDisplayMode('fullpage');
        $mpdf->shrink_tables_to_fit = 1;
        $mpdf->SetAuthor('Control Horario WEB - ' . $_SESSION['NOMBRE_SESION']);
        $mpdf->SetTitle($title);
        $mpdf->SetSubject($_titulo);
        $mpdf->SetCreator('Control Horario WEB');
        $mpdf->SetCompression(true);
        $mpdf->simpleTables = true;
        $mpdf->useSubstitutions = false;
        $_watermark = FusNuloPOST("_watermark", 'Horarios Asignados');
        /** Marca de Agua */
        // if (($_watermark)) {
        //     $mpdf->SetWatermarkText($_watermark);
        //     /** MARCA DE AGUA */
        //     $mpdf->showWatermarkText = true;
        //     $mpdf->watermark_font = 'Arial';
        //     $mpdf->watermarkTextAlpha = 0.1;
        // }

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

        $mpdf->list_indent_first_level = 1; // 1 or 0 - whether to indent the first level of a list

        // Load a stylesheet
        $stylesheet = file_get_contents('../../../css/stylepdf/mpdfstyletables.css');
        $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);

        //$mpdf->WriteHTML($stylesheet, 1); // The parameter 1 tells that this is css/style only and no body/html/text
        $chunks = explode("chunk", $buffer);
        foreach ($chunks as $key => $val) {
            $mpdf->WriteHTML($buffer, \Mpdf\HTMLParserMode::HTML_BODY);
        }


        // $mpdf->WriteHTML($buffer, 2);

        ob_end_clean();

        $mpdf->Output($NombreArchivo, \Mpdf\Output\Destination::FILE);

        $data = array('status' => 'ok', 'Mensaje' => 'Reporte Creado', 'archivo' => 'archivos/' . $NombreArchivo2, 'destino' => $_destino, 'x' => getmypid());
        echo json_encode($data);
        exit();
    } catch (\Mpdf\MpdfException $e) {
        echo $e->getMessage();
        // echo $formatter->getHtmlMessage();
        file_put_contents("../../../logs/error/Error_HorAsign_" . date('Ymd') . ".log", $e->getMessage(), FILE_APPEND | LOCK_EX);
        exit();
    }
}

<?php
ini_set('max_execution_time', 600); //180 seconds = 3 minutes
session_start();
header('Content-type: text/html; charset=utf-8');
// header("Content-Type: application/json");
header("Cache-Control: public");
header("Content-Type: application/pdf");
header("Content-Transfer-Encoding: binary");
require __DIR__ . '/../../../config/index.php';
ultimoacc();
secure_auth_ch();
$Modulo = '23';
ExisteModRol($Modulo);
E_ALL();

require_once __DIR__ . '/../../../vendor/autoload.php';
if (($_SERVER["REQUEST_METHOD"] == "POST")) {

    $_format = FusNuloPOST('_format', 'A4'); /** Tipo de Hoja */
    $_orientation = FusNuloPOST('_orientation', 'L'); /** L: Horizontal; P: Vertical */
    $_destino = FusNuloPOST('_destino', 'F'); /** I: Muestra PDF en pantalla; D: Descarga el archivo; F: Descarga el Archivo; V: Abre otra Pestaña */
    $_password = FusNuloPOST('_password', ''); /** Password de apertura del archivo */
    $_print = FusNuloPOST('_print', ''); /**  Bloquea la impresion del archivo */
    $_modify = FusNuloPOST('_modify', ''); /**  Bloquea la modificacion archivo */
    $_copy = FusNuloPOST('_copy', ''); /**  Bloquea la copiar datos del archivo */
    $_SaltoPag = FusNuloPOST('_SaltoPag', "0"); /** salto de pagina por legajo */
    $_annotforms = FusNuloPOST('_annotforms', ''); /**  Bloquea la Anotaciones del archivo 'annot-forms'*/
    $_nombre = FusNuloPOST('_nombre', "InforFic"); /**  Nombre del archivo*/
    $_titulo = FusNuloPOST('_titulo', ""); /**  titulo del reporte*/
    $_Por = FusNuloPOST("_Por", 'Leg'); /** Agrupar por: Fech, Leg, ApNo */

    $_titulo = $_titulo == '' ? 'INFORME DE FICHADAS' : $_titulo;
    $_nombre = $_nombre == '' ? strtoupper($_titulo) : $_nombre;
    // h1($_nombre); exit;
    $title = ($_nombre == 'InforFic') ? strtoupper($_titulo) : $_nombre;

    $_path = $_SERVER['DOCUMENT_ROOT'] . '/' . HOMEHOST . '/informes/inforfic/reporte/archivos/';
    $fecha = new DateTime();
    $MicroTime = microtime(true);
    $NombreArchivo = $_path . $_nombre . "_" . $MicroTime . ".pdf";
    $NombreArchivo2 = $_nombre . "_" . $MicroTime . ".pdf";

    if (
        test_input($_POST['FicFalta'] == '1'
            && test_input($_POST['_Por'] == 'ApNo')
        )) {
        $_titulo = 'INFORME DE FICHADAS POR NOMBRE (SOLO INCONSISTENTES)';
    } elseif (
        test_input($_POST['FicFalta'] == '1'
            && test_input($_POST['_Por'] == 'Leg')
        )) {
        $_titulo = 'INFORME DE FICHADAS POR LEGAJO (SOLO INCONSISTENTES)';
    } elseif (
        test_input($_POST['FicFalta'] == '1'
            && test_input($_POST['_Por'] == 'Fech')
        )) {
        $_titulo = 'INFORME DE FICHADAS POR FECHA (SOLO INCONSISTENTES)';
    } elseif (
        test_input($_POST['FicFalta'] == '0'
            && test_input($_POST['_Por'] == 'ApNo')
        )) {
        $_titulo = 'INFORME DE FICHADAS POR NOMBRE';
    } elseif (
        test_input($_POST['FicFalta'] == '0'
            && test_input($_POST['_Por'] == 'Leg')
        )) {
        $_titulo = 'INFORME DE FICHADAS POR LEGAJO';
    } elseif (
        test_input($_POST['FicFalta'] == '0'
            && test_input($_POST['_Por'] == 'Fech')
        )) {
        $_titulo = 'INFORME DE FICHADAS POR FECHA';
    }

    try {
        BorrarArchivosPDF('archivos/*.pdf'); /** Borra los archivos anteriores a la fecha actual */
        ini_set("pcre.backtrack_limit", "5000000");
        ob_start();
        require_once 'InforFic.php';
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
        $mpdf->SetSubject('Informe de Fichadas');
        $mpdf->SetCreator('Control Horario WEB');
        $mpdf->SetCompression(true);
        $mpdf->simpleTables = true;
        $mpdf->useSubstitutions = false;

        $_watermark = FusNuloPOST("_watermark", ''); /** Marca de Agua */
        if (($_watermark)) {
            $mpdf->SetWatermarkText($_watermark); /** MARCA DE AGUA */
            $mpdf->showWatermarkText = true;
            $mpdf->watermark_font = 'Arial';
            $mpdf->watermarkTextAlpha = 0.1;
        }

        // $mpdf->SetProtection(array());
        // $permissions = array("$_copy", "$_print" , "$_modify", "$_annotforms", "", "", "", "");
        // print_r($permissions);
        // $mpdf->SetProtection($permissions, "$_password", '');
        // $mpdf->SetProtection(array(), 'UserPassword', 'MyPassword');

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

        $mpdf->WriteHTML($stylesheet, 1); // The parameter 1 tells that this is css/style only and no body/html/text

        $chunks = explode("chunk", $buffer);
        foreach ($chunks as $key => $val) {
            $mpdf->WriteHTML($val, 2);
        }
        ob_end_clean();

        $mpdf->Output($NombreArchivo, \Mpdf\Output\Destination::FILE);

        $data = array('status' => 'ok', 'dato' => 'Reporte Creado.' . $_nombre, 'archivo' => $NombreArchivo2, 'destino' => $_destino, 'x' => getmypid());
        echo json_encode($data);
        exit();
    } catch (\Mpdf\MpdfException $e) {
        // $e->getMessage();
        // echo $formatter->getHtmlMessage();
        file_put_contents("../../logs/error/Error_PDF_InforFic_" . date('Ymd') . ".log", $e->getMessage(), FILE_APPEND | LOCK_EX);
        exit();
    }
}
<?php
ini_set('max_execution_time', 600); //180 seconds = 3 minutes
session_start();
header('Content-type: text/html; charset=utf-8');
header("Content-Type: application/json");
require __DIR__ . '../../../../config/index.php';
ultimoacc();
secure_auth_ch();
$Modulo='21';
ExisteModRol($Modulo);
error_reporting(E_ALL);
ini_set('display_errors', '0');

require_once __DIR__ . '../../../../vendor/autoload.php'; 
if (($_SERVER["REQUEST_METHOD"] == "POST")) {

    $_format      = FusNuloPOST('_format', 'A4'); /** Tipo de Hoja */
    $_orientation = FusNuloPOST('_orientation', 'L'); /** L: Horizontal; P: Vertical */
    $_destino     = FusNuloPOST('_destino', 'F'); /** I: Muestra PDF en pantalla; D: Descarga el archivo; F: Descarga el Archivo; V: Abre otra Pestaña */
    $_password    = FusNuloPOST('_password', ''); /** Password de apertura del archivo */
    $_print       = FusNuloPOST('_print', ''); /**  Bloquea la impresion del archivo */
    $_modify      = FusNuloPOST('_modify', ''); /**  Bloquea la modificacion archivo */
    $_copy        = FusNuloPOST('_copy', ''); /**  Bloquea la copiar datos del archivo */
    $_annotforms  = FusNuloPOST('_annotforms', ''); /**  Bloquea la Anotaciones del archivo 'annot-forms'*/
    $_nombre      = FusNuloPOST('_nombre', "ParteDiario"); /**  Nombre del archivo*/
    $_titulo      = FusNuloPOST('_titulo', ""); /**  Titulo del reporte*/
    $_PerSN       = FusNuloPOST('_PerSN', ""); /**  Personal Sin Novedades*/
    
// exit;
    if (
    test_input($_POST['FicNovA']=='1' 
    && test_input($_POST['FicNovI']=='0')
    && test_input($_POST['FicNovS']=='0')
    && test_input($_POST['FicNovT']=='0')
    )) {
        $_titulo = 'PARTE DIARIO (AUSENCIAS)';
    }elseif(
        test_input($_POST['FicNovT']=='1' 
        && test_input($_POST['FicNovI']=='0')
        && test_input($_POST['FicNovS']=='0')
        && test_input($_POST['FicNovA']=='0')
        )) {
            $_titulo = 'PARTE DIARIO (LLEGADAS TARDES)';
        }elseif(
            test_input($_POST['FicNovI']=='1' 
            && test_input($_POST['FicNovT']=='0')
            && test_input($_POST['FicNovS']=='0')
            && test_input($_POST['FicNovA']=='0')
            )) {
                $_titulo = 'PARTE DIARIO (INCUMPLIMIENTOS)';
            }elseif(
                test_input($_POST['FicNovS']=='1' 
                && test_input($_POST['FicNovT']=='0')
                && test_input($_POST['FicNovI']=='0')
                && test_input($_POST['FicNovA']=='0')
                )) {
                    $_titulo = 'PARTE DIARIO (SALIDAS ANTICIPADAS)';
                };

    $_titulo = $_titulo =='' ? 'PARTE DIARIO': $_titulo;
    $_nombre = $_nombre =='' ? strtoupper($_titulo): $_nombre;
    // h1($_nombre); exit;
    $title = ($_nombre == 'ParteDiario') ? strtoupper($_titulo): $_nombre;

    $_path = $_SERVER['DOCUMENT_ROOT'].'/'.HOMEHOST.'/informes/partedia/reporte/archivos/';
    $fecha = new DateTime();
    $MicroTime=microtime(true);
    $NombreArchivo=$_path.$_nombre."_".$MicroTime.".pdf";
    $NombreArchivo2=$_nombre."_".$MicroTime.".pdf";

    $FechaIni  = test_input(dr_fecha($_POST['_dr']));
    // try {
    //     ob_start();
    //     require_once  'ParteDiario.php';
    //     $buffer = ob_get_clean();
    //     $html2pdf = new Html2Pdf($_orientation, $_format , 'es', 'true', 'UTF-8', array(5, 5, 5, 5));
    //     $html2pdf->setTestIsImage(true);
    //     $html2pdf->setTestTdInOnePage(true);
    //     $html2pdf->AddFont('arial', 'normal', 'arial.php');
    //     $html2pdf->setDefaultFont('arial');
    //     // echo ($buffer);
    //     $html2pdf->pdf->SetDisplayMode('fullpage');
    //     $html2pdf->pdf->SetTitle($title);
    //     $html2pdf->pdf->SetSubject('Parte Diario');
    //     $html2pdf->pdf->SetAuthor('Control Horario WEB - '.$_SESSION['NOMBRE_SESION']);
    //     $html2pdf->pdf->SetCreator('Control Horario WEB');
    //     // $html2pdf->pdf->SetProtection('', $_password, '', '', '');
    //     $permissions = array($_print , $_modify, $_copy, $_annotforms, '', '', '', '');
    //     $html2pdf->pdf->SetProtection($permissions, $_password, '', '', '');
    //     $html2pdf->writeHTML($buffer);
    //     // $html2pdf->previewHTML($buffer);
    //     ob_end_clean();
    //     // $debug = new \Spipu\Html2Pdf\Debug\Debug();
    //     // $html2pdf->setModeDebug($debug);
    //     $html2pdf->output($NombreArchivo, 'F'); 
    //     $data = array('status' => 'ok', 'dato' => 'Reporte Creado.'.$_nombre, 'archivo'=> $NombreArchivo2, 'destino'=> $_destino, 'x'=>getmypid());
    //     echo json_encode($data);
    //     exit();
    // } catch (Html2PdfException $e) {
    //     $html2pdf->clean();
    //     $formatter = new ExceptionFormatter($e);
    //     EscribirArchivo("Error_PDF_ParteDia_".date('Ymd'), "../../../logs/error/", $formatter->getMessage(), false, false, false);
    //     exit();
    // }
    try {
        BorrarArchivosPDF('archivos/*.pdf'); /** Borra los archivos anteriores a la fecha actual */
        ini_set("pcre.backtrack_limit", "5000000");

        ob_start();
        require_once  'ParteDiario.php';
        $buffer = ob_get_clean();
        $mpdf = new \Mpdf\Mpdf([
        'mode' => 'c',
        'format' => $_format,
        'default_font_size'=> '10pt',
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
        $mpdf->SetAuthor('Control Horario WEB - '.$_SESSION['NOMBRE_SESION']);
        $mpdf->SetTitle($title);
        $mpdf->SetSubject('Parte Diario');
        $mpdf->SetCreator('Control Horario WEB');
        $mpdf->SetCompression(true);
        $mpdf->simpleTables = true;
        $mpdf->useSubstitutions = false;

        $_watermark = FusNuloPOST("_watermark",''); /** Marca de Agua */
        if(($_watermark)){
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
                        <p class="left bold">'.strtoupper($_titulo) .'</p>
                    </td>
                    <td style="width: 35%;" align="right">
                        <p class="right">'.strtoupper(DiaSemana($FechaIni)).'</p>
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
                    <td width="33%" style="text-align: right;">'.strtoupper($_titulo) .'</td>
                </tr>
            </table>');

        $mpdf->list_indent_first_level = 1; // 1 or 0 - whether to indent the first level of a list

        // Load a stylesheet
        $stylesheet = file_get_contents('../../../css/stylepdf/mpdfstyletables.css');

        $mpdf->WriteHTML($stylesheet, 1); // The parameter 1 tells that this is css/style only and no body/html/text
        
        $chunks = explode("chunk", $buffer);
        foreach($chunks as $key => $val) {
            $mpdf->WriteHTML($val,2);
        }
        ob_end_clean();

        $mpdf->Output($NombreArchivo, \Mpdf\Output\Destination::FILE);

        $data = array('status' => 'ok', 'dato' => 'Reporte Creado.'.$_nombre, 'archivo'=> $NombreArchivo2, 'destino'=> $_destino, 'x'=>getmypid());
        echo json_encode($data);
        exit();
    } catch (\Mpdf\MpdfException $e) {
        echo $e->getMessage();
        // echo $formatter->getHtmlMessage();
        EscribirArchivo("Error_PDF_InforFic_".date('Ymd'), "../../../logs/error/", $e->getMessage(), false, false, false);
        exit();
    }
}
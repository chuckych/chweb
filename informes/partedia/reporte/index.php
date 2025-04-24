<?php
ini_set('max_execution_time', 600); //180 seconds = 3 minutes
session_start();
header('Content-type: text/html; charset=utf-8');
header("Content-Type: application/json");
require __DIR__ . '/../../../config/index.php';
ultimoacc();
secure_auth_ch();
$Modulo = '21';
ExisteModRol($Modulo);
E_ALL();

require_once __DIR__ . '/../../../vendor/autoload.php';

try {
    if (($_SERVER["REQUEST_METHOD"] == "POST")) {

        $_format = FusNuloPOST('_format', 'A4');
        $_orientation = FusNuloPOST('_orientation', 'L');
        $_destino = FusNuloPOST('_destino', 'F');
        $_password = FusNuloPOST('_password', '');
        $_print = FusNuloPOST('_print', '');
        $_modify = FusNuloPOST('_modify', '');
        $_copy = FusNuloPOST('_copy', '');
        $_annotforms = FusNuloPOST('_annotforms', '');
        $_nombre = FusNuloPOST('_nombre', "ParteDiario");
        $_titulo = FusNuloPOST('_titulo', "");
        $_PerSN = FusNuloPOST('_PerSN', "");

        if (
            test_input(
                $_POST['FicNovA'] == '1'
                && test_input($_POST['FicNovI'] == '0')
                && test_input($_POST['FicNovS'] == '0')
                && test_input($_POST['FicNovT'] == '0')
            )
        ) {
            $_titulo = 'PARTE DIARIO (AUSENCIAS)';
        } elseif (
            test_input(
                $_POST['FicNovT'] == '1'
                && test_input($_POST['FicNovI'] == '0')
                && test_input($_POST['FicNovS'] == '0')
                && test_input($_POST['FicNovA'] == '0')
            )
        ) {
            $_titulo = 'PARTE DIARIO (LLEGADAS TARDES)';
        } elseif (
            test_input(
                $_POST['FicNovI'] == '1'
                && test_input($_POST['FicNovT'] == '0')
                && test_input($_POST['FicNovS'] == '0')
                && test_input($_POST['FicNovA'] == '0')
            )
        ) {
            $_titulo = 'PARTE DIARIO (INCUMPLIMIENTOS)';
        } else if (
            test_input(
                $_POST['FicNovS'] == '1'
                && test_input($_POST['FicNovT'] == '0')
                && test_input($_POST['FicNovI'] == '0')
                && test_input($_POST['FicNovA'] == '0')
            )
        ) {
            $_titulo = 'PARTE DIARIO (SALIDAS ANTICIPADAS)';
        }
        ;

        $_titulo = $_titulo == '' ? 'PARTE DIARIO' : $_titulo;
        $_nombre = $_nombre == '' ? strtoupper($_titulo) : $_nombre;
        $title = ($_nombre == 'ParteDiario') ? strtoupper($_titulo) : $_nombre;

        $_path = $_SERVER['DOCUMENT_ROOT'] . '/' . HOMEHOST . '/informes/partedia/reporte/archivos/';
        $fecha = new DateTime();
        $MicroTime = microtime(true);
        $NombreArchivo = $_path . $_nombre . "_" . $MicroTime . ".pdf";
        $NombreArchivo2 = $_nombre . "_" . $MicroTime . ".pdf";

        $FechaIni = test_input(dr_fecha($_POST['_dr']));

        BorrarArchivosPDF('archivos/*.pdf');
        ini_set("pcre.backtrack_limit", "5000000");

        ob_start();
        require_once 'ParteDiario.php';

        $buffer = ob_get_clean();

        // Verificar si el buffer contiene datos válidos
        if (empty($buffer)) {
            throw new Exception("El buffer está vacío. Verifica el contenido de 'ParteDiario.php'.");
        }

        // Imprimir el contenido del buffer para depuración
        // echo "<pre>";
        // print_r($buffer);
        // echo "</pre>";
        // exit();

        // Manejo de excepciones alrededor de la creación de la instancia de \Mpdf\Mpdf
        try {
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
        } catch (\Mpdf\MpdfException $e) {
            throw new Exception("Error al crear la instancia de Mpdf: " . $e->getMessage());
        }

        $mpdf->SetDisplayMode('fullpage');
        $mpdf->shrink_tables_to_fit = 1;
        $mpdf->SetAuthor('Control Horario WEB - ' . $_SESSION['NOMBRE_SESION']);
        $mpdf->SetTitle($title);
        $mpdf->SetSubject('Parte Diario');
        $mpdf->SetCreator('Control Horario WEB');
        $mpdf->SetCompression(true);
        $mpdf->simpleTables = true;
        $mpdf->useSubstitutions = false;

        $_watermark = $_POST['_watermark'] ?? '';
        if ($_watermark) {
            $mpdf->SetWatermarkText($_watermark);
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
                        <p class="right">' . strtoupper(DiaSemana($FechaIni)) . '</p>
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

        $mpdf->list_indent_first_level = 1;

        $pathCss = '../../../css/stylepdf/mpdfstyletables.css';
        if (file_exists($pathCss)) {
            $stylesheet = file_get_contents($pathCss);
        }

        $mpdf->WriteHTML($stylesheet, 1);
        $chunks = explode("chunk", $buffer);

        foreach ($chunks as $key => $val) {
            $mpdf->WriteHTML($val, 2);
        }
        ob_end_clean();

        $mpdf->Output($NombreArchivo, \Mpdf\Output\Destination::FILE);

        $data = array('status' => 'ok', 'dato' => 'Reporte Creado.' . $_nombre, 'archivo' => $NombreArchivo2, 'destino' => $_destino, 'x' => getmypid());
        echo json_encode($data);
        exit();
    }
} catch (\Mpdf\MpdfException $e) {
    echo $e->getMessage();
    file_put_contents("Error_PDF_InforFic_" . date('Ymd') . ".log", $e->getMessage(), FILE_APPEND | LOCK_EX);
    exit();
} catch (Exception $e) {
    echo $e->getMessage();
    file_put_contents("Error_PDF_InforFic_" . date('Ymd') . ".log", $e->getMessage(), FILE_APPEND | LOCK_EX);
    exit();
}

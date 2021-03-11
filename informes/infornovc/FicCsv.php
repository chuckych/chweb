<?php
ini_set('max_execution_time', 600); //180 seconds = 3 minutes
session_start();
require __DIR__ . '../../../config/index.php';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Cache-Control: max-age=0');
$datehis = date('YmdHis');
// header('Content-Disposition: attachment;filename="Reporte_Fichadas_'.$datehis.'.Csv"');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0
header("Content-Type: application/json");

ultimoacc();
secure_auth_ch();

$Modulo = '29';
ExisteModRol($Modulo);

error_reporting(E_ALL);
ini_set('display_errors', '0');

require_once __DIR__ . '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

$documento = new Spreadsheet();
# Como ya hay una hoja por defecto, la obtenemos, no la creamos
$spreadsheet = $documento->getActiveSheet();

$data = ($_POST['datos']);
$data = json_decode(($data), true);
# Escribir encabezado de los productos
$encabezado = [
    "Legajo",
    "Nombre",
    "Fecha Desde",
    "Fecha Hasta",
    "Total Presentes",
    "Total Ausentes",
    "Total Días",
    "Meses Presentes",
    "Meses Ausentes",
    "Total Meses",
];
$numeroDeFila = 2;
$spreadsheet->fromArray($encabezado, null, 'A1');
// print_r($data['data']); exit;
foreach ($data['data'] as $r) {

    $legajo          = $r['legajo'];
    $nombre          = $r['nombre'];
    $desde           = $r['desde'];
    $hasta           = $r['hasta'];
    $_presentes      = $r['_presentes'];
    $_ausentes       = $r['_ausentes'];
    $_totaldias      = $r['_totaldias'];
    $_convpres       = $r['_convpres'];
    $_convaus        = $r['_convaus'];
    $_convpres       = str_replace(",", ".", $_convpres);
    $_convaus        = str_replace(",", ".", $_convaus);
    $_totalmesesconv = $r['_totalmesesconv'];

    # Escribirlos en el documento
    $spreadsheet->setCellValueByColumnAndRow(1, $numeroDeFila, $legajo);
    $spreadsheet->setCellValueByColumnAndRow(2, $numeroDeFila, $nombre);
    $spreadsheet->setCellValueByColumnAndRow(3, $numeroDeFila, $desde);
    $spreadsheet->setCellValueByColumnAndRow(4, $numeroDeFila, $hasta);
    $spreadsheet->setCellValueByColumnAndRow(5, $numeroDeFila, $_presentes);
    $spreadsheet->setCellValueByColumnAndRow(6, $numeroDeFila, $_ausentes);
    $spreadsheet->setCellValueByColumnAndRow(7, $numeroDeFila, $_totaldias);
    $spreadsheet->setCellValueByColumnAndRow(8, $numeroDeFila, $_convpres);
    $spreadsheet->setCellValueByColumnAndRow(9, $numeroDeFila, $_convaus);
    $spreadsheet->setCellValueByColumnAndRow(10, $numeroDeFila, $_totalmesesconv);
    $numeroDeFila++;
}

# Crear un "escritor"
try {
    BorrarArchivosPDF('archivos/*.csv');
    /** Borra los archivos anteriores a la fecha actual */
    $NombreArchivo = "Reporte_Presentismo_" . time() . ".csv";

    $writer = new Csv($documento);
    # Le pasamos la ruta de guardado
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($documento, 'Csv');
    $writer->save('archivos/' . $NombreArchivo);
    // $writer->save('php://output');

    $data = array('status' => 'ok', 'archivo' => 'archivos/' . $NombreArchivo);
    echo json_encode($data);
    exit;
} catch (\Exception $e) {
    $data = array('status' => 'error');
    echo json_encode($data);
    exit;
}

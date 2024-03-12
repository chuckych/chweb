<?php
ini_set('max_execution_time', 600); //180 seconds = 3 minutes
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');
// If you're serving to IE over SSL, then the following may be needed
header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header('Pragma: public'); // HTTP/1.0
header("Content-Type: application/json");

ultimoacc();
secure_auth_ch_json();
$Modulo = '45';
ExisteModRol($Modulo);
E_ALL();

require_once __DIR__ . '../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xls;


$documento = new Spreadsheet();
$documento
    ->getProperties()
    ->setCreator("CHWEB")
    ->setLastModifiedBy('CHWEB')
    ->setTitle('Archivo exportado desde CHWEB')
    ->setDescription('Reporte desde CHWEB');

# Como ya hay una hoja por defecto, la obtenemos, no la creamos
$spreadsheet = $documento->getActiveSheet();
$spreadsheet->setTitle("TOTALES");

// Flight::json($data);
// exit;
$payload = $data['payloadHoras'];

$FechaIni = $payload['FechIni'];
$FechaFin = $payload['FechFin'];
$detalle = $data['legajos'] ?? [];
$tiposDeHoras = $data['tiposDeHs'] ?? [];
$novedades = $data['novedades'] ?? [];
# Escribir encabezado de los productos
$encabezado = [
    "Legajo",
    "Nombre",
];

if ($tiposDeHoras) {
    foreach ($tiposDeHoras as $tipo) {
        $encabezado[] = $tipo['THoDesc2'];
    }
}
if ($novedades) {
    foreach ($novedades as $nov) {
        $encabezado[] = $nov['NovDesc'];
    }
}
$last_key = count($encabezado) - 1;
function numberToLetter($num)
{
    $numeric = $num % 26;
    $letter = chr(65 + $numeric);
    $num2 = intval($num / 26);
    if ($num2 > 0) {
        return numberToLetter($num2 - 1) . $letter;
    } else {
        return $letter;
    }
}

$ultimaLetra = numberToLetter($last_key);

include 'estilosXls.php';

$numeroDeFila = 2;

// Flight::json($data);
// exit;
foreach ($detalle as $key => $r) {

    $objeto = $r;

    // foreach ($objeto as $k => $row) {

    // Flight::json($objeto['LegApNo']);
    // exit;

    $Lega = $objeto['Lega'];
    $LegApNo = $objeto['LegApNo'];
    $TotalesHoras = $objeto['TotalesHoras'] ?? [];

    // # Escribirlos en el documento
    $spreadsheet->setCellValueByColumnAndRow(1, $numeroDeFila, $Lega);
    $spreadsheet->setCellValueByColumnAndRow(2, $numeroDeFila, $LegApNo);
    // $spreadsheet->setCellValueByColumnAndRow(3, $numeroDeFila, $TotalesHoras);
    // get the name of the column 3
    $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(3);
    // get the value of the cell at column 3 and row $numeroDeFila
    $cellValue = $spreadsheet->getCell($columnLetter . '1')->getValue();

    // if ($TotalesHoras) {
    //     foreach ($TotalesHoras as $keyTh => $th) {
    //         if ($th['THoDesc2']) {
    //             $dato = filtrarElementoArray($TotalesHoras, $cellValue, $th['THoDesc2']);
    //             $spreadsheet->setCellValueByColumnAndRow($TotalesHoras + 3, $numeroDeFila, $th);
    //             Flight::json($dato);
    //             exit;
    //         }
    //     }
    // } else {
    //     $spreadsheet->setCellValueByColumnAndRow(3, $numeroDeFila, '00:00');
    // }
    $ultimaColumna = 3;
    if ($tiposDeHoras) {
        foreach ($tiposDeHoras as $keyTh => $valueTh) {
            $siguienteColumna = $ultimaColumna++;
            $valorDeLaCelda = ($TotalesHoras) ? filtrarElementoArray($TotalesHoras, 'HoraCodi', $valueTh['HoraCodi']) : '00:00';
            $valorDeLaCelda = ($valorDeLaCelda != '00:00') ? ($valorDeLaCelda[0]['EnHoras2']) : '00:00';
            // Flight::json($TotalesHoras);
            // exit;
            $spreadsheet->setCellValueByColumnAndRow($siguienteColumna, $numeroDeFila, $valorDeLaCelda);
            unset($valorDeLaCelda);
        }
    } else {
        foreach ($tiposDeHoras as $keyTh => $valueTh) {
            $siguienteColumna = $ultimaColumna++;
            $spreadsheet->setCellValueByColumnAndRow($siguienteColumna, $numeroDeFila, '00:00');
        }
    }

    $numeroDeFila++;
    // }
}

// Flight::json($rows);
// exit;

# Crear un "escritor"
try {
    BorrarArchivosPDF('archivos/*.xls'); /** Borra los archivos anteriores a la fecha actual */
    $MicroTime = microtime(true);
    $NombreArchivo = "Reporte_Totales_" . $MicroTime . ".xls";

    $writer = new Xls($documento);
    # Le pasamos la ruta de guardado
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($documento, 'Xls');
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
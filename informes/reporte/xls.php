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
$payload = $data['payloadHoras'] ?? [];
$Formato = $payload['Formato'] ?? '';
$VPor = $payload['VPor'] ?? '';

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
$countEncabezados = count($encabezado);
$TituloReporte = 'Reporte de Totales';

if ($VPor == 'todo' || $VPor == 'horas') {
    if ($tiposDeHoras) {
        foreach ($tiposDeHoras as $key => $tipo) {
            $encabezado[] = $tipo['THoDesc2'];
        }
    }
    $TituloReporte = 'Reporte de Totales de Horas';

    foreach ($encabezado as $key => $value) {
        if ($key >= $countEncabezados) {
            $letraCol = numberToLetter($key);
            switch ($Formato) {
                case 'enHoras':
                    $spreadsheet->getStyle($letraCol)->getNumberFormat()->setFormatCode('[h]:mm');
                    break;
                case 'enDecimal':
                    $spreadsheet->getStyle($letraCol)->getNumberFormat()->setFormatCode('0.00');
                    break;
            }
        }
    }
    $countEncabezados = count($encabezado);
}

if ($VPor == 'todo' || $VPor == 'novedades') {
    if ($novedades) {
        foreach ($novedades as $nov) {
            $encabezado[] = $nov['NovDesc'];
        }
    }
    $TituloReporte = 'Reporte de Totales de Novedades';

    foreach ($encabezado as $key => $value) {
        if ($key >= $countEncabezados) {
            $letraCol = numberToLetter($key);
            switch ($Formato) {
                case 'enHoras':
                    $spreadsheet->getStyle($letraCol)->getNumberFormat()->setFormatCode('[h]:mm');
                    break;
                case 'enDecimal':
                    $spreadsheet->getStyle($letraCol)->getNumberFormat()->setFormatCode('0.00');
                    break;
            }
        }
    }
    $countEncabezados = count($encabezado);
}
if ($VPor == 'todo') {
    $TituloReporte = 'Reporte de Totales de Horas y Novedades';
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

// Flight::json($payload) . exit;
try {
    foreach ($detalle as $key => $r) {

        $objeto = $r;

        $Lega = $objeto['Lega'];
        $LegApNo = $objeto['LegApNo'];
        $TotalesHoras = $objeto['TotalesHoras'] ?? [];
        $TotalesNovedades = $objeto['TotalesNovedades'] ?? [];

        // # Escribirlos en el documento
        $spreadsheet->setCellValueByColumnAndRow(1, $numeroDeFila, $Lega);
        $spreadsheet->setCellValueByColumnAndRow(2, $numeroDeFila, $LegApNo);

        $ultimaColumna = 3;

        if ($VPor == 'todo' || $VPor == 'horas') {
            foreach ($tiposDeHoras as $tipoHora) {
                $existe = false;
                foreach ($TotalesHoras as $total) {
                    if ($total['HoraCodi'] === $tipoHora['HoraCodi']) {
                        $existe = true;
                        break;
                    }
                }
                if (!$existe) {
                    $TotalesHoras[] = [
                        "HoraCodi" => $tipoHora['HoraCodi'],
                        "THoDesc" => $tipoHora['THoDesc'],
                        "THoDesc2" => $tipoHora['THoDesc2'],
                        "Cantidad" => 0,
                        "EnHoras" => "00:00",
                        "EnHoras1" => "00:00",
                        "EnHoras2" => "",
                        "EnMinutos" => 0,
                        "EnMinutos1" => 0,
                        "EnMinutos2" => 0,
                        "EnHorasDecimal" => 0,
                        "EnHorasDecimal1" => 0,
                        "EnHorasDecimal2" => ''
                    ];
                }
            }
            usort($TotalesHoras, function ($a, $b) {
                return $a['HoraCodi'] <=> $b['HoraCodi'];
            });

            if ($TotalesHoras) {
                foreach ($TotalesHoras as $keyTh => $valueTh) {
                    $siguienteColumna = $ultimaColumna++;
                    switch ($Formato) {
                        case 'enHoras':
                            $valorEnHoras = $valueTh['EnHoras2'] ? HorasToExcelMas24($valueTh['EnHoras2']) : '';
                            $spreadsheet->setCellValueByColumnAndRow($siguienteColumna, $numeroDeFila, $valorEnHoras);
                            break;
                        case 'enDecimal':
                            $valorDecimal = $valueTh['EnHorasDecimal2'] ? round($valueTh['EnHorasDecimal2'], 2) : '';
                            $spreadsheet->setCellValueByColumnAndRow($siguienteColumna, $numeroDeFila, $valorDecimal);
                            break;
                    }
                }
            }
        }

        if ($VPor == 'todo' || $VPor == 'novedades') {
            foreach ($novedades as $novedad) {
                $existe = false;
                foreach ($TotalesNovedades as $totalNov) {
                    if ($totalNov['NovCodi'] === $novedad['NovCodi']) {
                        $existe = true;
                        break;
                    }
                }
                if (!$existe) {
                    $TotalesNovedades[] = [
                        "NovCodi" => $novedad['NovCodi'],
                        "NovDesc" => $novedad['NovDesc'],
                        "Cantidad" => 12,
                        "EnHoras" => "",
                        "EnMinutos" => 0,
                        "EnHorasDecimal" => ''
                    ];
                }
            }
            usort($TotalesNovedades, function ($a, $b) {
                return $a['NovCodi'] <=> $b['NovCodi'];
            });

            if ($TotalesNovedades) {
                foreach ($TotalesNovedades as $keyNov => $valueNov) {
                    $siguienteColumna = $ultimaColumna++;
                    switch ($Formato) {
                        case 'enHoras':
                            $valorEnHorasNov = $valueNov['EnHoras'] ? HorasToExcelMas24($valueNov['EnHoras']) : '';
                            $spreadsheet->setCellValueByColumnAndRow($siguienteColumna, $numeroDeFila, $valorEnHorasNov);
                            break;
                        case 'enDecimal':
                            $valorDecimal = $valueNov['EnHorasDecimal'] ? round($valueNov['EnHorasDecimal'], 2) : '';
                            $spreadsheet->setCellValueByColumnAndRow($siguienteColumna, $numeroDeFila, $valorDecimal);
                            break;
                    }
                }
            }
        }

        $numeroDeFila++;

    }
} catch (\Throwable $th) {
    $errores[] = $th->getMessage();
}
// Flight::json($valor) . exit;


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

    $data = array('status' => 'ok', 'archivo' => 'archivos/' . $NombreArchivo, 'errores' => $errores ?? []);
    echo json_encode($data);
    exit;

} catch (\Exception $e) {
    $data = array('status' => 'error');
    echo json_encode($data);
    exit;
}
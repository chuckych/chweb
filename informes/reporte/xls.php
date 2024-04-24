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
// use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Csv;


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


$payload = $data['payloadHoras'] ?? [];
$Formato = $payload['Formato'] ?? '';
$VPor = $payload['VPor'] ?? '';
$getEstructura = $payload['estructura'] ?? '';
$extension = $payload['extension'] ?? '';

$FechaIni = $payload['FechIni'];
$FechaFin = $payload['FechFin'];
$detalle = $data['legajos'] ?? [];
$tiposDeHoras = $data['tiposDeHs'] ?? [];
$novedades = $data['novedades'] ?? [];
$estructuras = $data['estructuras'] ?? [];
$cantidades = $payload['cantidades'] ?? '';
$totales = $payload['totales'] ?? '';

// Flight::json($detalle) . exit;
if (!$detalle) {
    $data = array('status' => 'error', 'mensaje' => 'No hay datos para exportar');
    echo json_encode($data);
    exit;
}
# Escribir encabezado de los productos
$encabezado = [
    "Legajo",
    "Nombre",
];
if ($getEstructura == '1') {

    $encabezadoEstructuras = [
        "Empresa",
        "Planta",
        "Convenio",
        "Sector",
        "Sección",
        "Grupo",
        "Sucursal"
    ];

    foreach ($encabezadoEstructuras as $key => $value) {
        $encabezado[] = $value;
    }
    $letras = ['C', 'D', 'E', 'F', 'G', 'H', 'I'];
    foreach ($letras as $key => $value) {
        $spreadsheet->getColumnDimension($value)->setAutoSize(true);
    }
}

$countEncabezados = count($encabezado);
$TituloReporte = 'Reporte de Totales';
$letrasColHoras = [];
$letrasColNovedades = [];
$letrasColCantidad = [];

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
            $spreadsheet->getColumnDimension($letraCol)->setAutoSize(true);
            switch ($Formato) {
                case 'enHoras':
                    $spreadsheet->getStyle($letraCol)->getNumberFormat()->setFormatCode('[h]:mm');
                    break;
                case 'enDecimal':
                    $spreadsheet->getStyle($letraCol)->getNumberFormat()->setFormatCode('0.00');
                    break;
            }
            $letrasColHoras[] = $letraCol;
        }
    }
    $countEncabezados = count($encabezado);
}

if ($VPor == 'todo' || $VPor == 'novedades') {
    if ($novedades) {
        foreach ($novedades as $nov) {
            $encabezado[] = $nov['NovDesc'];
            if ($cantidades == '1') {
                $encabezado[] = 'Cant';
            }
        }
    }
    $TituloReporte = 'Reporte de Totales de Novedades';

    foreach ($encabezado as $key => $value) {
        if ($key >= $countEncabezados) {
            $letraCol = numberToLetter($key);
            $spreadsheet->getColumnDimension($letraCol)->setAutoSize(true);
            switch ($Formato) {
                case 'enHoras':
                    $spreadsheet->getStyle($letraCol)->getNumberFormat()->setFormatCode('[h]:mm');
                    break;
                case 'enDecimal':
                    $spreadsheet->getStyle($letraCol)->getNumberFormat()->setFormatCode('0.00');
                    break;
            }
            if ($value == 'Cant') {
                $spreadsheet->getStyle($letraCol)->getNumberFormat()->setFormatCode('0');
                $letrasColCantidad[] = $letraCol;
            } else {
                $letrasColNovedades[] = $letraCol;
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

function filtrarEstructura($estructura, $campo, $valor)
{
    if (empty($estructura))
        return '';

    if (intval($valor) === 0)
        return '';

    try {
        $resultado = array_filter($estructura, function ($estructura) use ($campo, $valor) {
            return $estructura[$campo] == $valor;
        });
        $data = array_values($resultado);
        return $data[0] ?? '';
    } catch (\Throwable $th) {
        return '';
    }
}
function filtrarSeccion($estructura, $sector, $seccion)
{
    if (empty($estructura))
        return '';

    if (intval($sector) === 0)
        return '';

    if (intval($seccion) === 0)
        return '';

    try {
        foreach ($estructura as $key => $value) {
            if ($value['Se2Codi'] == $seccion && $value['SecCodi'] == $sector) {
                return $value['Se2Desc'];
            }
        }
    } catch (\Throwable $th) {
        return '';
    }
}

try {
    foreach ($detalle as $key => $r) {

        $objeto = $r;

        $Lega = $objeto['Lega'];
        $LegApNo = trim($objeto['LegApNo']);

        if ($getEstructura == '1') {
            $Empr = filtrarEstructura($estructuras['Empresas'] ?? '', 'EmpCodi', $objeto['Empr'])['EmpRazon'] ?? '';
            $Plan = filtrarEstructura($estructuras['Plantas'] ?? '', 'PlaCodi', $objeto['Plan'])['PlaDesc'] ?? '';
            $Conv = filtrarEstructura($estructuras['Convenios'] ?? '', 'ConCodi', $objeto['Conv'])['ConDesc'] ?? '';
            $Sect = filtrarEstructura($estructuras['Sectores'] ?? '', 'SecCodi', $objeto['Sect'])['SecDesc'] ?? '';
            $Secc = filtrarSeccion($estructuras['Secciones'] ?? '', $objeto['Sect'], $objeto['Secc']);
            $Grup = filtrarEstructura($estructuras['Grupos'] ?? '', 'GruCodi', $objeto['Grup'])['GruDesc'] ?? '';
            $Sucu = filtrarEstructura($estructuras['Sucursales'] ?? '', 'SucCodi', $objeto['Sucu'])['SucDesc'] ?? '';
        }

        $TotalesHoras = $objeto['TotalesHoras'] ?? [];
        $TotalesNovedades = $objeto['TotalesNovedades'] ?? [];

        // # Escribirlos en el documento
        $spreadsheet->setCellValueByColumnAndRow(1, $numeroDeFila, $Lega);
        $spreadsheet->setCellValueByColumnAndRow(2, $numeroDeFila, $LegApNo);

        $ultimaColumna = 3;

        if ($getEstructura == '1') {
            $spreadsheet->setCellValueByColumnAndRow(3, $numeroDeFila, $Empr);
            $spreadsheet->setCellValueByColumnAndRow(4, $numeroDeFila, $Plan);
            $spreadsheet->setCellValueByColumnAndRow(5, $numeroDeFila, $Conv);
            $spreadsheet->setCellValueByColumnAndRow(6, $numeroDeFila, $Sect);
            $spreadsheet->setCellValueByColumnAndRow(7, $numeroDeFila, $Secc);
            $spreadsheet->setCellValueByColumnAndRow(8, $numeroDeFila, $Grup);
            $spreadsheet->setCellValueByColumnAndRow(9, $numeroDeFila, $Sucu);
            $ultimaColumna = 10;
        }


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
                        "Cantidad" => "",
                        "EnHoras" => "",
                        "EnMinutos" => 0,
                        "EnHorasDecimal" => ''
                    ];
                }
            }
            usort($TotalesNovedades, function ($a, $b) {
                return $a['NovCodi'] <=> $b['NovCodi'];
            });
            // Flight::json($TotalesNovedades) . exit;
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
                    if ($cantidades == '1') {
                        $siguienteColumna = $ultimaColumna++;
                        $valorCantidad = $valueNov['Cantidad'];
                        $spreadsheet->setCellValueByColumnAndRow($siguienteColumna, $numeroDeFila, $valorCantidad);
                    }
                }
            }
        }
        $spreadsheet->getRowDimension($numeroDeFila)->setRowHeight(25);
        $ultimaFila = $numeroDeFila;
        $numeroDeFila++;
    }
    function subTotales($arrayLetrasCol, $fila, $spreadsheet)
    {
        if (!$arrayLetrasCol)
            return;
        foreach ($arrayLetrasCol as $value) {
            $UltimaFila = $fila - 1;
            $UltimaFila2 = $fila;
            $Ultima = $value . ($UltimaFila);
            $Ultima_2 = $value . ($UltimaFila2);
            $FormulaHechas = '=SUBTOTAL(9,' . $value . '2:' . $Ultima . ')';
            $spreadsheet->setCellValue($Ultima_2, $FormulaHechas);
            $spreadsheet->getStyle($Ultima_2)->getAlignment()->setIndent(1);
            $spreadsheet->getStyle($Ultima_2)->getFont()->setBold(true);
            $spreadsheet->getColumnDimension($value)->setAutoSize(true);
        }
    }
    if ($totales == '1') {
        subTotales($letrasColHoras, $numeroDeFila, $spreadsheet); // Horas
        subTotales($letrasColNovedades, $numeroDeFila, $spreadsheet); // Novedades
        subTotales($letrasColCantidad, $numeroDeFila, $spreadsheet); // Cantidad
        $spreadsheet->getStyle('A' . $numeroDeFila . ':' . $ultimaLetra . $numeroDeFila)->getBorders()->getTop()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN); // Set border top
        $spreadsheet->getRowDimension($numeroDeFila)->setRowHeight(30); // Set row height
    }

    $todasLasCeldas = 'A1:' . $ultimaLetra . $ultimaFila;
    $spreadsheet->getStyle($todasLasCeldas)->getAlignment()->setIndent(1); // Set indent
    $spreadsheet->getStyle('A1')->getAlignment()->setIndent(1); // Set indent

} catch (\Throwable $th) {
    $errores[] = $th->getMessage();
}
// Flight::json($valor) . exit;


# Crear un "escritor"
try {
    BorrarArchivosPDF('archivos/*.xls');
    /** Borra los archivos anteriores a la fecha actual */
    $MicroTime = microtime(true);
    $TituloReporte = str_replace(' ', '_', $TituloReporte) . '_' . date('YmdHis');

    if ($extension == 'csv') {
        $NombreArchivo = "$TituloReporte.csv";
        $writer = new Csv($documento);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($documento, 'Csv');
    } else {
        $NombreArchivo = "$TituloReporte.xls";
        $writer = new Xls($documento);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($documento, 'Xls');
    }

    $writer->save('archivos/' . $NombreArchivo);
    // $writer->save('php://output');

    $data = array(
        'status' => 'ok',
        'archivo' => 'archivos/' . $NombreArchivo,
        'errores' => $errores ?? []
    );
    echo json_encode($data);
    exit;
} catch (\Exception $e) {
    $data = array('status' => 'error', 'mensaje' => $e->getMessage());
    echo json_encode($data);
    exit;
}

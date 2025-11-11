<?php
function align_col($array, $spreadsheet) // Función para alinear las columnas
{
    foreach ($array as $value => $e) {
        $align = $e['align'] ?? 'HORIZONTAL_LEFT'; // EJ. HORIZONTAL_LEFT
        $columna = $e['col']; // eje:. A
        switch ($align) {
            case 'HORIZONTAL_LEFT':
                $alignment = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
                break;
            case 'HORIZONTAL_CENTER':
                $alignment = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER;
                break;
            case 'HORIZONTAL_RIGHT':
                $alignment = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT;
                break;
            default:
                $alignment = \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT;
        }
        $spreadsheet->getStyle($columna)->getAlignment()->setHorizontal($alignment);
        // error_log($align . ' - '.$alignment. PHP_EOL, 3, 'alignment.log');
    }
}
function width_col($array, $spreadsheet)
{
    foreach ($array as $key => $e) {
        $letra = $e['col'];
        $spreadsheet->getColumnDimension($letra)->setWidth($e['ancho']);
    }
}
function format_col($array, $spreadsheet)
{
    foreach ($array as $key => $v) {
        $format = $v['format']; // EJ. 0
        $columna = $v['col'];
        $spreadsheet->getStyle($columna)->getNumberFormat()->setFormatCode($format);
    }
}
function indent_rows($firstCol, $lastCol, $spreadsheet, $indent = 1)
{
    $cols = range($firstCol, $lastCol);
    foreach ($cols as $value) {
        $spreadsheet->getStyle($value)->getAlignment()->setIndent($indent);
    }
}
function hora_to_excel($Hora)
{
    $Hora = !empty($Hora) ? $Hora : '00:00:00';
    $timestamp = new \DateTime($Hora);
    $excelTimestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($timestamp);
    $excelDate = floor($excelTimestamp);
    $Hora = ($excelTimestamp - $excelDate) == 0 ? '' : $excelTimestamp - $excelDate;
    return $Hora;
}
function fecha_to_excel($Fecha)
{
    $timestamp = new \DateTime($Fecha);
    $excelTimestamp = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($timestamp);
    $excelDate = floor($excelTimestamp);
    $Fecha = ($excelTimestamp);
    return $Fecha;
}
function number_to_letter($num = 0)
{
    $numeric = $num % 26;
    $letter = chr(65 + $numeric);
    $num2 = intval($num / 26);
    if ($num2 > 0) {
        return number_to_letter($num2 - 1) . $letter;
    } else {
        return $letter;
    }
}
function min_excel($value)
{
    if (!is_numeric($value))
        return 0;
    $min = $value / 60;
    $min = ($min / 24);
    return $min;
}
function config_sheet($configSheet)
{
    $sheet = $configSheet['sheet'];
    $columnasExcel = $configSheet['columnasExcel'];
    $firstCol = 'A';
    $lastCol = end($columnasExcel)['col'];
    $styleArray = [
        'font' => [
            'bold' => true,
        ],
        'borders' => [
            'bottom' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_HAIR,
            ],
        ],
    ];
    $encabezados = $configSheet['encabezados'];
    $autoFilter = $configSheet['autoFilter'] ? true : false;
    $orientation = $configSheet['orientation'];
    $paperSize = $configSheet['paperSize'];
    $margin = $configSheet['margin'];
    $fitToWidth = $configSheet['fitToWidth'];
    $fitToHeight = $configSheet['fitToHeight'];
    $title = $configSheet['title'];
    $oddHeader = $configSheet['oddHeader'];
    $oddFooter = $configSheet['oddFooter'];
    $showGridlines = $configSheet['showGridlines'] ? true : false;
    $verticalAlignment = $configSheet['verticalAlignment'];
    $freezePane = $configSheet['freezePane'] + 1;
    $zoomScale = $configSheet['zoomScale'];
    $firstRowHeight = $configSheet['firstRowHeight'];
    $allRowHeight = $configSheet['allRowHeight'];
    $colorTab = $configSheet['colorTab'];
    $textoEncabezados = array_keys($columnasExcel);
    $indentRows = $configSheet['indentRows'];
    $cleanFiles = $configSheet['cleanFiles'];

    switch ($orientation) {
        case 'landscape':
            $orientationMatch = \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE;
            break;
        case 'portrait':
            $orientationMatch = \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_PORTRAIT;
            break;
        default:
            $orientationMatch = \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE;
    }

    switch ($paperSize) {
        case 'A4':
            $paperSizeMatch = \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4;
            break;
        case 'LETTER':
            $paperSizeMatch = \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_LETTER;
            break;
        default:
            $paperSizeMatch = \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4;
    }

    switch ($verticalAlignment) {
        case 'center':
            $verticalAlignmentMatch = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
            break;
        case 'top':
            $verticalAlignmentMatch = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP;
            break;
        case 'bottom':
            $verticalAlignmentMatch = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_BOTTOM;
            break;
        default:
            $verticalAlignmentMatch = \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER;
    }

    $sheet->setTitle($title);
    $encabezados ? $sheet->getStyle("{$firstCol}1:{$lastCol}1")->applyFromArray($styleArray) : '';
    $encabezados && $autoFilter ? $sheet->setAutoFilter("{$firstCol}1:{$lastCol}1") : ''; // Si los encabezados están activados y el filtro automático está activado
    $encabezados ? $sheet->fromArray($textoEncabezados, null, "{$firstCol}1") : ''; // Encabezados
    $sheet->getPageSetup()->setOrientation($orientationMatch);
    $sheet->getPageSetup()->setPaperSize($paperSizeMatch);
    $sheet->getPageMargins()->setTop($margin['top']);
    $sheet->getPageMargins()->setRight($margin['right']);
    $sheet->getPageMargins()->setLeft($margin['left']);
    $sheet->getPageMargins()->setBottom($margin['bottom']);
    $sheet->getPageSetup()->setFitToWidth($fitToWidth);
    $sheet->getPageSetup()->setFitToHeight($fitToHeight);
    $sheet->getHeaderFooter()->setOddHeader("&L&B{$oddHeader}");
    $sheet->getHeaderFooter()->setOddFooter("&L{$oddFooter}&RPágina &P de &N");
    $sheet->setShowGridlines($showGridlines);
    $sheet->getStyle("{$firstCol}1:{$lastCol}1")->getAlignment()->setVertical($verticalAlignmentMatch);
    $sheet->getStyle("{$firstCol}:{$lastCol}")->getAlignment()->setVertical($verticalAlignmentMatch);
    $encabezados ? $sheet->freezePane("{$firstCol}{$freezePane}") : ''; // Inmovilizar fila
    $sheet->getSheetView()->setZoomScale($zoomScale);
    $encabezados ? $sheet->getRowDimension('1')->setRowHeight($firstRowHeight) : '';
    $sheet->getDefaultRowDimension()->setRowHeight($allRowHeight);
    $sheet->getTabColor()->setRGB($colorTab);
    format_col($columnasExcel, $sheet);
    align_col($columnasExcel, $sheet);
    width_col($columnasExcel, $sheet);
    $indentRows ? indent_rows($firstCol, $lastCol, $sheet) : '';
    $cleanFiles['active'] ? clean_files($cleanFiles['path'], $cleanFiles['days'], $cleanFiles['pattern']) : '';
}
function value_to_cell($valor, $type, $columnasExcel = [], $fila = 1)
{
    switch ($type) {
        case 'time':
            $value = hora_to_excel($valor); // Formatea la hora
            break;
        case 'date':
            $value = $valor == '0000-00-00 00:00:00' ? '' : ($valor ? fecha_to_excel($valor) : '');
            break;
        case 'formula':
            $value = value_formula($valor, $columnasExcel, $fila);
            break;
        case 'minutes':
            $value = min_excel($valor);
            break;
        default:
            $value = $valor;
    }
    return $value;
}
function value_formula(string $valor, array $columnasExcel, int $fila): string
{
    // Use more efficient regex matching with preg_match instead of preg_match_all
    if (!preg_match_all('/@([^#]+)#/', $valor, $matches)) {
        return '';
    }

    // Use array_flip for more efficient lookup and unique filtering
    $uniqueKeys = array_unique($matches[1]);
    $result = [];

    foreach ($uniqueKeys as $key) {
        if (isset($columnasExcel[$key])) {
            $result[$key] = $columnasExcel[$key]['col'];
        }
    }

    // Replace row placeholder and column references in a single pass
    $processedValor = str_replace('#FILA', $fila, $valor);
    foreach ($result as $key => $value) {
        $processedValor = str_replace("@{$key}", $value, $processedValor);
    }
    return $processedValor;
}
function add_letter_column_to_array($array)
{
    $counter = 0;
    foreach ($array as $key => $value) {
        // añadir la clave counter a cada valor de $columnasExcel
        $array[$key]['col'] = number_to_letter($counter);
        $counter++;
    }
    return $array;
}
function slugify($string)
{
    // Verificar si la extensión intl está disponible
    if (function_exists('transliterator_transliterate')) {
        // Reemplazar caracteres especiales con sus equivalentes ASCII usando transliterator
        $string = transliterator_transliterate('Any-Latin; Latin-ASCII; Lower()', $string);
    } else {
        // Alternativa sin intl: reemplazar manualmente caracteres comunes
        $string = mb_strtolower($string, 'UTF-8');
        
        // Mapa de caracteres especiales a ASCII
        $caracteresEspeciales = [
            'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a', 'ã' => 'a', 'å' => 'a',
            'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e',
            'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i',
            'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o', 'õ' => 'o', 'ø' => 'o',
            'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u',
            'ñ' => 'n', 'ç' => 'c',
            'Á' => 'A', 'À' => 'A', 'Ä' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Å' => 'A',
            'É' => 'E', 'È' => 'E', 'Ë' => 'E', 'Ê' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Ï' => 'I', 'Î' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Ö' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ø' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Ü' => 'U', 'Û' => 'U',
            'Ñ' => 'N', 'Ç' => 'C',
        ];
        
        $string = strtr($string, $caracteresEspeciales);
    }

    // Reemplazar todo lo que no sea una letra, número o guion por guiones
    $string = preg_replace('/[^a-z0-9]+/i', '-', $string);

    // Eliminar guiones al principio y al final
    $string = trim($string, '-');

    return $string;
}
/**
 * Aplica subtotal a una columna específica
 * @param object $spreadsheet Objeto de la hoja de cálculo
 * @param array $columnasExcel Array de columnas
 * @param string $encabezado Nombre del encabezado de la columna
 * @param int $numeroDeFila Número de la fila donde aplicar el subtotal
 * @param array $opciones Opciones adicionales para el subtotal
 */
function aplicarSubtotal($spreadsheet, $columnasExcel, $encabezado, $numeroDeFila, $opciones = [])
{
    // Verificar si la columna existe
    if (!isset($columnasExcel[$encabezado])) {
        return false;
    }

    // Opciones por defecto
    $defaultOpciones = [
        'formato' => null, // '[h]:mm', '0.00', etc.
        'negrita' => true,
        'filaInicio' => 2, // Fila donde inician los datos
        'funcionSubtotal' => 9 // 9 = SUM, 1 = AVERAGE, etc.
    ];

    $opciones = array_merge($defaultOpciones, $opciones);

    $columna = $columnasExcel[$encabezado]['col'];
    $celdaInicio = "{$columna}{$opciones['filaInicio']}";
    $celdaFin = "{$columna}" . ($numeroDeFila - 1);
    $celdaSubTotal = "{$columna}{$numeroDeFila}";

    // Aplicar la fórmula de subtotal
    $spreadsheet->setCellValue($celdaSubTotal, "=SUBTOTAL({$opciones['funcionSubtotal']},{$celdaInicio}:{$celdaFin})");

    // Aplicar formato si se especifica
    if ($opciones['formato']) {
        $spreadsheet->getStyle($celdaSubTotal)->getNumberFormat()->setFormatCode($opciones['formato']);
    }

    // Aplicar negrita si está activada
    if ($opciones['negrita']) {
        $spreadsheet->getStyle($celdaSubTotal)->getFont()->setBold(true);
    }

    return true;
}
/**
 * Aplica colores a las celdas según el contenido del valor
 * @param object $spreadsheet Objeto de la hoja de cálculo
 * @param string $cellAddress Dirección de la celda (ej: "A1")
 * @param mixed $valor Valor de la celda
 * @param array $allowedKeys Claves permitidas para aplicar colores
 * @param string $currentKey Clave actual que se está procesando
 */
function aplicarColorCelda($spreadsheet, $cellAddress, $valor, $allowedKeys = ['Ingreso', 'Egreso'], $currentKey = '')
{

    // Verificar si la clave actual está en las permitidas
    if (!in_array($currentKey, $allowedKeys) || !is_string($valor)) {
        return;
    }

    $color = null;

    // Determinar el color según el contenido
    if (strpos($valor, 'MM') !== false || strpos($valor, 'NM') !== false) {
        $color = \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED;
    } elseif (strpos($valor, 'M') !== false) {
        $color = \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLUE;
    } elseif (strpos($valor, 'N') !== false) {
        $color = \PhpOffice\PhpSpreadsheet\Style\Color::COLOR_BLACK;
    }

    // eliminar el needle del valor
    // $valor = str_replace(['(MM)', '(NM)', '(M)', '(N)'], '', $valor);
    // $spreadsheet->setCellValue($cellAddress, trim($valor));

    // Aplicar el color si se encontró una coincidencia
    if ($color) {
        $spreadsheet->getStyle($cellAddress)->getFont()->getColor()->setARGB($color);
    }
}


/**
 * Aplica borde a un grupo de filas
 * @param object $spreadsheet Objeto de la hoja de cálculo
 * @param array $columnasExcel Array de columnas
 * @param int $filaInicio Fila de inicio del grupo
 * @param int $filaFin Fila de fin del grupo
 */
function aplicarBordeGrupo($spreadsheet, $columnasExcel, $filaInicio, $filaFin)
{
    if ($filaInicio > $filaFin) {
        return;
    }

    // Obtener primera y última columna
    $primeraColumna = reset($columnasExcel)['col'];
    $ultimaColumna = end($columnasExcel)['col'];

    // Definir el rango
    $rango = "{$primeraColumna}{$filaInicio}:{$ultimaColumna}{$filaFin}";

    // Aplicar borde inferior grueso al último registro del grupo
    $rangoUltimaFila = "{$primeraColumna}{$filaFin}:{$ultimaColumna}{$filaFin}";

    $spreadsheet->getStyle($rangoUltimaFila)->getBorders()->getBottom()->setBorderStyle(
        \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK
    );

    // Opcional: También puedes aplicar un borde lateral al grupo completo
    $spreadsheet->getStyle($rango)->getBorders()->getOutline()->setBorderStyle(
        \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
    );
}
/**
 * Aplica subtotales por legajo en una fila específica
 * @param object $spreadsheet Objeto de la hoja de cálculo
 * @param array $columnasExcel Array de columnas
 * @param int $filaInicio Fila de inicio del grupo
 * @param int $filaFin Fila de fin del grupo
 * @param int $filaSubtotal Fila donde insertar el subtotal
 * @param array $datosGrupo Datos del grupo actual para obtener Legajo y Apellido y Nombre
 */
function aplicarSubtotalPorLegajo($spreadsheet, $columnasExcel, $filaInicio, $filaFin, $filaSubtotal, $datosGrupo = [])
{
    foreach ($columnasExcel as $key => $e) {
        if ($e['type'] == 'number') {
            $letra = $e['col'];
            $formula = "=SUBTOTAL(9,{$letra}{$filaInicio}:{$letra}{$filaFin})";
            $spreadsheet->setCellValue("{$letra}{$filaSubtotal}", $formula);

            // Aplicar formato
            $spreadsheet->getStyle("{$letra}{$filaSubtotal}")
                ->getNumberFormat()
                ->setFormatCode('0.00');

            // Aplicar negrita
            $spreadsheet->getStyle("{$letra}{$filaSubtotal}")
                ->getFont()
                ->setBold(true);
        } elseif ($e['key'] == 'Legajo') {
            // Mantener el valor del legajo para el autofiltro
            $letra = $e['col'];
            $valorLegajo = $datosGrupo['Legajo'] ?? '';
            $spreadsheet->setCellValue("{$letra}{$filaSubtotal}", $valorLegajo);
            $spreadsheet->getStyle("{$letra}{$filaSubtotal}")
                ->getFont()
                ->setBold(true);
        } elseif ($e['key'] == 'Apellido y Nombre') {
            // Mantener el valor del apellido y nombre para el autofiltro
            $letra = $e['col'];
            $valorNombre = $datosGrupo['Apellido y Nombre'] ?? '';
            $spreadsheet->setCellValue("{$letra}{$filaSubtotal}", $valorNombre);
            $spreadsheet->getStyle("{$letra}{$filaSubtotal}")
                ->getFont()
                ->setBold(true);
        } elseif ($e['key'] == 'Sector') {
            // Mantener el valor del apellido y nombre para el autofiltro
            $letra = $e['col'];
            $valorNombre = $datosGrupo['Sector'] ?? '';
            $spreadsheet->setCellValue("{$letra}{$filaSubtotal}", $valorNombre);
            $spreadsheet->getStyle("{$letra}{$filaSubtotal}")
                ->getFont()
                ->setBold(true);
        }
    }

    // Aplicar borde superior a la fila de subtotal
    $primeraColumna = reset($columnasExcel)['col'];
    $ultimaColumna = end($columnasExcel)['col'];
    $rangoSubtotal = "{$primeraColumna}{$filaSubtotal}:{$ultimaColumna}{$filaSubtotal}";

    $spreadsheet->getStyle($rangoSubtotal)->getBorders()->getTop()->setBorderStyle(
        \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
    );
}
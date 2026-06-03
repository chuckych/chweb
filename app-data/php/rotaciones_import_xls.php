<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

return static function (string $tmpFilePath, string $originalName = ''): array {
    if (!class_exists(IOFactory::class)) {
        require __DIR__ . '/../../vendor/autoload.php';
    }

    if (!is_file($tmpFilePath) || !is_readable($tmpFilePath)) {
        throw new Exception('No se pudo leer el archivo subido.');
    }

    $extension = strtolower(pathinfo((string) $originalName, PATHINFO_EXTENSION));
    if (!in_array($extension, ['xls', 'xlsx'], true)) {
        throw new Exception('Solo se permiten archivos con extensión .xls o .xlsx.');
    }

    $mime = '';
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo !== false) {
            $detected = finfo_file($finfo, $tmpFilePath);
            $mime = is_string($detected) ? strtolower($detected) : '';
            finfo_close($finfo);
        }
    }

    $allowedMimes = [
        'application/vnd.ms-excel',
        'application/msexcel',
        'application/x-msexcel',
        'application/x-ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/zip',
        'application/octet-stream',
    ];

    if ($mime !== '' && !in_array($mime, $allowedMimes, true)) {
        throw new Exception('El archivo subido no tiene un tipo MIME válido para Excel.');
    }

    $identifiedType = IOFactory::identify($tmpFilePath);
    if (!in_array($identifiedType, ['Xls', 'Xlsx'], true)) {
        throw new Exception('El archivo no es un Excel válido (.xls o .xlsx).');
    }

    if (($extension === 'xls' && $identifiedType !== 'Xls') || ($extension === 'xlsx' && $identifiedType !== 'Xlsx')) {
        throw new Exception('La extensión del archivo no coincide con su contenido real.');
    }

    $reader = IOFactory::createReader($identifiedType);
    $reader->setReadDataOnly(true);
    $spreadsheet = $reader->load($tmpFilePath);
    $sheet = $spreadsheet->getActiveSheet();

    $highestColumn = $sheet->getHighestDataColumn();
    $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
    $highestRow = (int) $sheet->getHighestDataRow();

    $headerToColumn = [];
    for ($col = 1; $col <= $highestColumnIndex; $col++) {
        $header = trim((string) $sheet->getCell([$col, 1])->getFormattedValue());
        if ($header !== '') {
            $headerToColumn[$header] = $col;
        }
    }

    $requiredHeaders = ['RotCodi', 'RotDesc', 'RotItem', 'RotHora'];
    $missingHeaders = [];
    foreach ($requiredHeaders as $required) {
        if (!array_key_exists($required, $headerToColumn)) {
            $missingHeaders[] = $required;
        }
    }

    if (!empty($missingHeaders)) {
        throw new Exception('Faltan columnas obligatorias en el archivo: ' . implode(', ', $missingHeaders));
    }

    $warnings = [];
    $grouped = [];

    for ($row = 2; $row <= $highestRow; $row++) {
        $raw = [];
        $hasData = false;

        foreach ($headerToColumn as $header => $columnIndex) {
            $value = trim((string) $sheet->getCell([$columnIndex, $row])->getFormattedValue());
            if ($value !== '') {
                $hasData = true;
            }
            $raw[$header] = $value;
        }

        if (!$hasData) {
            continue;
        }

        $missing = [];
        foreach ($requiredHeaders as $required) {
            if (trim((string) ($raw[$required] ?? '')) === '') {
                $missing[] = $required;
            }
        }

        if (!empty($missing)) {
            $warnings[] = [
                'fila' => $row,
                'campos' => $missing,
                'mensaje' => 'Campos obligatorios faltantes: ' . implode(', ', $missing),
            ];
            continue;
        }

        $rotCodiDigits = preg_replace('/\D+/', '', (string) ($raw['RotCodi'] ?? ''));
        $rotItemDigits = preg_replace('/\D+/', '', (string) ($raw['RotItem'] ?? ''));
        $rotHoraDigits = preg_replace('/\D+/', '', (string) ($raw['RotHora'] ?? ''));

        $rotCodi = (int) ($rotCodiDigits ?: 0);
        $rotItem = (int) ($rotItemDigits ?: 0);
        $rotHora = (int) ($rotHoraDigits ?: 0);
        $rotDiasRaw = trim((string) ($raw['RotDias'] ?? ''));
        $rotDias = (int) (preg_replace('/\D+/', '', $rotDiasRaw) ?: 1);

        if ($rotCodi <= 0 || $rotCodi > 32767) {
            $warnings[] = [
                'fila' => $row,
                'campos' => ['RotCodi'],
                'mensaje' => 'RotCodi inválido. Debe estar entre 1 y 32767.',
            ];
            continue;
        }

        if ($rotItem <= 0) {
            $warnings[] = [
                'fila' => $row,
                'campos' => ['RotItem'],
                'mensaje' => 'RotItem inválido. Debe ser mayor a 0.',
            ];
            continue;
        }

        if ($rotHora <= 0) {
            $warnings[] = [
                'fila' => $row,
                'campos' => ['RotHora'],
                'mensaje' => 'RotHora inválido. Debe ser mayor a 0.',
            ];
            continue;
        }

        if ($rotDias <= 0) {
            $rotDias = 1;
        }

        $rotDesc = trim((string) ($raw['RotDesc'] ?? ''));
        $user = trim((string) ($raw['User'] ?? ''));

        $groupKey = (string) $rotCodi;
        if (!isset($grouped[$groupKey])) {
            $grouped[$groupKey] = [
                'RotCodi' => $rotCodi,
                'RotDesc' => $rotDesc,
                'User' => $user,
                'Horarios' => [],
                '__seenItems' => [],
                '__seenHoras' => [],
            ];
        }

        if ($grouped[$groupKey]['RotDesc'] === '' && $rotDesc !== '') {
            $grouped[$groupKey]['RotDesc'] = $rotDesc;
        }

        if ($grouped[$groupKey]['RotDesc'] !== '' && $rotDesc !== '' && strcasecmp($grouped[$groupKey]['RotDesc'], $rotDesc) !== 0) {
            $warnings[] = [
                'fila' => $row,
                'campos' => ['RotDesc'],
                'mensaje' => "Descripción inconsistente para RotCodi {$rotCodi}. Se usará la primera descripción válida.",
            ];
        }

        if ($grouped[$groupKey]['User'] === '' && $user !== '') {
            $grouped[$groupKey]['User'] = $user;
        }

        if (isset($grouped[$groupKey]['__seenItems'][$rotItem])) {
            $warnings[] = [
                'fila' => $row,
                'campos' => ['RotItem'],
                'mensaje' => "RotItem repetido ({$rotItem}) dentro de RotCodi {$rotCodi}.",
            ];
            continue;
        }

        if (isset($grouped[$groupKey]['__seenHoras'][$rotHora])) {
            $warnings[] = [
                'fila' => $row,
                'campos' => ['RotHora'],
                'mensaje' => "RotHora repetido ({$rotHora}) dentro de RotCodi {$rotCodi}.",
            ];
            continue;
        }

        $grouped[$groupKey]['__seenItems'][$rotItem] = true;
        $grouped[$groupKey]['__seenHoras'][$rotHora] = true;

        $grouped[$groupKey]['Horarios'][] = [
            'RotItem' => $rotItem,
            'RotHora' => $rotHora,
            'RotDias' => $rotDias,
        ];
    }

    $payloadRows = [];
    foreach ($grouped as $group) {
        if (empty($group['Horarios'])) {
            $warnings[] = [
                'fila' => '-',
                'campos' => ['Horarios'],
                'mensaje' => "RotCodi {$group['RotCodi']} no tiene items válidos para procesar.",
            ];
            continue;
        }

        if (trim((string) $group['RotDesc']) === '') {
            $warnings[] = [
                'fila' => '-',
                'campos' => ['RotDesc'],
                'mensaje' => "RotCodi {$group['RotCodi']} no tiene descripción válida.",
            ];
            continue;
        }

        unset($group['__seenItems'], $group['__seenHoras']);
        $payloadRows[] = $group;
    }

    if (method_exists($spreadsheet, 'disconnectWorksheets')) {
        $spreadsheet->disconnectWorksheets();
    }
    unset($spreadsheet);

    return [
        'rows' => $payloadRows,
        'warnings' => $warnings,
        'meta' => [
            'extension' => $extension,
            'mime' => $mime,
            'tipo' => $identifiedType,
            'filasProcesadas' => max(0, $highestRow - 1),
            'rotacionesValidas' => count($payloadRows),
        ],
    ];
};

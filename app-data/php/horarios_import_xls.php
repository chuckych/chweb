<?php

declare(strict_types=1);

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as SpreadsheetDate;

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

    $requiredHeaders = ['HorCodi', 'HorDesc', 'HorID'];
    $missingHeaders = [];
    foreach ($requiredHeaders as $required) {
        if (!array_key_exists($required, $headerToColumn)) {
            $missingHeaders[] = $required;
        }
    }

    if (!empty($missingHeaders)) {
        throw new Exception('Faltan columnas obligatorias en el archivo: ' . implode(', ', $missingHeaders));
    }

    $defaults = [
        'HorColor' => '#000000',
        'HorDomi' => '0',
        'HorLune' => '0',
        'HorMart' => '0',
        'HorMier' => '0',
        'HorJuev' => '0',
        'HorVier' => '0',
        'HorSaba' => '0',
        'HorFeri' => '0',
        'HorDoDe' => '00:00',
        'HorLuDe' => '00:00',
        'HorMaDe' => '00:00',
        'HorMiDe' => '00:00',
        'HorJuDe' => '00:00',
        'HorViDe' => '00:00',
        'HorSaDe' => '00:00',
        'HorFeDe' => '00:00',
        'HorDoHa' => '00:00',
        'HorLuHa' => '00:00',
        'HorMaHa' => '00:00',
        'HorMiHa' => '00:00',
        'HorJuHa' => '00:00',
        'HorViHa' => '00:00',
        'HorSaHa' => '00:00',
        'HorFeHa' => '00:00',
        'HorDoRe' => '00:00',
        'HorLuRe' => '00:00',
        'HorMaRe' => '00:00',
        'HorMiRe' => '00:00',
        'HorJuRe' => '00:00',
        'HorViRe' => '00:00',
        'HorSaRe' => '00:00',
        'HorFeRe' => '00:00',
        'HorDoLi' => '0',
        'HorLuLi' => '0',
        'HorMaLi' => '0',
        'HorMiLi' => '0',
        'HorJuLi' => '0',
        'HorViLi' => '0',
        'HorSaLi' => '0',
        'HorFeLi' => '0',
        'HorDoHs' => '00:00',
        'HorLuHs' => '00:00',
        'HorMaHs' => '00:00',
        'HorMiHs' => '00:00',
        'HorJuHs' => '00:00',
        'HorViHs' => '00:00',
        'HorSaHs' => '00:00',
        'HorFeHs' => '00:00',
    ];

    $warnings = [];
    $payloadRows = [];

    $timeFields = [
        'HorDoDe', 'HorLuDe', 'HorMaDe', 'HorMiDe', 'HorJuDe', 'HorViDe', 'HorSaDe', 'HorFeDe',
        'HorDoHa', 'HorLuHa', 'HorMaHa', 'HorMiHa', 'HorJuHa', 'HorViHa', 'HorSaHa', 'HorFeHa',
        'HorDoRe', 'HorLuRe', 'HorMaRe', 'HorMiRe', 'HorJuRe', 'HorViRe', 'HorSaRe', 'HorFeRe',
        'HorDoHs', 'HorLuHs', 'HorMaHs', 'HorMiHs', 'HorJuHs', 'HorViHs', 'HorSaHs', 'HorFeHs',
    ];
    $timeFieldSet = array_fill_keys($timeFields, true);

    $normalizeExcelTimeValue = static function ($cell, string $formattedValue): string {
        $formatted = trim($formattedValue);
        if ($formatted === '') {
            return '';
        }

        // Si ya viene como HH:mm o HH:mm:ss, normalizamos a HH:mm.
        if (preg_match('/^(\d{1,2}):(\d{2})(?::\d{2})?$/', $formatted, $match)) {
            $h = max(0, min(23, (int) $match[1]));
            $m = max(0, min(59, (int) $match[2]));
            return sprintf('%02d:%02d', $h, $m);
        }

        $rawValue = $cell->getValue();
        $numeric = null;

        if (is_numeric($rawValue)) {
            $numeric = (float) $rawValue;
        } elseif (is_numeric(str_replace(',', '.', $formatted))) {
            $numeric = (float) str_replace(',', '.', $formatted);
        }

        if ($numeric === null) {
            return $formatted;
        }

        try {
            $dt = SpreadsheetDate::excelToDateTimeObject($numeric);
            return $dt->format('H:i');
        } catch (\Throwable $th) {
            $seconds = (int) round(fmod(max(0.0, $numeric), 1.0) * 86400);
            $seconds = (($seconds % 86400) + 86400) % 86400;
            return gmdate('H:i', $seconds);
        }
    };

    for ($row = 2; $row <= $highestRow; $row++) {
        $raw = [];
        $hasData = false;

        foreach ($headerToColumn as $header => $columnIndex) {
            $cell = $sheet->getCell([$columnIndex, $row]);
            $value = trim((string) $cell->getFormattedValue());
            if (isset($timeFieldSet[$header])) {
                $value = $normalizeExcelTimeValue($cell, $value);
            }
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

        $horCodiDigits = preg_replace('/\D+/', '', (string) ($raw['HorCodi'] ?? ''));
        $horCodi = (int) ($horCodiDigits ?: 0);
        if ($horCodi <= 0 || $horCodi > 32767) {
            $warnings[] = [
                'fila' => $row,
                'campos' => ['HorCodi'],
                'mensaje' => 'HorCodi inválido. Debe estar entre 1 y 32767.',
            ];
            continue;
        }

        $horId = preg_replace('/[^a-zA-Z0-9]/', '', (string) ($raw['HorID'] ?? ''));
        $horId = substr($horId, 0, 3);
        if ($horId === '') {
            $warnings[] = [
                'fila' => $row,
                'campos' => ['HorID'],
                'mensaje' => 'HorID inválido. Debe contener al menos un carácter alfanumérico.',
            ];
            continue;
        }

        $item = [
            'HorCodi' => $horCodi,
            'HorDesc' => trim((string) ($raw['HorDesc'] ?? '')),
            'HorID' => $horId,
            'HorColor' => '#000000',
        ] + $defaults;

        foreach ($defaults as $field => $defaultValue) {
            if (array_key_exists($field, $raw) && $raw[$field] !== '') {
                $item[$field] = (string) $raw[$field];
            }
        }

        if (array_key_exists('HorColor', $raw) && $raw['HorColor'] !== '') {
            $color = strtoupper((string) $raw['HorColor']);
            if (preg_match('/^#?[0-9A-F]{6}$/', $color)) {
                $item['HorColor'] = $color[0] === '#' ? $color : ('#' . $color);
            }
        }

        $payloadRows[] = $item;
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
        ],
    ];
};

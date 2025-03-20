<?php
// Deshabilitar la visualización de errores en producción
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Establecer tiempo máximo de ejecución para evitar scripts infinitos
set_time_limit(60); // 60 segundos máximo

// Corregir la ruta de autoload
require __DIR__ . '/../../vendor/autoload.php';
$hashFile = md5(microtime(true));
// Crear o verificar archivo de log
$logDirectory = __DIR__ . '/../../logs/uploads';
if (!file_exists($logDirectory)) {
    if (!mkdir($logDirectory, 0755, true)) {
        // Si no se puede crear el directorio, usar un directorio alternativo
        $logDirectory = __DIR__ . '/temp';
        if (!file_exists($logDirectory)) {
            mkdir($logDirectory, 0755, true);
        }
    }
}
$logFile = $logDirectory . '/upload_' . date('Y-m-d') . '.log';

// Función para registrar eventos
function logEvent($message, $type = 'INFO')
{
    global $logFile;
    if ($logFile && is_writable(dirname($logFile))) {
        $timestamp = date('Y-m-d H:i:s');
        $clientIP = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        $logMessage = "[$timestamp][$type][$clientIP] $message" . PHP_EOL;
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }
}

// Configuración inicial
header('Content-Type: application/json');
$response = [
    'success' => false,
    'message' => '',
    'data' => [],
    'errors' => []
];

try {
    // Verificar token CSRF
    session_start();
    if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token'])) {
        if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            logEvent("Intento de subida con token CSRF inválido", "WARNING");
            $response['message'] = 'Error de validación de seguridad. Inténtelo de nuevo.';
            echo json_encode($response);
            exit;
        }
    }

    // Verificar si se recibió un archivo
    if (!isset($_FILES['userFile']) || $_FILES['userFile']['error'] !== UPLOAD_ERR_OK) {
        $errorMessage = 'No se recibió ningún archivo o hubo un error en la subida.';
        if (isset($_FILES['userFile']['error'])) {
            $uploadErrors = [
                1 => 'El archivo excede el tamaño máximo permitido por el servidor.',
                2 => 'El archivo excede el tamaño máximo permitido por el formulario.',
                3 => 'El archivo fue subido parcialmente.',
                4 => 'No se subió ningún archivo.',
                6 => 'Falta una carpeta temporal.',
                7 => 'No se pudo escribir el archivo en el disco.',
                8 => 'Una extensión de PHP detuvo la subida del archivo.'
            ];
            $errorMessage = $uploadErrors[$_FILES['userFile']['error']] ?? $errorMessage;
        }
        logEvent("Error en subida de archivo: " . $errorMessage, "ERROR");
        $response['message'] = $errorMessage;
        echo json_encode($response);
        exit;
    }

    // Validar que se reciba un archivo
    $file = $_FILES['userFile'];

    // Verificar el tipo real del archivo
    if (class_exists('finfo')) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
    } else {
        // Fallback si finfo no está disponible
        $mimeType = $file['type'];
    }

    $allowedMimeTypes = [
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/octet-stream', // Algunos servidores pueden detectar Excel como octet-stream
        'application/zip', // Algunos Excel nuevos son detectados como ZIP
    ];

    // Verificar extensión del archivo de manera adicional
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExtensions = ['xls', 'xlsx'];

    if (!in_array($fileExtension, $allowedExtensions)) {
        logEvent("Intento de subir archivo con extensión no permitida: {$fileExtension}", "WARNING");
        $response['message'] = 'Solo se permiten archivos con extensión .xls o .xlsx';
        echo json_encode($response);
        exit;
    }

    // Crear/asegurar directorio temporal
    $tempDirectory = __DIR__ . '/temp';
    if (!file_exists($tempDirectory)) {
        if (!mkdir($tempDirectory, 0755, true)) {
            throw new \Exception("No se pudo crear el directorio temporal");
        }
    }

    // Verificamos que podamos escribir en el directorio temporal
    if (!is_writable($tempDirectory)) {
        throw new \Exception("No se puede escribir en el directorio temporal");
    }

    // Configurar la clase Upload
    $upload = new \Verot\Upload\Upload($file);

    // Generar nombre de archivo único con hash para evitar colisiones
    $uniqueId = md5(uniqid(mt_rand(), true));
    $upload->file_new_name_body = 'import_' . date('YmdHis') . '_' . substr($uniqueId, 0, 8);
    $upload->file_overwrite = true;
    $upload->file_max_size = 15 * 1024 * 1024; // 15MB
    $upload->allowed = array_merge($allowedMimeTypes, ['application/excel']); // Incluir más tipos posibles

    // Registrar intento de subida
    logEvent("Intento de subida de archivo: {$file['name']} ({$file['size']} bytes, {$mimeType})", "INFO");

    // Procesar subida
    if ($upload->uploaded) {
        $upload->process($tempDirectory);

        if ($upload->processed) {
            $uploadedFilePath = $tempDirectory . '/' . $upload->file_dst_name;
            logEvent("Archivo procesado correctamente: " . $upload->file_dst_name, "INFO");

            // Procesar el archivo Excel con manejo de excepciones
            try {
                // Limitar memoria y tiempo para prevenir ataques DoS
                ini_set('memory_limit', '256M');

                // Detectar tipo de archivo de forma segura
                $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($uploadedFilePath);

                // Crear reader según tipo con configuraciones de seguridad
                $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);

                // Deshabilitar macros y otros elementos potencialmente peligrosos
                if (method_exists($reader, 'setReadDataOnly')) {
                    $reader->setReadDataOnly(true); // No cargar macros ni fórmulas
                }

                // Cargar archivo
                $spreadsheet = $reader->load($uploadedFilePath);

                // Obtener hoja activa
                $worksheet = $spreadsheet->getActiveSheet();

                $highestRow = min($worksheet->getHighestRow(), 1000); // Limitar a 1000 filas máximo

                // Validar si hay datos (al menos encabezados y una fila)
                if ($highestRow < 2) {
                    logEvent("Archivo sin datos suficientes", "WARNING");
                    $response['message'] = 'El archivo no contiene datos suficientes.';
                    echo json_encode($response);
                    // Eliminar archivo temporal
                    if (file_exists($uploadedFilePath)) {
                        unlink($uploadedFilePath);
                    }
                    exit;
                }

                // Array para almacenar filas procesadas correctamente
                $validRows = [];
                // Array para almacenar errores por fila
                $errorRows = [];
                // Array para almacenar IDs ya procesados (evitar duplicados)
                $processedIds = [];

                // Procesar filas (omitir la primera fila que son los encabezados)
                for ($row = 2; $row <= $highestRow; $row++) {
                    try {
                        $rowData = [];
                        $rowErrors = [];

                        // numero de fila
                        $rowData['row'] = $row;

                        // Leer datos de la fila con manejo de excepciones
                        $id = trim($worksheet->getCell([1, $row])->getValue() ?? '');
                        // si el id esta vacio se salta la fila
                        $nombreApellido = trim($worksheet->getCell([2, $row])->getValue() ?? '');
                        $estado = trim($worksheet->getCell([3, $row])->getValue() ?? '');
                        $visualizarZona = trim($worksheet->getCell([4, $row])->getValue() ?? '');
                        $bloqueoFechaInicio = $worksheet->getCell([5, $row])->getValue() ?? '';
                        $bloqueoFechaFin = $worksheet->getCell([6, $row])->getValue() ?? '';

                        // Sanitizar entradas para prevenir XSS
                        $id = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
                        $nombreApellido = htmlspecialchars($nombreApellido, ENT_QUOTES, 'UTF-8');
                        $estado = htmlspecialchars($estado, ENT_QUOTES, 'UTF-8');
                        $visualizarZona = htmlspecialchars($visualizarZona, ENT_QUOTES, 'UTF-8');

                        // Validar ID (obligatorio y debe ser número entero)
                        if (empty($id)) {
                            $rowErrors[] = "Fila $row: El ID es obligatorio";
                        } elseif (!filter_var($id, FILTER_VALIDATE_INT)) {
                            $rowErrors[] = "Fila $row: El ID debe ser un número entero";
                        } elseif (in_array((int) $id, $processedIds)) {
                            // Validar que el ID no esté duplicado
                            $rowErrors[] = "Fila $row: El ID $id ya existe en otra fila. No se permiten IDs duplicados";
                        } else {
                            $rowData['id'] = (int) $id;
                            // Añadir ID al array de IDs procesados
                            $processedIds[] = (int) $id;
                        }

                        // Validar Nombre y Apellido (obligatorio, máximo 50 caracteres)
                        if (empty($nombreApellido)) {
                            $rowErrors[] = "Fila $row: El Nombre y Apellido es obligatorio";
                        } else {
                            $rowData['nombre_apellido'] = substr($nombreApellido, 0, 50);
                        }

                        // Validar Estado (obligatorio, debe ser "activo" o "bloqueado")
                        if (empty($estado)) {
                            $rowErrors[] = "Fila $row: El Estado es obligatorio";
                        } elseif (!in_array(strtolower($estado), ['activo', 'bloqueado'])) {
                            $rowErrors[] = "Fila $row: El Estado debe ser 'activo' o 'bloqueado'";
                        } else {
                            $rowData['estado'] = strtolower($estado);
                        }

                        // Validar Visualizar zona (obligatorio, debe ser "activo" o "inactivo")
                        if (empty($visualizarZona)) {
                            $rowErrors[] = "Fila $row: Visualizar zona es obligatorio";
                        } elseif (!in_array(strtolower($visualizarZona), ['activo', 'inactivo'])) {
                            $rowErrors[] = "Fila $row: Visualizar zona debe ser 'activo' o 'inactivo'";
                        } else {
                            $rowData['visualizar_zona'] = strtolower($visualizarZona);
                        }

                        // Variables para almacenar fechas procesadas
                        $fechaInicioProcesada = null;
                        $fechaFinProcesada = null;
                        $tieneFechaInicio = !empty($bloqueoFechaInicio);
                        $tieneFechaFin = !empty($bloqueoFechaFin);

                        // Validar Bloqueo Fecha inicio (opcional, formato YYYY-mm-dd)
                        if ($tieneFechaInicio) {
                            // Intentar formatear la fecha si es un número de Excel
                            if (is_numeric($bloqueoFechaInicio)) {
                                $fechaInicioProcesada = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($bloqueoFechaInicio)->format('Y-m-d');
                            } else {
                                $fechaInicioProcesada = $bloqueoFechaInicio;
                            }

                            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaInicioProcesada)) {
                                $rowData['bloqueo_fecha_inicio'] = $fechaInicioProcesada;
                            } else {
                                $rowErrors[] = "Fila $row: Bloqueo Fecha inicio debe tener formato YYYY-MM-DD";
                                $fechaInicioProcesada = null; // Invalidar fecha para evitar comparaciones posteriores
                            }
                        } else {
                            $rowData['bloqueo_fecha_inicio'] = null;
                        }

                        // Validar Bloqueo Fecha fin (opcional, formato YYYY-mm-dd)
                        if ($tieneFechaFin) {
                            // Intentar formatear la fecha si es un número de Excel
                            if (is_numeric($bloqueoFechaFin)) {
                                $fechaFinProcesada = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($bloqueoFechaFin)->format('Y-m-d');
                            } else {
                                $fechaFinProcesada = $bloqueoFechaFin;
                            }

                            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fechaFinProcesada)) {
                                $rowData['bloqueo_fecha_fin'] = $fechaFinProcesada;
                            } else {
                                $rowErrors[] = "Fila $row: Bloqueo Fecha fin debe tener formato YYYY-MM-DD";
                                $fechaFinProcesada = null; // Invalidar fecha para evitar comparaciones posteriores
                            }
                        } else {
                            $rowData['bloqueo_fecha_fin'] = null;
                        }

                        // Validaciones adicionales para las fechas
                        if (($tieneFechaInicio && !$tieneFechaFin) || (!$tieneFechaInicio && $tieneFechaFin)) {
                            // Si solo una de las fechas está presente
                            $rowErrors[] = "Fila $row: Si proporciona una fecha de bloqueo, debe proporcionar ambas (inicio y fin)";
                        } else if ($tieneFechaInicio && $tieneFechaFin && $fechaInicioProcesada && $fechaFinProcesada) {
                            // Si ambas fechas están presentes y son válidas, verificar que inicio <= fin
                            $fechaInicioObj = new DateTime($fechaInicioProcesada);
                            $fechaFinObj = new DateTime($fechaFinProcesada);

                            if ($fechaInicioObj > $fechaFinObj) {
                                $rowErrors[] = "Fila $row: La fecha de inicio de bloqueo no puede ser posterior a la fecha de fin";

                                // Opcional: eliminar las fechas de los datos ya que son inválidas por la relación
                                unset($rowData['bloqueo_fecha_inicio']);
                                unset($rowData['bloqueo_fecha_fin']);
                            }
                        }

                        // Si hay errores, guardarlos, sino guardar la fila como válida
                        if (!empty($rowErrors)) {
                            $errorRows[] = [
                                'row' => $row,
                                'errors' => $rowErrors
                            ];
                        } else {
                            $validRows[] = $rowData;
                        }
                    } catch (\Exception $e) {
                        // Capturar errores al procesar una fila específica
                        $errorRows[] = [
                            'row' => $row,
                            'errors' => ["Error al procesar la fila: " . $e->getMessage()]
                        ];
                        logEvent("Error al procesar fila $row: " . $e->getMessage(), "ERROR");
                    }
                }

                // Eliminar archivo temporal de forma segura
                if (file_exists($uploadedFilePath)) {
                    unlink($uploadedFilePath);
                    logEvent("Archivo temporal eliminado: $uploadedFilePath", "INFO");
                }

                // Preparar respuesta
                $response['success'] = true;
                $response['message'] = 'Archivo procesado correctamente.';
                $response['data'] = [
                    'total_rows' => $highestRow - 1, // Restar la fila de encabezados
                    'valid_rows' => count($validRows),
                    'error_rows' => count($errorRows),
                    'processed_rows' => $validRows
                ];
                $response['errors'] = $errorRows;
                $response['flag'] = $hashFile;

                // Definir las opciones como una combinación de constantes usando el operador |
                $jsonOption = JSON_PRETTY_PRINT
                    | JSON_UNESCAPED_UNICODE
                    | JSON_UNESCAPED_SLASHES
                    | JSON_NUMERIC_CHECK;

                // Codificar los datos en formato JSON con las opciones especificadas
                $jsonValids = json_encode($validRows, $jsonOption);

                // Verificar si json_encode tuvo éxito
                if ($jsonValids === false) {
                    // Manejar el error si json_encode falla
                    throw new Exception('Error al codificar los datos JSON: ' . json_last_error_msg());
                }

                // Guardar los datos JSON en un archivo
                $filePath = __DIR__ . '/temp/' . $hashFile . '.json';
                if (file_put_contents($filePath, $jsonValids) === false) {
                    // Manejar el error si file_put_contents falla
                    throw new Exception('Error al escribir el archivo JSON.');
                }

                // Registrar resultado exitoso
                $message = "Procesamiento exitoso: " . ($highestRow - 1) . " filas totales, " . count($validRows) . " válidas, " . count($errorRows) . " con errores";
                logEvent($message, "INFO");

            } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                // Error específico de lectura de Excel
                logEvent("Error al leer archivo Excel: " . $e->getMessage(), "ERROR");
                $response['message'] = 'El archivo Excel no es válido o está corrupto.';

                // Asegurar que se elimine el archivo temporal en caso de error
                if (file_exists($uploadedFilePath)) {
                    unlink($uploadedFilePath);
                }
            } catch (\Exception $e) {
                // Error general
                logEvent("Error al procesar archivo: " . $e->getMessage(), "ERROR");
                $response['message'] = 'Error al procesar el archivo. Por favor, inténtelo de nuevo.';

                // Asegurar que se elimine el archivo temporal en caso de error
                if (file_exists($uploadedFilePath)) {
                    unlink($uploadedFilePath);
                }
            }
        } else {
            logEvent("Error al procesar el archivo: " . $upload->error, "ERROR");
            $response['message'] = 'Error al procesar el archivo: ' . $upload->error;
        }
    } else {
        logEvent("Error en la validación del archivo: " . $upload->error, "ERROR");
        $response['message'] = 'Error en la validación del archivo: ' . $upload->error;
    }
} catch (\Exception $e) {
    // Capturar cualquier otra excepción no prevista
    logEvent("Error no controlado: " . $e->getMessage(), "ERROR");
    $response['message'] = 'Ha ocurrido un error inesperado. Por favor, inténtelo de nuevo.';
}

// Enviar respuesta JSON
echo json_encode($response);

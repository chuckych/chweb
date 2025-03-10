<?php

function opt_encode(): int
{
    return JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT;
}
function deleteOldLogs($days = 2, $path)
{
    $files = glob($path . '/*_import-conf-proy.log');
    foreach ($files as $file) {
        if (is_file($file)) {
            $fechaFile = explode('_', basename($file))[0];
            $fechaFile = strtotime($fechaFile);
            $fechaActual = strtotime(date('Y-m-d'));
            $diferencia = ($fechaActual - $fechaFile) / (60 * 60 * 24);
            if ($diferencia > $days) {
                unlink($file);
                logger("Archivo eliminado: $file");
            }
        }
    }
    logger("Limpieza de logs completada. Archivos eliminados: " . count($files));
}
function logger($msg)
{
    $fecha = date('Y-m-d');
    $logFile = __DIR__ . '../../archivos/logs/' . $fecha . '_import-conf-proy.log';
    $log = date('Y-m-d H:i:s') . ' → ' . print_r($msg, true) . PHP_EOL;
    file_put_contents($logFile, $log, FILE_APPEND);
    // echo $log;
}
function logger_clean()
{
    $fecha = date('Y-m-d');
    $logFile = __DIR__ . '../../archivos/logs/' . $fecha . '_import-conf-proy.log';
    file_put_contents($logFile, '');
}
function sumar_dias($fecha, $dias)
{
    return date('Y-m-d', strtotime($fecha . ' + ' . $dias . ' days'));
}
function str_ucwords($str)
{
    return trim(ucwords(strtolower($str)));
}
function put_json($path, $data)
{
    file_put_contents($path, json_encode($data, opt_encode()));
}
function last_date($connpdo, $tabla, $col)
{
    $sql = "SELECT MAX($col) as FeHo FROM $tabla";
    $stmt = $connpdo->prepare($sql);
    $stmt->execute();
    $fecha = $stmt->fetch(PDO::FETCH_ASSOC)['FeHo'];
    return $fecha ?? date('Y-m-d H:i:s');
}
function get_db($connpdo, $tabla, $col)
{
    $sql = "SELECT * FROM $tabla";
    $stmt = $connpdo->prepare($sql);
    $stmt->execute();

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($data as $key => $empresa) {
        $data[$key][$col] = str_ucwords($empresa[$col]);
    }
    $data = array_column($data, null, $col); // Crear un array asociativo de empresas con la descripción como clave
    return $data ?? [];
}
function datos_db($tabla, $connpdo)
{
    $conf = [
        'proy_empresas' => ['EmpDesc', 'EmpID', 'EmpFeHo'],
        'proy_estados' => ['EstDesc', 'EstID', 'EstFeHo'],
        'proy_proyectos' => ['ProyNom', 'ProyID', 'ProyFeHo'],
    ];

    $path_update = __DIR__ . '../../archivos/last_update_' . $tabla . '.json';
    $path_tabla = __DIR__ . '../../archivos/' . $tabla . '.json';
    $path_tabla_key = __DIR__ . '../../archivos/' . $tabla . '_key.json';
    // Última fecha de actualización de datos
    $date = last_date($connpdo, $tabla, $conf[$tabla][2]);

    if (!file_exists($path_update)) {
        // Si no existe el archivo crearlo
        logger('Se crea el archivo last_update_' . $tabla . ' en cache ya que no existe.');
        put_json($path_update, ['date' => $date]);
    }

    if (!file_exists($path_tabla)) {
        // Si no existe el archivo crearlo
        logger('Se crea el archivo ' . $tabla . ' en cache ya que no existe.');
        $data = get_db($connpdo, $tabla, $conf[$tabla][0]);
        $dataKey = array_column($data, $conf[$tabla][0]);
        put_json($path_tabla, $data);
        put_json($path_tabla_key, $dataKey);
    }

    // Obtener la fecha de la última actualización de datos desde el archivo last_update_$tabla.json
    $cache_date = json_decode(file_get_contents($path_update), true);

    if ($cache_date['date'] >= $date) {
        // Si la fecha de la última actualización es mayor o igual a la fecha de la última actualización de datos, retornar el archivo $tabla de cache
        logger('Se obtienen los datos desde el archivo ' . $tabla . ' cache');
        $data = json_decode(file_get_contents($path_tabla), true);
        $dataKey = json_decode(file_get_contents($path_tabla_key), true);
    } else {
        // Si la fecha de la última actualización es menor a la fecha de la última actualización de datos, actualizar el archivo $tabla.json
        logger('Se actualiza el archivo ' . $tabla . ' de cache con nuevos datos.');
        $data = get_db($connpdo, $tabla, $conf[$tabla][0]);
        $dataKey = array_column($data, $conf[$tabla][0]);
        put_json($path_tabla, $data);
        put_json($path_update, ['date' => $date]);
        put_json($path_tabla_key, $dataKey);
    }
    return [
        'data' => $data,
        'keys' => $dataKey,
    ];
}
function search_value($array, $desc, $keyID)
{
    return $array[$desc][$keyID] ?? null;
}
function crear_proyectos_nuevos($conn, $array, $empresasDB, $estadosDB) // Crear proyectos nuevos
{
    try {
        // Si existen proyectos nuevos
        if (!empty($array)) {
            $sqlInsert = "INSERT INTO proy_proyectos (ProyDesc, ProyNom, ProyEmpr, ProyPlant, ProyResp, ProyEsta, ProyUsePlant, ProyObs, ProyIni, ProyFin, ProyAlta, ProyFeHo, Cliente) VALUES "; // Query de inserción de proyectos
            $values = []; // Array de valores
            $params = []; // Array de parámetros

            // Recorrer los proyectos nuevos
            foreach ($array as $index => $proyecto) {
                // Buscar el ID de la empresa en el array de empresas
                $empresa = search_value($empresasDB, $proyecto['empresa'], 'EmpID');

                // Si la empresa no existe, omitir
                if ($empresa === null) {
                    continue;
                }
                // Buscar el ID del estado en el array de estados
                $estado = search_value($estadosDB, $proyecto['estado'], 'EstID');
                // Si el estado no existe, omitir
                if ($estado === null) {
                    continue;
                }

                $values[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"; // Agregar un valor al array de valores
                $params[] = $proyecto['descripcion']; // Descripción del proyecto
                $params[] = $proyecto['nombre']; // Nombre del proyecto
                $params[] = $empresa; // ID de la empresa
                $params[] = $proyecto['procesos']; // ID de la plantilla de procesos
                $params[] = $proyecto['responsable']; // ID del responsable
                $params[] = $estado; // ID del estado
                $params[] = 0; // Usar plantilla de procesos
                $params[] = $proyecto['observaciones']; // Observaciones del proyecto
                $params[] = $proyecto['inicio']; // Fecha de inicio del proyecto
                $params[] = $proyecto['fin']; // Fecha de finalización del proyecto
                $params[] = $proyecto['fecha_creacion']; // Fecha de creación del proyecto
                $params[] = $proyecto['fecha_creacion']; // Fecha de modificación del proyecto
                $params[] = 1;
            }
            // Si existen valores
            if (!empty($values)) {
                try {
                    $sqlInsert .= implode(', ', $values); // Unir los valores
                    $stmtInsert = $conn->prepare($sqlInsert); // Preparar la consulta de inserción
                    $stmtInsert->execute($params); // Ejecutar la consulta de inserción
                    $affectedRows = $stmtInsert->rowCount(); // Obtener las filas afectadas

                    return $affectedRows > 0; // Retornar verdadero si se crearon proyectos

                } catch (\Throwable $th) {
                    logger("Error al insertar proyectos: " . $th->getMessage());
                    return false;
                }
            }
        }
    } catch (\Throwable $th) {
        logger("Error al crear proyectos nuevos: " . $th->getMessage());
        return false;
    }
}
function crear_empresas_nuevas($connpdo, $empresasNuevas)
{
    $sqlInsert = "INSERT INTO proy_empresas (EmpDesc, EmpTel, EmpObs, EmpAlta, EmpFeHo, Cliente) VALUES (:empDesc, :empTel, :empObs, :empAlta, :empFeHo, :Cliente)";
    $stmtInsert = $connpdo->prepare($sqlInsert);
    $affectedRows = 0;
    foreach ($empresasNuevas as $empresa) {
        $empDesc = $empresa;
        $stmtInsert->execute([':empDesc' => $empDesc, ':empTel' => '', ':empObs' => '', ':empAlta' => date('Y-m-d H:i:s'), ':empFeHo' => date('Y-m-d H:i:s'), ':Cliente' => '1']);
        logger("Empresa: '{$empDesc}' creada correctamente");
        $affectedRows++;
    }
    return $affectedRows > 0;
}
function update_estados_proy($diffProy, $connpdo)
{
    if (empty($diffProy)) {
        return 0;
    }
    $sqlUpdate = "UPDATE proy_proyectos SET ProyEsta = :ProyEsta WHERE ProyNom = :ProyNom";
    $stmtUpdate = $connpdo->prepare($sqlUpdate);
    $affectedRows = 0;
    foreach ($diffProy as $diff) {
        $stmtUpdate->execute([':ProyEsta' => $diff['estaID'], ':ProyNom' => $diff['nombre']]);
        $affectedRows += $stmtUpdate->rowCount();
        // logger('Estado actualizado para el proyecto: ' . $diff['nombre']);
    }
    unset($diffProy);
    logger('Número de proyectos actualizados: ' . $affectedRows);
    logger('Estados de proyectos actualizados correctamente.');
    datos_db('proy_proyectos', $connpdo);
    return $affectedRows;
}
function isFileInUse($filePath)
{
    $fileHandle = @fopen($filePath, 'r+');
    if ($fileHandle === false) {
        return true; // El archivo está en uso
    }
    fclose($fileHandle);
    return false; // El archivo no está en uso
}
function copiarArchivoTemporal($filePath)
{
    $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . basename($filePath);
    if (!copy($filePath, $tempPath)) {
        throw new Exception("No se pudo copiar el archivo temporalmente", 500);
    }
    logger("Archivo temporal copiado correctamente: '{$tempPath}'");
    return $tempPath;
}
/**
 * Procesa datos de una planilla para extraer información de proyectos y empresas
 * 
 * @param array $sheetData Datos de la planilla a procesar
 * @param array $estadoDesc Estados válidos de proyectos
 * @param array $proyectosDBNom Nombres de proyectos existentes en la base de datos
 * @param array $empresasDBDesc Nombres de empresas existentes en la base de datos
 * @param int $ProyectoResponsable ID del responsable por defecto
 * @param int $ProyectoProcesos ID de la plantilla de procesos por defecto
 * @param int $ProyectoDuracion Duración en días por defecto
 * @param string $fechaActual Fecha de inicio para nuevos proyectos
 * @param string $fechaFin Fecha de fin calculada para nuevos proyectos
 * @return array Devuelve un array con empresas nuevas, proyectos nuevos y todos los proyectos de la planilla
 */
function procesarDatosPlanilla(
    array $sheetData,
    array $estadoDesc,
    array $proyectosDBNom,
    array $empresasDBDesc,
    $ProyectoResponsable,
    $ProyectoProcesos,
    $ProyectoDuracion,
    $fechaActual,
    $fechaFin
): array {
    try {

        $empresasNuevas = [];
        $proyectosPlanilla = [];
        $proyectosNuevos = [];
        $fecha_creacion = date('Y-m-d H:i');

        // Convertir arrays de búsqueda a conjuntos (utilizando claves como valores para búsqueda O(1))
        $estadosValidos = array_flip($estadoDesc);
        $proyectosExistentes = array_flip($proyectosDBNom);
        $empresasExistentes = array_flip($empresasDBDesc);

        // Array para controlar nombres de proyectos ya procesados en la planilla
        $nombresProyectosProcesados = [];

        foreach ($sheetData as $fila) {
            // Extraer y limpiar datos de la fila

            $nombre = str_ucwords($fila[0]);

            $datos = [
                'nombre' => str_ucwords($fila[0]),
                'descripcion' => trim($fila[2]),
                'empresa' => str_ucwords(trim($fila[3])),
                'observaciones' => trim($fila[4]),
                'estado' => str_ucwords(trim($fila[6])),
                'responsable' => $ProyectoResponsable,
                'procesos' => $ProyectoProcesos,
                'duracion' => $ProyectoDuracion,
                'inicio' => $fechaActual,
                'fin' => $fechaFin,
                'fecha_creacion' => $fecha_creacion
            ];

            // Validar estado del proyecto
            if (!isset($estadosValidos[$datos['estado']])) {
                logger("Se omite registro ya que el estado no existe en la base de datos: {$datos['estado']}");
                continue;
            }

            // Validar si la empresa está vacía
            if (empty($datos['empresa'])) {
                logger("Se omite registro ya que la empresa está vacía.");
                continue;
            }

            // Validar si el nombre está vacío
            if (empty($datos['nombre'])) {
                logger("Se omite registro ya que el nombre está vacío.");
                continue;
            }

            // Validar si ya procesamos un proyecto con este nombre en esta misma planilla
            if (isset($nombresProyectosProcesados[$nombre])) {
                logger("Se omite registro duplicado para el proyecto: {$nombre}");
                continue;
            }

            // Marcar este nombre de proyecto como procesado para evitar duplicados
            $nombresProyectosProcesados[$nombre] = true;

            // Guardar todos los proyectos válidos de la planilla
            $proyectosPlanilla[] = $datos;

            // Verificar si el proyecto es nuevo
            if (!isset($proyectosExistentes[$datos['nombre']])) {
                $proyectosNuevos[] = $datos;
                // Agregar al conjunto para evitar duplicados en las siguientes iteraciones
                $proyectosExistentes[$datos['nombre']] = true;
            }

            // Verificar si la empresa es nueva
            if (!isset($empresasExistentes[$datos['empresa']])) {
                $empresasNuevas[] = $datos['empresa'];
                // Agregar al conjunto para evitar duplicados en las siguientes iteraciones
                $empresasExistentes[$datos['empresa']] = true;
            }
        }

        $result = [
            'empresasNuevas' => $empresasNuevas,
            'proyectosPlanilla' => $proyectosPlanilla,
            'proyectosNuevos' => $proyectosNuevos,
        ];

        return $result;

    } catch (\Throwable $th) {
        logger("Error al procesar datos de la planilla: " . $th->getMessage());
        return [];
    } finally {
        // Liberar memoria
        unset($estadosValidos);
        unset($proyectosExistentes);
        unset($empresasExistentes);
        unset($nombresProyectosProcesados);
    }
}
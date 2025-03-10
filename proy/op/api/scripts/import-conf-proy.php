<?php
require __DIR__ . '../../../../../config/conect_pdo.php';
date_default_timezone_set('America/Argentina/Buenos_Aires');
require __DIR__ . '/funciones-import.php';
$dirname = __DIR__ . '../../archivos/logs';
try {

    /**
     * +---------------------------------------------------------------------+
     * | INDICE DE SCRIPTS DE IMPORTACIÓN DE PROYECTOS                       |
     * +---------------------------------------------------------------------+
     * | (0) Eliminar archivos de logs antiguos                              |
     * | (1) Importar configuración de proyectos desde archivo .json         |
     * | (2) Obtener y validar la extension del archivo                      |
     * | (3) Leer el archivo excel de planilla de proyectos                  |
     * | (3.1) Filtrar los datos obligatorios de la planilla de proyectos    |
     * | (4) Procesar los datos de la planilla y crear una array asociativo  |
     * | (5) Crear empresas nuevas en la base de datos si existen.           |
     * | (6) Verificar si los proyectos únicos existen en la base de         |
     * | datos y crearlos si no existen.                                     |
     * | (7) Actualizar array de proyectos con ID de empresa y estado        |
     * | (8) Actualizar estados de proyectos en la base de datos             |
     * | (9) Fin de importación de configuración de proyectos                |
     * +---------------------------------------------------------------------+
     */

    /**
     * +---------------------------------------------------------------+
     * | (0) Eliminar archivos de logs antiguos                        |    
     * +---------------------------------------------------------------+
     */
    deleteOldLogs(2, __DIR__ . '../../archivos/logs');
    /**
     * +---------------------------------------------------------------+
     * | (1) Importar configuración de proyectos desde archivo .json   |
     * +---------------------------------------------------------------+
     * | - Importar configuración de proyectos desde archivo .json     |
     * | - Archivo de configuración: conf-import-proy.json             |
     * +---------------------------------------------------------------+
     */
    logger('Inicio de importación de configuración de proyectos');
    $pathArchivo = __DIR__ . '../../archivos/conf-import-proy.json';

    if (!file_exists($pathArchivo)) {
        throw new Exception("No existe el archivo", 400);
    }

    $configuracion = json_decode(file_get_contents($pathArchivo), true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error al decodificar el archivo JSON: " . json_last_error_msg(), 400);
    }

    $ProyectoPlanilla = $configuracion['ProyectoPlanilla'];
    $ProyectoProcesos = $configuracion['ProyectoProcesos'];
    $ProyectoProcesosStr = $configuracion['ProyectoProcesosStr'];
    $ProyectoResponsable = $configuracion['ProyectoResponsable'];
    $ProyectoResponsableStr = $configuracion['ProyectoResponsableStr'];
    $ProyectoDuracion = $configuracion['ProyectoDuracion'];
    $ProyectoImportar = $configuracion['ProyectoImportar'];
    /** 
     * +---------------------------------------------------------------+
     * | (1) Fin de importacion de configuración de proyectos          |
     * +---------------------------------------------------------------+ 
     */
    logger('Configuración de proyectos importada correctamente');
    if ($ProyectoImportar != 'true') {
        throw new Exception("No se encuentra activa la importación de planilla.", 400);
    }
    logger('Inicio de importación de proyectos');
    /**
     * +---------------------------------------------------------------+
     * | (2) Obtener y validar la extension del archivo                |
     * +---------------------------------------------------------------+
     */
    logger('Validación de extensión del archivo');
    if (!file_exists($ProyectoPlanilla)) {
        throw new Exception("El archivo no existe", 400);
    }

    $tempFilePath = null;

    $pathInfo = pathinfo($ProyectoPlanilla);
    $extension = $pathInfo['extension'];
    $filename = $pathInfo['filename'];
    $dirname = $pathInfo['dirname'];

    // obtener la fecha del archivo
    $fechaArchivo = date('d/m/Y H:i:s', filemtime($ProyectoPlanilla));

    if (isFileInUse($ProyectoPlanilla)) {
        logger("El archivo '{$filename}' está en uso por otro programa, intentando copiar temporalmente...");
        $tempFilePath = copiarArchivoTemporal($ProyectoPlanilla);
        $ProyectoPlanilla = $tempFilePath;
    }

    $validExtensions = ['xlsm', 'xlsx'];

    if (!in_array($extension, $validExtensions)) {
        throw new Exception("La extensión del archivo no es válida", 400);
    }
    logger('Extensión del archivo válida');
    /**
     * +---------------------------------------------------------------+
     * | (2) Fin de validación de extensión del archivo                |
     * +---------------------------------------------------------------+
     */
    logger("Lectura de archivo de planilla de proyectos '$filename.$extension'");
    /**
     * +---------------------------------------------------------------+
     * | (3) Leer el archivo excel de planilla de proyectos            |
     * +---------------------------------------------------------------+
     */
    $sheetData = [];
    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
    $spreadsheet = $reader->load($ProyectoPlanilla);
    $sheetData = $spreadsheet->getSheet(0)->toArray(null, true, false, false);
    $sheetData = array_slice($sheetData, 2); // Omitir las dos primeras filas

    // Eliminar el archivo temporal si se creó
    if ($tempFilePath !== null) {
        logger("Eliminando archivo temporal... '{$tempFilePath}'");
        unlink($tempFilePath);
    }

    $sheetData = array_map(function ($row) {
        return array_slice($row, 2, 7);
    }, $sheetData); // Omitir las dos primeras columnas y obtener solo las 7 columnas necesarias
    logger('Archivo de planilla de proyectos leído correctamente');
    /**
     * +-------------------------------------------------------------------+
     * | (3.1) Filtrar los datos obligatorios de la planilla de proyectos  |
     * | -> Si la columna 0 es null Omitir (nombre del proyecto)           |
     * | -> Si la columna 3 es null Omitir la fila (empresa del proyecto)  |
     * | -> Si la columna 6 es null Omitir la fila (estado del proyecto)   |
     * +-------------------------------------------------------------------+
     */
    logger('Filtrado de datos obligatorios de la planilla de proyectos');
    $sheetData = array_filter($sheetData, function ($row) {
        return $row[6] !== null && $row[6] !== '' && $row[0] !== null && $row[0] !== '' && $row[3] !== null && $row[3] !== '';
    });
    $sheetData = array_values($sheetData); // Reindexar el array de datos
    logger('Filtrado de datos completado. Filas restantes: ' . count($sheetData));
    if (empty($sheetData)) {
        throw new Exception("No hay datos en la planilla", 400);
    }
    /**
     * +-------------------------------------------------------------------------+
     * | (3.1) Fin de filtrado de datos obligatorios de la planilla de proyectos |
     * | (3) Fin de lectura de archivo de planilla de proyectos        |
     * | > Como resultado obtenemos un array $sheetData                |
     * +---------------------------------------------------------------+
     */

    /**
     * +---------------------------------------------------------------------+
     * | (4) Procesar los datos de la planilla y crear una array asociativo  |
     * | de proyectos con los datos extraídos del archivo Excel.             |
     * +---------------------------------------------------------------------+
     */

    // Obtener los datos de la base de datos
    $proy_proyectos = datos_db('proy_proyectos', $connpdo); // Proyectos.
    $proy_estados = datos_db('proy_estados', $connpdo); // Estados.
    $proy_empresas = datos_db('proy_empresas', $connpdo); // Empresas.

    // Fecha Inicio del proyecto (Fecha actual)
    $fechaActual = date('Y-m-d');
    // Fecha de finalización del proyecto (Fecha actual + Duración definidia en configuración)
    $fechaFin = sumar_dias($fechaActual, $ProyectoDuracion);

    $proyectosDB = $proy_proyectos['data']; // array de proyectos de la base de datos
    $proyectosDBNom = $proy_proyectos['keys']; // array de nombres de proyectos de la base de datos

    $estadosDB = $proy_estados['data']; // array de estados de la base de datos
    $estadoDesc = $proy_estados['keys']; // array de estados desc de la base de datos

    $empresasDB = $proy_empresas['data']; // array de empresas de la base de datos
    $empresasDBDesc = $proy_empresas['keys']; // array de descripciones de empresas de la base de datos

    $empresasNuevas = []; // Array de empresas nuevas
    $proyectosPlanilla = []; // Array de proyectos de la planilla
    $proyectosNuevos = []; // Array de proyectos nuevos

    $procesarDatosPlanilla = procesarDatosPlanilla(
        $sheetData,
        $estadoDesc,
        $proyectosDBNom,
        $empresasDBDesc,
        $ProyectoResponsable,
        $ProyectoProcesos,
        $ProyectoDuracion,
        $fechaActual,
        $fechaFin);

    $empresasNuevas = $procesarDatosPlanilla['empresasNuevas']; // Array de empresas nuevas
    $proyectosPlanilla = $procesarDatosPlanilla['proyectosPlanilla']; // Array de proyectos de la planilla
    $proyectosNuevos = $procesarDatosPlanilla['proyectosNuevos']; // Array de proyectos nuevos

    // echo json_encode($proyectosNuevos, opt_encode());
    // exit;

    // foreach ($sheetData as $key => $value) {

    //     $nombre = trim($value[0]); // Nombre del proyecto (Columna 0)
    //     $empresa = str_ucwords($value[3]); // Empresa del proyecto (Columna 3)
    //     $estado = str_ucwords($value[6]); // Estado del proyecto (Columna 6)
    //     $descripcion = trim($value[2]); // Descripción del proyecto
    //     $observaciones = trim($value[4]); // Observaciones del proyecto
    //     $fecha_creacion = date('Y-m-d H:i'); // Fecha de creación del proyecto

    //     /**
    //      * +---------------------------------------------------------------+
    //      * | Validar si el estado del proyecto existe en la base de datos  |
    //      * | Si no existe, omitir la fila.                                 |
    //      * +---------------------------------------------------------------+
    //      */

    //     if (!in_array($estado, $estadoDesc)) {
    //         logger("Se omite registro ya que el estado no existe en la base de datos: {$estado}");
    //         continue; // Omitir si el estado no existe en la base de datos
    //     }

    //     /**
    //      * +---------------------------------------------------------------+
    //      * | Validar si la empresa está vacía.                             |
    //      * | Si está vacía, omitir la fila.                                |
    //      * +---------------------------------------------------------------+
    //      */
    //     if (empty($empresa)) {
    //         logger("Se omite registro ya que la empresa está vacía.");
    //         continue;
    //     }

    //     /**
    //      * +-------------------------------------------------------------------+
    //      * | Validar si el proyecto no se encuentra en el array proyectosDBNom |
    //      * | y almacenar el proyecto si es nuevo en el array proyectosNuevos   |
    //      * +-------------------------------------------------------------------+
    //      */

    //     if (!in_array(str_ucwords($nombre), $proyectosDBNom)) {
    //         $proyectosNuevos[] = [
    //             'nombre' => $nombre, // Nombre del proyecto
    //             'descripcion' => $descripcion, // Descripción del proyecto
    //             'empresa' => $empresa, // Empresa del proyecto (Columna 3)
    //             'observaciones' => $observaciones, // Observaciones del proyecto
    //             'estado' => $estado, // Estado del proyecto (Columna 6)
    //             'responsable' => $ProyectoResponsable, // ID Responsable del proyecto
    //             'procesos' => $ProyectoProcesos, // ID Plantilla de Procesos del proyecto
    //             'duracion' => $ProyectoDuracion, // Duración del proyecto en días
    //             'inicio' => $fechaActual, // Fecha de inicio del proyecto
    //             'fin' => $fechaFin, // Fecha de finalización del proyecto
    //             'fecha_creacion' => $fecha_creacion, // Fecha de creación del proyecto
    //         ];
    //     }

    //     /**
    //      * +-----------------------------------------------------------------+
    //      * | Validar si la empresa no se encuentra en el array empresasDBDesc  |
    //      * | y almacenar la empresa si es nueva en el array empresasNuevas   |
    //      * +-----------------------------------------------------------------+
    //      */
    //     if (!in_array($empresa, $empresasDBDesc)) {
    //         $empresasNuevas[] = $empresa;
    //     }

    //     /**
    //      * +---------------------------------------------------------------+
    //      * | Procesar los datos de la fila y crear un array asociativo     |
    //      * | con los datos extraídos de la fila.                           |
    //      * +---------------------------------------------------------------+
    //      */

    //     $proyectosPlanilla[] = [
    //         'nombre' => $nombre, // Nombre del proyecto
    //         'descripcion' => $descripcion, // Descripción del proyecto
    //         'empresa' => $empresa, // Empresa del proyecto (Columna 3)
    //         'observaciones' => $observaciones, // Observaciones del proyecto
    //         'estado' => $estado, // Estado del proyecto (Columna 6)
    //         'responsable' => $ProyectoResponsable, // ID Responsable del proyecto
    //         'procesos' => $ProyectoProcesos, // ID Plantilla de Procesos del proyecto
    //         'duracion' => $ProyectoDuracion, // Duración del proyecto en días
    //         'inicio' => $fechaActual, // Fecha de inicio del proyecto
    //         'fin' => $fechaFin, // Fecha de finalización del proyecto
    //         'fecha_creacion' => $fecha_creacion, // Fecha de creación del proyecto
    //     ];
    // }
    /**
     * +------------------------------------------------------------+
     * | (4) Fin de procesamiento de datos de sheetData             |
     * | > Como resultado obtenemos un array asociativo $proyectosPlanilla  |
     * +------------------------------------------------------------+
     */
    logger("Procesamiento de planilla '{$filename}' completado. " . count($proyectosPlanilla) . " proyectos procesados");
    /**
     * +---------------------------------------------------------------+
     * | (5) Crear empresas nuevas en la base de datos si existen.     |
     * +---------------------------------------------------------------+
     */
    logger('Empresas nuevas: ' . count($empresasNuevas));
    // Si existen empresas nuevas que no existen en la base de datos, crearlas
    if (!empty($empresasNuevas)) {
        logger('Creando de empresas nuevas . . .');
        // Crear las empresas nuevas si existen
        $crearEmpresas = crear_empresas_nuevas($connpdo, $empresasNuevas);
        if ($crearEmpresas) {
            // Actualizar las empresas de la base de datos
            $proy_empresas = datos_db('proy_empresas', $connpdo);
            // Obtener las empresas de la base de datos
            $empresasDB = $proy_empresas['data'];
            $empresasDBDesc = $proy_empresas['keys'];
        }
    }
    /**
     * +---------------------------------------------------------------+
     * | (5) Fin de verificación de empresas nuevas                    |
     * +---------------------------------------------------------------+
     */

    /**
     * +---------------------------------------------------------------+
     * | (6) Verificar si los proyectos únicos existen en la base de   |
     * | datos y crearlos si no existen.                               |
     * +---------------------------------------------------------------+
     */
    logger('Verificación de proyectos únicos en la base de datos');

    $ProyDB = $proy_proyectos['data']; // Obtener los proyectos de la base de datos
    $ProyDBNom = $proy_proyectos['keys']; // Obtener solo los nombres de los proyectos

    logger('Cantidad de proyectos nuevos: ' . count($proyectosNuevos));
    // Crear los proyectos nuevos si existen
    $crearProyectos = crear_proyectos_nuevos($connpdo, $proyectosNuevos, $empresasDB, $estadosDB);
    if ($crearProyectos) {
        // Si se crearon los proyectos nuevos, actualizar los proyectos de la base de datos
        $proy_proyectos = datos_db('proy_proyectos', $connpdo);
        // Actualizar los proyectos de la base de datos
        $ProyDB = $proy_proyectos['data'];
        $ProyDBNom = $proy_proyectos['keys'];
        logger('Proyectos creados correctamente: ' . count($proyectosNuevos));
    }
    /**
     * +---------------------------------------------------------------+
     * | (6) Fin de verificación de proyectos nuevos                   |
     * +---------------------------------------------------------------+
     */

    /**
     * +---------------------------------------------------------------+
     * | (7) Actualizar array de proyectos con ID de empresa y estado  |
     * +---------------------------------------------------------------+
     */
    logger('Actualizar proyectos con ID de empresa y estado');
    $proyectos = [];
    foreach ($proyectosPlanilla as $key => $proyecto) {
        $proyecto['emprID'] = search_value($empresasDBDesc, $proyecto['empresa'], 'EmpID');
        $proyecto['estaID'] = search_value($estadosDB, $proyecto['estado'], 'EstID');
        $proyecto['nombre'] = str_ucwords($proyecto['nombre']);
        ksort($proyecto); // Ordenar el array por clave
        // Agregar el proyecto al array de proyectos actualizado
        $proyectos[] = $proyecto;
    }
    /**
     * +---------------------------------------------------------------+
     * | (7) Fin de actualización de de proyectos                      |
     * +---------------------------------------------------------------+
     */

    /**
     * +---------------------------------------------------------------+
     * | (8) Actualizar estados de proyectos en la base de datos       |
     * +---------------------------------------------------------------+
     */
    $diffProy = [];
    logger('Comparación de proyectos en la base de datos con proyectos actualizados');
    // Comparar los proyectos de la base de datos con los proyectos actualizados y encontrar las diferencias comparando las claves estaID de proyectos y ProyEsta de ProyDB
    $diffProy = array_filter(
        $proyectos,
        function ($proyecto) use ($ProyDB) {
            $proyectoDB = array_filter(
                $ProyDB,
                function ($proyectoDB) use ($proyecto) {
                    return $proyectoDB['ProyNom'] === $proyecto['nombre'] && $proyectoDB['ProyEsta'] !== $proyecto['estaID'];
                });
            return !empty($proyectoDB);
        });
    $updateEstados = 0;
    if ($diffProy) {
        logger('Número de proyectos con diferencias de estado: ' . count($diffProy));
        logger('Actualización de estados de proyectos en la base de datos');
        $updateEstados = update_estados_proy($diffProy, $connpdo);
    }
    /**
     * +---------------------------------------------------------------+
     * | (9) Fin de actualización de estados de proyectos              |
     * +---------------------------------------------------------------+
     */

    logger('Fin del proceso de actualización de proyectos.');
    logger('→ Proceso de importación de proyectos finalizado correctamente ←');

    echo json_encode(
        [
            'status' => 200,
            'data' => [
                'proyectos_creados' => count($proyectosNuevos),
                'proyectos_actualizados' => $updateEstados,
                'empresas_creadas' => count($empresasNuevas),
                'archivo' => "{$filename}.{$extension} ({$fechaArchivo})",
                'linkArchivo' => $ProyectoPlanilla,
            ],
            'message' => 'Proceso finalizado correctamente',
        ], opt_encode()
    );
    $connpdo = null;
} catch (\Throwable $th) {
    http_response_code(400);
    logger('→ Fin de importación de proyectos con errores ←');
    logger('→ error: "' . $th->getMessage() . '" ←');
    logger('→ Código de error: ' . $th->getCode() . ' ←');
    logger('+---------------------------------------------------------------+');
    echo json_encode(
        [
            'status' => $th->getCode(),
            'error' => $th->getMessage(),
            'message' => 'Error al importar los proyectos'
        ], opt_encode()
    );
    if (is_writable($dirname)) {
        file_put_contents($dirname . '/' . date('Y-m-d') . '_error_import.log', $th->getMessage(), FILE_APPEND);
    } else {
        logger('No se tienen permisos para escribir en la carpeta: ' . $dirname);
    }
}
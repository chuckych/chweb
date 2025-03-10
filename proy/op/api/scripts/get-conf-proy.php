<?php

try {
    $pathArchivo = __DIR__ . '../../archivos/conf-import-proy.json';
    if (!file_exists($pathArchivo)) {
        $configuracion = [
            'ProyectoDuracion' => 30,
            'ProyectoImportar' => 0,
            'ProyectoProcesos' => 0,
            'ProyectoResponsable' => 0,
            'ProyectoPlanilla' => '',
            'ProyectoProcesosStr' => '',
            'ProyectoResponsableStr' => ''
        ];
        file_put_contents($pathArchivo, json_encode($configuracion, JSON_PRETTY_PRINT));
    }
    $configuracion = json_decode(file_get_contents($pathArchivo), true);

    return Flight::json(
        [
            'status' => 200,
            'message' => 'get-conf-proy',
            'data' => $configuracion
        ]
    );

} catch (\Throwable $th) {
    return Flight::json(
        [
            'status' => 500,
            'error' => 'No se pudo obtener la configuración',
            'message' => $th->getMessage()
        ]
    );
}
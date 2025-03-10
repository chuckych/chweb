<?php
/**
 * @var $request \Flight
 */

$ProyectoDuracion = intval($request->data->ProyectoDuracion);
$ProyectoImportar = $request->data->ProyectoImportar ?? 'false';
$ProyectoProcesos = $request->data->ProyectoProcesos ?? 0;
$ProyectoResponsable = $request->data->ProyectoResponsable ?? 0;
$ProyectoPlanilla = $request->data->ProyectoPlanilla ?? '';
$ProyectoProcesosStr = $request->data->ProyectoProcesosStr ?? '';
$ProyectoResponsableStr = $request->data->ProyectoResponsableStr ?? '';

try {

    if (!file_exists($ProyectoPlanilla)) {
        throw new Exception("No existe el archivo", 1);
    }

    $configuracion = [
        'ProyectoDuracion' => $ProyectoDuracion ? $ProyectoDuracion : 30,
        'ProyectoImportar' => $ProyectoImportar,
        'ProyectoProcesos' => $ProyectoProcesos,
        'ProyectoResponsable' => $ProyectoResponsable,
        'ProyectoPlanilla' => $ProyectoPlanilla,
        'ProyectoProcesosStr' => $ProyectoProcesosStr,
        'ProyectoResponsableStr' => $ProyectoResponsableStr
    ];

    $pathArchivo = __DIR__ . '../../archivos/conf-import-proy.json';

    file_put_contents($pathArchivo, json_encode($configuracion, JSON_PRETTY_PRINT));

} catch (\Throwable $th) {
    return Flight::json(
        [
            'status' => 500,
            'error' => 'No se pudo guardar la configuración',
            'message' => $th->getMessage()
        ]
    );
}

return Flight::json(
    [
        'status' => 200,
        'message' => 'set-conf-proy',
        'ProyectoDuracion' => $ProyectoDuracion,
    ]
);
<?php
require __DIR__ . '/../fn.php';
header("Content-Type: application/json");
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
tz();
tzLang();
errorReport();

$control->check_get(['proceso'], $request->base . $request->url);
$control->check_method("POST");
$proceso = $control->check_param($_GET['proceso'], '1', 'proceso');
$control->check_json();

$checkArray = new fnArray();
$validar = new validaRequest();

$countInsert = 0;
$countUpdate = 0;

try {
    $payload = json_decode($request->getBody(), true);
    $totalRecibido = count($payload) ?? 0;
    $payload = $checkArray->removeEmptySubarrays($payload);

    if (empty($payload)) {
        http_response_code(400);
        (response('', 0, 'No hay Datos', 400, $time_start, 0, $idCompany));
        exit;
    }
} catch (Exception $e) {
    error_log("Error al procesar payload JSON: " . $e->getMessage());
    http_response_code(400);
    (response('', 0, 'Error al procesar datos recibidos', 400, $time_start, 0, $idCompany));
    exit;
}

foreach ($payload as &$element) {
    $element['FechaHora'] = date('Y-m-d H:i:s');
}
// La función recibe el array como parámetro y devuelve un array con los errores encontrados. También hace un unset de los elementos que tienen errores en el array original. Para usar la función, puedes hacer lo siguiente:
$requestPayload = $validar->interperson($payload);

$payload = $requestPayload['payload'];
$errores = $requestPayload['errores'];
try {
    // $stmt = $dbApiQuery("SELECT * FROM INTERPERSONAL");
    $stmt = $db->query("SELECT * FROM INTERPERSONAL");
} catch (Exception $e) {
    error_log("Error al consultar tabla INTERPERSONAL: " . $e->getMessage());
    http_response_code(500);
    (response('', 0, 'Error al consultar base de datos', 500, $time_start, 0, $idCompany));
    exit;
}

if (!empty($stmt)) { /** Si encontramos datos en la tabla INTERPERSONAL buscamos buscamos duplicados y hacemos update de los mismos */

    $compara = $checkArray->comparar($payload, $stmt, 'LegNume');

    $payload = $compara['no_duplicados']; // obtenemos los no duplicados para insertarlos luego

    $payload_duplicados = $compara['duplicados1'];

    if ($payload_duplicados) { // si encontramos duplicados
        $countUpdate = count($payload_duplicados); // contamos los duplicados
        $payload_duplicados_formatted = []; // array temporal para evitar duplicados
        foreach ($payload_duplicados as $key => $v) { // ordenamos el array 
            extract($v);

            // Formatear fechas correctamente, manejando el valor "0" o vacío
            $fechaNacimiento = '1753-01-01';
            $fechaIngreso = '1753-01-01';
            $fechaEgreso = '1753-01-01';

            if (!empty($LegFeNa) && $LegFeNa !== '0') {
                $tempDate = fechFormat($LegFeNa, "Y-m-d");
                $fechaNacimiento = ($tempDate && $tempDate !== '0') ? $tempDate : '1753-01-01';
            }

            if (!empty($LegFeIn) && $LegFeIn !== '0') {
                $tempDate = fechFormat($LegFeIn, "Y-m-d");
                $fechaIngreso = ($tempDate && $tempDate !== '0') ? $tempDate : '1753-01-01';
            }

            if (!empty($LegFeEg) && $LegFeEg !== '0') {
                $tempDate = fechFormat($LegFeEg, "Y-m-d");
                $fechaEgreso = ($tempDate && $tempDate !== '0') ? $tempDate : '1753-01-01';
            }

            $payload_duplicados_formatted[] = array(
                "LegNume" => $LegNume ?? '',
                "LegApNo" => $LegApNo ?? '',
                "LegTDoc" => $LegTDoc ?? '',
                "LegDocu" => $LegDocu ?? '',
                "LegCUIT" => $LegCUIT ?? '',
                "LegDomi" => $LegDomi ?? '',
                "LegDoNu" => $LegDoNu ?? '',
                "LegDoPi" => $LegDoPi ?? '',
                "LegDoDP" => $LegDoDP ?? '',
                "LegDoOb" => $LegDoOb ?? '',
                "LegCOPO" => $LegCOPO ?? '',
                "LegTel1" => $LegTel1 ?? '',
                "LegTeO1" => $LegTeO1 ?? '',
                "LegTel2" => $LegTel2 ?? '',
                "LegTeO2" => $LegTeO2 ?? '',
                "LegTel3" => $LegTel3 ?? '',
                "LegMail" => $LegMail ?? '',
                "LegEsCi" => ($LegEsCi ?? '0') ? $LegEsCi : '0',
                "LegSexo" => ($LegSexo ?? '0') ? $LegSexo : '0',
                "LegFeNa" => $fechaNacimiento,
                "LegTipo" => ($LegTipo ?? '0') ? $LegTipo : '0',
                "LegFeIn" => $fechaIngreso,
                "LegFeEg" => $fechaEgreso,
                "NacCodi" => ($NacCodi ?? '0') ? $NacCodi : '0',
                "NacDesc" => $NacDesc ?? '',
                "ProCodi" => ($ProCodi ?? '0') ? $ProCodi : '0',
                "ProDesc" => $ProDesc ?? '',
                "LocCodi" => ($LocCodi ?? '0') ? $LocCodi : '0',
                "LocDesc" => $LocDesc ?? '',
                "EmpCodi" => ($EmpCodi ?? '0') ? $EmpCodi : '0',
                "EmpRazon" => $EmpRazon ?? '',
                "PlaCodi" => ($PlaCodi ?? '0') ? $PlaCodi : '0',
                "PlaDesc" => $PlaDesc ?? '',
                "SucCodi" => ($SucCodi ?? '0') ? $SucCodi : '0',
                "SucDesc" => $SucDesc ?? '',
                "SecCodi" => ($SecCodi ?? '0') ? $SecCodi : '0',
                "SecDesc" => $SecDesc ?? '',
                "Se2Codi" => ($Se2Codi ?? '0') ? $Se2Codi : '0',
                "Se2Desc" => $Se2Desc ?? '',
                "GruCodi" => ($GruCodi ?? '0') ? $GruCodi : '0',
                "GruDesc" => $GruDesc ?? '',
                "ConCodi" => ($ConCodi ?? '0') ? $ConCodi : '0',
                "ConDesc" => $ConDesc ?? '',
                "FechaHora" => $FechaHora
            );
        }
        try {
            $update = '';
            foreach ($payload_duplicados_formatted as $record) {
                $conditionValue = $record['LegNume'];
                $update .= "UPDATE INTERPERSONAL SET ";
                foreach ($record as $column => $value) {
                    if ($column !== 'LegNume') {
                        $update .= "$column = '$value', ";
                    }
                }
                $update = rtrim($update, ", ");
                $update .= " WHERE LegNume = '$conditionValue'; ";
            }
            // error_log("Query UPDATE: " . $update);
            $db->save($update);
        } catch (Exception $e) {
            error_log("Error al actualizar registros duplicados: " . $e->getMessage());
            http_response_code(500);
            (response('', 0, 'Error al actualizar registros existentes', 500, $time_start, 0, $idCompany));
            exit;
        }
    }
}

$cols = [
    "LegNume",
    "LegApNo",
    "LegTDoc",
    "LegDocu",
    "LegCUIT",
    "LegDomi",
    "LegDoNu",
    "LegDoPi",
    "LegDoDP",
    "LegDoOb",
    "LegCOPO",
    "LegTel1",
    "LegTeO1",
    "LegTel2",
    "LegTeO2",
    "LegTel3",
    "LegMail",
    "LegEsCi",
    "LegSexo",
    "LegFeNa",
    "LegTipo",
    "LegFeIn",
    "LegFeEg",
    "NacCodi",
    "NacDesc",
    "ProCodi",
    "ProDesc",
    "LocCodi",
    "LocDesc",
    "EmpCodi",
    "EmpRazon",
    "PlaCodi",
    "PlaDesc",
    "SucCodi",
    "SucDesc",
    "SecCodi",
    "SecDesc",
    "Se2Codi",
    "Se2Desc",
    "GruCodi",
    "GruDesc",
    "ConCodi",
    "ConDesc",
    "FechaHora",
];

if ($payload) {
    try {
        $db->save("UPDATE BDCONEXIONES set CnFecUpd = '20230101' WHERE CnCodi = '0'");
        $countInsert = count($payload);

        foreach ($payload as $keys => $v) {
            extract($v); // extraemos el array para poder usar las variables con su nombre original y no como un array asociativo, esto nos facilita la insercion de los datos en la tabla interpersonal

            // Formatear fechas correctamente, manejando el valor "0" o vacío
            $fechaNacimiento = '1753-01-01';
            $fechaIngreso = '1753-01-01';
            $fechaEgreso = '1753-01-01';

            if (!empty($LegFeNa) && $LegFeNa !== '0') {
                $tempDate = fechFormat($LegFeNa, "Y-m-d");
                $fechaNacimiento = ($tempDate && $tempDate !== '0') ? $tempDate : '1753-01-01';
            }

            if (!empty($LegFeIn) && $LegFeIn !== '0') {
                $tempDate = fechFormat($LegFeIn, "Y-m-d");
                $fechaIngreso = ($tempDate && $tempDate !== '0') ? $tempDate : '1753-01-01';
            }

            if (!empty($LegFeEg) && $LegFeEg !== '0') {
                $tempDate = fechFormat($LegFeEg, "Y-m-d");
                $fechaEgreso = ($tempDate && $tempDate !== '0') ? $tempDate : '1753-01-01';
            }

            $dataPayload[] = [
                "LegNume" => $LegNume ?? '',
                "LegApNo" => $LegApNo ?? '',
                "LegTDoc" => $LegTDoc ?? '',
                "LegDocu" => $LegDocu ?? '',
                "LegCUIT" => $LegCUIT ?? '',
                "LegDomi" => $LegDomi ?? '',
                "LegDoNu" => $LegDoNu ?? '',
                "LegDoPi" => $LegDoPi ?? '',
                "LegDoDP" => $LegDoDP ?? '',
                "LegDoOb" => $LegDoOb ?? '',
                "LegCOPO" => $LegCOPO ?? '',
                "LegTel1" => $LegTel1 ?? '',
                "LegTeO1" => $LegTeO1 ?? '',
                "LegTel2" => $LegTel2 ?? '',
                "LegTeO2" => $LegTeO2 ?? '',
                "LegTel3" => $LegTel3 ?? '',
                "LegMail" => $LegMail ?? '',
                "LegEsCi" => ($LegEsCi ?? '0') ? $LegEsCi : '0',
                "LegSexo" => ($LegSexo ?? '0') ? $LegSexo : '0',
                "LegFeNa" => $fechaNacimiento,
                "LegTipo" => ($LegTipo ?? '0') ? $LegTipo : '0',
                "LegFeIn" => $fechaIngreso,
                "LegFeEg" => $fechaEgreso,
                "NacCodi" => ($NacCodi ?? '0') ? $NacCodi : '0',
                "NacDesc" => $NacDesc ?? '',
                "ProCodi" => ($ProCodi ?? '0') ? $ProCodi : '0',
                "ProDesc" => $ProDesc ?? '',
                "LocCodi" => ($LocCodi ?? '0') ? $LocCodi : '0',
                "LocDesc" => $LocDesc ?? '',
                "EmpCodi" => ($EmpCodi ?? '0') ? $EmpCodi : '0',
                "EmpRazon" => $EmpRazon ?? '',
                "PlaCodi" => ($PlaCodi ?? '0') ? $PlaCodi : '0',
                "PlaDesc" => $PlaDesc ?? '',
                "SucCodi" => ($SucCodi ?? '0') ? $SucCodi : '0',
                "SucDesc" => $SucDesc ?? '',
                "SecCodi" => ($SecCodi ?? '0') ? $SecCodi : '0',
                "SecDesc" => $SecDesc ?? '',
                "Se2Codi" => ($Se2Codi ?? '0') ? $Se2Codi : '0',
                "Se2Desc" => $Se2Desc ?? '',
                "GruCodi" => ($GruCodi ?? '0') ? $GruCodi : '0',
                "GruDesc" => $GruDesc ?? '',
                "ConCodi" => ($ConCodi ?? '0') ? $ConCodi : '0',
                "ConDesc" => $ConDesc ?? '',
                "FechaHora" => $FechaHora
            ];
        }

        $values = [];
        foreach ($dataPayload as $item) {
            $values[] = "('" . implode("', '", $item) . "')";
        }

        $values = implode(", ", $values);

        $cols = (implode(',', $cols));

        $stmt = $db->save("INSERT INTO INTERPERSONAL($cols) VALUES $values");

        if (empty($stmt)) {
            error_log("Error al insertar registros en INTERPERSONAL: stmt vacío");
            http_response_code(400);
            (response('', 0, 'Error al insertar registros', 400, $time_start, 0, $idCompany));
            exit;
        }
    } catch (Exception $e) {
        error_log("Error al insertar nuevos registros: " . $e->getMessage());
        http_response_code(500);
        (response('', 0, 'Error al insertar nuevos registros en base de datos', 500, $time_start, 0, $idCompany));
        exit;
    }
}

try {

    $procesado = $proceso ? $ws->request('/INTERPERSONAL', 'POST', '{}') : '';

} catch (Exception $e) {
    error_log("Error al realizar request al web service INTERPERSONAL: " . $e->getMessage());
    $procesado = null;
}

$totalProcesados = $countInsert + $countUpdate;
$procesadoStr = ($procesado ?? '') === 'Proceso terminado' ? true : '';
$data = [
    // "total recibido" => $totalRecibido,
    "total procesado" => $totalProcesados,
    "total Insert" => $countInsert,
    "total Update" => $countUpdate,
    "proceso" => $procesadoStr,
    "proceso_msg" => ($procesado ?? '') === '' ? '' : $procesado,
    "errores" => $errores
];
http_response_code(200);
(response($data, $totalProcesados, 'OK', 200, $time_start, 0, $idCompany));
exit;
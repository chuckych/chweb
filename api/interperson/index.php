<?php
require __DIR__ . '../../fn.php';
header("Content-Type: application/json");
ini_set('max_execution_time', 900); //900 seconds = 15 minutes
tz();
tzLang();
errorReport();

$control->check_get(array('proceso'), $request->base . $request->url);
$control->check_method("POST");
$proceso = $control->check_param($_GET['proceso'], '1', 'proceso');
$control->check_json();

$checkArray = new fnArray();
$validar = new validaRequest();

$countInsert = 0;
$countUpdate = 0;
$payload = json_decode($request->getBody(), true);
$totalRecibido = count($payload) ?? 0;
$payload = $checkArray->removeEmptySubarrays($payload);

if (empty($payload)) {
    http_response_code(400);
    (response('', 0, 'No hay Datos', 400, $time_start, 0, $idCompany));
    exit;
}

foreach ($payload as &$element) {
    $element['FechaHora'] = date('Y-m-d H:i:s');
}
// La función recibe el array como parámetro y devuelve un array con los errores encontrados. También hace un unset de los elementos que tienen errores en el array original. Para usar la función, puedes hacer lo siguiente:
$requestPayload = $validar->interperson($payload);

$payload = $requestPayload['payload'];
$errores = $requestPayload['errores'];

// $stmt = $dbApiQuery("SELECT * FROM INTERPERSONAL");
$stmt = $db->query("SELECT * FROM INTERPERSONAL");

if (($stmt)) { /** Si encontramos datos en la tabla INTERPERSONAL buscamos buscamos duplicados y hacemos update de los mismos */

    $compara = $checkArray->comparar($payload, $stmt, 'LegNume');

    $payload = $compara['no_duplicados'];
    $payload_duplicados = $compara['duplicados1'];

    if ($payload_duplicados) { // si encontramos duplicados
        $countUpdate = count($payload_duplicados); // contamos los duplicados
        foreach ($payload_duplicados as $key => $v) { // ordenamos el array 
            extract($v);
            $payload_duplicados[] = array(
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
                "LegFeNa" => ($LegFeNa ?? '0') ? fechFormat($LegFeNa, "Ymd") : '1753-01-01',
                "LegTipo" => ($LegTipo ?? '0') ? $LegTipo : '0',
                "LegFeIn" => ($LegFeIn ?? '0') ? fechFormat($LegFeIn, "Ymd") : '1753-01-01',
                "LegFeEg" => ($LegFeEg ?? '0') ? fechFormat($LegFeEg, "Ymd") : '1753-01-01',
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
        $update = '';
        foreach ($payload_duplicados as $record) {
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
        $db->save($update);
    }
}

$cols = array(
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
);

if ($payload) {
    $db->save("UPDATE BDCONEXIONES set CnFecUpd = '20230101' WHERE CnCodi = '0'");
    $countInsert = count($payload);

    foreach ($payload as $keys => $v) {
        extract($v);
        $dataPayload[] = array(
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
            "LegFeNa" => ($LegFeNa ?? '0') ? fechFormat($LegFeNa, "Ymd") : '1753-01-01',
            "LegTipo" => ($LegTipo ?? '0') ? $LegTipo : '0',
            "LegFeIn" => ($LegFeIn ?? '0') ? fechFormat($LegFeIn, "Ymd") : '1753-01-01',
            "LegFeEg" => ($LegFeEg ?? '0') ? fechFormat($LegFeEg, "Ymd") : '1753-01-01',
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

    $values = array();
    foreach ($dataPayload as $item) {
        $values[] = "('" . implode("', '", $item) . "')";
    }

    $values = implode(", ", $values);

    // $params = (implode("','", $params));
    $cols = (implode(',', $cols));
    // print_r("INSERT INTO INTERPERSONAL($cols) VALUES $values");
    // exit;
    $stmt = $db->save("INSERT INTO INTERPERSONAL($cols) VALUES $values");

    if (empty($stmt)) {
        http_response_code(400);
        (response('', 0, 'Error', 400, $time_start, 0, $idCompany));
        exit;
    }
}

if ($proceso) { // si el parametro proceso viene en uno enviamos post a interpersonal y esperamos respuesta
    $procesado = $ws->request("/INTERPERSONAL", "POST");
} else {
    $procesado = $ws->request("/INTERPERSONAL", "POST", '', '', false);
}

$totalProcesados = $countInsert + $countUpdate;
$data = array(
    // "total recibido" => $totalRecibido,
    "total procesado" => $totalProcesados,
    "total Insert" => $countInsert,
    "total Update" => $countUpdate,
    "proceso" => ($procesado == null) ? '' : $procesado,
    "errores" => $errores
);
http_response_code(200);
(response($data, $totalProcesados, 'OK', 200, $time_start, 0, $idCompany));
exit;
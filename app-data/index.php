<?php
require __DIR__ . '/../config/session_start.php';
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../config/index.php';
require __DIR__ . '/fn_app-data.php';

ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");

if (!$_SESSION) {
    Flight::json(["error" => "Sesión finalizada."]);
    exit;
}
// sleep(1);
$token = sha1($_SESSION['RECID_CLIENTE']);

define('HOSTCHWEB', gethostCHWeb());
define('URLAPI', HOSTCHWEB . "/" . HOMEHOST);

clean_files('json/', 1, 'json');
clean_files('archivos/', 1, 'xls');

Flight::route('/novedades-all', function () {

    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/estruct/";
    $queryParams = array(
        "start" => 0,
        "length" => 5000,
        "Estruct" => "Nov",
        "Codi" => novedadesRol()
    );

    $data = ch_api($endpoint, '', 'GET', $queryParams); // Obtenemos las novedades
    $arrayData = json_decode($data, true);
    $novedades = $arrayData['DATA'] ?? [];

    $queryParams = array(
        "start" => 0,
        "length" => 5000,
        "Estruct" => "NovC",
    );
    $data = ch_api($endpoint, '', 'GET', $queryParams); // Obtenemos las causas de las novedades
    $arrayData = json_decode($data, true);
    $causas = $arrayData['DATA'] ?? [];

    $noveAgrupaPorTipo = array_reduce($novedades, function ($result, $item) {
        $key = $item['TipoDesc'];
        if (!isset($result[$key])) {
            $result[$key] = [];
        }
        $result[$key][] = $item;
        return $result;
    }, []);

    $arr = array(
        "novedades" => $novedades ?? [],
        "causas" => $causas ?? [],
        "agrupadas" => $noveAgrupaPorTipo ?? []
    );

    Flight::json($arr);
});
Flight::route('/novedades-agrupa', function () {
    // sleep('2');
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/estruct/";
    $queryParams = array(
        "start" => 0,
        "length" => 5000,
        "Estruct" => "Nov",
        "Codi" => novedadesRol()
    );

    $data = ch_api($endpoint, '', 'GET', $queryParams);

    $arrayData = json_decode($data, true);

    $novedades = $arrayData['DATA'] ?? [];

    $noveAgrupaPorTipo = array_reduce($novedades, function ($result, $item) {
        $key = $item['TipoDesc'];
        if (!isset($result[$key])) {
            $result[$key] = [];
        }
        $result[$key][] = $item;
        return $result;
    }, []);

    $json = array(
        "novedades" => ($noveAgrupaPorTipo) ?? [],
    );
    Flight::json($json);
});
Flight::route('/novedades/@NoveTipo/(@NoveCodi)', function ($NoveTipo, $NoveCodi) {
    // sleep('2');
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/estruct/";
    $method = 'GET';
    $queryParams = array(
        "start" => 0,
        "length" => 5000,
        "Estruct" => "Nov"
    );

    $data = ch_api($endpoint, '', $method, $queryParams);

    $arrayData = json_decode($data, true);

    if ($NoveTipo > 2) {
        $novedades = array_filter($arrayData['DATA'], function ($element) {
            return $element['Tipo'] > 2;
        });
    } else {
        $novedades = array_filter($arrayData['DATA'], function ($element) use ($NoveTipo) {
            return $element['Tipo'] == $NoveTipo;
        });
    }

    $json = array(
        "novedades" => array_values($novedades) ?? [],
        "causas" => (intval($NoveCodi) > 0) ? getNoveCausas($NoveCodi) ?? [] : [],
        "NoveCodi" => $NoveCodi ?? '',
        "NoveTipo" => $NoveTipo ?? ''
    );
    Flight::json($json);
});
Flight::route('/causas/@NoveCodi', function ($NoveCodi) {
    $json = array(
        "causas" => (getNoveCausas($NoveCodi)) ?? [],
    );
    Flight::json($json);
});
Flight::route('POST /ficha/@legajo/@fecha', function ($legajo, $fecha) {

    $opt = ["getNov" => "1", "getONov" => "0", "getHor" => "1", "getFic" => "1"];
    $data = [];
    $data = getFicNovHorSimple($legajo, $fecha, $opt);

    if ($data) {
        $data[0]['NoveDelete'] = $_SESSION['ABM_ROL']['bNov'];
        $data[0]['NoveAdd'] = $_SESSION['ABM_ROL']['aNov'];
    }
    // print_r($data) . exit;
    Flight::json($data);
});
Flight::route('PUT /novedad', function () {

    if ($_SESSION['ABM_ROL']['mNov'] == '0') {
        Flight::json(array("error" => "No tiene permisos para modificar novedades."));
        return;
    }

    $payload = Flight::request()->data;

    $legajo = $payload['Lega'];
    $fecha = $payload['Fecha'];
    $noveM = $payload['NoveM'];
    $nove = $payload['Nove'];

    $cierre = getCierreFicha($legajo, $fecha);

    if ($cierre['Estado'] != 'abierto') {
        Flight::json(array("error" => "No se puede eliminar la novedad, la ficha se encuentra cerrada."));
        return;
    }

    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/novedades";
    $method = 'PUT';
    $rs = ch_api($endpoint, array($payload), $method, '');
    $result = json_decode($rs, true);

    $result['MESSAGE'] = $result['MESSAGE'] ?? 'ERROR';

    if ($result['MESSAGE'] == "OK") {

        $noves = array($nove, $noveM);
        $dataNovedad = getNovedad($noves);

        $getNovedad = array_filter($dataNovedad, function ($element) use ($nove) {
            return $element['Codi'] == $nove;
        });
        $getNovedad2 = array_filter($dataNovedad, function ($element) use ($noveM) {
            return $element['Codi'] == $noveM;
        });

        $getNovedad = (array_values($getNovedad));
        $getNovedad2 = (array_values($getNovedad2));

        $aud = 'Modificación Novedad: (' . $nove . ') ' . $getNovedad[0]['Desc'] . '. Por novedad (' . $noveM . ') ' . $getNovedad2[0]['Desc'] . '. Legajo: ' . $legajo . '. Fecha: ' . fechformat($fecha);
        audito_ch('M', $aud, '2');
    }

    Flight::json($result);
});
Flight::route('POST /novedad', function () {

    if ($_SESSION['ABM_ROL']['aNov'] == '0') {
        Flight::json(array("error" => "No tiene permisos para ingresar novedades."));
        return;
    }
    $payload = Flight::request()->data;

    if (!$payload['Nove']) {
        Flight::json(array("error" => "La novedad es requerida."));
        return;
    }


    $legajo = $payload['Lega'];
    $fecha = $payload['Fecha'];

    $opt = array("getNov" => "1", "getFic" => "1");
    $dataFicNov = getFicNovHorSimple($legajo, $fecha, $opt);
    $data = $dataFicNov[0] ?? [];
    $dataFic = $dataFicNov[0]['Fich'] ?? [];

    $getNovedad = getNovedad($payload['Nove']);

    if (empty($data)) {
        Flight::json(array("error" => "No se puede crear la novedad, no se encontró la ficha."));
        return;
    }

    $dataNovedad = $data['Nove'] ?? []; // Obtenemos las novedades de la ficha
    $dataCierra = $data['Cierre'] ?? []; // Obtenemos el cierre de la ficha
    $tipoNovedadRecibida = intval($getNovedad[0]['Tipo']); // Obtenemos el tipo de novedad

    /** Si la ficha tiene fichadas y el tipo de novedad es del tipo ausencia y la novedad forzada es 0
     * no se puede crear la novedad porque ya existen fichadas para el día.
     */
    if (count($dataFic) > 0 && $tipoNovedadRecibida > 2 && intval($payload['Cate']) === 0) {
        Flight::json(array("error" => "No se puede crear la novedad del tipo ausencia, existen fichadas para el día."));
        return;
    }
    /** */

    foreach ($dataNovedad as $key => $value) {

        if ($value['Codi'] == $payload['Nove']) {
            Flight::json(array("error" => "No se puede crear la novedad, ya existe una novedad con el mismo código."));
            return;
        }

        if (intval($payload['Cate']) === 0) { // Si la novedad no viene forzada chequeamos que no exista una novedad del mismo tipo

            if ((intval($value['NoTi']) > 2) && (intval($tipoNovedadRecibida) > 2)) {
                Flight::json(array("error" => "No se puede crear la novedad, ya existe una novedad del mismo tipo."));
                return;
            }

            if (intval($value['NoTi']) === intval($tipoNovedadRecibida)) {
                Flight::json(array("error" => "No se puede crear la novedad, ya existe una novedad del mismo tipo."));
                return;
            }
        }
    }

    if ($dataCierra['Estado'] != 'abierto') {
        Flight::json(array("error" => "No se puede crear la novedad, la ficha se encuentra cerrada."));
        return;
    }

    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/novedades?procesar=1";

    $rs = ch_api($endpoint, array($payload), 'POST', '');

    $result = json_decode($rs, true);

    $result['MESSAGE'] = $result['MESSAGE'] ?? 'ERROR';

    if ($result['MESSAGE'] == "OK") {
        $aud = 'Alta Novedad: (' . $payload['Nove'] . ') ' . $getNovedad[0]['Desc'] . ' de Legajo: ' . $legajo . ' Fecha: ' . fechformat($fecha);
        audito_ch('A', $aud, '2');
    }

    Flight::json($result);
});
Flight::route('DELETE /novedad', function () {

    if ($_SESSION['ABM_ROL']['bNov'] == '0') {
        Flight::json(array("error" => "No tiene permisos para eliminar novedades."));
        return;
    }

    $payload = Flight::request();
    $legajo = $payload->data['Lega'];
    $fecha = $payload->data['Fecha'];
    $nove = $payload->data['Nove'];

    $cierre = getCierreFicha($legajo, $fecha);

    if ($cierre['Estado'] != 'abierto') {
        Flight::json(array("error" => "No se puede eliminar la novedad, la ficha se encuentra cerrada."));
        return;
    }

    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/novedades?procesar=1";
    $rs = ch_api($endpoint, array($payload->data), 'DELETE', '');
    $result = json_decode($rs, true);

    $result['MESSAGE'] = $result['MESSAGE'] ?? 'ERROR';

    if ($result['MESSAGE'] == "OK") {
        $getNovedad = getNovedad($nove);
        $aud = 'Baja Novedad: (' . $getNovedad[0]['Codi'] . ') ' . $getNovedad[0]['Desc'] . '. Legajo: ' . $legajo . '. Fecha: ' . fechformat($fecha);
        audito_ch('B', $aud, '2');
    }

    Flight::json($result);
});
Flight::route('POST /horas/totales', function () {

    $payload = Flight::request()->data->getData();
    // Flight::json($payload) . exit;

    $payload['DTHoras'] = $payload['DTHoras'] ?? false;
    $payload['flag'] = $payload['flag'] ?? false;

    $payload['Empr'] = $payload['Empr'] ?? [];
    $payload['Plan'] = $payload['Plan'] ?? [];
    $payload['Conv'] = $payload['Conv'] ?? [];
    $payload['Sect'] = $payload['Sect'] ?? [];
    $payload['Sec2'] = $payload['Sec2'] ?? [];
    $payload['Grup'] = $payload['Grup'] ?? [];
    $payload['Sucu'] = $payload['Sucu'] ?? [];
    $payload['Lega'] = $payload['Lega'] ?? [];
    $payload['LegTipo'] = $payload['LegTipo'] ?? [];

    $emprRol = ($_SESSION['EmprRol']) ? explode(',', $_SESSION['EmprRol']) : [];
    $planRol = ($_SESSION['PlanRol']) ? explode(',', $_SESSION['PlanRol']) : [];
    $convRol = ($_SESSION['ConvRol']) ? explode(',', $_SESSION['ConvRol']) : [];
    $sectRol = ($_SESSION['SectRol']) ? explode(',', $_SESSION['SectRol']) : [];
    $sec2Rol = ($_SESSION['Sec2Rol']) ? explode(',', $_SESSION['Sec2Rol']) : [];
    $grupRol = ($_SESSION['GrupRol']) ? explode(',', $_SESSION['GrupRol']) : [];
    $sucuRol = ($_SESSION['SucuRol']) ? explode(',', $_SESSION['SucuRol']) : [];
    $persRol = ($_SESSION['EstrUser']) ? explode(',', $_SESSION['EstrUser']) : [];

    $payload['Plan'] = (!$payload['Plan']) ? mergeArray($payload['Plan'], $planRol) : $payload['Plan'];
    $payload['Empr'] = (!$payload['Empr']) ? mergeArray($payload['Empr'], $emprRol) : $payload['Empr'];
    $payload['Conv'] = (!$payload['Conv']) ? mergeArray($payload['Conv'], $convRol) : $payload['Conv'];
    $payload['Sect'] = (!$payload['Sect']) ? mergeArray($payload['Sect'], $sectRol) : $payload['Sect'];
    $payload['Sec2'] = (!$payload['Sec2']) ? mergeArray($payload['Sec2'], $sec2Rol) : $payload['Sec2'];
    $payload['Grup'] = (!$payload['Grup']) ? mergeArray($payload['Grup'], $grupRol) : $payload['Grup'];
    $payload['Sucu'] = (!$payload['Sucu']) ? mergeArray($payload['Sucu'], $sucuRol) : $payload['Sucu'];
    $payload['Lega'] = (!$payload['Lega']) ? mergeArray($payload['Lega'], $persRol) : $payload['Lega'];

    $data = [];

    if ($payload['DTHoras'] == 'true') {
        $data = getHorasTotalesDT($payload);
        $nameFile = $payload['flag'];
        $file = fopen("json/total_horas_$nameFile.json", "w") or die("Unable to open file!");
        $file2 = fopen("json/payload_horas_$nameFile.json", "w") or die("Unable to open file!");
        fwrite($file, json_encode($data));
        fwrite($file2, json_encode($payload));
        fclose($file);
        fclose($file2);
        Flight::json($data);
        exit;
    }
    // Flight::json($payload) . exit;

    $data = getHorasTotales($payload);

    Flight::json($data);
});
Flight::route('POST /novedades/totales', function () {

    $payload = Flight::request()->data->getData();
    // Flight::json($payload) . exit;

    $payload['DTNovedades'] = $payload['DTNovedades'] ?? false;
    $payload['flag'] = $payload['flag'] ?? false;

    $payload['Empr'] = $payload['Empr'] ?? [];
    $payload['Plan'] = $payload['Plan'] ?? [];
    $payload['Conv'] = $payload['Conv'] ?? [];
    $payload['Sect'] = $payload['Sect'] ?? [];
    $payload['Sec2'] = $payload['Sec2'] ?? [];
    $payload['Grup'] = $payload['Grup'] ?? [];
    $payload['Sucu'] = $payload['Sucu'] ?? [];
    $payload['Lega'] = $payload['Lega'] ?? [];

    $emprRol = ($_SESSION['EmprRol']) ? explode(',', $_SESSION['EmprRol']) : [];
    $planRol = ($_SESSION['PlanRol']) ? explode(',', $_SESSION['PlanRol']) : [];
    $convRol = ($_SESSION['ConvRol']) ? explode(',', $_SESSION['ConvRol']) : [];
    $sectRol = ($_SESSION['SectRol']) ? explode(',', $_SESSION['SectRol']) : [];
    $sec2Rol = ($_SESSION['Sec2Rol']) ? explode(',', $_SESSION['Sec2Rol']) : [];
    $grupRol = ($_SESSION['GrupRol']) ? explode(',', $_SESSION['GrupRol']) : [];
    $sucuRol = ($_SESSION['SucuRol']) ? explode(',', $_SESSION['SucuRol']) : [];
    $persRol = ($_SESSION['EstrUser']) ? explode(',', $_SESSION['EstrUser']) : [];

    $payload['Plan'] = (!$payload['Plan']) ? mergeArray($payload['Plan'], $planRol) : $payload['Plan'];
    $payload['Empr'] = (!$payload['Empr']) ? mergeArray($payload['Empr'], $emprRol) : $payload['Empr'];
    $payload['Conv'] = (!$payload['Conv']) ? mergeArray($payload['Conv'], $convRol) : $payload['Conv'];
    $payload['Sect'] = (!$payload['Sect']) ? mergeArray($payload['Sect'], $sectRol) : $payload['Sect'];
    $payload['Sec2'] = (!$payload['Sec2']) ? mergeArray($payload['Sec2'], $sec2Rol) : $payload['Sec2'];
    $payload['Grup'] = (!$payload['Grup']) ? mergeArray($payload['Grup'], $grupRol) : $payload['Grup'];
    $payload['Sucu'] = (!$payload['Sucu']) ? mergeArray($payload['Sucu'], $sucuRol) : $payload['Sucu'];
    $payload['Lega'] = (!$payload['Lega']) ? mergeArray($payload['Lega'], $persRol) : $payload['Lega'];

    $data = [];

    if ($payload['DTNovedades'] == 'true') {
        $data = getNovedadesTotalesDT($payload);
        $nameFile = $payload['flag'];
        $file = fopen("json/total_novedades_$nameFile.json", "w") or die("Unable to open file!");
        $file2 = fopen("json/payload_novedades_$nameFile.json", "w") or die("Unable to open file!");
        fwrite($file, json_encode($data));
        fwrite($file2, json_encode($payload));
        fclose($file);
        fclose($file2);
        Flight::json($data);
        exit;
    }

    $data = getNovedadesTotales($payload);

    Flight::json($data);
});
Flight::route('GET /horas/payload', function () {

    $payload = Flight::request()->query->getData();
    $payload['flag'] = $payload['flag'] ?? false;
    $payload['VPorFormato'] = $payload['VPorFormato'] ?? '';
    $payload['VPor'] = $payload['VPor'] ?? '';

    $file = "json/payload_horas_" . $payload['flag'] . ".json";
    if (file_exists($file)) {
        $dataPayload = file_get_contents($file);
        $dataPayload = json_decode($dataPayload, true);
    }
    if ($payload['VPorFormato']) {
        $dataPayload['Formato'] = $payload['VPorFormato'] ?? 'json';
    }
    if ($payload['VPor']) {
        $dataPayload['VPor'] = $payload['VPor'] ?? 'json';
    }
    if (array_key_exists('estructura', $payload)) {
        $dataPayload['estructura'] = $payload['estructura'];
    }
    if (array_key_exists('cantidades', $payload)) {
        $dataPayload['cantidades'] = $payload['cantidades'];
    }
    if (array_key_exists('extension', $payload)) {
        $dataPayload['extension'] = $payload['extension'];
    }
    if (array_key_exists('totales', $payload)) {
        $dataPayload['totales'] = $payload['totales'];
    }

    $nameFile = $payload['flag'];
    $file2 = fopen("json/payload_horas_$nameFile.json", "w") or die("Unable to open file!");
    fwrite($file2, json_encode($dataPayload));
    fclose($file2);

    Flight::json($dataPayload);
});
Flight::route('GET /export/totales', function () {
    $payload = Flight::request()->query->getData();
    $payload['flag'] = $payload['flag'] ?? false;

    if ($payload['flag']) {
        $fileHoras = "json/total_horas_" . $payload['flag'] . ".json";
        $filePayloadNovedades = "json/payload_novedades_" . $payload['flag'] . ".json";
        $filePayloadHoras = "json/payload_horas_" . $payload['flag'] . ".json";

        if (file_exists($filePayloadNovedades)) {
            $dataPayloadNovedades = file_get_contents($filePayloadNovedades);
            $dataPayloadNovedades = json_decode($dataPayloadNovedades, true);
        }

        if (file_exists($filePayloadHoras)) {
            $dataPayloadHoras = file_get_contents($filePayloadHoras);
            $dataPayloadHoras = json_decode($dataPayloadHoras, true);
        }

        if (file_exists($fileHoras)) {
            $dataHoras = file_get_contents($fileHoras);
            $dataHoras = json_decode($dataHoras, true);
            $dataHorasTotales = $dataHoras['totales'];
            // Flight::json($dataHorasTotales) . exit;
            $dataHorasTotalesTryAT = $dataHoras['totalesTryAT'];
            $dataHorasTiposHoras = $dataHoras['tiposHoras'];
            $dataHorasData = $dataHoras['data'];
        }
        $fileNovedades = "json/total_novedades_" . $payload['flag'] . ".json";
        if (file_exists($fileNovedades)) {
            $dataNovedades = file_get_contents($fileNovedades);
            $dataNovedades = json_decode($dataNovedades, true);
            // Flight::json($dataNovedades) . exit;
            $dataNovedadesNove = $dataNovedades['novedades'];
            $dataNovedadesTotales = $dataNovedades['totales'];
            $dataNovedadesData = $dataNovedades['data'];
        }
    }
    // Array para almacenar los resultados agrupados
    $data = [];

    $array_original = [
        "horas" => $dataHorasData ?? [],
        "novedades" => $dataNovedadesData ?? []
    ];

    $estructEmpr = [];
    $estructPlan = [];
    $estructConv = [];
    $estructSect = [];
    $estructSecc = [];
    $estructGrup = [];
    $estructSucu = [];

    // print_r($array_original) . exit;
    // Agrupar por Lega los arrays Totales de Horas
    foreach ($array_original['horas'] as $hora) {
        $lega = $hora['Lega'];
        unset($hora['Lega']);
        $data['legajos'][$lega]['LegApNo'] = $hora['LegApNo'] ?? '';
        $data['legajos'][$lega]['Lega'] = $lega ?? '';
        $data['legajos'][$lega]['Empr'] = $hora['Empr'] ?? '';
        $data['legajos'][$lega]['Plan'] = $hora['Plan'] ?? '';
        $data['legajos'][$lega]['Conv'] = $hora['Conv'] ?? '';
        $data['legajos'][$lega]['Sect'] = $hora['Sect'] ?? '';
        $data['legajos'][$lega]['Secc'] = $hora['Secc'] ?? '';
        $data['legajos'][$lega]['Grup'] = $hora['Grup'] ?? '';
        $data['legajos'][$lega]['Sucu'] = $hora['Sucu'] ?? '';
        $data['legajos'][$lega]['TotalesHoras'] = $hora['Totales'] ?? [];
        $data['legajos'][$lega]['HsATyTR'] = $hora['HsATyTR'] ?? [];

        $estructEmpr[$hora['Empr']] = $hora['Empr'];
        $estructPlan[$hora['Plan']] = $hora['Plan'];
        $estructConv[$hora['Conv']] = $hora['Conv'];
        $estructSect[$hora['Sect']] = $hora['Sect'];
        $estructSecc[$hora['Secc']] = $hora['Sect'] . $hora['Secc'] == 00 ? '' : ($hora['Sect'] . $hora['Secc']);
        $estructGrup[$hora['Grup']] = $hora['Grup'];
        $estructSucu[$hora['Sucu']] = $hora['Sucu'];
    }

    // Agrupar por Lega los arrays Totales de novedades
    foreach ($array_original['novedades'] as $novedad) {
        $lega = $novedad['Lega'];
        unset($novedad['Lega']);
        $data['legajos'][$lega]['Lega'] = $lega ?? '';
        $data['legajos'][$lega]['LegApNo'] = $novedad['LegApNo'] ?? '';
        $data['legajos'][$lega]['Empr'] = $novedad['Empr'] ?? '';
        $data['legajos'][$lega]['Plan'] = $novedad['Plan'] ?? '';
        $data['legajos'][$lega]['Conv'] = $novedad['Conv'] ?? '';
        $data['legajos'][$lega]['Sect'] = $novedad['Sect'] ?? '';
        $data['legajos'][$lega]['Secc'] = $novedad['Secc'] ?? '';
        $data['legajos'][$lega]['Grup'] = $novedad['Grup'] ?? '';
        $data['legajos'][$lega]['Sucu'] = $novedad['Sucu'] ?? '';
        $data['legajos'][$lega]['TotalesNovedades'] = $novedad['Totales'] ?? [];

        $estructEmpr[$novedad['Empr']] = $novedad['Empr'];
        $estructPlan[$novedad['Plan']] = $novedad['Plan'];
        $estructConv[$novedad['Conv']] = $novedad['Conv'];
        $estructSect[$novedad['Sect']] = $novedad['Sect'];
        $estructSecc[$novedad['Secc']] = $novedad['Sect'] . $novedad['Secc'] == 00 ? '' : ($novedad['Sect'] . $novedad['Secc']);
        $estructGrup[$novedad['Grup']] = $novedad['Grup'];
        $estructSucu[$novedad['Sucu']] = $novedad['Sucu'];
    }

    $estructuras = [
        "Empr" => implode(',', array_filter($estructEmpr)),
        "Plan" => implode(',', array_filter($estructPlan)),
        "Conv" => implode(',', array_filter($estructConv)),
        "Sect" => implode(',', array_filter($estructSect)),
        "Secc" => implode(',', array_filter($estructSecc)),
        "Grup" => implode(',', array_filter($estructGrup)),
        "Sucu" => implode(',', array_filter($estructSucu)),
    ];

    $data['totalesTryATHs'] = $dataHorasTotalesTryAT ?? '';
    $data['tiposDeHs'] = $dataHorasTiposHoras ?? '';
    $data['totalesHs'] = $dataHorasTotales ?? '';
    $data['totalesNovedades'] = $dataNovedadesTotales ?? '';
    $data['novedades'] = $dataNovedadesNove ?? '';
    $data['payloadNovedades'] = $dataPayloadNovedades ?? '';
    $data['payloadHoras'] = $dataPayloadHoras ?? '';
    $data['estructuras'] = estructuras($estructuras) ?? '';

    include '../informes/reporte/xls.php';
});
Flight::route('/fechas/horas', function () {
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/horas/dateMinMax";
    $data = ch_api($endpoint, '', 'GET', '');
    $arrayData = json_decode($data, true);
    if (($arrayData['RESPONSE_CODE'] ?? '') == '200 OK') {
        $arrayData = $arrayData['DATA'] ?? [];
    } else {
        $arrayData = [];
    }
    Flight::json($arrayData ?? []);
});
Flight::route('/fechas/fichas', function () {
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/fichas/dateMinMax";
    $data = ch_api($endpoint, '', 'GET', '');
    $arrayData = json_decode($data, true);
    if (($arrayData['RESPONSE_CODE'] ?? '') == '200 OK') {
        $arrayData = $arrayData['DATA'] ?? [];
    } else {
        $arrayData = [];
    }
    Flight::json($arrayData ?? []);
});
Flight::route('POST /get_personal_horarios', function () {
    require __DIR__ . '/../op/horarios/getPersonal.php';


    // $horarioLegajos = get_horario_actual($arrLegajos) ?? []; // esto llama al webservice de horarios. lo reemplazamos por el Flight::asignados($payload);
    // print_r($data) . exit;
    $fechaHoy = date('Y-m-d');
    $payload = [
        "FechaDesde" => $fechaHoy,
        "FechaHasta" => $fechaHoy,
        "Legajos" => $arrLegajos ?? [],
    ];

    $response = Flight::asignados($payload);
    $DATA = $response['DATA'] ?? [];
    // $totalData = count($DATA);

    $dataHorarios = [];
    foreach ($DATA as $legajoData) {
        $dataHorarios = array_merge($dataHorarios, $legajoData);
    }
    // Mapear datos en una sola pasada
    $horarioLegajos = array_map(function ($horario) {
        $sinHorario = $horario['CodigoHorario'] == '0';

        $TipoAsignStr = $sinHorario
            ? 'Sin horario Asignado (Franco)'
            : $horario['Asignacion'] . ' por Legajo (' . $horario['Horario'] . ') ' . $horario['DescripcionHorario'];

        return [
            'Codigo' => $horario['CodigoHorario'],
            'Descanso' => $horario['Descanso'],
            'Desde' => $horario['Entrada'],
            'Hasta' => $horario['Salida'],
            'Dia' => $horario['Dia'],
            'Fecha' => $horario['Fecha'],
            'Feriado' => $horario['Feriado'] == '1' ? 'Sí' : 'No',
            'Horario' => $horario['DescripcionHorario'],
            'HorarioID' => $horario['HorID'],
            'Laboral' => $horario['Laboral'] == '1' ? 'Sí' : 'No',
            'Legajo' => $horario['Legajo'],
            'TipoAsign' => $sinHorario ? 'Sin horario Asignado (Franco)' : $horario['Asignacion'],
            'TipoAsignStr' => $TipoAsignStr,
        ];
    }, $dataHorarios);

    // Alternativa más eficiente usando generador (para datasets grandes)
    function procesarHorarios($DATA)
    {
        foreach ($DATA as $legajoData) {
            foreach ($legajoData as $horario) {
                $sinHorario = $horario['CodigoHorario'] == '0';

                yield [
                    'Codigo' => $horario['CodigoHorario'],
                    'Descanso' => $horario['Descanso'],
                    'Desde' => $horario['Entrada'],
                    'Hasta' => $horario['Salida'],
                    'Dia' => $horario['Dia'],
                    'Fecha' => $horario['Fecha'],
                    'Feriado' => $horario['Feriado'] == '1' ? 'Sí' : 'No',
                    'Horario' => $horario['DescripcionHorario'],
                    'HorarioID' => $horario['HorID'],
                    'Laboral' => $horario['Laboral'] == '1' ? 'Sí' : 'No',
                    'Legajo' => $horario['Legajo'],
                    'TipoAsign' => $sinHorario ? 'Sin horario Asignado (Franco)' : $horario['Asignacion'],
                    'TipoAsignStr' => $sinHorario
                        ? 'Sin horario Asignado (Franco)'
                        : $horario['Asignacion'] . ' por Legajo (' . $horario['Horario'] . ') ' . $horario['DescripcionHorario'],
                ];
            }
        }
    }

    // Para usar el generador:
    // $horarioLegajos = iterator_to_array(procesarHorarios($DATA));
    if ($horarioLegajos) {
        // Crear índice hash por legajo
        $horariosPorLegajo = array_column($horarioLegajos, null, 'Legajo');

        // Asignar horario a cada empleado
        foreach ($data as $key => $empleado) {
            $data[$key]['pers_horario'] = $horariosPorLegajo[$empleado['pers_legajo']] ?? [];
        }
    }

    $json_data = [
        "draw" => intval($params['draw']),
        "recordsTotal" => intval($totalRecords),
        "recordsFiltered" => intval($totalRecords),
        "data" => $data ?? [],
        "horarios" => $horarioLegajos ?? [],
        "legajos" => $arrLegajos ?? [],
        "request" => Flight::request()->data,
        'error' => $error,
    ];

    Flight::json($json_data);
});
Flight::route('/horarios', function () {
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/horarios/";
    $data = ch_api($endpoint, '', 'GET', '');
    $arrayDataHorarios = json_decode($data, true);
    $arrayDataHorarios = (($arrayDataHorarios['RESPONSE_CODE'] ?? '') == '200 OK') ? $arrayDataHorarios['DATA'] ?? [] : [];

    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/horarios/rotacion";
    $data = ch_api($endpoint, '', 'GET', '');
    $arrayDataRotacion = json_decode($data, true);
    $arrayDataRotacion = (($arrayDataRotacion['RESPONSE_CODE'] ?? '') == '200 OK') ? $arrayDataRotacion['DATA'] ?? [] : [];

    $horariosColumn = $arrayDataHorarios ? array_column($arrayDataHorarios, null, 'Codi') : [];
    $rotacionColumn = $arrayDataRotacion ? array_column($arrayDataRotacion, null, 'RotCodi') : [];

    $datos = [
        'rotacion' => $arrayDataRotacion,
        'horarios' => $arrayDataHorarios,
        'horariosColumn' => $horariosColumn,
        'rotacionColumn' => $rotacionColumn,
        'acciones' => [
            'aCit' => intval($_SESSION['ABM_ROL']['aCit']) ?? 0,
            'mCit' => intval($_SESSION['ABM_ROL']['mCit']) ?? 0,
            'bCit' => intval($_SESSION['ABM_ROL']['bCit']) ?? 0,
            'aTur' => intval($_SESSION['ABM_ROL']['aTur']) ?? 0,
            'mTur' => intval($_SESSION['ABM_ROL']['mTur']) ?? 0,
            'bTur' => intval($_SESSION['ABM_ROL']['bTur']) ?? 0,
            'Proc' => intval($_SESSION['ABM_ROL']['Proc']) ?? 0,
        ]
    ];
    Flight::json($datos ?? []);
});
Flight::route('/horarios/asign/@legajo', function ($legajo) {

    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/horarios/asign/legajo/$legajo";
    $data = ch_api($endpoint, '', 'GET', '');
    $asign = json_decode($data, true);
    $asign = (($asign['RESPONSE_CODE'] ?? '') == '200 OK') ? $asign['DATA'] ?? [] : [];
    if ($asign['desde-hasta']) {
        foreach ($asign['desde-hasta'] as $key => $value) {
            $F1 = date('Ymd', strtotime($value['Ho2Fec1']));
            $F2 = date('Ymd', strtotime($value['Ho2Fec2']));
            $F3 = date('Ymd', strtotime($value['FechaHora']));
            $uniqueKey = $F1 . $F2 . $value['Ho2Hora'] . $value['Ho2Lega'] . $F3;
            $asign['desde-hasta'][$key]['UniqueKey'] = $uniqueKey;
            // $asign['desde-hasta-unique'][] = $uniqueKey;
        }
    }
    if ($asign['rotacion']) {
        foreach ($asign['rotacion'] as $key => $value) {
            $F1 = date('Ymd', strtotime($value['RoLFech']));
            $F2 = date('Ymd', strtotime($value['RoLVenc']));
            $F3 = date('Ymd', strtotime($value['FechaHora']));
            $uniqueKey = $F1 . $F2 . $value['RoLRota'] . $value['RoLLega'] . $F3;
            $asign['rotacion'][$key]['UniqueKey'] = $uniqueKey;
            $asign['rotacion-unique'][] = $uniqueKey;
        }
    }
    if ($asign['desde']) {
        foreach ($asign['desde'] as $key => $value) {
            $F1 = date('Ymd', strtotime($value['Ho1Fech']));
            $F3 = date('Ymd', strtotime($value['FechaHora']));
            $uniqueKey = $F1 . $value['Ho1Hora'] . $value['Ho1Lega'] . $F3;
            $asign['desde'][$key]['UniqueKey'] = $uniqueKey;
            // $asign['desde-unique'][] = $uniqueKey;
        }
    }
    Flight::json($asign ?? []);
});
Flight::route('/rotacion', function () {
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/horarios/rotacion";
    $data = ch_api($endpoint, '', 'GET', '');
    $arrayData = json_decode($data, true);
    $arrayData = (($arrayData['RESPONSE_CODE'] ?? '') == '200 OK') ? $arrayData['DATA'] ?? [] : [];
    Flight::json($arrayData ?? []);
});
Flight::route('POST /horarios/@tipo', function ($tipo) {

    $urlValid = [
        '/horarios/desde',
        '/horarios/desde-hasta',
        '/horarios/legajo-desde',
        '/horarios/delete-legajo-desde',
        '/horarios/legajo-desde-hasta',
        '/horarios/delete-legajo-desde-hasta',
        '/horarios/delete-legajo-citacion',
        '/horarios/rotacion',
        '/horarios/legajo-rotacion',
        '/horarios/citacion',
        '/horarios/legajo-citacion',
        '/horarios/edit-legajo-citacion',
        '/horarios/edit-legajo-rotacion',
        '/horarios/delete-legajo-rotacion',
    ];

    $acciones = new stdClass();
    $acciones->aCit = intval($_SESSION['ABM_ROL']['aCit']) ?? 0;
    $acciones->mCit = intval($_SESSION['ABM_ROL']['mCit']) ?? 0;
    $acciones->bCit = intval($_SESSION['ABM_ROL']['bCit']) ?? 0;
    $acciones->aTur = intval($_SESSION['ABM_ROL']['aTur']) ?? 0;
    $acciones->mTur = intval($_SESSION['ABM_ROL']['mTur']) ?? 0;
    $acciones->bTur = intval($_SESSION['ABM_ROL']['bTur']) ?? 0;
    $acciones->Proc = intval($_SESSION['ABM_ROL']['Proc']) ?? 0;

    $request = Flight::request();
    $url = $request->url ?? '';
    $method = $request->method ?? '';

    if (!in_array($url, $urlValid)) {
        Flight::notFound();
    }

    $payload = $request->data;

    $LegNume = $payload['LegNume'] ?? '';
    $Procesar = $payload['Procesar'] ?? false;

    if ($Procesar) {
        if ($acciones->Proc == 0) {
            throw new Exception('No tiene permisos para procesar horarios', 400);
        }
    }

    $Empr = $payload['Filtros']['Emp'] ?? [];
    $Plan = $payload['Filtros']['Plan'] ?? [];
    $Conv = $payload['Filtros']['Conv'] ?? [];
    $Sect = $payload['Filtros']['Sect'] ?? [];
    $Sec2 = $payload['Filtros']['Sec2'] ?? [];
    $Grup = $payload['Filtros']['Grup'] ?? [];
    $Sucu = $payload['Filtros']['Sucur'] ?? [];
    $Lega = $payload['Filtros']['Per'] ?? [];
    $Regla = $payload['Filtros']['Regla'] ?? [];
    $Tare = $payload['Filtros']['Tare'] ?? [];
    $Search = $payload['Filtros']['search']['value'] ?? '';
    $Entr = $payload['Entr'] ?? '00:00';
    $Sale = $payload['Sale'] ?? '00:00';
    $Desc = $payload['Desc'] ?? '00:00';
    $Marcados = $payload['Marcados'] ?? [];

    $Codi = $payload['Codi'] ?? '';
    $Fecha = $payload['Fecha'] ?? '';
    $FechaD = $payload['FechaD'] ?? '';
    $FechaH = $payload['FechaH'] ?? '';
    $Vence = $payload['Vence'] ?? '';
    $Dias = $payload['Dias'] ?? '';

    if (!$payload['Fecha']) {
        throw new Exception('La fecha es requerida', 200);
    }

    $emprRol = ($_SESSION['EmprRol']) ? explode(',', $_SESSION['EmprRol']) : [];
    $planRol = ($_SESSION['PlanRol']) ? explode(',', $_SESSION['PlanRol']) : [];
    $convRol = ($_SESSION['ConvRol']) ? explode(',', $_SESSION['ConvRol']) : [];
    $sectRol = ($_SESSION['SectRol']) ? explode(',', $_SESSION['SectRol']) : [];
    $sec2Rol = ($_SESSION['Sec2Rol']) ? explode(',', $_SESSION['Sec2Rol']) : [];
    $grupRol = ($_SESSION['GrupRol']) ? explode(',', $_SESSION['GrupRol']) : [];
    $sucuRol = ($_SESSION['SucuRol']) ? explode(',', $_SESSION['SucuRol']) : [];
    $persRol = ($_SESSION['EstrUser']) ? explode(',', $_SESSION['EstrUser']) : [];

    $payload['Empr'] = (!$Empr) ? mergeArray($Empr, $emprRol) : $Empr;
    $payload['Plan'] = (!$Plan) ? mergeArray($Plan, $planRol) : $Plan;
    $payload['Conv'] = (!$Conv) ? mergeArray($Conv, $convRol) : $Conv;
    $payload['Sect'] = (!$Sect) ? mergeArray($Sect, $sectRol) : $Sect;
    $payload['Sec2'] = (!$Sec2) ? mergeArray($Sec2, $sec2Rol) : $Sec2;
    $payload['Grup'] = (!$Grup) ? mergeArray($Grup, $grupRol) : $Grup;
    $payload['Sucu'] = (!$Sucu) ? mergeArray($Sucu, $sucuRol) : $Sucu;
    $payload['Lega'] = (!$Lega) ? mergeArray($Lega, $persRol) : $Lega;
    $payload['Regla'] = $Regla;
    $payload['Tare'] = $Tare;

    $Legajos = [];
    $textOK = '';

    $user = $_SESSION['NOMBRE_SESION'];

    $payloadPersonal = [
        "Empr" => $payload['Empr'],
        "Plan" => $payload['Plan'],
        "Conv" => $payload['Conv'],
        "Sect" => $payload['Sect'],
        "Sec2" => $payload['Sec2'],
        "Grup" => $payload['Grup'],
        "Sucu" => $payload['Sucu'],
        "Nume" => $payload['Lega'],
        "RegCH" => $payload['Regla'],
        "TareProd" => $payload['Tare'],
        "ApNoNume" => $Search,
        "length" => 10000,
        "Baja" => [0],
        "getEstruct" => 0
    ];

    if ($url == '/horarios/desde' || $url == '/horarios/desde-hasta' || $url == '/horarios/rotacion') {

        if ($acciones->aTur == 0) {
            throw new Exception('No tiene permisos para asignar horarios', 400);
        }

        if ($Marcados) {
            $Legajos = $Marcados;
        } else {
            $personal = Flight::personal($payloadPersonal);
            if ($personal) {
                foreach ($personal as $key => $p) {
                    $Legajos[] = $p['Lega'];
                }
            }
        }
        $payloadHorarios = [
            "Lega" => $Legajos,
            "Fecha" => $Fecha,
            "FechaD" => $FechaD,
            "FechaH" => $FechaH,
            "Vence" => $Vence,
            "Dias" => $Dias,
            "Codi" => $Codi,
            "User" => $user,
            "Proc" => $Procesar
        ];
    }

    if ($url === '/horarios/legajo-desde' || $url === '/horarios/legajo-desde-hasta' || $url === '/horarios/delete-legajo-desde' || $url === '/horarios/delete-legajo-desde-hasta' || $url === '/horarios/delete-legajo-citacion' || $url == '/horarios/edit-legajo-citacion' || $url == '/horarios/edit-legajo-rotacion' || $url == '/horarios/delete-legajo-rotacion') {

        if (!$LegNume) {
            throw new Exception('El legajo es requerido', 400);
        }

        if ($acciones->aTur == 0) {
            throw new Exception('No tiene permisos para asignar horarios', 400);
        }

        $payloadHorarios = [
            "Fecha" => $Fecha,
            "FechaD" => $FechaD,
            "FechaH" => $FechaH,
            "Entr" => $Entr,
            "Sale" => $Sale,
            "Desc" => $Desc,
            "Vence" => $Vence,
            "Dias" => $Dias,
            "Proc" => $Procesar,
            "Codi" => $Codi,
            "User" => $user,
            "Lega" => [$LegNume],
        ];

        if ($url === '/horarios/delete-legajo-desde') {
            if ($acciones->bTur == 0) {
                throw new Exception('No tiene permisos para eliminar horarios', 400);
            }
            $method = 'DELETE';
            $tipo = 'desde';
        }
        if ($url === '/horarios/delete-legajo-desde-hasta') {
            if ($acciones->bTur == 0) {
                throw new Exception('No tiene permisos para eliminar horarios', 400);
            }
            $method = 'DELETE';
            $tipo = 'desde-hasta';
        }
        if ($url === '/horarios/delete-legajo-citacion') {
            if ($acciones->bCit == 0) {
                throw new Exception('No tiene permisos para eliminar citaciones', 400);
            }
            $method = 'DELETE';
            $tipo = 'citacion';
            $textOK = "Citacion eliminada";
        }
        if ($url === '/horarios/edit-legajo-citacion') {
            if ($acciones->bCit == 0) {
                throw new Exception('No tiene permisos para eliminar citaciones', 400);
            }
            $tipo = 'citacion';
            $textOK = "Citacion editada";
        }
        if ($url === '/horarios/edit-legajo-rotacion') {
            if ($acciones->bTur == 0) {
                throw new Exception('No tiene permisos para eliminar rotaciones', 400);
            }
            $tipo = 'rotacion';
            $textOK = "Rotación editada";
        }
        if ($url === '/horarios/delete-legajo-rotacion') {
            if ($acciones->bTur == 0) {
                throw new Exception('No tiene permisos para eliminar rotaciones', 400);
            }
            $method = 'DELETE';
            $tipo = 'rotacion';
            $textOK = "Rotación eliminada";
        }
    }
    if ($url == '/horarios/citacion' || $url == '/horarios/legajo-citacion') {
        if ($acciones->aCit == 0) {
            throw new Exception('No tiene permisos para asignar citaciones', 400);
        }
        if ($url == '/horarios/citacion') {

            if ($Marcados) {
                $Legajos = $Marcados;
            } else {
                $personal = Flight::personal($payloadPersonal);
                if ($personal) {
                    foreach ($personal as $key => $p) {
                        $Legajos[] = $p['Lega'];
                    }
                }
            }
        }
        if ($url == '/horarios/legajo-citacion') {
            if (!$LegNume) {
                throw new Exception('El legajo es requerido', 400);
            }
            $Legajos = [$LegNume];
        }

        $payloadHorarios = [
            "Fecha" => $Fecha,
            "Proc" => $Procesar,
            "Entr" => $Entr,
            "Sale" => $Sale,
            "Desc" => $Desc,
            "User" => $user,
            "Lega" => $Legajos,
        ];
        $textOK = "Citaciones asignadas";
    }

    $endpoint = URLAPI . "/api/v1/horarios/{$tipo}/"; // URL API
    $data = ch_api($endpoint, $payloadHorarios, $method, ''); // Consumimos API
    $arrayData = json_decode($data, true); // Decodificamos JSON
    $result = (($arrayData['RESPONSE_CODE'] ?? '') == '200 OK') ? true : false;
    $status = $result ? 'ok' : 'error'; // Estado de la respuesta
    $total = $result ? $arrayData['TOTAL'] ?? 0 : 0; // Total de registros
    $data = $result ? $arrayData['DATA'] ?? 0 : 0; // Datos de la respuesta
    $messageErr = !$result ? $arrayData['MESSAGE'] ?? 'Error al asignar horarios' : ''; // Mensaje de error
    if ($textOK == '') {
        $textOK = ($method == 'POST') ? "Horarios asignados" : "Horarios eliminados";
    }
    $messageOk = "{$textOK} ({$total})"; // Mensaje de éxito
    $message = $result ? $messageOk : $messageErr; // Mensaje de respuesta

    if ($data) {
        $aud = auditoria_multiple($data, 33);
    }
    Flight::json([
        'status' => $status,
        'message' => $message,
        'aud' => $aud ?? false
    ]);
});
Flight::route('POST /test_connect', function () {
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/conectar";
    $payload = Flight::request()->data;
    $data = ch_api($endpoint, $payload, 'POST', '');
    $arrayData = json_decode($data, true);
    Flight::json($arrayData ?? []);
});
Flight::route('POST /estruct/fichas/', function () {
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/fichasestruct/";
    $payload = Flight::request()->data;
    $data = ch_api($endpoint, ($payload), 'POST', '');

    $arrayData = json_decode($data, true);

    if (($arrayData['RESPONSE_CODE'] ?? '') == '200 OK') {
        $arrayData = $arrayData['DATA'] ?? [];
    } else {
        Flight::json(array('status' => 'error', 'message' => $arrayData['MESSAGE']), 204);
        exit;
    }
    Flight::json($arrayData ?? []);
});
Flight::route('POST /estructuras/alta/', function () {

    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/estructuras/alta/";
    $payload = Flight::request()->data;

    $data = ch_api($endpoint, ($payload), 'POST', '');

    $arrayData = json_decode($data, true);

    if ($arrayData['MESSAGE'] == 'OK') {
        $Cod = $arrayData['DATA']['Cod'] ?? '';
        $Desc = $arrayData['DATA']['Desc'] ?? '';
        $Dato = $payload->Estruct . ': ' . $Desc . ': ' . $Cod;
        audito_ch('A', $Dato, '10');
    }

    Flight::json($arrayData ?? []);
});
Flight::route('/parametros/liquid', function () {
    $data = getParamLiquid();
    Flight::json($data);
});
Flight::route('/fichas/dates', function () {
    $data = getFichasMinMax();
    Flight::json($data);
});
Flight::route('GET /horas', function () {
    $data = get_tipo_hora();
    Flight::json($data);
});
Flight::route('GET /novedades', function () {
    $data = get_novedades();
    Flight::json($data);
});
Flight::route('GET /nove-horas/data', function () {
    $nove = get_novedades();
    $horas = get_tipo_hora();
    $queryParams = ['cliente' => $_SESSION['ID_CLIENTE'] ?? '', 'descripcion' => '', 'modulo' => 46, 'valores' => ''];
    $params = get_params($queryParams) ?? [];

    $paramsKeyDescripcion = array_column($params, null, 'descripcion');

    $mapParams = [
        'Enferm',
        'Accid',
        'Varias',
        'Incid',
        'Susp. Disc',
        'Ideales',
        'Vac',
        'Susp',
        'Paro',
    ];
    $tiposHoras = $horas;

    $Existentes = array_filter($paramsKeyDescripcion, function ($key) use ($mapParams) {
        return in_array($key, $mapParams);
    }, ARRAY_FILTER_USE_KEY);

    // Obtener los valores de $mapParams que no están en $Existentes
    $NoExistentes = array_filter($mapParams, function ($key) use ($Existentes) {
        return !array_key_exists($key, $Existentes);
    });

    $NoExistentes = array_values($NoExistentes); // Re-indexar el array

    if ($NoExistentes) {
        $params = [];
        foreach ($NoExistentes as $key => $value) {
            $bodyAdd[] = [
                'descripcion' => $value,
                'valores' => '',
                'modulo' => 46,
                'cliente' => $_SESSION['ID_CLIENTE'] ?? '',
            ];
        }
        add_params($bodyAdd);
        $params = get_params($queryParams) ?? [];
    }

    $horasParams = horasCustom($params);

    array_push($horas, ...$horasParams);

    $queryColumn = ['cliente' => $_SESSION['ID_CLIENTE'] ?? '', 'descripcion' => 'columnas', 'modulo' => 46, 'valores' => ''];
    $columnas = get_params($queryColumn) ?? [];
    $valores = $columnas[0]['valores'] ?? [];

    $queryColumnSorted = ['cliente' => $_SESSION['ID_CLIENTE'] ?? '', 'descripcion' => 'columnasSorted', 'modulo' => 46, 'valores' => ''];
    $columnasSorted = get_params($queryColumnSorted) ?? [];
    $valoresSorted = $columnasSorted[0]['valores'] ?? [];

    if ($valores) {
        $valores = explode(',', $valores);
        $valores = array_map('intval', $valores);
    }


    if ($valoresSorted) {
        // Convertir $valoresSorted en un array de enteros
        $valoresSorted = array_map('intval', explode(',', $valoresSorted));
        // Crear un array asociativo con THoCodi como clave y el array como valor
        $horas = array_column($horas, null, 'THoCodi');
        // Crear un nuevo array ordenado según $valoresSorted
        $horasOrdenadas = [];
        foreach ($valoresSorted as $THoCodi) {
            if (isset($horas[$THoCodi])) {
                $horasOrdenadas[$THoCodi] = $horas[$THoCodi];
            }
        }

        $horasRestantes = array_diff_key($horas, $horasOrdenadas);
        $horas = array_merge($horasOrdenadas, $horasRestantes);
    }

    Flight::json([
        'novedades' => $nove ?? [],
        'horas' => $horas ?? [],
        'tiposHoras' => $tiposHoras ?? [],
        'params' => $params ?? [],
        'columnas' => $valores ?? [],
        'columnasSorted' => $valoresSorted ?? [],
        'horasOrdenadas' => $horasOrdenadas ?? [],
        'horasRestantes' => $horasRestantes ?? [],
    ]);
});

Flight::route('POST /prysmian/@tipo', function ($tipo) {
    try {
        $cliente = $_SESSION['ID_CLIENTE'] ?? ''; // Obtener el ID del cliente

        if (!in_array($tipo, ['view', 'xls'])) { // Validar el tipo de reporte
            throw new Exception('Tipo de reporte no válido', 400);
        }

        $request = Flight::request() ?? []; // Obtener la petición
        $payload = $request->data ?? []; // Obtener los datos de la petición
        if (!$payload) { // Si no hay datos, retornar un array vacío
            throw new Exception('No se recibieron datos', 204);
        }

        switch ($payload['Reporte'] ?? '') { // Según el tipo de reporte
            case '1':
                $data = fic_nove_horas($payload) ?? []; // Obtener datos novedades
                if (!$data) { // Si no hay datos, retornar un array vacío
                    return Flight::json([]);
                }
                $Datos = procesar_por_intervalos($data, $payload);

                if ($tipo == 'view') {
                    Flight::json($Datos['Data']);
                }
                if ($tipo == 'xls') {
                    require __DIR__ . '/fn_spreadsheet.php';
                    include __DIR__ . '/../informes/custom/prysmian/xls.php';
                }
                break;
            case '2':
                try {
                    unset($payload['data']);

                    $payload['NovA'] = [];
                    $payload['getHor'] = "1";
                    $payload['getNov'] = "1";

                    foreach (['FechIni', 'FechFin'] as $key) {
                        if (!$payload[$key] ?? '') { // Validar que las fechas estén presentes
                            throw new Exception("{$key} es requerida", 400);
                        }
                        $payload[$key] = date('Y-m-d', strtotime($payload[$key])); // Convertir la fecha a formato 'Y-m-d'
                    }
                    // Obtener la lista de legajos según los parámetros especificados en $payload
                    $legajos = v1_api('/fichas/legajos', 'POST', $payload) ?? []; // Obtener legajos

                    if (!$legajos) { // Si no hay datos, retornar un array vacío
                        throw new Exception('No se encontraron legajos', 200);
                    }

                    // Itera sobre la estructura obtenida con los parámetros especificados
                    foreach (get_estructura(['length' => 1000, 'Estruct' => 'Sec']) ?? [] as $sector) {
                        // Asigna al array $sectores el código del sector como clave y la primera palabra de la descripción como valor
                        // Si no hay un espacio en la descripción, usa la descripción completa
                        $sectores[$sector['Codi']] = strstr($sector['Desc'], ' ', true) ?: $sector['Desc'];
                    }

                    $payload['length'] = 100000;
                    // $payload['LegTipo'] = [];

                    array_walk($legajos, function (&$value) use ($sectores) {
                        // Asigna a 'FicSectStr' el valor correspondiente del array $sectores
                        // Si no existe una clave en $sectores para 'FicSectStr', asigna una cadena vacía
                        $value['FicSectStr'] = $sectores[$value['FicSect']] ?? '';
                    });

                    $colsExcel = colsExcel();

                    if (!$colsExcel) { // Si no hay datos, retornar un array vacío
                        return Flight::json([]);
                    }

                    $keysHoras = array_keys($colsExcel);
                    $legajosColumn = array_column($legajos, null, 'FicLega');
                    $defaultValues = array_fill_keys($keysHoras, 0); // añadir keyHoras a legajosColumn con el valor 0

                    array_walk($legajosColumn, function (&$item) use ($defaultValues) { // Iterar sobre legajosColumn
                        $item += $defaultValues; // Añadir valores por defecto
                    });

                    $payload['HsTrAT'] = 1;
                    $horas = v1_api('/horas/totales', 'POST', $payload) ?? []; // Obtener tipos de horas
                    $horasData = $horas['data'] ?? []; // Obtener datos de horas totales por legajo
                    $horasColumn = array_column($horasData, null, 'Lega');

                    $payload['Dias'] = [2, 3, 4, 5, 6, 7];
                    $payload['Hora'] = [90];
                    $payload['HoraMin'] = "00:01";

                    $ideales = v1_api('/horas/totales', 'POST', $payload) ?? []; // Obtener tipos de horas
                    $idealesData = $ideales['data'] ?? []; // Obtener datos de horas totales por legajo
                    $idealesColumn = array_column($idealesData, null, 'Lega');
                    foreach (['Dias', 'Hora', 'HoraMin'] as $value) {
                        unset($payload[$value]);
                    }

                    // añadir las horas a legajosColumn con el valor correspondiente según el tipo de hora
                    array_walk($horasColumn, function ($value, $Lega) use (&$legajosColumn) {
                        if (!empty($value['Totales'])) { // Si hay datos en 'Totales'
                            foreach ($value['Totales'] as $total) { // Iterar sobre 'Totales'
                                if (isset($total['THoDesc2'], $total['EnMinutos2'])) { // Si existen 'THoDesc2' y 'EnHoras2'
                                    $legajosColumn[$Lega][$total['THoDesc2']] = $total['EnMinutos2']; // Asignar 'EnHoras2' a 'THoDesc2'
                                }
                            }
                        }
                        $hsTr = $value['HsATyTR'] ?? ''; // Obtener 'HsATyTR'
                        if (!empty($hsTr)) { // Si hay datos en 'HsATyTR'
                            $legajosColumn[$Lega]['HsATr'] = $hsTr['HsATEnMinutos']; // Asignar 'HsATr' a 'HsATEnHoras'
                            $legajosColumn[$Lega]['HsTr'] = $hsTr['HsTrEnMinutos']; // Asignar 'HsTr' a 'HsTrEnHoras'
                        }
                    });

                    array_walk($idealesColumn, function ($value, $Lega) use (&$legajosColumn) {
                        if (!empty($value['Totales'])) { // Si hay datos en 'Totales'
                            foreach ($value['Totales'] as $total) { // Iterar sobre 'Totales'
                                if (isset($total['THoDesc2'], $total['EnMinutos2'])) { // Si existen 'THoDesc2' y 'EnHoras2'
                                    $legajosColumn[$Lega]['Ideales'] = $total['EnMinutos2']; // Asignar 'EnHoras2' a 'THoDesc2'
                                }
                            }
                        }
                    });

                    $clavesCustom = [
                        'Enferm' => 'Enferm',
                        'Accid' => 'Accid',
                        'Susp. Disc' => 'SuspDisc',
                        'Vac' => 'Vac',
                        'Susp' => 'Susp',
                        'Paro' => 'Paro',
                        'Incid' => 'Incid',
                    ];

                    $valoresCustom = get_params(['cliente' => $cliente, 'descripcion' => '', 'modulo' => 46]) ?? [];
                    $params = $valoresCustom;
                    $valoresCustom = array_column($valoresCustom, null, 'descripcion');
                    foreach ($clavesCustom as $key => $clave) {
                        $codigosNove = explode(',', $valoresCustom[$key]['valores'] ?? '') ?? []; // Obtener los codigos de novedad de 'valoresCustom' segun $key y convertirlos en un array
                        $payload['Nove'] = $codigosNove;
                        $legajosColumn = horas_custom($legajosColumn, $payload, $key);
                        unset($payload['Nove']);
                    }

                    $tipoHora = v1_api('/horas/data', 'GET', []) ?? []; // Obtener legajos

                    $horasParams = horasCustom($params);
                    array_push($horasParams, ...$tipoHora);
                    $detalleTipoHoras = array_column($horasParams, null, 'THoCodi');

                    $tipoHora = array_column($tipoHora, null, 'THoCodi');
                    $strMerienda = $tipoHora['50']['THoDesc2'] ?? 'MERIENDA'; // Obtener la descripcion 2 de 'HORAS MERIENDA'
                    $strNormales = $tipoHora['90']['THoDesc2'] ?? 'NORMAL'; // Obtener la descripcion 2 de 'HORAS NORMALES'

                    foreach ($legajosColumn as $key => $value) {
                        $horasATrabajar = $value['HsATr'] ?? 0;
                        $horasMerienda = $value[$strMerienda] ?? 0;
                        $horasNormales = $value[$strNormales] ?? 0;
                        $varias = array_sum([$horasATrabajar, $horasMerienda]);

                        $variasARestar = array_sum([
                            $horasNormales,
                            $value['Enferm'] ?? 0,
                            $value['Accid'] ?? 0,
                            $value['Susp. Disc'] ?? 0,
                            $value['Vac'] ?? 0,
                            $value['Susp'] ?? 0,
                            $value['Paro'] ?? 0,
                            $value['Incid'] ?? 0
                        ]);

                        $calculoVarias = $varias - $variasARestar;
                        $legajosColumn[$key]['Varias'] = $calculoVarias >= 0 ? $calculoVarias : 0;
                    }

                    // recorrer legajosColumn y todos los valores que sean del tipo integer aplicarle al funcion minutos_a_horas_decimal
                    array_walk_recursive($legajosColumn, function (&$item, $key) {
                        if (is_int($item)) {
                            $item = minutos_a_horas_decimal($item);
                        }
                    });

                    $columnasSorted = $valoresCustom['columnasSorted']['valores'] ?? '';
                    $columnasSorted = explode(',', $columnasSorted);
                    $columnasSorted = array_map('intval', $columnasSorted);


                    $initKeys = [
                        'FicSectStr' => [
                            'titulo' => 'Sector',
                            'tipo' => 'string',
                        ],
                        'FicLega' => [
                            'titulo' => 'Legajo',
                            'tipo' => 'string',
                        ],
                        'FicApNo' => [
                            'titulo' => 'Apellido y Nombre',
                            'tipo' => 'string',
                        ],
                    ];

                    foreach ($columnasSorted as $key => $value) {
                        $clave = $detalleTipoHoras[$value]['THoDesc2'] ?? '';
                        $columnKeys[$clave] = [
                            'titulo' => $clave,
                            'tipo' => 'number',
                        ];
                    }

                    $columnKeys = array_merge($initKeys, $columnKeys);

                    $rs = [
                        'data' => array_values($legajosColumn),
                        'columnKeys' => $columnKeys,
                    ];

                    if ($tipo == 'view') {
                        return Flight::json($rs);
                    }

                    if ($tipo == 'xls') {
                        $Datos['Data'] = $legajosColumn;
                        require __DIR__ . '/fn_spreadsheet.php';
                        $colsExcel = colsExcel();
                        include __DIR__ . '/../informes/custom/prysmian/xls2.php';
                    }

                } catch (\Throwable $th) {
                    throw new Exception($th->getMessage(), 400);
                }

                break;
            default:
                Flight::json([]);
                break;
        }
    } catch (\Throwable $th) {
        $code = $th->getCode() ?? 400;
        file_put_contents('error.log', $th->getMessage() . PHP_EOL, FILE_APPEND);
        switch ($code) {
            case 404:
                Flight::notFound();
                break;
            default:
                Flight::json(['status' => 'error', 'message' => $th->getMessage()], $code);
                break;
        }
    }

});

Flight::route('POST /params', function () {

    $request = Flight::request();
    $payload = $request->data;
    $payload['cliente'] = $_SESSION['ID_CLIENTE'] ?? '';

    Flight::json(add_params([$payload]));

});
Flight::route('GET /params', function () {
    $request = Flight::request();
    $queryData = $request->query ?? '';

    $queryColumn = ['cliente' => $_SESSION['ID_CLIENTE'] ?? '', 'descripcion' => $queryData['descripcion'], 'modulo' => $queryData['modulo'], 'valores' => ''];

    $params = get_params($queryColumn) ?? [];
    Flight::json($params);
});
Flight::route('POST /personal/filtros', function () {

    $request = Flight::request();
    $data = $request->data ?? [];
    $flag = $data['flag'] ?? 0;

    $e = intval($data['estructura']) ?? '';
    $dt = intval($data['datatable']) ?? '';

    // Obtener los datos de los roles de la session 
    $emprRol = empresasRol();
    $planRol = plantasRol();
    $convRol = conveniosRol();
    $sectRol = sectoresRol();
    $sec2Rol = seccionesRol();
    $grupRol = gruposRol();
    $sucuRol = sucursalesRol();
    $persRol = legajosRol();

    /**
     * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - *
     * | Obtener los datos de del request. y si $e ($data['estructura']),  | *
     * | coincide con el tipo de filtro, se asigna un array vacío          | *
     * - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - *
     **/
    $dataEmpr = $e === 1 ? [] : $data['empresas'] ?? [];
    $dataPlan = $e === 2 ? [] : $data['plantas'] ?? [];
    $dataConv = $e === 3 ? [] : $data['convenios'] ?? [];
    $dataSect = $e === 4 ? [] : $data['sectores'] ?? [];
    $dataSecc = $e === 5 ? [] : $data['secciones'] ?? [];
    $dataGrup = $e === 6 ? [] : $data['grupos'] ?? [];
    $dataSucu = $e === 7 ? [] : $data['sucursales'] ?? [];
    $dataPers = $e === 8 ? [] : $data['personal'] ?? [];
    $dataPers = $dt !== 1 ? $dataPers : $data['personal'] ?? [];

    // Merge de los datos de los roles con los datos del request
    $empr = mergeArray($dataEmpr, $emprRol);
    $plan = mergeArray($dataPlan, $planRol);
    $conv = mergeArray($dataConv, $convRol);
    $sect = mergeArray($dataSect, $sectRol);
    $secc = mergeArray($dataSecc, $sec2Rol);
    $grup = mergeArray($dataGrup, $grupRol);
    $sucu = mergeArray($dataSucu, $sucuRol);
    $pers = mergeArray($dataPers, $persRol);


    $payload = [
        'estructura' => $data['estructura'] ?? '',
        'activo' => $data['activo'] ?? 1,
        'tipo' => $data['tipo'] ?? '0',
        'estado' => $data['estado'] ?? '0',
        'descripcion' => $data['descripcion'] ?? '',
        'nullCant' => $data['nullCant'] ?? 0,
        'strict' => $data['strict'] ?? 0,
        'proyectar' => $data['proyectar'] ?? 2,
        'filtros' => [
            'empresas' => $empr,
            'plantas' => $plan,
            'convenios' => $conv,
            'sectores' => $sect,
            'secciones' => $secc,
            'grupos' => $grup,
            'sucursales' => $sucu,
            'personal' => $pers,
        ]
    ];

    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/personal/filtros";
    $personal = ch_api($endpoint, $payload, 'POST', '');
    $arrayData = json_decode($personal, true);
    $result = (($arrayData['RESPONSE_CODE'] ?? '') == '200 OK') ? $arrayData['DATA'] : [];

    Flight::json($result ?? []);
});
Flight::route('POST /proyectar', function () {

    $request = Flight::request();
    $payload = $request->data ?? [];

    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/proyectar";
    $proyectar = ch_api($endpoint, $payload, 'POST', '');
    $arrayData = json_decode($proyectar, true);
    $result = (($arrayData['RESPONSE_CODE'] ?? '') == '200 OK') ? $arrayData : [];
    // $result['payload'] = $payload; // Agregar los legajos al resultado

    if ($result) {
        $FechaDesde = $payload['FechaDesde'] ?? '';
        $FechaHasta = $payload['FechaHasta'] ?? '';
        $Legajos = $payload['Legajos'] ?? [];

        // fomatear las fechas a 'd/m/Y'
        $FechaDesde = date('d/m/Y', strtotime($FechaDesde));
        $FechaHasta = date('d/m/Y', strtotime($FechaHasta));

        foreach ($Legajos as $legajo) {
            $arrayAuditoria[] = [
                'AudTipo' => 'A',
                'AudDato' => "Proyectar Horas: Legajo: {$legajo} desde {$FechaDesde} hasta {$FechaHasta}",
            ];
        }

        auditoria_multiple($arrayAuditoria, 47);
    }

    sleep(5); // Simular un tiempo de espera para la proyección
    Flight::json($result ?? []);

});
Flight::route('DELETE /proyectar', function () {

    $request = Flight::request();
    $payload = $request->data ?? [];

    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/proyectar";
    $proyectar = ch_api($endpoint, $payload, 'DELETE', '');
    $arrayData = json_decode($proyectar, true);
    $result = (($arrayData['RESPONSE_CODE'] ?? '') == '200 OK') ? $arrayData : [];
    // $result['payload'] = $payload; // Agregar los legajos al resultado

    if ($result) {
        $FechaDesde = $payload['FechaDesde'] ?? '';
        $FechaHasta = $payload['FechaHasta'] ?? '';
        $Legajos = $payload['Legajos'] ?? [];

        // fomatear las fechas a 'd/m/Y'
        $FechaDesde = date('d/m/Y', strtotime($FechaDesde));
        $FechaHasta = date('d/m/Y', strtotime($FechaHasta));

        foreach ($Legajos as $legajo) {
            $arrayAuditoria[] = [
                'AudTipo' => 'B',
                'AudDato' => "Proyectar Horas: Legajo: {$legajo} desde {$FechaDesde} hasta {$FechaHasta}",
            ];
        }

        auditoria_multiple($arrayAuditoria, 47);
    }

    Flight::json($result ?? []);

});
Flight::route('POST /asignados', function () {
    $request = Flight::request();
    $payload = $request->data ?? [];
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/horarios/asignados";
    $horarios = ch_api($endpoint, $payload, 'POST', '');
    $horarios = json_decode($horarios, true);

    $result = $horarios;

    Flight::json($result ?? []);
});
Flight::map('Forbidden', function ($mensaje) {
    Flight::json(['status' => 'error', 'message' => $mensaje], 403);
    exit;
});
Flight::map('notFound', function () {
    $request = Flight::request();
    $url = $request->url ?? '';
    $method = $request->method ?? '';
    Flight::json(['status' => 'error', 'message' => "Not found: ({$method}) {$url}"], 404);
    exit;
});
Flight::set('flight.log_errors', true);

Flight::map('error', function ($ex) {
    $code_protected = $ex->getCode() ?? 400;

    switch ($code_protected) {
        case 404:
            Flight::notFound();
            break;
        case 403:
            Flight::Forbidden($ex->getMessage());
            break;
    }

    if ($code_protected == 404) {
        Flight::notFound();
    }
    $text = $ex->getMessage();
    Flight::json(['status' => 'error', 'message' => $text], $code_protected);
});

Flight::start(); // Inicio FlightPHP
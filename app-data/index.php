<?php
require __DIR__ . '../../config/session_start.php';
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
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
    $novedades = $arrayData['DATA'] ?? array();

    $queryParams = array(
        "start" => 0,
        "length" => 5000,
        "Estruct" => "NovC",
    );
    $data = ch_api($endpoint, '', 'GET', $queryParams); // Obtenemos las causas de las novedades
    $arrayData = json_decode($data, true);
    $causas = $arrayData['DATA'] ?? array();

    $noveAgrupaPorTipo = array_reduce($novedades, function ($result, $item) {
        $key = $item['TipoDesc'];
        if (!isset($result[$key])) {
            $result[$key] = [];
        }
        $result[$key][] = $item;
        return $result;
    }, []);

    $arr = array(
        "novedades" => $novedades ?? array(),
        "causas" => $causas ?? array(),
        "agrupadas" => $noveAgrupaPorTipo ?? array()
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

    $novedades = $arrayData['DATA'] ?? array();

    $noveAgrupaPorTipo = array_reduce($novedades, function ($result, $item) {
        $key = $item['TipoDesc'];
        if (!isset($result[$key])) {
            $result[$key] = [];
        }
        $result[$key][] = $item;
        return $result;
    }, []);

    $json = array(
        "novedades" => ($noveAgrupaPorTipo) ?? array(),
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
        "novedades" => array_values($novedades) ?? array(),
        "causas" => (intval($NoveCodi) > 0) ? getNoveCausas($NoveCodi) ?? array() : array(),
        "NoveCodi" => $NoveCodi ?? '',
        "NoveTipo" => $NoveTipo ?? ''
    );
    Flight::json($json);
});
Flight::route('/causas/@NoveCodi', function ($NoveCodi) {
    $json = array(
        "causas" => (getNoveCausas($NoveCodi)) ?? array(),
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
    $data = $dataFicNov[0] ?? array();
    $dataFic = $dataFicNov[0]['Fich'] ?? array();

    $getNovedad = getNovedad($payload['Nove']);

    if (empty($data)) {
        Flight::json(array("error" => "No se puede crear la novedad, no se encontró la ficha."));
        return;
    }

    $dataNovedad = $data['Nove'] ?? array(); // Obtenemos las novedades de la ficha
    $dataCierra = $data['Cierre'] ?? array(); // Obtenemos el cierre de la ficha
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

    $data = array();

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

    $data = array();

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
    if ($arrayData['RESPONSE_CODE'] == '200 OK') {
        $arrayData = $arrayData['DATA'] ?? array();
    } else {
        $arrayData = array();
    }
    Flight::json($arrayData ?? array());
});
Flight::route('/fechas/fichas', function () {
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/fichas/dateMinMax";
    $data = ch_api($endpoint, '', 'GET', '');
    $arrayData = json_decode($data, true);
    if ($arrayData['RESPONSE_CODE'] == '200 OK') {
        $arrayData = $arrayData['DATA'] ?? array();
    } else {
        $arrayData = array();
    }
    Flight::json($arrayData ?? array());
});
Flight::route('POST /get_personal_horarios', function () {
    require __DIR__ . '../../op/horarios/getPersonal.php';
    $horarioLegajos = get_horario_actual($arrLegajos) ?? [];
    // $dataPersonal = ($json_data['data']) ?? [];
    if ($horarioLegajos) {
        foreach ($data as $key => $value) {
            // encontrar el legajo en el array de horarios y agregarlo al array de personal
            $horario = array_filter($horarioLegajos, function ($element) use ($value) {
                return $element['Legajo'] == $value['pers_legajo'];
            });
            $horario = array_values($horario);
            $data[$key]['pers_horario'] = $horario[0] ?? [];
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
    $arrayDataHorarios = ($arrayDataHorarios['RESPONSE_CODE'] == '200 OK') ? $arrayDataHorarios['DATA'] ?? [] : [];

    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/horarios/rotacion";
    $data = ch_api($endpoint, '', 'GET', '');
    $arrayDataRotacion = json_decode($data, true);
    $arrayDataRotacion = ($arrayDataRotacion['RESPONSE_CODE'] == '200 OK') ? $arrayDataRotacion['DATA'] ?? [] : [];

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
    // sleep(1);
    $asign = ($asign['RESPONSE_CODE'] == '200 OK') ? $asign['DATA'] ?? [] : [];
    Flight::json($asign ?? []);
});
Flight::route('/rotacion', function () {
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/horarios/rotacion";
    $data = ch_api($endpoint, '', 'GET', '');
    $arrayData = json_decode($data, true);
    $arrayData = ($arrayData['RESPONSE_CODE'] == '200 OK') ? $arrayData['DATA'] ?? [] : [];
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

    if ($arrayData['RESPONSE_CODE'] == '200 OK') {
        $arrayData = $arrayData['DATA'] ?? array();
    } else {
        Flight::json(array('status' => 'error', 'message' => $arrayData['MESSAGE']), 204);
        exit;
    }
    Flight::json($arrayData ?? array());
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

    Flight::json($arrayData ?? array());
});
Flight::route('/parametros/liquid', function () {
    $data = getParamLiquid();
    Flight::json($data);
});
Flight::route('/fichas/dates', function () {
    $data = getFichasMinMax();
    Flight::json($data);
});
Flight::route('GET /horas/tipohora', function () {
    $data = get_tipo_hora();
    Flight::json($data);
});

Flight::route('POST /prysmian/@tipo', function ($tipo) {
    try {

        $validRequestTypes = ['view', 'xls']; // Tipos de reporte válidos

        if (!in_array($tipo, $validRequestTypes)) {
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
                    include __DIR__ . '../../informes/custom/prysmian/xls.php';
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
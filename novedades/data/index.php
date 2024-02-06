<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';

if (!$_SESSION) {
    Flight::json(array("error" => "Sesión finalizada."));
    exit;
}
// sleep(2);

ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");

$token = sha1($_SESSION['RECID_CLIENTE']);

/**
 * Realiza una llamada a la API CH.
 *
 * @param string $endpoint El endpoint de la API.
 * @param array $payload El payload de la solicitud (opcional).
 * @param string $method El método de la solicitud (opcional, por defecto es GET).
 * @param array $queryParams Los parámetros de la consulta (opcional).
 * @return mixed Los datos devueltos por la API o false si hay un error.
 * @throws Exception Si ocurre un error durante la llamada a la API.
 */
function ch_api()
{
    timeZone();
    timeZone_lang();

    $argumento = func_get_args(); // Obtengo los argumentos de la función en un array   
    $endpoint = $argumento[0] ?? ''; // Obtengo el endpoint
    $payload = $argumento[1] ?? array(); // Obtengo el payload
    $method = $argumento[2] ?? 'GET'; // Obtengo el método
    $queryParams = $argumento[3] ?? array(); // Obtengo los parámetro de la query
    $method = strtoupper($method); // Convierto el método a mayúsculas

    try {

        if (!$endpoint) {
            throw new Exception('API CH: ' . date('Y-m-d H:i:s') . ' Endpoint no definido');
        }

        $endpoint = ($queryParams) ? $endpoint . "?" . http_build_query($queryParams) : $endpoint; // Si hay parámetros de query, los agrego al endpoint

        $ch = curl_init(); // Inicializo curl

        curl_setopt($ch, CURLOPT_URL, $endpoint); // Seteo la url
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Seteo el retorno de la respuesta
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Seteo el timeout de la conexión
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); // Seteo la verificación del host
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Seteo la verificación del peer
        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            ($payload) ? curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload)) : '';
        }
        if ($method == 'GET') {
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        }
        if ($method == 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            ($payload) ? curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload)) : '';
        }
        if ($method == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            ($payload) ? curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload)) : '';
        }

        $token = sha1($_SESSION['RECID_CLIENTE']);
        $headers = array(
            "Accept: */*",
            'Content-Type: application/json',
            "Token:" . $token . "",
        );

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Seteo los headers

        $file_contents = curl_exec($ch); // Ejecuto curl

        $curl_errno = curl_errno($ch); // get error code
        $curl_error = curl_error($ch); // get error information

        if ($curl_errno > 0) { // si hay error
            $text = "cURL Error ($curl_errno): $curl_error"; // set error message
            throw new Exception($text);
        }
        if (!$file_contents) {
            throw new Exception('API CH: ' . date('Y-m-d H:i:s') . ' Error al obtener datos');
        }
        curl_close($ch);
        $text = 'API CH: ' . date('Y-m-d H:i:s') . ' ' . json_encode($file_contents);
        return $file_contents;
    } catch (\Exception $e) {
        curl_close($ch);
        return false;
    }
}
/**
 * Obtiene las causas de una novedad específica.
 *
 * @param string $novedad El código de la novedad.
 * @return array Un array con las causas de la novedad o un array vacío si no se encuentran causas.
 */
function getNoveCausas($novedad)
{
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/estruct/";
    $method = 'GET';
    $queryParams = array(
        "start" => 0,
        "length" => 5000,
        "Estruct" => "NovC"
    );

    $data = ch_api($endpoint, '', $method, $queryParams);

    $arrayData = json_decode($data, true);

    $rs = array_filter($arrayData['DATA'], function ($element) use ($novedad) {
        return $element['CodiNov'] == $novedad;
    });

    return array_values($rs) ?? array();
}
/**
 * Obtiene los datos de una ficha de novedades y horarios de forma simplificada.
 *
 * @param string $legajo El legajo del empleado.
 * @param string $fecha La fecha para la cual se obtendrán los datos.
 * @param array $opt Opciones adicionales para filtrar los datos.
 * @return array|false Los datos de la ficha de novedades y horarios, o false si no se proporciona el legajo o la fecha.
 */
function getFicNovHorSimple($legajo, $fecha, $opt)
{
    if (!$legajo)
        return false;
    if (!$fecha)
        return false;

    // $opt = array("getNov" => "0", "getONov" => "0", "getHor" => "0", "getFic" => "0", "getEstruct" => "0", "getCierre" => "0");

    $payload = array(
        "FechIni" => $fecha,
        "FechFin" => $fecha,
        "onlyReg" => "0",
        "getReg" => $opt['getFic'] ?? 0,
        "getNov" => $opt['getNov'] ?? 0,
        "getONov" => $opt['getONov'] ?? 0,
        "getHor" => $opt['getHor'] ?? 0,
        "getEstruct" => $opt['getEstruct'] ?? 0,
        "getCierre" => $opt['getCierre'] ?? 1,
        "NovEx" => "",
        "ONovEx" => "",
        "HoraEx" => "",
        "LegApNo" => "",
        "LegDocu" => [],
        "LegRegCH" => [],
        "LegTipo" => [],
        "LegaD" => "",
        "LegaH" => "",
        "Lega" => [$legajo],
        "Empr" => [],
        "Plan" => [],
        "Conv" => [],
        "Sec2" => [],
        "Sect" => [],
        "Grup" => [],
        "Sucu" => [],
        "NovT" => [],
        "NovS" => [],
        "NovA" => [],
        "NovI" => [],
        "DiaL" => [],
        "DiaF" => [],
        "HsAT" => [],
        "HsTr" => [],
        "HorE" => [],
        "HorS" => [],
        "HorD" => [],
        "Falta" => [],
        "Nove" => [],
        "NoTi" => [],
        "ONov" => [],
        "Hora" => [],
        "Esta" => [],
        "EstaNov" => [0, 1, 2],
        "start" => 0,
        "length" => $opt['length'] ?? 1
    );
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/ficnovhor/";
    $data = ch_api($endpoint, $payload, 'POST', '');
    $arrayData = json_decode($data, true);
    $arrayData['DATA'] = $arrayData['DATA'] ?? '';
    if (empty($arrayData['DATA'])) {
        return [];
    }
    return array($arrayData['DATA'][0]);
}
/**
 * Obtiene el cierre de ficha para un legajo y fecha específicos.
 *
 * @param string $legajo El legajo del empleado.
 * @param string $fecha La fecha para la cual se desea obtener el cierre de ficha.
 * @return array El cierre de ficha para el legajo y fecha especificados.
 */
function getCierreFicha($legajo, $fecha)
{
    if (!$legajo || !$fecha) {
        return array();
    }

    $opt = array("getNov" => "0", "getONov" => "0", "getHor" => "0", "getFic" => "0");
    $data = getFicNovHorSimple($legajo, $fecha, $opt);
    $cierre = $data[0]['Cierre'] ?? array();
    return $cierre;
}
/**
 * Retrieves a specific novedad from the API.
 *
 * @param string | array $novedad The code of the novedad to retrieve.
 * @return array The data of the novedad, or an empty array if not found.
 */
function getNovedad($novedad)
{
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/estruct/";
    $method = 'GET';
    $queryParams = array(
        "start" => 0,
        "length" => 5000,
        "Estruct" => "Nov",
        "Codi" => (is_array($novedad)) ? $novedad : [$novedad]
    );

    $data = ch_api($endpoint, '', $method, $queryParams);

    $arrayData = json_decode($data, true);
    return ($arrayData['DATA']) ?? array();
}
Flight::route('/novedades-all', function () {
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/estruct/";
    $queryParams = array(
        "start" => 0,
        "length" => 5000,
        "Estruct" => "Nov"
    );
    $data = ch_api($endpoint, '', 'GET', $queryParams); // Obtenemos las novedades
    $arrayData = json_decode($data, true);
    $novedades = $arrayData['DATA'] ?? array();

    $queryParams = array(
        "start" => 0,
        "length" => 5000,
        "Estruct" => "NovC"
    );
    $data = ch_api($endpoint, '', 'GET', $queryParams); // Obtenemos las causas de las novedades
    $arrayData = json_decode($data, true);
    $causas = $arrayData['DATA'] ?? array();

    $arr = array(
        "novedades" => $novedades,
        "causas" => $causas
    );

    Flight::json($arr);
});
Flight::route('/novedades-agrupa', function () {
    // sleep('2');
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/estruct/";
    $queryParams = array(
        "start" => 0,
        "length" => 5000,
        "Estruct" => "Nov"
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
    $opt = array("getNov" => "1", "getONov" => "0", "getHor" => "1", "getFic" => "1");
    $data = getFicNovHorSimple($legajo, $fecha, $opt);
    if ($data) {
        $data[0]['NoveDelete'] = $_SESSION['ABM_ROL']['bNov'];
        $data[0]['NoveAdd'] = $_SESSION['ABM_ROL']['aNov'];
    }
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



Flight::start(); // Inicio FlightPHP
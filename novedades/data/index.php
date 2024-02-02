<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';

if (!$_SESSION) {
    Flight::json(array("error" => "Sesión finalizada."));
    exit;
}
// sleep(1);

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
    return $arrayData['DATA'][0];
}

Flight::route('/novedades/@NoveTipo/(@NoveCodi)', function ($NoveTipo, $NoveCodi) {

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
    Flight::json($data);
});
Flight::route('PUT /novedad', function () {

    if ($_SESSION['ABM_ROL']['mNov'] == '0') {
        Flight::json(array("error" => "No tiene permisos para modificar novedades."));
        return;
    }

    $payload = Flight::request()->data;
    $endpoint = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/novedades/";
    $method = 'PUT';
    $rs = ch_api($endpoint, array($payload), $method, '');
    Flight::json(json_decode($rs, true));
});


Flight::start(); // Inicio FlightPHP
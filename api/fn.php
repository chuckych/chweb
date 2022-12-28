<?php
// ini_set('memory_limit', '500M');
require '../../vendor/autoload.php';
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
$time_start = timeStart(); // Inicio
$pathLog  = __DIR__ . '/logs/'; // path de Logs Api
cleanFile($pathLog, 1, '.log'); // Elimina logs de los ultimos 7 días.
$iniData = (getIni(__DIR__ . '../../mobileApikey.php'));
header('WWW-Authenticate: Basic');
$_SERVER['HTTP_TOKEN'] = $_SERVER['HTTP_TOKEN'] ?? '';
$dataC     = checkToken($_SERVER['HTTP_TOKEN'], $iniData); // valida el token
$idCompany = $dataC['idCompany']; // Id de la cuenta {int}
$_SERVER['PHP_AUTH_USER'] = $_SERVER['PHP_AUTH_USER'] ?? '';
$_SERVER['PHP_AUTH_PW']   = $_SERVER['PHP_AUTH_PW'] ?? '';
$validData = $wc = '';

$request = Flight::request();
$dp      = $request->data;
$method  = $request->method;

$dp->start  = $dp->start ?? '';
$start      = intval(empty($dp->start) ? 0 : $dp->start);

$dp->length = $dp->length ?? '';
$length     = intval(empty($dp->length) ? 10 : $dp->length);
// Flight::json($request).exit;

$passAuth = explode('/', $_SERVER['PHP_SELF']);
/**
 * Valida el authenticate header
 */
if ($_SERVER['PHP_AUTH_USER'] != 'chweb' || $_SERVER['PHP_AUTH_PW'] != $passAuth[1]) {
    header('WWW-Authenticate: Basic realm="My Realm"');
    http_response_code(401);
    (response(array(), 0, 'Unauthorized User', 401, $time_start, 0, $idCompany));
    exit;
}
/**
 * Datos de la cuenta
 */
$dataCompany  = array(
    'host'        => $dataC['DBHost'],
    'user'        => $dataC['DBUser'],
    'pass'        => $dataC['DBPass'],
    'db'          => $dataC['DBName'],
    'auth'        => $dataC['DBAuth'],
    'idCompany'   => $dataC['idCompany'],
    'nameCompany' => $dataC['nameCompany'],
    'hostCHWeb'   => $dataC['hostCHWeb'],
);
/**
 * Devuelve valores separados por @separator de un array
 * @array {array} array de datos
 * @key {string} key a procesar
 * @separator {string} separador del valor
 */
function implodeArrayByKey(array $array, $key, $separator = ',')
{
    if ($array && $key) {
        $i = array_unique(array_column($array, $key));
        $i = implode("$separator", $i);
        return $i;
    }
    return false;
}
/**
 * convierte decimales en horas
 * @dec {float} numero decimal
 */
function decimalToTime($dec)
{
    // start by converting to seconds
    $s = ($dec * 3600);
    // we're given hours, so let's get those the easy way
    $h = floor($dec);
    // since we've "calculated" hours, let's remove them from the seconds variable
    $s -= $h * 3600;
    // calculate minutes left
    $m = floor($s / 60);
    // remove those from seconds as well
    $s -= $m * 60;
    // return the time formatted HH:MM:SS
    // return lz($hours).":".lz($minutes).":".lz($seconds);
    return lz($h) . ":" . lz($m);
}
/**
 * @regTipo {int} valor
 */
function tipoFic($regTipo)
{
    switch ($regTipo) {
        case '0':
            $t = 'Normal';
            break;
        case '1':
            $t = 'Manual';
            break;
        default:
            $t = 'Normal';
            break;
    }
    return $t;
}
function filtrarObjetoArr($array, $key, $valor) // Funcion para filtrar un objeto
{
    $a = array();
    if ($array && $key && $valor) {
        foreach ($array as $v) {
            if ($v[$key] === $valor) {
                $a[] = $v;
            }
        }
    }
    return $a;
}
function filtrarObjetoArr2($array, $key, $key2, $valor, $valor2) // Funcion para filtrar un objeto
{
    $a = array();
    if ($array && $key && $key2 && $valor && $valor2) {
        foreach ($array as $v) {
            if ($v[$key] === $valor && $v[$key2] === $valor2) {
                $a[] = $v;
            }
        }
        // $a = array_filter($array, function ($e) use ($key, $key2, $valor, $valor2) {
        //     return $e[$key] === $valor && $e[$key2] === $valor2;
        // });
        // foreach ($a as $key => $x) {
        //     $a[] = $x;
        // }
    }
    return $a;
}
// lz = leading zero
function lz($num)
{
    return (strlen($num) < 2) ? "0{$num}" : $num;
}
/** 
 * @param {String} Zona Horaria. Por defecto America/Argentina/Buenos_Aires
 */
function tz($tz = 'America/Argentina/Buenos_Aires')
{
    return date_default_timezone_set($tz);
}
/**
 * @param {String} Idioma. Por defecto es_ES
 */
function tzLang($tzLang = "es_ES")
{
    return setlocale(LC_TIME, $tzLang);
}
function dateTimeNow()
{
    tz();
    $t = date("Y-m-d H:i:s");
    return $t;
}
function errorReport()
{
    if ($_SERVER['SERVER_NAME'] == 'localhost') { // Si es localhost
        error_reporting(E_ALL); // Muestra todos los errores
        ini_set('display_errors', '1'); // Muestra todos los errores
    } else {
        error_reporting(E_ALL);
        ini_set('display_errors', '0');
    }
}
/** 
 * @url Ruta del archivo INI de configuracion de cuentas
 */
function getIni($url) // obtiene el json de la url
{
    if (!file_exists($url)) { // Si no existe el archivo
        writeLog("No existe archivo \"$url\"", __DIR__ . "/logs/" . date('Ymd') . "_getIni.log", ''); // escribimos en el log
        return false; // devolvemos false
    }
    $data = file_get_contents($url); // obtenemos el contenido del archivo
    if (!$data) { // si el contenido está vacío
        writeLog("No hay informacion en el archivo \"$url\"", __DIR__ . "/logs/" . date('Ymd') . "_getIni.log", ''); // escribimos en el log
        return false; // devolvemos false
    }
    $data = parse_ini_file($url, true); // Obtenemos los datos del data.php
    return $data; // devolvemos el json
}
/**
 * @token {string} token api
 * @inidata {array} array data
 */
function checkToken($token, $iniData = array())
{
    $vkey = $idCompany = '';
    $data = array();
    if ($iniData) {
        foreach ($iniData as $v) {
            if ($v['Token'] == $token) {
                $idCompany = $v['idCompany'];
                $vkey      = $v['recidCompany'];
                $data = array(
                    $v
                );
                break;
            }
        }
        if (!$vkey) {
            http_response_code(400);
            (response(array(), 0, 'Invalid Token', 400, timeStart(), 0, $idCompany));
            exit;
        }
    } else {
        http_response_code(400);
        (response(array(), 0, 'Required Data Ini', 400, timeStart(), 0, $idCompany));
        exit;
    }
    return $data[0];
}
/**
 * 
 * @data {array} response data
 * @total {int} count data
 * @msg {string} mensaje de respuesta default OK
 * @code {int} http_response_code
 * @tiempoScript {floatval} duración del srcipt, default 0
 * @idCompany {int} id de la cuenta
 */
// $start = start();
// $length = length();
// $response = function ($data = array(), $total = 0, $msg = 'OK', $code = 200, $time_start = 0, $count = 0, $idCompany = 0) use ($start,$length)
function response($data = array(), $total = 0, $msg = 'OK', $code = 200, $time_start = 0, $count = 0, $idCompany = 0)
{
    $code = intval($code);
    $start  = ($code != 400) ? start() : 0;
    $length  = ($code != 400) ? length() : 0;

    $time_end = microtime(true);
    $tiempoScript = number_format($time_end - $time_start, 4);

    $array = array(
        'RESPONSE_CODE' => http_response_code(intval($code)),
        'START'         => intval($start),
        'LENGTH'        => intval($length),
        'TOTAL'         => intval($total),
        'COUNT'         => intval($count),
        'MESSAGE'       => $msg,
        'TIME'          => floatval($tiempoScript),
        // 'REQUEST_URI'   => $_SERVER['REQUEST_URI'],
        'DATA' => $data,
    );

    echo json_encode($array, JSON_PRETTY_PRINT);

    /** LOG API CONFIG */
    // $textParams = array();
    // foreach ($_REQUEST as $key => $value) {
    //     $arrRequest[] = "$key=$value";
    //     array_push($textParams, $arrRequest);
    // }

    $textParams = urldecode($_SERVER['REQUEST_URI']); // convert to string

    $ipAdress = $_SERVER['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'] ?? '';
    $agent    = $_SERVER['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $idCompany    = $idCompany;

    if ($agent) {
        require_once __DIR__ . '../../control/PhpUserAgent/src/UserAgentParser.php';
        $parsedagent[] = parse_user_agent($agent);
        foreach ($parsedagent as $key => $value) {
            $platform = $value['platform'];
            $browser  = $value['browser'];
            $version  = $value['version'];
        }
        $agent = $platform . ' ' . $browser . ' ' . $version;
    }

    $pathLog  = __DIR__ . '/logs/'; // path Log Api
    $nameLog  = date('Ymd') . '_request_' . padLeft($idCompany, 3, 0) . '.log'; // path Log Api
    /** start text log*/
    $TextLog = "\n REQUEST  = [ $textParams ]\n RESPONSE = [ RESPONSE_CODE=\"$array[RESPONSE_CODE]\" START=\"$array[START]\" LENGTH=\"$array[LENGTH]\" TOTAL=\"$array[TOTAL]\" COUNT=\"$array[COUNT]\" MESSAGE=\"$array[MESSAGE]\" TIME=\"$array[TIME]\" IP=\"$ipAdress\" AGENT=\"$agent\" ]\n----------";
    /** end text log*/
    writeLog($TextLog, $pathLog . $nameLog); // Log Api
    /** END LOG API CONFIG */
    exit;
}
/** 
 * @path {string} ruta de los archivos a eliminar
 * @dias {int} cantidad de días para atras de los archivos a mantener sin eliminar
 * @ext {string} extensión del archivo a eliminar
 */
function cleanFile($path, $dias, $ext) // borra los archivo a partir de una cantidad de días
{
    $files = glob($path . '*' . $ext); //obtenemos el nombre de todos los ficheros
    if ($files) {
        foreach ($files as $file) { // recorremos todos los ficheros.
            $lastModifiedTime = filemtime($file); // obtenemos la fecha de modificación del fichero
            $currentTime      = time(); // obtenemos la fecha actual
            $dateDiff         = dateDiff(date('Ymd', $lastModifiedTime), date('Ymd', $currentTime)); // obtenemos la diferencia de fechas
            ($dateDiff >= intval($dias)) ? unlink($file) : ''; //elimino el fichero
        }
    }
}
/** 
 * @query {string} query sql obligatorio
 */
$dbApiQuery = function ($query, $count = 0) use ($dataCompany) {
    if (!$query) {
        http_response_code(400);
        (response(array(), 0, 'empty query', 400, timeStart(), 0, $dataCompany['idCompany']));
        exit;
    }
    require __DIR__ . './connectDBPDO.php';
    try {
        $resultSet = array();
        $stmt = $conn->query($query);
        while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $resultSet[] = $r;
        }
        return $resultSet;
        $stmt = null;
        $conn = null;
    } catch (Exception $e) {
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorMSQuery.log'; // ruta del archivo de Log de errores
        writeLog(PHP_EOL . 'Message: ' . json_encode($e->getMessage(), JSON_UNESCAPED_UNICODE) . PHP_EOL . 'Source: ' . '"' . $_SERVER['REQUEST_URI'] . '"', $pathLog); // escribir en el log de errores el error
        writeLog(PHP_EOL . 'Query: ' . $query, $pathLog); // escribir en el log de errores el error
        http_response_code(400);
        (response(array(), 0, $e->getMessage(), 400, timeStart(), 0, ''));
        exit;
    }
};
/**
 * @text {string} texto del log
 * @path {string} ruta del archivo con su extension
 * @type {string} defecto false, value export = text sin fecha hora
 */
function writeLog($text, $path, $type = false)
{
    $log    = fopen($path, 'a');
    $date   = dateTimeNow();
    $text   = ($type == 'export') ? $text . "\n" : $date . ' ' . $text . "\n";
    file_put_contents($path, $text, FILE_APPEND | LOCK_EX);
}
/** 
 * @str {string} valor
 * @lenght {int} cantidad de caracteres
 * @pad {string} delimitador de caracteres, defecto '' (un espacio)
 */
function padLeft($str, $length, $pad = ' ')
{
    if ($str && $length) {
        return str_pad($str, intval($length), $pad, STR_PAD_LEFT);
    } else {
        return false;
    }
}
/**
 * @$date_1 {string} Fecha 1
 * @$date_2 {string} Fecha 2
 * @differenceFormat {string} default '%a'
 */
function dateDiff($date_1, $date_2, $differenceFormat = '%a') // diferencia en días entre dos fechas
{
    if ($date_1 && $date_2) {
        $datetime1 = date_create($date_1); // creo la fecha 1
        $datetime2 = date_create($date_2); // creo la fecha 2
        $interval = date_diff($datetime1, $datetime2); // obtengo la diferencia de fechas
        return $interval->format($differenceFormat); // devuelvo el número de días
    }
    return false;
}
function start()
{
    $request = Flight::request();
    $p = $request->data;
    // $p = $_REQUEST;
    // $p = file_get_contents("php://input");
    // $p = json_decode($p, true);
    $p->start = $p->start ?? '0';
    $start  = empty($p->start) ? 0 : $p->start;
    return intval($start);
}
function length()
{
    // $p = $_REQUEST;
    // $p = file_get_contents("php://input");
    // $p = json_decode($p, true);
    $request = Flight::request();
    $p = $request->data;
    $p->length = $p->length ?? '';
    $length = empty($p->length) ? 10 : $p->length;
    return intval($length);
}
/** 
 * @hora {string} hora en formato HH:MM
 */
function horaMin($hora)
{
    if ($hora) {
        $hora = explode(":", $hora);
        $MinHora = intval($hora[0]) * 60;
        $Min = intval($hora[1] ?? '');
        $Minutos = $MinHora + $Min;
        return $Minutos;
    }
    return false;
}
/**
 * @array {array} matriz para filtrar
 * @key {string} llave de la matriz a filtrar
 * @valor {string} valor de la llave
 */
function filtrarObjeto($array, $key, $valor) // Funcion para filtrar un objeto
{
    if ($array && $key && $valor) {
        $r = array_filter($array, function ($e) use ($key, $valor) {
            return $e[$key] === $valor;
        });
        foreach ($r as $key => $value) {
            return ($value);
        }
    }
    return false;
}
/** 
 * inicio en microsegundos 
 */
function timeStart()
{
    return microtime(true);
}
/**
 * @datetime {string} fecha hora
 * @format {string} default "Y-m-d"
 */
function fechFormat($dateTime, $format = 'Y-m-d')
{
    if ($dateTime) {
        if ($dateTime != '0000-00-00 00:00:00') {
            $x = date_create($dateTime);
            $x  = date_format($x, $format);
            return $x;
        } else {
            return false;
        }
    }
    return false;
}
/**
 * @key {string} Parámetro a controlar
 * @valor {string} or {int} valor a controlar
 * @type {string} si es string o int
 * @lenght {int} la cantidad maxima de caracteres
 * @validArr {array} array de valores admitidos
 */
function vp($value, $key, $type = 'str', $length = 1, $validArr = array())
{
    if ($value) {
        if ($type == 'int') {
            if ($value) {
                if (!is_numeric($value)) {
                    http_response_code(400);
                    (response(array(), 0, "Parámetro '$key' de ser {int}. Valor '$value'", 400, timeStart(), 0, 0));
                    exit;
                } else {
                    if (!filter_var($value, FILTER_VALIDATE_INT)) {
                        http_response_code(400);
                        (response(array(), 0, "Parámetro '$key' de ser {int}. Valor = '$value'", 400, timeStart(), 0, 0));
                        exit;
                    }
                }
                if (strlen($value) > $length) {
                    http_response_code(400);
                    (response(array(), 0, "Parámetro '$key' de ser menor o igual a '$length' caracteres. Valor '$value'", 400, timeStart(), 0, 0));
                    exit;
                }
                if (($value) < 0) {
                    http_response_code(400);
                    (response(array(), 0, "Parámetro '$key' de ser mayor o igual a '1'. Valor '$value'", 400, timeStart(), 0, 0));
                    exit;
                }
            }
        }
        if ($type == 'int01') {
            if ($value) {
                switch ($value) {
                    case (!is_numeric($value)):
                        http_response_code(400);
                        (response(array(), 0, "Parámetro '$key' debe ser {int}. Valor '$value'", 400, timeStart(), 0, 0));
                        exit;
                        break;
                    case (!filter_var($value, FILTER_VALIDATE_INT)):
                        http_response_code(400);
                        (response(array(), 0, "Parámetro '$key' debe ser {int}. Valor = '$value'", 400, timeStart(), 0, 0));
                        exit;
                        break;
                    case (strlen($value) > $length):
                        http_response_code(400);
                        (response(array(), 0, "Parámetro '$key' debe ser igual a '$length' caracter. Valor '$value'", 400, timeStart(), 0, 0));
                        exit;
                        break;
                    case (($value) < 0):
                        http_response_code(400);
                        (response(array(), 0, "Parámetro '$key' debe ser mayor o igual a '1'. Valor '$value'", 400, timeStart(), 0, 0));
                        exit;
                        break;
                    case (($value) > 1):
                        http_response_code(400);
                        (response(array(), 0, "Parámetro '$key' no puede ser mayor '1'. Valor '$value'", 400, timeStart(), 0, 0));
                        exit;
                        break;
                    default:
                        break;
                }

                // if (!is_numeric($value)) {
                //     http_response_code(400);
                //     (response(array(), 0, "Parámetro '$key' debe ser {int}. Valor '$value'", 400, timeStart(), 0, 0));
                //     exit;
                // } else {
                //     if (!filter_var($value, FILTER_VALIDATE_INT)) {
                //         http_response_code(400);
                //         (response(array(), 0, "Parámetro '$key' debe ser {int}. Valor = '$value'", 400, timeStart(), 0, 0));
                //         exit;
                //     }
                // }
                // if (strlen($value) > $length) {
                //     http_response_code(400);
                //     (response(array(), 0, "Parámetro '$key' debe ser igual a '$length' caracter. Valor '$value'", 400, timeStart(), 0, 0));
                //     exit;
                // }
                // if (($value) < 0) {
                //     http_response_code(400);
                //     (response(array(), 0, "Parámetro '$key' debe ser mayor o igual a '1'. Valor '$value'", 400, timeStart(), 0, 0));
                //     exit;
                // }
                // if (($value) > 1) {
                //     http_response_code(400);
                //     (response(array(), 0, "Parámetro '$key' no puede ser mayor '1'. Valor '$value'", 400, timeStart(), 0, 0));
                //     exit;
                // }
            }
        }
        if ($type == 'intArray') {
            if ($value) {
                if (!is_array($value)) {
                    http_response_code(400);
                    (response(array(), 0, "Parámetro '$key' debe ser un {array}. Valor '$value'", 400, timeStart(), 0, 0));
                    exit;
                }
                foreach (array_unique($value) as $v) {
                    if ($v) {
                        if (!is_numeric($v)) {
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' de ser {int}. Valor = '$v'", 400, timeStart(), 0, 0));
                            exit;
                        } else {
                            if (!filter_var($v, FILTER_VALIDATE_INT)) {
                                http_response_code(400);
                                (response(array(), 0, "Parámetro '$key' de ser {int}. Valor = '$v'", 400, timeStart(), 0, 0));
                                exit;
                            }
                        }
                    }
                    if (($v) < 0) {
                        http_response_code(400);
                        (response(array(), 0, "Parámetro '$key' de ser mayor o igual a '0'", 400, timeStart(), 0, 0));
                        exit;
                    }
                    if (strlen($v) > $length) {
                        http_response_code(400);
                        (response(array(), 0, "Parámetro '$key' de ser menor o igual a '$length' caracteres. Valor '$v'", 400, timeStart(), 0, 0));
                        exit;
                    }
                }
            }
        }
        if ($type == 'intArrayM8') {
            if ($value) {
                if (!is_array($value)) {
                    http_response_code(400);
                    (response(array(), 0, "Parámetro '$key' debe ser un {array}. Valor '$value'", 400, timeStart(), 0, 0));
                    exit;
                }
                foreach ($value as $v) {
                    if ($v) {
                        if (!is_numeric($v)) {
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' de ser {int}. Valor = '$v'", 400, timeStart(), 0, 0));
                            exit;
                        } else {
                            if (!filter_var($v, FILTER_VALIDATE_INT)) {
                                http_response_code(400);
                                (response(array(), 0, "Parámetro '$key' de ser {int}. Valor = '$v'", 400, timeStart(), 0, 0));
                                exit;
                            }
                        }
                    }
                    if (($v) < 0) {
                        http_response_code(400);
                        (response(array(), 0, "Parámetro '$key' de ser mayor o igual a '0'", 400, timeStart(), 0, 0));
                        exit;
                    }
                    if (strlen($v) > $length) {
                        http_response_code(400);
                        (response(array(), 0, "Parámetro '$key' de ser menor o igual a '$length' caracteres. Valor '$v'", 400, timeStart(), 0, 0));
                        exit;
                    }
                    if (($v) > 8) {
                        http_response_code(400);
                        (response(array(), 0, "Parámetro '$key' de ser menor o igual a '8'", 400, timeStart(), 0, 0));
                        exit;
                    }
                }
            }
        }
        if ($type == 'intArrayM0') { // {int}mayor a 0
            if ($value) {
                if (!is_array($value)) {
                    http_response_code(400);
                    (response(array(), 0, "Parámetro '$key' debe ser un {array}. Valor '$value'", 400, timeStart(), 0, 0));
                    exit;
                }
                foreach ($value as $v) {
                    if ($v) {
                        if (!is_numeric($v)) {
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' de ser {int}. Valor = '$v'", 400, timeStart(), 0, 0));
                            exit;
                        }
                        if (!filter_var($v, FILTER_VALIDATE_INT)) {
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' de ser {int}. Valor = '$v'", 400, timeStart(), 0, 0));
                            exit;
                        }
                        if ($v === 0) {
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' de ser mayor a '0'", 400, timeStart(), 0, 0));
                            exit;
                        }
                        if ($v < 0) {
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' no debe ser menor a '0'", 400, timeStart(), 0, 0));
                            exit;
                        }
                        if (strlen($v) > $length) {
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' de ser menor o igual a '$length'. Valor '$v'", 400, timeStart(), 0, 0));
                            exit;
                        }
                    }
                }
            }
        }
        if ($type == 'numArray01') {
            if ($value) {
                if (!is_array($value)) {
                    http_response_code(400);
                    (response(array(), 0, "Parámetro '$key' debe ser un {array}. Valor '$value'", 400, timeStart(), 0, 0));
                    exit;
                }
                foreach ($value as $v) {
                    if ($v) {
                        if (!is_numeric($v)) {
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' de ser {int}. Valor = '$v'", 400, timeStart(), 0, 0));
                            exit;
                        }
                        if (($v) < 0) {
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' de ser mayor o igual a '0'. Valor = '$v'", 400, timeStart(), 0, 0));
                            exit;
                        }
                        if (($v) > 1) {
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' de '0' o '1'. Valor = '$v'", 400, timeStart(), 0, 0));
                            exit;
                        }
                        if (strlen($v) > $length) {
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' de ser menor o igual a '$length'.Valor '$v'", 400, timeStart(), 0, 0));
                            exit;
                        }
                    }
                }
            }
        }
        if ($type == 'strArray') {
            if ($value) {
                if (!is_array($value)) {
                    http_response_code(400);
                    (response(array(), 0, "Parámetro '$key' debe ser un {array}. Valor '$value'", 400, timeStart(), 0, 0));
                    exit;
                }
                foreach ($value as $v) {
                    if (strlen($v) > $length) {
                        if ($v) {
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' de ser menor o igual a '$length'. Valor '$v'", 400, timeStart(), 0, 0));
                            exit;
                        }
                    }
                }
            }
        }
        if ($type == 'strArrayMMlength') {
            if ($value) {
                if (!is_array($value)) {
                    http_response_code(400);
                    (response(array(), 0, "Parámetro '$key' debe ser un {array}. Valor '$value'", 400, timeStart(), 0, 0));
                    exit;
                }
                foreach ($value as $v) {
                    if ($v) {
                        if (strlen($v) <> $length) {
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' debe contener '$length'. Valor '$v'", 400, timeStart(), 0, 0));
                            exit;
                        }
                    }
                }
            }
        }
        if ($type == 'str') {
            if ($value) {
                if (strlen($value) > $length) {
                    http_response_code(400);
                    (response(array(), 0, "Parámetro '$key' de ser menor o igual a '$length' caracteres. Valor '$value", 400, timeStart(), 0, 0));
                    exit;
                }
            }
        }
        if ($type == 'strArraySel2') {
            if ($value) {
                if (!is_array($value)) {
                    http_response_code(400);
                    (response(array(), 0, "Parámetro '$key' debe ser un {array}. Valor '$value'", 400, timeStart(), 0, 0));
                    exit;
                }
                foreach ($value as $v) {
                    if (strlen($v) < 3 && $v != '') {
                        http_response_code(400);
                        (response(array(), 0, "Parámetro '$key' erroneo. Valor '$v'. Debe ser formato 1-1. Donde el primer elemento es el Sector y el segundo elemento es la secciónxx.", 400, timeStart(), 0, 0));
                        exit;
                    }
                    if ($v) {
                        if (!strpos($v, '-')) {
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' erroneo. Valor '$v'. Debe ser formato 1-1. Donde el primer elemento es el Sector y el segundo elemento es la sección.", 400, timeStart(), 0, 0));
                            exit;
                        }
                        $vArr = explode('-',$v);
                        if(count($vArr)>2){
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' erroneo. Valor '$v'. Debe ser formato 1-1. Donde el primer elemento es el Sector y el segundo elemento es la sección.", 400, timeStart(), 0, 0));
                            exit;
                        }
                        $index0 = ($vArr[0]);
                        $index1 = ($vArr[1]);
                        if ($index0 == '0'|| $index1 == '0') {
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' erroneo. Valor '$v'. Debe ser formato 1-1. Donde el primer elemento es el Sector y el segundo elemento es la sección y los valores no pueden ser 0 (ceros)", 400, timeStart(), 0, 0));
                            exit;
                        }
                        if (!is_numeric($index0) || !is_numeric($index1)) {
                            http_response_code(400);
                            (response(array(), 0, "Parámetro '$key' erroneo. Valor '$v'. Debe ser formato 1-1. Donde el primer elemento es el Sector y el segundo elemento es la sección y los valores deben ser números enteros", 400, timeStart(), 0, 0));
                            exit;
                        }
                    }
                }
            }
        }
    }
    if ($type == 'strValid') {
        if ($value) {
            if (!in_array($value, $validArr)) {
                $valores = implode(', ', $validArr);
                http_response_code(400);
                (response("Valor de parámetro '$key' es inválido. Valor '$value'. Valores disponibles: $valores", 0, 'Error', 400, timeStart(), 0, 0));
                exit;
            }
        } else {
            http_response_code(400);
            (response("Parámetro $key es requerido.", 0, 'Error', 400, timeStart(), 0, 0));
            exit;
        }
    }
    return $value;
}
function isValidJSON($str)
{
    json_decode($str);
    return json_last_error() == JSON_ERROR_NONE;
}
function calculaEdad($fecha)
{
    if ($fecha) {
        if ($fecha != '1753-01-01') {
            $dia_actual = date("Y-m-d");
            $edad_diff = date_diff(date_create($fecha), date_create($dia_actual));
            return $edad_diff;
        }
    }
    return '';
}
function calculaEdadStr($fecha)
{
    if ($fecha) {
        if ($fecha != '1753-01-01') {
            $EdadStr = '';
            $Edad      = intval(calculaEdad(fechFormat($fecha, 'Y-m-d'))->format('%y'));
            $EdadMeses = intval(calculaEdad(fechFormat($fecha, 'Y-m-d'))->format('%m'));
            $EdadDias  = intval(calculaEdad(fechFormat($fecha, 'Y-m-d'))->format('%d'));
            $EdadStr .= ($Edad) ? $Edad . (($Edad > 1) ? ' Años' : ' Año') : '';
            $EdadStr .= ($EdadMeses) ? ' ' . (($EdadMeses > 1) ? $EdadMeses . ' Meses' : $EdadMeses . ' Mes') : '';
            $EdadStr .= ($EdadDias) ? ' ' . (($EdadDias > 1) ? $EdadDias . ' Días' : $EdadDias . ' Día') : '';
            return trim($EdadStr);
        }
    }
    return '';
}
function IncTiStr($LegIncTi)
{
    if ($LegIncTi) {
        switch ($LegIncTi) {
            case '0':
                return "Estándar sin control de descanso";
                break;
            case '1':
                return "Estándar con control de descanso";
                break;
            case '2':
                return "(Hs. a Trabajar - Hs. Trabajadas)";
                break;
            case '3':
                return "(Hs. a Trabajar - Hs. Trabajadas) - Descanso como tolerancia";
                break;
            case '4':
                return "(Hs. a Trabajar - Hs. Trabajadas) + Incumplimiento de descanso";
                break;
            case '5':
                return "Recortado sin control de descanso";
                break;
            case '6':
                return "Recortado con control de descanso";
                break;
            default:
                return "Sin definir";
                break;
        }
    }
    return '';
}
function LegHoAlStr($LegHoAl)
{
    if ($LegHoAl) {
        switch ($LegHoAl) {
            case '0':
                return "Según Asignación";
                break;
            case '1':
                return "Alternativo según fichadas";
                break;
            default:
                return "Sin definir";
                break;
        }
    }
    return '';
}
function fecha($date, $format = 'Y-m-d')
{
    try {
        $date  = new DateTime($date);
        $date  = $date->format($format);
    } catch (exception $e) {
        file_put_contents(__DIR__ . "/logs/" . date('Ymd') . "_errFecha.log", date('Y-m-d H:i') . ' ' . $_SERVER['PHP_SELF'] . ' ' . $e->getMessage() . "\n", FILE_APPEND | LOCK_EX);
        return false;
    }
    return $date;
}
function diaSemana($Ymd)
{
    tz();
    tzLang();
    $scheduled_day = $Ymd;
    $days = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado'];
    $day = date('w', strtotime($scheduled_day));
    $scheduled_day = $days[$day];
    return $scheduled_day;
}

$authBasic = base64_encode('chweb:' . $dataC['homeHost']);
$token     = $_SERVER['HTTP_TOKEN'];

$requestApi = function ($url, $payload, $timeout = 10) use ($authBasic, $token) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "Accept: */*",
        'Content-Type: application/json',
        'Authorization: Basic ' . $authBasic, // Basic Authentication
        "Token: $token",
    ));
    $file_contents = curl_exec($ch);
    $curl_errno    = curl_errno($ch); // get error code
    $curl_error    = curl_error($ch); // get error information

    if ($curl_errno > 0) { // si hay error
        $text = "cURL Error ($curl_errno): $curl_error"; // set error message
        $pathLog = __DIR__ . '.' . date('Ymd') . '_errorCurl.log'; // ruta del archivo de Log de errores
        fileLog($text, $pathLog); // escribir en el log de errores el error
    }

    curl_close($ch);
    if ($file_contents) {
        return $file_contents;
    } else {
        $pathLog = __DIR__ . '.' . date('Ymd') . '_errorCurl.log'; // ruta del archivo de Log de errores
        fileLog('Error al obtener datos', $pathLog); // escribir en el log de errores el error
        return false;
    }
};

/**
 * @param {str} document_number -> string solo digitos
 * @param {str} gender -> debe contener H, M o S
 * @return {str}
 **/
function getCuil($document_number, $gender)
{
    /** Formula: https://es.wikipedia.org/wiki/Clave_%C3%9Anica_de_Identificaci%C3%B3n_Tributaria */
    $AB = '';
    $C  = '';
    // define('HOMBRE', ["HOMBRE", "M", "MALE"]);
    // define('MUJER', ["MUJER", "F", "FEMALE"]);
    // define('SOCIEDAD', ["SOCIEDAD", "S", "SOCIETY"]);

    $HOMBRE   = ["HOMBRE", "M", "MALE"];
    $MUJER    = ["MUJER", "F", "FEMALE"];
    $SOCIEDAD = ["SOCIEDAD", "S", "SOCIETY"];

    $gender = ucwords($gender);
    $document_number = str_pad($document_number, 8, '0', STR_PAD_LEFT);

    // Defino el valor del prefijo.
    if (array_search($gender, $HOMBRE)) {
        $AB = "20";
    } else if (array_search($gender, $MUJER)) {
        $AB = "27";
    } else {
        $AB = "30";
    }

    $multiplicadores = [3, 2, 7, 6, 5, 4, 3, 2];

    // Realizo las dos primeras multiplicaciones por separado.
    $calculo = intval(substr($AB, 0, 1)) * 5 + intval(substr($AB, 1, 1)) * 4;
    /*
    * Recorro el arreglo y el numero de document_number para
    * realizar las multiplicaciones.
    */
    for ($i = 0; $i < 8; $i++) {
        $calculo += intval(substr($document_number, $i, 1)) * $multiplicadores[$i];
    }
    // Calculo el resto.
    $resto = (intval($calculo) % 11);
    /*
    * Llevo a cabo la evaluacion de las tres condiciones para
    * determinar el valor de C y conocer el valor definitivo de
    * AB.
    */
    if ($AB != 30 && $resto == 1) {
        if ($AB == 20) {
            $C = "9";
        } else {
            $C = "4";
        }
        $AB = "23";
    } else if ($resto === 0) {
        $C = "0";
    } else {
        $C = 11 - $resto;
    }
    $cuil_cuit = $AB . '-' . $document_number . '-' . $C;
    // $text = date('H:i:s') . " DNI: \"$document_number\". GENERO: \"$gender\". CUIL : \"$cuil_cuit\".\n";
    // file_put_contents(date('Y-m-d') . '_logRequest.log', $text, FILE_APPEND | LOCK_EX);
    return $cuil_cuit;
}

<?php
ini_set('max_execution_time', 1800); //1800 seconds = 30 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
error_reporting(E_ALL);
ini_set('display_errors', '0');
$start = microtime(true);
require __DIR__ . '../../../../vendor/autoload.php';
timeZone_task();

$_SERVER["argv"][1] = $_SERVER["argv"][1] ?? '';

if ($_SERVER["argv"][1] != "1ec558a60b5dda24597816c924776716018caf8b") {
    $data = array(
        'Mensaje' => 'Parametro no valido',
        'date' => date('Y-m-d H:i:s'),
        'status' => 'error'
    );
    sendEmailTask("Error al ejecutar Task Mobile Light " . date('Y-m-d H:i:s'), "Error al ejecutar Task Mobile Light " . date('Y-m-d H:i:s'));
    Flight::json($data);
    exit;
}

$iniKeys = (getDataIni_task(__DIR__ . '../../../../mobileApikey.php'));

if (!$iniKeys) { /** si no existe el archivo o no hay información*/
    $texterr = "No existe Archivo mobileApikey o no hay información en el mismo";
    sendEmailTask("Task Mobile: Error iniKeys", "<pre>$texterr<pre>"); // Enviamos Email con detalle del error
    Flight::json($array['error']);
    exit;
}

function E_ALL()
{
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
}
function CountAgrupArray($array, $keyName = 'key')
{
    $countMap = array();
    foreach ($array as $value) {
        if (isset($countMap[$value])) {
            $countMap[$value]++;
        } else {
            $countMap[$value] = 1;
        }
    }
    $output = array();
    foreach ($countMap as $value => $count) {
        $registros = ($count > 1) ? 'registros' : 'registro';
        $output[] = array("[$value]: $count $registros");
        // $o = array_push($output, $value.': '.$count.' registros');
    }
    $simple_array = array_reduce($output, 'array_merge', []);
    return $simple_array;
}
function sanitizeAppVersion($string)
{
    /** realizamos una comprobación en la cadena $string y si ésta contiene una versión numérica con formato "x.x.x" de longitud 3 o superior, se extrae y se devuelve. Para hacer esto, utiliza la función preg_match() de expresiones regulares para buscar un patrón que contenga cualquier combinación de números y puntos de longitud completa, y luego utilizar preg_replace() para eliminar los números después del segundo punto. Si no se encuentra ninguna versión en la cadena, se devuelve simplemente la cadena original. */
    // string ejemplo  = v1.5.4  - 20220915 - ip83 - settings_hrprocess
    if (!$string)
        return '';
    if (preg_match('/([\d\.]+)\s/', $string, $match)) {
        $version = $match[1];
        $version = preg_replace('/\.(\d{3,}).*/', '', $version);
        return $version; // Salida: 1.5.4
    } else {
        return $string; // string
    }
}
function sendEmailTask($subjet, $body)
{
    $url = 'https://ht-api.helpticket.com.ar/sendMail/';

    $data = array(
        "subjet" => $subjet,
        "to" => "task-aws-mobile",
        "replyTo" => "task-aws-mobile",
        "body" => $body
    );

    $timeout = 10;
    $ch = curl_init(); // initialize curl handle
    curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); // The number of seconds to wait while trying to connect
    curl_setopt($ch, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $headers = array(
        'Content-Type:application/json',
        // Le enviamos JSON al servidor con los datos
        'Token:e47c43594cf22b42588c687c7f7e9871a52245ac' // token
    );
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); // Add headers
    $data_content = curl_exec($ch); // extract information from response
    $curl_errno = curl_errno($ch); // get error code
    $curl_error = curl_error($ch); // get error information
    if ($curl_errno > 0) { // si hay error
        $text = "cURL Error ($curl_errno): $curl_error"; // set error message
        fileLog($text, __DIR__ . "/logs/" . date('Ymd') . "_sendEmail.log"); // escribimos 
        exit; // salimos del script
    }
    curl_close($ch); // close curl handle
    // return ($data_content);
}
function MSQuery_task($query)
{
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    require __DIR__ . '../../../../config/conect_mssql.php';
    try {
        $stmt = sqlsrv_query($link, $query, $params, $options);
        if ($stmt === false) {
            $error = sqlsrv_errors();
            foreach ($error as $key => $e) {
                throw new Exception(($e['message']));
            }
            return false;
        } else {
            return true;
        }

    } catch (\Throwable $th) { // si hay error en la consulta
        $pathLog = __DIR__ . '/logs/error_insert_CH/';
        fileLog($th->getMessage(), $pathLog . '/' . date('Ymd') . '_error_MSQuery.log');
    }
}
function filtrarObjeto_task($array, $key, $valor) // Funcion para filtrar un objeto
{
    $r = array_filter($array, function ($e) use ($key, $valor) {
        return $e[$key] === $valor;
    });
    foreach ($r as $key => $value) {
        return ($value);
    }
}
function pdoQuery_task($sql)
{
    require __DIR__ . '../../../../config/conect_pdo.php';
    try {
        $stmt = $connpdo->prepare($sql);
        return ($stmt->execute()) ? true : false;
    } catch (\Throwable $th) { // si hay error en la consulta
        $pathLog = __DIR__ . '../../../../logs/' . date('Ymd') . '_errorPdoQuery.log'; // ruta del archivo de Log de errores
        fileLog($th->getMessage(), $pathLog); // escribir en el log de errores el error
    }
    $stmt = null;
}
function PrintRespuestaJson($status, $Mensaje)
{
    $data = array('status' => $status, 'Mensaje' => $Mensaje);
    echo json_encode($data);
    /** Imprimo json con resultado */
}
function simple_pdoQuery($sql)
{
    require __DIR__ . '../../../../config/conect_pdo.php';
    try {
        $stmt = $connpdo->prepare($sql);
        $stmt->execute();
        // $result = $stmt->fetch(PDO::FETCH_ASSOC);
        while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
            return $row;
        }
    } catch (\Throwable $th) { // si hay error en la consulta
        $pathLog = __DIR__ . '../../../../logs/' . date('Ymd') . '_errorPdoQuery.log'; // ruta del archivo de Log de errores
        fileLog($th->getMessage(), $pathLog); // escribir en el log de errores el error
    }
    $stmt = null;
}
function dateDifference_task($date_1, $date_2, $differenceFormat = '%a') // diferencia en días entre dos fechas
{
    $datetime1 = date_create($date_1); // creo la fecha 1
    $datetime2 = date_create($date_2); // creo la fecha 2
    $interval = date_diff($datetime1, $datetime2); // obtengo la diferencia de fechas
    return $interval->format($differenceFormat); // devuelvo el número de días
}
function borrarLogs_task($path, $dias, $ext) // borra los logs a partir de una cantidad de días
{
    $files = glob($path . '*' . $ext); //obtenemos el nombre de todos los ficheros
    foreach ($files as $file) { // recorremos todos los ficheros.
        $lastModifiedTime = filemtime($file); // obtenemos la fecha de modificación del fichero
        $currentTime = time(); // obtenemos la fecha actual
        $dateDiff = dateDifference_task(date('Ymd', $lastModifiedTime), date('Ymd', $currentTime)); // obtenemos la diferencia de fechas
        ($dateDiff >= $dias) ? unlink($file) : ''; //elimino el fichero
    }
}
function timeZone_task()
{
    return date_default_timezone_set('America/Argentina/Buenos_Aires');
}
function fileLog($text, $ruta_archivo, $type = false)
{
    timeZone_task();
    if (!is_dir(dirname($ruta_archivo))) { // Comprobar si el directorio existe
        mkdir(dirname($ruta_archivo), 0777, true); //Crear el directorio si no existe
    }
    $log = fopen($ruta_archivo, 'a');
    $date = date("Y-m-d H:i:s");
    $text = ($type == 'export') ? $text . "\n" : $date . ' ' . $text . "\n";
    fwrite($log, $text);
    fclose($log);
}
function fileLogJson_task($text, $ruta_archivo, $date = true)
{
    if ($date) {
        $log = fopen(date('YmdHis') . '_' . $ruta_archivo, 'w');
    } else {
        $log = fopen($ruta_archivo, 'w');
    }
    $text = json_encode($text, JSON_PRETTY_PRINT) . "\n";
    fwrite($log, $text);
    fclose($log);
}
function getDataIni_task($url) // obtiene el json de la url
{
    if (file_exists($url)) { // si existe el archivo
        $data = file_get_contents($url); // obtenemos el contenido del archivo
        if ($data) { // si el contenido no está vacío
            $data = parse_ini_file($url, true); // Obtenemos los datos del data.php
            return $data; // devolvemos el json
        } else { // si el contenido está vacío
            fileLog("No hay informacion en el archivo \"$url\"", __DIR__ . "/logs/" . date('Ymd') . "_getDataIni.log", ''); // escribimos en el log
            return false; // devolvemos false
        }
    } else { // si no existe el archivo
        fileLog("No existe archivo \"$url\"", __DIR__ . "/logs/" . date('Ymd') . "_getDataIni.log", ''); // escribimos en el log
        return false; // devolvemos false
    }
}
function writeFlags_task($assoc, $path)
{
    $content = "; <?php exit; ?> <-- ¡No eliminar esta línea! -->\n";
    foreach ($assoc as $key => $elem) {
        $content .= "[" . $key . "]\n";
        foreach ($elem as $key2 => $elem2) {
            if (is_array($elem2)) {
                for ($i = 0; $i < count($elem2); $i++) {
                    $content .= $key2 . "[] =\"" . $elem2[$i] . "\"\n";
                }
            } else if ($elem2 == "")
                $content .= $key2 . " =\n";
            else
                $content .= $key2 . " = \"" . $elem2 . "\"\n";
        }
    }
    // $path = __DIR__ . '/flags.ini';
    if (!$handle = fopen($path, 'w')) {
        return false;
    }
    $success = fwrite($handle, $content);
    fclose($handle);
    return $success;
}
function statusFlags_task($statusFlags, $pathFlags, $createdDate)
{
    $assoc = array(
        'flags' => array(
            'lastDate' => $createdDate,
            'download' => $statusFlags,
            'datetime' => date('Y-m-d H:i:s'),
        ),
    );
    writeFlags_task($assoc, $pathFlags);
    $text = ($statusFlags == '2') ? "Se marco Bandera de espera" : "Se marco Bandera de descarga";
    $pathLog = __DIR__ . '/logs/flagsLog';
    fileLog($text, $pathLog . '/' . date('Ymd') . '_flagsLog_aws.log');
    borrarLogs_task($pathLog . '/', 5, '.log');
}
function request_api($url, $timeout = 10)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $headers = [
        'Content-Type: application/json',
        'Authorization: 7BB3A26C25687BCD56A9BAF353A78'
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $file_contents = curl_exec($ch);
    $curl_errno = curl_errno($ch); // get error code
    $curl_error = curl_error($ch); // get error information

    if ($curl_errno > 0) { // si hay error
        $text = "cURL Error ($curl_errno): $curl_error"; // set error message
        $pathLog = __DIR__ . '../../../../logs/' . date('Ymd') . '_errorCurl.log'; // ruta del archivo de Log de errores
        sendEmailTask("Error al conectar con AWS " . date('Y-m-d H:i:s'), $text);
        fileLog($text, $pathLog); // escribir en el log de errores el error
    }
    curl_close($ch);
    if ($file_contents) {
        return $file_contents;
    } else {
        $pathLog = __DIR__ . '../../../../logs/' . date('Ymd') . '_errorCurl.log'; // ruta del archivo de Log de errores
        fileLog('Error al obtener datos', $pathLog); // escribir en el log de errores el error
        return false;
    }
    // exit;
}
function queryCalcZone_task($lat, $lng, $idCompany)
{
    $query = "
            SELECT
            `rg`.*,
        (
                (
                    (
                        acos(
                            sin(($lat * pi() / 180)) * sin((`rg`.`lat` * pi() / 180)) + cos(($lat * pi() / 180)) * cos((`rg`.`lat` * pi() / 180)) * cos((($lng - `rg`.`lng`) * pi() / 180))
                        )
                    ) * 180 / pi()
                ) * 60 * 1.1515 * 1.609344
            ) as distancia
        FROM
            reg_zones rg WHERE `rg`.`id_company` = $idCompany
        -- HAVING (distancia <= 0.1)
        ORDER BY distancia ASC, rg.id DESC LIMIT 1
    ";
    return $query;
}
function saveFilePhoto($fechaHora, $eventType, $companyCode, $createdDate, $phoneid, $attphoto, $calidad = 20)
{

    $eplodeFechaHora = explode(' ', $fechaHora);
    $eplodeFecha = explode('-', $eplodeFechaHora[0]);
    $PathAnio = $eplodeFecha[0];
    $PathMes = $eplodeFecha[1];
    $PathDia = $eplodeFecha[2];

    /** Guardamos la foto del base64 en formato jpg y comprimida al 80%*/
    if ($eventType == '2') {
        try {
            $filename = "../fotos/$companyCode/$PathAnio/$PathMes/$PathDia/index.php";
            $dirname = dirname($filename);
            if (!is_dir($dirname)) {
                mkdir($dirname, 0755, true);
                $log = fopen($dirname . '/index.php', 'a');
                $text = '<?php exit;';
                fwrite($log, $text);
                fclose($log);
            }
            $f = fopen($dirname . '/' . $createdDate . '_' . $phoneid . '.jpg', "w") or die("Unable to open file!");
            fwrite($f, base64_decode($attphoto));
            fclose($f);
            $rutaImagenOriginal = $dirname . '/' . $createdDate . '_' . $phoneid . '.jpg';
            $imagenOriginal = imagecreatefromjpeg($rutaImagenOriginal); //Abrimos la imagen de origen
            $rutaImagenComprimida = $dirname . '/' . $createdDate . '_' . $phoneid . '.jpg'; //Ruta de la imagen a comprimir
            // $calidad = Valor entre 0 y 100. Mayor calidad, mayor peso
            imagejpeg($imagenOriginal, $rutaImagenComprimida, $calidad); //Guardamos la imagen comprimida
            //$contenidoBinario = file_get_contents($rutaImagenComprimida);
            //$imagenComoBase64 = base64_encode($contenidoBinario);
        } catch (Exception $e) {
            fileLog($e->getMessage(), __DIR__ . '/logs/' . date('Ymd') . '_error_save_photo.log');            
        }
    }

}

function guardar_imagen($string_base64, $directorio) {
    try {
        // Decodificar el string de imagen base64
        $data = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $string_base64));
        // Crear el archivo .jpg en el directorio especificado
        if (!file_exists($directorio)) { // Comprobar si el directorio existe
            mkdir($directorio, 0777, true); //Crear el directorio si no existe
            $log = fopen($directorio . '/index.php', 'a');
            $text = '<?php exit;';
            fwrite($log, $text);
            fclose($log);
        }

        $file = "{$directorio}/".uniqid().".jpg";
        $success = file_put_contents($file, $data);
        
        // Comprimir la calidad de la imagen al 20%
        $image = imagecreatefromjpeg($file);
        $quality = 20;
        imagejpeg($image, $file, $quality);
        imagedestroy($image);
        
        // Registrar en un archivo de registro de errores si hay algún error
        if (!$success) {
            fileLog("No se pudo guardar la imagen: ".$file."\n", __DIR__ . '/logs/' . date('Ymd') . '_error_save_photo.log');
            return false;
        } else {
            return true;
        }
    } catch (Exception $e) {
        fileLog("Error al guardar la imagen: ".$e->getMessage()."\n", __DIR__ . '/logs/' . date('Ymd') . '_error_save_photo.log');            
        return false;   
    }
}

function combine_keys($item)
{
    $fecha_hora = '';
    if ($item['dateTime'] != null) {
        $dateTime = ($item['dateTime']) ? substr($item['dateTime'], 0, 10) : $item['dateTime'];
        $fecha_hora = date('Y-m-d H:i:s', $dateTime); //convertir la marca de tiempo a formato 'Y-m-d H:i:s'
    }
    return $item['employeId'] . ' ' . $item['companyCode'] . ' ' . $fecha_hora;
}
$GeneralLogsPath = __DIR__ . '/logs/' . date('Ymd') . '_error.log';

$pathFlags = __DIR__ . '/flags_aws.php'; // ruta del archivo de Log de errores
$flags = (getDataIni_task($pathFlags));
if (!$flags) {
    $assoc = array(
        'flags' => array(
            'lastDate' => '1646871812711',
            'download' => 1,
            'datetime' => date('Y-m-d H:i:s'),
        ),
    );
    writeFlags_task($assoc, $pathFlags);
    $flags = (getDataIni_task($pathFlags));
    $flags_lastDate = $flags['flags']['lastDate'];
    $flags_download = $flags['flags']['download'];
} else {
    $flags_lastDate = $flags['flags']['lastDate'];
    $flags_download = $flags['flags']['download'];
}

$data = array();
$arrayData = array();
$ingresado = array();

if ($flags_download == 2) {
    $data = array(
        'Mensaje' => 'Aguarde. Hay procesos de descarga en ejecucion.',
        'date' => date('Y-m-d H:i:s'),
        'status' => 'no',
        'time' => '',
        'total' => 0
    );
    echo json_encode(array('Response' => $data));
    exit;
}

statusFlags_task(2, $pathFlags, $flags_lastDate); // marcar bandera de espera

$url = "http://awsapi.chweb.ar:7575/attention/api/punch-event/light/" . $flags_lastDate;
$array = json_decode(request_api($url), true); // Llamamos al endpoint attention/api/punch-event/light/{createddate}. Este devuelve registros a partir del {$flags_lastDate}. Pero sin las fotos. De aca vamos a recuperar los IDPunch para iterarlos mas adelante y descargar registro por registro.

$array['status'] = $array['status'] ?? ''; // Inicializamos algunos valores
$array['payload'] = $array['payload'] ?? ''; // Inicializamos algunos valores

if ($array['status'] == 500) { // Si obtenemos un error 500 salimos del script
    $flags_lastDate = $flags['flags']['lastDate'];
    statusFlags_task(1, $pathFlags, $flags_lastDate); // Inicializamos el flag con el created date que inciamos
    $fechaHora = date('Y-m-d H:i:s');
    $texterr = "$fechaHora Error: \"$array[error]\" Path: \"$array[path]\"";
    sendEmailTask("Task Mobile: $array[error]", "<pre>$array[timestamp]<br>$array[path]<pre>"); // Enviamos Email con detalle del error
    Flight::json($array['error']);
    exit;
}

if (empty($array['payload'])) { // Si el Payload esta vacio. Salimos del script
    $flags_lastDate = $flags['flags']['lastDate'];
    statusFlags_task(1, $pathFlags, $flags_lastDate); // Inicializamos el flag con el created date que inciamos 
    Flight::json("No Hay Registros Nuevos");
    exit;
}

$duplicados = array();
$totalDuplicados = array();

$counts = array_count_values(array_map('combine_keys', $array['payload']));
foreach ($counts as $key => $value) {
    if ($value > 1) {
        $tt = ($value - 1); // Le restamos uno para solo contabilizar los duplicados sobrantes y no contabilizar el registro inicial.
        $tt = (($tt) > 1) ? "$tt registros" : "$tt registro";
        $duplicados[] = "$key : " . ($tt);
        $totalDuplicados[] = ($tt);
    }
}
$totalDuplicados = array_sum($totalDuplicados);

/** Creamos array's de datos customizados para despues usarlos en el log final */
$columncreatedDate = (array_column($array['payload'], 'createdDate')); // crea un nuevo array que contiene los valores de la clave "createdDate" de cada elemento del array asociativo en el array "$array[payload]".
$columnemployeId = (array_column($array['payload'], 'employeId')); // Idem Anterior
$columnIdPunch = (array_column($array['payload'], 'id')); // Idem Anterior
$columnIdCompany = (array_column($array['payload'], 'companyCode')); // Idem Anterior
$columnAppVersion = (array_column($array['payload'], 'appVersion')); // Idem Anterior
$columnLocked = (array_column($array['payload'], 'locked')); // Idem Anterior
$ultimacreatedDate = (end($columncreatedDate)); // Obtengo el ultimo created date del arragle "$columncreatedDate"

foreach ($columnAppVersion as $key => $apv) {
    $colApp[] = sanitizeAppVersion($apv); // Sanitizamos el array "$columnAppVersion"
}
$t = count($columnIdPunch); // Contamos la cantidad de valores del array "$columnIdPunch". Que en definitiva es la cantidad de registros a procesar
$DataRequest = array(
    /** Array para el log */
    'companys' => CountAgrupArray($columnIdCompany, 'company'),
    'users' => CountAgrupArray($columnemployeId, 'employeId'),
    'appVersion' => CountAgrupArray($colApp, 'appVersion'),
    'locked' => CountAgrupArray($columnLocked, 'appVersion'),
    'duplicados' => $duplicados ?? '',
    'totalDuplicados' => ($totalDuplicados),
    'total' => (($t) > 1) ? "$t registros" : "$t registro",
    'ingresados' => (intval($t) - intval($totalDuplicados))
);
$payload = array();

foreach ($columnIdPunch as $keyPunch => $idPunch) {


    try {
        $url2 = "http://awsapi.chweb.ar:7575/attention/api/punch-event/get/" . $idPunch;
        $arr = json_decode(request_api($url2), true); // Llamamos al endpoint attention/api/punch-event/get/{idPunch}. Este devuelve un array con la fichada completa para procesarla.

        $arr['type'] = $arr['type'] ?? '';
        $arr['status'] = $arr['status'] ?? '';
        $arr['message'] = $arr['message'] ?? '';

        if ($arr['status'] == 500) { // Si obtenemos un error 500 salimos del script
            $fechaHora = date('Y-m-d H:i:s');
            $texterr = "$fechaHora Error: \"$arr[error]\" Path: \"$arr[path]\"";
            sendEmailTask("Task Mobile: $arr[error]", "<pre>$arr[timestamp]<br>$arr[path]<pre>"); // Enviamos Email con detalle del error
            continue;
        }

    } catch (Exception $e) {
        fileLog($e->getMessage(), __DIR__ . '/logs/' . date('Ymd') . '_error_punch-event_get.log');
    }

    $cp = array(); // iniciamos el cp con un array vacio. cp = custom Payload

    if (empty($arr['payload'])) {
        continue;
    }
    if (empty($arr['payload']['employeId'])) {
        continue; // si el USer ID viene nulo continuamos con el proximo ID Punch
    }
    if (intval($arr['payload']['employeId'] > 2147483647)) {
        continue; // si el USer ID es mayor a 2147483647. Validamos que sea un integer
    }

    /** customizamos el paylod */

    $timestamp = $arr['payload']['dateTime'] ?? 0;
    $timestamp = ($timestamp) ? substr($timestamp, 0, 10) : $timestamp;
    $dates = new DateTime("@" . $timestamp); // crea un nuevo objeto DateTime basado en la marca de tiempo "timestamp Unix" // El símbolo "@" se utiliza para indicar que la cadena que sigue a continuación es una marca de tiempo Unix. Se concatena con el valor de la marca de tiempo que se pasa como parámetro.
    $dates->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires')); // establece la zona horaria del objeto DateTime representado por la variable $dates. La función setTimezone() es llamada en el objeto $dates y le pasa como argumento un nuevo objeto DateTimeZone que contiene la cadena de la zona horaria deseada. En este caso, se está estableciendo la zona horaria a "America/Argentina/Buenos_Aires", lo que significa que la fecha y hora representada por el objeto $dates serán ajustadas a la fecha y hora en Buenos Aires, Argentina.
    $fechaHora = $dates->format('Y-m-d H:i:s'); // Este código formatea el valor datetime almacenado en la variable $dates en una cadena con el siguiente formato: "yyyy-mm-dd hh:mm:ss". La cadena resultante se devuelve mediante el método format() del objeto DateTime.
    $fechaHoraCH = $dates->format('Ymd'); // idem anterior formato: "Ymd"
    $hora = $dates->format('H:i'); // idem anterior formato: "H:i"
    $appVersion = sanitizeAppVersion(addslashes($arr['payload']['appVersion'])); // santizamos el appVersion
    $attphoto = addslashes($arr['payload']['attphoto']); // La imagen de la fichada {base64}
    $checkPhoto = ($attphoto) ? '0' : '1'; // Si la variable attphoto no esta vacía asignamos "1" sino "0"
    $companyCode = $arr['payload']['companyCode'] ?? ''; // ID de la Empresa
    $createdDate = $arr['payload']['createdDate'] ?? ''; // Timestamp de la creación del registros en la API
    $employeId = $arr['payload']['employeId'] ?? ''; // ID del usuario
    $eventType = $arr['payload']['eventType'] ?? ''; // Typo de Evento
    $lat = $arr['payload']['position']['lat'] ?? ''; // Latitud
    $lng = $arr['payload']['position']['lng'] ?? ''; // Longiud
    $gpsStatus = $arr['payload']['position']['gpsStatus'] ?? ''; // status del GPS. Si estaba encendido el GS en el dispositivo
    $phoneid = $arr['payload']['phoneid'] ?? ''; // phoneID del dispositivo
    $regid = addslashes($arr['payload']['regid']) ?? ''; // regID del dispositivo
    $operationType = $arr['payload']['operationType'] ?? '';
    $confidence = $arr['payload']['confidence'] ?? ''; // valor en numeros de coincindecia del reconocimeinto facial. Cuanto mas cerca al 100 es correcto.
    $arr['payload']['locked'] = $arr['payload']['locked'] ?? '';
    $locked = ($arr['payload']['locked'] == false) ? '0' : $arr['payload']['locked']; // si esta bloqueaod el usuario viene en 1. Tambien informa 1 cuano el legajo es invalido
    $error = addslashes($arr['payload']['error']) ?? ''; // Error si el usuario esta bloqueado
    $id_api = $arr['payload']['id'] ?? ''; // IDPunch de la API
    $operation = $arr['payload']['observations'] ?? ''; // operaciones. Para la funcion de ronda

    /** Calculamos la Zona */
    $zona = '';
    if ($lat != 0.0) {
        $query = queryCalcZone_task($lat, $lng, $companyCode);
        $zona = simple_pdoQuery($query);
    }
    $distancia2 = '';
    if ($zona) {
        try {
            $radio = round(intval($zona['radio']) / 1000, 2);
            $distancia = ($zona['distancia']) ? round($zona['distancia'], 2) : 0;
            $distancia2 = ($zona['distancia']) ? ($zona['distancia']) : 0;
            $idZone = ($distancia <= $radio) ? $zona['id'] : '0';
        } catch (Exception $e) {
            fileLog('Error: ' . $e->getMessage() . '\n', $GeneralLogsPath);
        }
    } else {
        $idZone = '0';
    }
    /** Fin calculo Zona */

    $eventZone = '0'; // inicializamos el Evento de la zona
    if ($idZone != '0') { // Si la Zona es diferente a 0 entonces se calcula el evento consultando la tabla de zona y evento
        $a = simple_pdoQuery("SELECT `evento` FROM `reg_zones` WHERE `id_company` = '$companyCode' AND `evento` != '0' AND `id` = '$idZone' LIMIT 1");
        $eventZone = $a['evento'] ?? '0'; // Retornamos el Evento de la zona
    }

    $s = simple_pdoQuery("SELECT `id`, `phoneid`, `id_company`, `nombre`, `evento`, `appVersion` FROM `reg_device_` WHERE `phoneid` = '$phoneid' AND `id_company` = $companyCode");

    if ($s) {
        (pdoQuery_task("UPDATE `reg_device_` SET `appVersion` = '$appVersion', `regid` = '$regid' WHERE `id` = '$s[id]' AND id_company = $companyCode"));
        $deviceID = $s['id'];
    } else {
        (pdoQuery_task("INSERT INTO `reg_device_` (`appVersion`, `regid`, `phoneid`, `id_company`, `evento`, `nombre`) VALUES ('$appVersion', '$regid', '$phoneid', '$companyCode', 0, '$phoneid')"));
        $getDeviceID = simple_pdoQuery("SELECT `id` FROM reg_device_ WHERE phoneid = '$phoneid' AND id_company = $companyCode");
        $deviceID = $getDeviceID['id'];
    }

    $b = simple_pdoQuery("SELECT `evento` FROM `reg_device_` WHERE `id_company` = '$companyCode' AND `id` = '$deviceID' LIMIT 1");

    $eventDevice = $b['evento'] ?? ''; // Retornamos el Evento del dispositivo
    $identified = ($confidence >= 75) ? '1' : '0'; // Si el confidence que trae el registro es mayor a 75 lo validamos como reconociemito facial identificado y lo marcamos con una bandera = "1"

    $insertDB = false; // Inicializamos insertDB
    if (intval($employeId) > 0) { // comprobamos el employeId que sea mayor a 0. Por si las moscas.
        $query = "INSERT INTO `reg_` (`reg_uid`, `id_user`, `phoneid`, `deviceID`, `id_company`,`createdDate`,`fechaHora`,`lat`,`lng`, `idZone`, `distance`, `eventZone`, `eventDevice`, `gpsStatus`,`eventType`,`operationType`, `operation`, `appVersion`, `attphoto`, `confidence`, `identified`, `locked`, `error`, `id_api`) VALUES(UUID(),'$employeId', '$phoneid', '$deviceID','$companyCode','$createdDate', '$fechaHora', '$lat','$lng', '$idZone', '$distancia2', '$eventZone', '$eventDevice', '$gpsStatus','$eventType', '$operationType', '$operation', '$appVersion', '$checkPhoto', '$confidence', '$identified', '$locked', '$error', '$id_api')";
        $insertDB = pdoQuery_task($query); // Hacemos el insert del registro (Fichada) en la tabla
    }

    if ($insertDB) {
        $ingresado[] = 1;
        /** Si el insert del registro se genero correctamente. Continuamos con el proceso de guardar la imagen en formato jpg en la carpeta photos.
         * Luego con el proceso de INSERT en la tabla FICHADAS de control horarios si corresponde de acuerdo a la configuración de la Company del registro. Esta configuración esta en el archivo  "mobileApikey.php" y la variable {localCH} debe estar en "0"*/

        $eplodeFechaHora = explode(' ', $fechaHora);
        $eplodeFecha = explode('-', $eplodeFechaHora[0]);
        $PathAnio = $eplodeFecha[0];
        $PathMes = $eplodeFecha[1];
        $PathDia = $eplodeFecha[2];

        saveFilePhoto($fechaHora, $eventType, $companyCode, $createdDate, $phoneid, $attphoto, 20);
        // guardar_imagen($attphoto, __DIR__ . '/photos/test/');

        $dataApikeys = filtrarObjeto_task($iniKeys, 'idCompany', $companyCode); // Filtramos el archivo "mobileApikey.php" para encontrar la configuracion {companyCode}

        if (!$dataApikeys) {
            continue;
        }

        $dataApikeys['nameCompany'] = $dataApikeys['nameCompany'] ?? '';
        $dataApikeys['idCompany'] = $dataApikeys['idCompany'] ?? '';
        $dataApikeys['recidCompany'] = $dataApikeys['recidCompany'] ?? '';
        $dataApikeys['localCH'] = $dataApikeys['localCH'] ?? '1';

        $Legajo = str_pad($employeId, 11, "0", STR_PAD_LEFT); // Creamos la variable $Legajo y autocompletamos con ceros a la izquierdael legajo

        $nameCompany = (str_replace(' ', '_', $dataApikeys['nameCompany'])); // Si el nombre de la cuenta tiene espacio, los remplzamos por guion bajo
        $nameCompany = (str_replace('.', '', $nameCompany)); // Si el nombre de la cuenta tiene puntos, los remplzamos por vacio
        $recidCompany = $dataApikeys['recidCompany'];
        $localCH = $dataApikeys['localCH'];
        $idCompany = $dataApikeys['idCompany'];

        $pathLog = __DIR__ . '/logs/descargas/' . $nameCompany . '/' . $PathAnio . '/' . $PathMes;
        $text = "$Legajo $createdDate $fechaHora $lat $lng";
        fileLog($text, $pathLog . '/' . date('Ymd') . '_cuenta_' . $nameCompany . '.log'); // Guardo Log de la fichada en la carpeta descargas y en la carpeta del nombre de la cuenta por año mes y dia

        $nameCompany = str_replace(" ", "-", $dataApikeys['nameCompany']);
        $nameCompany = (str_replace('.', '', $nameCompany)); // Si el nombre de la cuenta tiene puntos, los remplzamos por vacio
        // Si el nombre de la cuenta tiene espacio, los remplzamos por guion medio
        if ($locked == '1') { // Si la fichada viene bloqueada, guardamos en el Log reg_locked_CH correspondiente
            $text = "Usuario Bloqueado $Legajo $fechaHoraCH $hora";
            $pathLog = __DIR__ . '/logs/reg_locked_CH/' . $nameCompany . '/' . $PathAnio . '/' . $PathMes;
            fileLog($text, $pathLog . '/' . date('Ymd') . '_cuenta_' . $nameCompany . '.log');
        }

        if ($localCH == '1') { // si el localCh viene en 1 guardamos el log reg_external_CH
            $text = "$Legajo $fechaHoraCH $hora";
            $pathLog = __DIR__ . '/logs/reg_external_CH/' . $nameCompany . '/' . $PathAnio . '/' . $PathMes;
            fileLog($text, $pathLog . '/' . date('Ymd') . '_cuenta_' . $nameCompany . '.log'); // Guardo Log de las Fichadas descargadas
        }

        if ($locked != '1' && $localCH == '0') { // Si no esta bloqueado y tiene local CH
            /** Guardamos en la tabla fichadas */
            $eventZone = $eventZone ?? '0';
            $query = "INSERT INTO FICHADAS (RegTarj, RegFech, RegHora, RegRelo, RegLect, RegEsta) VALUES ('$employeId', '$fechaHoraCH', '$hora', '9999', '$eventZone', '0')"; // Insertamos en la tabla Fichadas de control horario

            if ($recidCompany) {
                $_GET['_c'] = $recidCompany;
                if (MSQuery_task($query)) {
                    $eventZone = str_pad($eventZone, 4, "0", STR_PAD_LEFT);
                    $text = "$Legajo $fechaHoraCH $hora 9999 $eventZone 0";
                    $pathLog = __DIR__ . '/logs/insert_CH/' . $nameCompany . '/' . $PathAnio . '/' . $PathMes;
                    fileLog($text, $pathLog . '/' . date('Ymd') . '_cuenta_' . $nameCompany . '.log');
                } else {
                    $text = "No se pudo insertar el registro en TABLA FICHADAS CH: $Legajo $fechaHoraCH $hora 9999 9999 0";
                    $pathLog = __DIR__ . '/logs/error_insert_CH/' . $nameCompany . '/' . $PathAnio . '/' . $PathMes;
                    fileLog($text, $pathLog . '/' . date('Ymd') . '_cuenta_' . $nameCompany . '.log');
                }
            }
        }
    }

    $hashreg = $companyCode . $employeId . $fechaHora;

    $user = str_pad($employeId, 8, " ", STR_PAD_LEFT);
    $company = str_pad($companyCode, 2, " ", STR_PAD_LEFT);
    $punch = str_pad(($keyPunch + 1), 3, "0", STR_PAD_LEFT);
    $total = str_pad($DataRequest['total'], 3, " ", STR_PAD_LEFT);
    $textoecho = " Company: $company.";
    $textoecho .= " User: $user.";
    $textoecho .= " Fecha/Hora: $fechaHora.";
    $textoecho .= " App: $appVersion.";
    echo 'Registro -> ' . $idPunch . ' - ' . ($punch) . '/' . $total . ' -' . $textoecho . PHP_EOL;
}

// Finalizamos el script
fileLogJson_task($ultimacreatedDate, 'createdDate.json', false); // un json con la fecha de la ultima descarga
statusFlags_task(1, $pathFlags, $ultimacreatedDate);
$end = microtime(true);
$timeInSeconds = round($end - $start, 2);

/**El siguiente código convierte el resultado en minutos si es mayor a 59 segundos: */
if ($timeInSeconds >= 60) {
    $timeInMinutes = round(($timeInSeconds / 60), 2);
    $time = "Tiempo de ejecucion: " . $timeInMinutes . " minutos.";
} else {
    $time = "Tiempo de ejecucion: " . $timeInSeconds . " segundos.";
}

echo PHP_EOL . '## Fin de la transferencia. ' . $time . ' ##' . PHP_EOL . PHP_EOL;
$ingresadoReales = array_sum($ingresado);

$DataRequest['ingresados reales'] = $ingresadoReales; // añadimos el total de ingresados reales

$r = json_encode(array('detalle' => $DataRequest, 'time' => ($time)), JSON_PRETTY_PRINT);
fileLog($r, __DIR__ . '/logs/' . date('Ymd') . '_aws_task_light.log'); // guardamos log con el resumen de toda la transferencia

if ($DataRequest['total'] > 50) {
    sendEmailTask("Task Mobile. Información de transferencia " . date('d-m-Y H:i:s'), "<pre>Se Transfirieron mas de 50 registros en una misma transferencia. <br>$r</pre>");
}

Flight::json(array('detalle' => $DataRequest, 'time' => ($time))); // devolvemos una respuesta en json

exit;
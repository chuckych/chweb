<?php
ini_set('max_execution_time', 1800); //1800 seconds = 30 minutes
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
error_reporting(E_ALL);
ini_set('display_errors', '0');
$start = microtime(true);
require __DIR__ . '../../../../vendor/autoload.php';
require_once __DIR__ . './fn_task.php';
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
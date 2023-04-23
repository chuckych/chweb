<?php
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
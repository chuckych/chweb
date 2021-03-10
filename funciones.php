<?php
function version()
{
    return 'v0.0.85';
}
function E_ALL()
{
	if ($_SERVER['SERVER_NAME'] == 'localhost') {
		error_reporting(E_ALL);
		ini_set('display_errors', '1');
	} else {
		error_reporting(E_ALL);
		ini_set('display_errors', '0');
	}
}
function secure_auth_ch()
{
    if (
        $_SESSION["secure_auth_ch"] !== true
        || (empty($_SESSION['UID']) || is_int($_SESSION['UID']))
        || ($_SESSION['IP_CLIENTE'] !== $_SERVER['REMOTE_ADDR'])
        || ($_SESSION['USER_AGENT'] !== $_SERVER['HTTP_USER_AGENT'])
        || ($_SESSION['DIA_ACTUAL'] !== hoy())
    ) {
        // echo '<script>alert("SESION EXPIRADA")</script>';
        echo '<script>window.location.href="/' . HOMEHOST . '/login/"</script>';
        header("location:/" . HOMEHOST . "/login/");
        http_response_code(403);
        exit;
    } else {
        /** chequeamos si el usuario y la password son iguales. si se cumple la condición, lo redirigimos a cambiar la clave */ (password_verify($_SESSION["user"], $_SESSION["HASH_CLAVE"])) ? header('Location:/' . HOMEHOST . '/usuarios/perfil/') : '';
        /** */
        $fechaGuardada = $_SESSION["ultimoAcceso"];
        $ahora = date("Y-m-d H:i:s");
        $tiempo_transcurrido = (strtotime($ahora) - strtotime($fechaGuardada));
        /** comparamos el tiempo transcurrido */
        if ($tiempo_transcurrido >= $_SESSION["LIMIT_SESION"]) {
            /** Si pasaron 60 minutos o más */
            session_destroy();
            /** destruyo la sesión */
            header("location:/" . HOMEHOST . "/login/?sesion");
            /** envío al usuario a la pag. de autenticación */
            exit();
            /** sino, actualizo la fecha de la sesión */
        } else {
            $_SESSION["ultimoAcceso"] = $ahora;
        }
    }
    session_regenerate_id();
    E_ALL();
}
/** ultimaacc */
function ultimoacc()
{
    return $_SESSION["ultimoAcceso"] = date("Y-m-d H:i:s");
}
/** Seguridad injections SQL */
function secureVar($key)
{
    $key = htmlspecialchars(stripslashes($key));
    $key = str_ireplace("script", "blocked", $key);
    $key = htmlentities($key, ENT_QUOTES);
    return $key;
}
function vjs()
{
    if ($_SERVER['SERVER_NAME'] == 'localhost') {
		return time();
	} else {
		return version();
	}
}
function API_KEY_MAPS()
{
    return 'AIzaSyCFs9lj9k7WZAyuwzDJwOiSiragUA9Xwg0';
}
$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET, 'keyset');

function encabezado($titulo)
{
    return '<div class="row"><div class="col-12 radius-0"><p class="bg-white shadow-sm p-3 text-dark lead">' . $titulo . '</p></div></div>';
}
/** GENERAR generaPass */
function recid()
{
    $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZ-1234567890abcdefghijklmnopqrstuvwxyz";
    $longitudCadena = strlen($cadena);
    $pass = "";
    $longitudPass = 8;
    for ($i = 1; $i <= $longitudPass; $i++) {
        $pos = rand(0, $longitudCadena - 1);
        $pass .= substr($cadena, $pos, 1);
    }
    return $pass;
}
function Ident()
{
    $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $longitudCadena = strlen($cadena);
    $Ident = "";
    $longitudIdent = 3;
    for ($i = 1; $i <= $longitudIdent; $i++) {
        $pos = rand(0, $longitudCadena - 1);
        $Ident .= substr($cadena, $pos, 1);
    }
    return $Ident;
}
function statusData($status, $dato)
{
    $data = array('status' => $status, 'dato' => $dato);
    echo json_encode($data);
    exit;
}
function recid2($cant)
{
    $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZ-1234567890abcdefghijklmnopqrstuvwxyz";
    $longitudCadena = strlen($cadena);
    $pass = "";
    $longitudPass = $cant;
    for ($i = 1; $i <= $longitudPass; $i++) {
        $pos = rand(0, $longitudCadena - 1);
        $pass .= substr($cadena, $pos, 1);
    }
    return $pass;
}
function getBrowser($user_agent)
{
    if (strpos($user_agent, 'MSIE') !== FALSE)
        return 'Internet explorer';
    elseif (strpos($user_agent, 'Edge') || strpos($user_agent, 'Edg') !== FALSE) //Microsoft Edge
        return 'Microsoft Edge';
    elseif (strpos($user_agent, 'Trident') !== FALSE) //IE 11
        return 'Internet explorer';
    elseif (strpos($user_agent, 'Opera Mini') !== FALSE)
        return "Opera Mini";
    elseif (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR') !== FALSE)
        return "Opera";
    elseif (strpos($user_agent, 'Firefox') !== FALSE)
        return 'Mozilla Firefox';
    elseif (strpos($user_agent, 'Chrome') !== FALSE)
        return 'Google Chrome';
    elseif (strpos($user_agent, 'Safari') !== FALSE)
        return "Safari";
    else
        return 'No hemos podido detectar su navegador';
}
function AttrDisabled($ValTrue)
{
    return $ValTrue == 1 ? '' : 'disabled';
}
function ocultarIE()
{
    if (getBrowser($_SERVER['HTTP_USER_AGENT']) == 'Internet explorer') {
        echo 'd-none';
    }
}
function BrowserIE()
{
    return getBrowser($_SERVER['HTTP_USER_AGENT']) == 'Internet explorer' ? true : false;
}
function BorrarArchivosPDF($RutaFiles)
{
    $files = glob($RutaFiles); //obtenemos el nombre de todos los ficheros
    foreach ($files as $file) {
        $lastModifiedTime = filemtime($file);
        $currentTime = time();
        $timeDiff = abs($currentTime - $lastModifiedTime) / (60 * 60);
        /** Genera el resulta en Horas decimal */
        if (is_file($file) && $timeDiff > 1)
            /** borra arcchivos con diferencia de horas mayor a 1 */
            unlink($file); //elimino el fichero
    }
}
function BorrarArchivo($RutaFiles)
{
    $files = glob($RutaFiles); //obtenemos el nombre de todos los ficheros
    foreach ($files as $file) {
        if (is_file($file))
            /** borra arcchivos con diferencia de horas mayor a 1 */
            unlink($file); //elimino el fichero
    }
}
/** GENERAR generacontraseña */
function GeneraClave()
{
    $cadena0 = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $cadena1 = "12345678901234567890";
    $cadena2 = "abcdefghijklmnopqrstuvwxyz";
    $cadena = ($cadena0) . ($cadena1) . ($cadena2);
    $longitudCadena = strlen($cadena);
    $pass = "";
    $longitudPass = 12;
    for ($i = 1; $i <= $longitudPass; $i++) {
        $pos = rand(0, $longitudCadena - 1);
        $pass .= substr($cadena, $pos, 1);
    }
    return $pass;
}
function token()
{
    return sha1('ie&%$sg@dQdtW@!""#');
}
function EscribirArchivo($nombre_archivo, $ruta_archivo, $texto, $date, $clear, $backup)
{
    require_once __DIR__ . '../log.class.php';
    /** LLAMAMOS A LA CLASE LOG */
    $log = new Log($nombre_archivo, $ruta_archivo);
    $log->insert($texto, $date, $clear, $backup);
    return $log;
}
function encabezado_mod($bgc, $colortexto, $img, $titulo, $imgclass)
{
    $QueryString = empty($_SERVER['QUERY_STRING']) ? '' : '?' . $_SERVER['QUERY_STRING'];

    echo '
    <div class="row text-' . $colortexto . ' ' . $bgc . ' radius-0">
    <div class="col-12">
    <p class="h6 fw4 py-2 m-0">
    <a href="' . $_SERVER['PHP_SELF'] . $QueryString . '">
    <img src="/' . HOMEHOST . '/img/' . $img . '" alt="' . $titulo . '" class="w30 mr-2 img-fluid ' . $imgclass . ' img-thumbnail">
    </a><span class="" id="Encabezado" style="position:absolute;margin-top:6px;">' . $titulo . '</span><span class="float-right fontpp" style="color:#efefef">' . version() . '</span>
    </p>
    </div>
    </div>';
}
function encabezado_mod2($bgc, $colortexto, $svg, $titulo, $width, $class)
{

    $countModRol = (count($_SESSION['MODS_ROL']));
    $userLogout = $version = $icon_clock_history = $icon_person_circle = '';

    if (BrowserIE()) {
        $icon_clock_history = '<svg width="1.6em" height="1.6em" viewBox="0 0 16 16" class="bi bi-clock-history text-white mr-1" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8.515 1.019A7 7 0 0 0 8 1V0a8 8 0 0 1 .589.022l-.074.997zm2.004.45a7.003 7.003 0 0 0-.985-.299l.219-.976c.383.086.76.2 1.126.342l-.36.933zm1.37.71a7.01 7.01 0 0 0-.439-.27l.493-.87a8.025 8.025 0 0 1 .979.654l-.615.789a6.996 6.996 0 0 0-.418-.302zm1.834 1.79a6.99 6.99 0 0 0-.653-.796l.724-.69c.27.285.52.59.747.91l-.818.576zm.744 1.352a7.08 7.08 0 0 0-.214-.468l.893-.45a7.976 7.976 0 0 1 .45 1.088l-.95.313a7.023 7.023 0 0 0-.179-.483zm.53 2.507a6.991 6.991 0 0 0-.1-1.025l.985-.17c.067.386.106.778.116 1.17l-1 .025zm-.131 1.538c.033-.17.06-.339.081-.51l.993.123a7.957 7.957 0 0 1-.23 1.155l-.964-.267c.046-.165.086-.332.12-.501zm-.952 2.379c.184-.29.346-.594.486-.908l.914.405c-.16.36-.345.706-.555 1.038l-.845-.535zm-.964 1.205c.122-.122.239-.248.35-.378l.758.653a8.073 8.073 0 0 1-.401.432l-.707-.707z"/><path fill-rule="evenodd" d="M8 1a7 7 0 1 0 4.95 11.95l.707.707A8.001 8.001 0 1 1 8 0v1z"/><path fill-rule="evenodd" d="M7.5 3a.5.5 0 0 1 .5.5v5.21l3.248 1.856a.5.5 0 0 1-.496.868l-3.5-2A.5.5 0 0 1 7 9V3.5a.5.5 0 0 1 .5-.5z"/></svg>';
        $icon_shield_lock_fill = '<svg width="1.6em" height="1.6em" viewBox="0 0 16 16" class="bi bi-shield-lock-fill text-white mr-1" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 .5c-.662 0-1.77.249-2.813.525a61.11 61.11 0 0 0-2.772.815 1.454 1.454 0 0 0-1.003 1.184c-.573 4.197.756 7.307 2.368 9.365a11.192 11.192 0 0 0 2.417 2.3c.371.256.715.451 1.007.586.27.124.558.225.796.225s.527-.101.796-.225c.292-.135.636-.33 1.007-.586a11.191 11.191 0 0 0 2.418-2.3c1.611-2.058 2.94-5.168 2.367-9.365a1.454 1.454 0 0 0-1.003-1.184 61.09 61.09 0 0 0-2.772-.815C9.77.749 8.663.5 8 .5zm.5 7.415a1.5 1.5 0 1 0-1 0l-.385 1.99a.5.5 0 0 0 .491.595h.788a.5.5 0 0 0 .49-.595L8.5 7.915z"/></svg>';
        $icon_person_circle = '<svg width="1.6em" height="1.6em" viewBox="0 0 16 16" class="bi bi-person-circle" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M13.468 12.37C12.758 11.226 11.195 10 8 10s-4.757 1.225-5.468 2.37A6.987 6.987 0 0 0 8 15a6.987 6.987 0 0 0 5.468-2.63z"/><path fill-rule="evenodd" d="M8 9a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><path fill-rule="evenodd" d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zM0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8z"/></svg>';
        $icon_box_arrow_right = '<svg width="1.6em" height="1.6em" viewBox="0 0 16 16" class="bi bi-box-arrow-right" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0v2z"/><path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/></svg>';
    } else {
        $icon_clock_history = '<svg class="bi mr-1 text-white mr-1" width="18" height="18" fill="currentColor"><use xlink:href="/' . HOMEHOST . '/img/bootstrap-icons.svg#clock-history" /></svg>';
        $icon_shield_lock_fill = '<svg class="bi mr-1 text-white mr-1" width="18" height="18" fill="currentColor"><use xlink:href="/' . HOMEHOST . '/img/bootstrap-icons.svg#shield-lock-fill" /></svg>';
        $icon_person_circle = '<svg class="bi mr-1" width="18" height="18" fill="currentColor"><use xlink:href="/' . HOMEHOST . '/img/bootstrap-icons.svg#person-circle" /></svg>';
        $icon_box_arrow_right = '<svg class="bi mr-1" width="18" height="18" fill="currentColor"><use xlink:href="/' . HOMEHOST . '/img/bootstrap-icons.svg#box-arrow-right" /></svg>';
    }
    if ($_SERVER['SCRIPT_NAME'] == '/' . HOMEHOST . '/usuarios/perfil/index.php') {
        $icono = '<span data-toggle="tooltip" data-placement="top" data-html="true" data-original-title="<b>MIS HORAS</b>" aria-describedby="tooltip" >
    <a href="/' . HOMEHOST . '/mishoras/" class="btn btn-sm btn-custom text-white">
        ' . $icon_clock_history . '
    </a></span>';
    } else {
        $icono = '<span data-toggle="tooltip" data-placement="top" data-html="true" data-original-title="<b>MI PERFIL</b>" aria-describedby="tooltip" >
    <a href="/' . HOMEHOST . '/usuarios/perfil/" class="btn btn-sm btn-custom text-white">
        ' . $icon_person_circle . '
    </a></span>';
    }

    if ($countModRol == '1') {
        $userLogout = '
<span class="p-2 float-right" >' . $icono . '
<span data-toggle="tooltip" data-placement="top" data-html="true" data-original-title="<b>SALIR</b>" aria-describedby="tooltip" >
<a href="/' . HOMEHOST . '/logout.php" title="Salir" class="btn btn-sm btn-custom">' . $icon_box_arrow_right . '</a></span>
</span>';
    }
    if ($countModRol != '1') {
        $version = '<span class="float-right fontpp" style="color:#efefef;margin-top:-10px; padding-right:10px">' . version() . '</span>';
    }
    $QueryString = empty($_SERVER['QUERY_STRING']) ? '' : '?' . $_SERVER['QUERY_STRING'];
    if ($_SERVER['SCRIPT_NAME'] == '/' . HOMEHOST . '/mishoras/index.php') {
        if (BrowserIE()) {
            $svg = $icon_clock_history;
        } else {
            $svg = '<svg class="bi img-fluid ' . $class . '" width="' . $width . '" height="' . $width . '" fill="currentColor"><use xlink:href="/' . HOMEHOST . '/img/bootstrap-icons.svg#' . $svg . '" />
            </svg>';
        }
    } elseif ($_SERVER['SCRIPT_NAME'] == '/' . HOMEHOST . '/usuarios/perfil/index.php') {
        if (BrowserIE()) {
            $svg = $icon_shield_lock_fill;
        } else {
            $svg = '<svg class="bi img-fluid ' . $class . '" width="' . $width . '" height="' . $width . '" fill="currentColor"><use xlink:href="/' . HOMEHOST . '/img/bootstrap-icons.svg#' . $svg . '" />
            </svg>';
        }
    } else {
        $svg = '<svg class="bi img-fluid ' . $class . '" width="' . $width . '" height="' . $width . '" fill="currentColor"><use xlink:href="/' . HOMEHOST . '/img/bootstrap-icons.svg#' . $svg . '" />
        </svg>';
    }
    echo '
    <div class="row text-' . $colortexto . ' ' . $bgc . ' radius-0">
        <div class="col-8 d-flex align-items-center">
            <div class="h6 fw4 py-2 m-0">
                <a href="' . $_SERVER['PHP_SELF'] . $QueryString . '">
                    ' . $svg . '
                </a>
                <span class="text-nowrap" id="Encabezado" style="position:absolute;margin-top:4px;">' . $titulo . '</span>
            </div>
        </div>
        <div class="col-4 d-flex align-items-center justify-content-end pr-1">
            ' . $userLogout . $version . '
        </div>
    </div>';
}
/** Función HOST */
function host()
{
    if (array_key_exists('HTTPS', $_SERVER) && $_SERVER["HTTPS"] == "on") {
        $http = 'https://' . $_SERVER['HTTP_HOST'];
    } else {
        $http = 'http://' . $_SERVER['HTTP_HOST'];
    }
    return $http;
}
function valida_campo($name)
{
    if (($name) == '') {
        return true;
    } else {
        return false;
    }
}
function pagina($pagina)
{
    $pag = (!isset($_GET['p']) or $_GET['p'] == "index.php") ? $pagina : $_GET['p'];
    return $pag;
}
function valida_legajo($user)
{
    if (trim($user) == '') {
        return false;
    } else {
        return true;
    }
}
function valida_password($pass)
{
    if (trim($pass) == '') {
        return false;
    } else {
        return true;
    }
}
function error_prepre($var)
{
    if (!$var) {
        echo "<b>Declaración no pudo ser preparada.</b>";
        //die(print_r(sqlsrv_errors(), true)); 
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                echo "SQLSTATE: " . $error['SQLSTATE'] . "<br />";
                echo "code: " . $error['code'] . "<br />";
                echo "message: " . $error['message'] . "<br />";
            }
        }
    }
}
function error_execute($var, $var2)
{
    if (!sqlsrv_execute($var)) {
        echo "<b>La declaración " . $var2 . " no se pudo ejecutar</b>\n";
        // die(print_r(sqlsrv_errors(), true));
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                echo "SQLSTATE: " . $error['SQLSTATE'] . "<br />";
                echo "code: " . $error['code'] . "<br />";
                echo "message: " . $error['message'] . "<br />";
            }
        }
    }
}
function error_query($var, $var2)
{
    if (!$var) {
        echo "<b>La sentencia " . $var2 . " no se pudo ejecutar</b><br />";
        // die(print_r(sqlsrv_errors(), true));
        if (($errors = sqlsrv_errors()) != null) {
            date_default_timezone_set('America/Argentina/Buenos_Aires');
            $fechaarch = date('dmY');
            $fechaarch2 = date('d/m/Y H:i:s');
            $nombre_archivo = __DIR__ . "/logs/log_error_sql_" . $fechaarch . ".txt";
            if ($archivo = fopen($nombre_archivo, "a")) {
                foreach ($errors as $error) {
                    if (fwrite($archivo, "------------------------------\n"
                        . $fechaarch2 . "\nLa sentencia " . $var2 . " no se pudo ejecutar\n SQLSTATE: " . $error['SQLSTATE'] . "\n code: " . $error['code'] . "\n message: " . $error['message'] . " \n------------------------------\n"))
                        fclose($archivo);
                }
            }
            // foreach($errors as $error) {
            //     echo "SQLSTATE: ".$error[ 'SQLSTATE']."<br />";
            //     echo "code: ".$error[ 'code']."<br />";
            //     echo "message: ".$error[ 'message']."<br />";
            // }
        }
    }
}
function UnsetGet($variable)
{
    $_GET[$variable] = isset($_GET[$variable]) ? $_GET[$variable] : '';
    return $_GET[$variable];
}
/** Función UnsetPost() */
function UnsetPost($variable)
{
    $_POST[$variable] = ($_POST[$variable]) ?? '';
    return $_POST[$variable];
}
function UnsetVar($UnsetVar)
{
    if (!isset($UnsetVar)) {
        $UnsetVar = "";
    }
    return $UnsetVar;
}
function FormatHora($var)
{
    // if($var <= -60){ /** Si el valor es menor o igual a menos -60 */
    $segundos = $var * 60;
    $horas    = intval($segundos / 3600);
    $minutos  = floor(($segundos - ($horas * 3600)) / 60);
    $segundos = $segundos - ($horas * 3600) - ($minutos * 60);
    $horas    = str_pad($horas, 2, "0", STR_PAD_LEFT);
    // $minutos  = ($minutos < 0) ? str_replace("-", "", $minutos) : $minutos;
    $minutos  = str_pad($minutos, 2, "0", STR_PAD_LEFT);
    $hora     = $horas . ':' . $minutos;
    switch ($hora) {
        case '00:00':
            $hora = "-";
            break;
    }
    return $hora;
}
function FormatHoraR($var)
{
    // if($var <= -60){ /** Si el valor es menor o igual a menos -60 */
    $segundos = $var * 60;
    $horas    = intval($segundos / 3600);
    $minutos  = floor(($segundos - ($horas * 3600)) / 60);
    $segundos = $segundos - ($horas * 3600) - ($minutos * 60);
    $horas    = str_pad($horas, 2, "0", STR_PAD_LEFT);
    $minutos  = ($minutos < 0) ? str_replace("-", "", $minutos) : $minutos;
    $minutos  = str_pad($minutos, 2, "0", STR_PAD_LEFT);
    $hora     = $horas . ':' . $minutos;
    switch ($hora) {
        case '00:00':
            $hora = "0";
            break;
    }
    return $hora;
}
function MinExcel($min)
{
    $min = $min / 60;
    $min    = ($min / 24);
    return $min;
}
function FormatHora_Neg($var)
{
    $hora = (str_replace("-", "-00:", $var));
    return $hora;
}
function FormatHora_chart($var)
{
    $segundos = $var * 60;
    $horas    = floor($segundos / 3600);
    $minutos  = floor(($segundos - ($horas * 3600)) / 60);
    $segundos = $segundos - ($horas * 3600) - ($minutos * 60);
    $horas    = str_pad($horas, 2, "0", STR_PAD_LEFT);
    $minutos  = str_pad($minutos, 2, "0", STR_PAD_LEFT);
    $hora     = $horas . "" . $minutos;
    switch ($hora) {
        case '0000':
            $hora = "-";
            break;
    }
    return $hora;
}
function cero4($var)
{
    $var = str_pad($var, 4, "0", STR_PAD_LEFT);
    return $var;
}
function br()
{
    echo "<br />";
}
function h1($texto)
{
    echo "<h1>" . $texto . "</h1>";
}
function h2($texto)
{
    echo "<h2>" . $texto . "</h2>";
}
function h3($texto)
{
    echo "<h3>" . $texto . "</h3>";
}
function h4($texto)
{
    echo "<h4>" . $texto . "</h4>";
}
function pre($cod)
{
    echo "<pre>" . $cod . "</pre>";
}
function no()
{
    echo "d-none";
}
function nombrearchivo()
{
    $nombrearchivo = basename($_SERVER['PHP_SELF'], ".php") . PHP_EOL;
    return ucfirst($nombrearchivo);
}
function test_input($data)
{
    $data = trim($data);
    // $data = stripslashes($data);
    // $data = htmlspecialchars($data);
    $data = htmlspecialchars(stripslashes($data));
    $data = str_ireplace("script", "blocked", $data);
    $data = htmlentities($data, ENT_QUOTES);

    return $data;
}
function test_input2($data)
{
    foreach ($data as $key => $value) {
        $value = trim($data);
        // $data = stripslashes($data);
        // $data = htmlspecialchars($data);
        // $data = htmlspecialchars(stripslashes($data));
        $value = str_ireplace("script", "blocked", $data);
        $value = htmlentities($data, ENT_QUOTES);

    }
    return $value;
    
}
function hoy()
{
    $hoy = date('Y-m-d');
    return rtrim($hoy);
}
function FechaString($var)
{
    $date        = date_create("$var");
    $FormatFecha = date_format($date, "Ymd");
    return $FormatFecha;
}
function sino($var)
{
    switch ($var) {
        case '0':
            $var = "No";
            break;
        case '1':
            $var = "Sí";
            break;
    }
    return $var;
}
function ceronull($var)
{
    switch ($var) {
        case '0':
        case '00:00':
        case '0:00':
        case '':
        case null:
            $var = '-';
            break;
    }
    return $var;
}
function horacero($var)
{
    switch ($var) {
        case '00:00':
            $var = "-";
            break;
    }
    return $var;
}
function show()
{
    if (($_GET['q'] == 'true')) {
        $var = '';
    } else {
        $var = "collapse";
    }
    return $var;
}
function nombre_dia($var)
{
    switch ($var) {
        case '7':
            $var = "Domingo";
            break;
        case '1':
            $var = "Lunes";
            break;
        case '2':
            $var = "Martes";
            break;
        case '3':
            $var = "Miércoles";
            break;
        case '4':
            $var = "Jueves";
            break;
        case '5':
            $var = "Viernes";
            break;
        case '6':
            $var = "Sábado";
            break;
    }
    return $var;
}
function tipo_doc($var)
{
    switch ($var) {
        case '0':
            $var = "DU";
            break;
        case '1':
            $var = "DNI";
            break;
        case '2':
            $var = "CI";
            break;
        case '3':
            $var = "LC";
            break;
        case '4':
            $var = "LE";
            break;
        case '5':
            $var = "PAS";
            break;
    }
    return $var;
}
function nombre_dias($var, $abreviado)
{
    if ($abreviado) {
        switch ($var) {
            case '7':
                $var = "Dom";
                break;
            case '1':
                $var = "Lun";
                break;
            case '2':
                $var = "Mar";
                break;
            case '3':
                $var = "Mié";
                break;
            case '4':
                $var = "Jue";
                break;
            case '5':
                $var = "Vie";
                break;
            case '6':
                $var = "Sáb";
                break;
        }
    } else {
        switch ($var) {
            case '7':
                $var = "Domingo";
                break;
            case '1':
                $var = "Lunes";
                break;
            case '2':
                $var = "Martes";
                break;
            case '3':
                $var = "Miércoles";
                break;
            case '4':
                $var = "Jueves";
                break;
            case '5':
                $var = "Viernes";
                break;
            case '6':
                $var = "Sábado";
                break;
        }
    }
    return $var;
}
function DiaSemana($Ymd)
{
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    setlocale(LC_TIME, "spanish");
    $scheduled_day = $Ymd;
    $days = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado'];
    $day = date('w', strtotime($scheduled_day));
    $scheduled_day = "$days[$day] " . date('d/m/Y', strtotime($scheduled_day));
    return $scheduled_day;
}
function DiaSemana_Numero($Ymd)
{
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    setlocale(LC_TIME, "spanish");
    $scheduled_day = $Ymd;
    $days = [7, 1, 2, 3, 4, 5, 6];
    $day = date('w', strtotime($scheduled_day));
    $scheduled_day = $days[$day];
    return $scheduled_day;
}
function Nombre_Mes($Ymd)
{
    $scheduled_month = $Ymd;
    $Month = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    $mes = date('n', strtotime($scheduled_month));
    $scheduled_month = $Month[$mes];
    return $scheduled_month;
}
function Nombre_MesNum($NumeroMes)
{
    switch ($NumeroMes) {
        case '1':
            $text = 'Enero';
            break;
        case '2':
            $text = 'Febrero';
            break;
        case '3':
            $text = 'Marzo';
            break;
        case '4':
            $text = 'Abril';
            break;
        case '5':
            $text = 'Mayo';
            break;
        case '6':
            $text = 'Junio';
            break;
        case '7':
            $text = 'Julio';
            break;
        case '8':
            $text = 'Agosto';
            break;
        case '9':
            $text = 'Septiembre';
            break;
        case '10':
            $text = 'Octubre';
            break;
        case '11':
            $text = 'Noviembre';
            break;
        case '12':
            $text = 'Diciembre';
            break;
        default:
            $text = 'No hay Mes';
            break;
    }
    return $text;
}
function DiaSemana2($Ymd)
{
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    setlocale(LC_TIME, "spanish");
    $scheduled_day = $Ymd;
    $days = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado'];
    $day = date('w', strtotime($scheduled_day));
    $scheduled_day = "$days[$day] <br/>" . date('d.m.Y', strtotime($scheduled_day));
    return $scheduled_day;
}
function DiaSemana4($Ymd)
{
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    setlocale(LC_TIME, "spanish");
    $scheduled_day = $Ymd;
    $days = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado'];
    $day = date('w', strtotime($scheduled_day));
    $scheduled_day = "$days[$day]" . '&nbsp;' . date('d/m/Y', strtotime($scheduled_day));
    return $scheduled_day;
}
function DiaSemana3($Ymd)
{
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    setlocale(LC_TIME, "spanish");
    $scheduled_day = $Ymd;
    $days = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado'];
    $day = date('w', strtotime($scheduled_day));
    $scheduled_day = $days[$day];
    return $scheduled_day;
}
function fechformat($fecha)
{
    $fecha = date_create($fecha);
    $fecha = date_format($fecha, 'd/m/Y');
    return $fecha;
}
function Fech_Format_Var($fecha, $var)
{
    $fecha = date_create($fecha);
    $fecha = date_format($fecha, $var);
    return $fecha;
}
function fechformat2($var)
{
    $dato = date_create($var);
    $var  = date_format($dato, 'Y-m-d');
    return $var;
}
function FechaFormatH($FechaHora)
{
    $dato = date_create($FechaHora);
    $var  = date_format($dato, "d/m/Y H:i");
    return $var;
}
function FechaFormatVar($FechaHora, $var)
{
    $dato = date_create($FechaHora);
    $var  = date_format($dato, $var);
    return $var;
}
function fechformatM($var)
{
    $dato = date_create("$var");
    $var = date_format($dato, "d-m-Y");
    return $var;
}
function Fecha_String($var)
{
    $dato = date_create($var);
    $var = date_format($dato, 'Ymd');
    return $var;
}
function user_session()
{
    $var = $_SESSION['legajo'];
    return $var;
}
function inicio()
{
    require __DIR__ . '../config/conect_mysql.php';
    $sql = "SELECT usuarios.id FROM usuarios";
    $rs = mysqli_query($link, $sql);
    $numrows = mysqli_num_rows($rs);
    mysqli_free_result($rs);
    mysqli_close($link);
    return $numrows;
}
function principal($rol)
{
    require __DIR__ . '../config/conect_mysql.php';
    $sql = "SELECT usuarios.id FROM usuarios 
    JOIN roles ON usuarios.rol = roles.id
    WHERE usuarios.principal = '1' AND roles.recid = '$rol'";
    // print_r($sql);
    $rs = mysqli_query($link, $sql);
    $numrows = mysqli_num_rows($rs);
    mysqli_free_result($rs);
    mysqli_close($link);
    $numrows = ($numrows == '1') ? true : false;
    return $numrows;
}
function token_exist($recid_cliente)
{
    require __DIR__ . '../config/conect_mysql.php';
    $sql = "SELECT clientes.id FROM clientes
    WHERE clientes.tkmobile !='' AND clientes.recid = '$recid_cliente'";
    $rs = mysqli_query($link, $sql);
    $numrows = mysqli_num_rows($rs);
    mysqli_free_result($rs);
    mysqli_close($link);
    $numrows = ($numrows == '1') ? true : false;
    return $numrows;
}
/** FUNCIÓN PARA LEER LE ARCHIVO */
function DatoArchivo($ruta)
{
    $ruta  = __DIR__ . $ruta;
    /** Ruta del archivo */
    $fp    = fopen($ruta, "r");
    /** Abrimos el archivo */
    $dato  = fgets($fp);
    /** Leemos el archivo */
    fclose($fp);
    /**  Cerramos el archivo */
    return $dato;
    /** retornamos el valor */
}
/** FUNCION PARA TRAER DATOS DESDE API*/
function GetDatosApi($url, $key)
{
    $Get   = file_get_contents($url);
    /** Traemos el fichero completo de la url */
    $array = json_decode($Get, TRUE);
    /** Decodificamos el string de JSON que devuelve el get */
    $array = $array[$key];
    /** Array de los datos obtenidos del Get con la clave de acuerdo al manual de la API */
    return $array;
    /** Retornamos el resultado */
}
function dnone($var)
{
    switch ($var) {
        case '0':
        case '0,00':
        case '00:00':
        case '00:00:00':
        case '00:00:01':
        case '00:00:10':
        case null:
        case '':
            $var = "d-none";
            break;
    }
    return $var;
}
function ExisteCliente($recid)
{
    /** Verificamos el recid de cliente para ver si existe. 
     * Sino existe redirigimos a Clientes*/
    $query="SELECT clientes.recid as recid, clientes.nombre as nombre FROM clientes WHERE clientes.id >'0' AND clientes.recid='$recid' LIMIT 1";
    require 'config/conect_mysql.php';
    $result = mysqli_query($link, $query);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) :
            $nombre = $row['nombre'];
        endwhile;
        mysqli_free_result($result);
        mysqli_close($link);
        return $nombre;
    }else{
        header("Location: /" . HOMEHOST . "/usuarios/clientes/");
    }

    // CountRegMayorCeroMySql($query) ? '' : header("Location: /" . HOMEHOST . "/usuarios/clientes/");
    /** redirect */
}
function ExisteRol2($recid)
{
    /** Verificamos el recid de cliente para ver si existe. 
     * Sino existe redirigimos a Clientes*/
    $q = "SELECT roles.recid as recid, roles.nombre as nombre FROM roles WHERE roles.recid='$recid' LIMIT 1";
    require 'config/conect_mysql.php';
    $rs = mysqli_query($link, $q);
    if (mysqli_num_rows($rs) > 0) {
        while ($r = mysqli_fetch_assoc($rs)) :
            $n = $r['nombre'];
        endwhile;
        mysqli_free_result($rs);
        mysqli_close($link);
        return $n;
    }else{
        header("Location: /" . HOMEHOST . "/usuarios/clientes/");
    }

    // CountRegMayorCeroMySql($query) ? '' : header("Location: /" . HOMEHOST . "/usuarios/clientes/");
    /** redirect */
}
function ExisteRol($recid)
{
    /** Verificamos el recid de cliente para ver si existe. 
     * Sino existe redirigimos a Clientes*/
    $url   = host() . "/" . HOMEHOST . "/data/GetRoles.php?tk=" . token() . "&recid=" . $recid;
    $json  = file_get_contents($url);
    $array = json_decode($json, TRUE);
    $count = (count($array[0]['roles']));
    ($count) ? '' : header("Location: /" . HOMEHOST . "/usuarios/clientes/");
    /** redirect */
    /** 
     *  
     */
}
function Cliente_c($recid)
{
    $url   = host() . "/" . HOMEHOST . "/data/GetClientes.php?tk=" . token() . "&recid=" . $recid;
    $json         = file_get_contents($url);
    $array        = json_decode($json, TRUE);
    $data         = $array[0]['clientes'];
    if (is_array($data)) :
        // $r = array_filter($data, function ($e) {
        //     return $e['recid'] == $_GET['_c'];
        // });
        foreach ($data as $value) :
            $id_c     = $value['id'];
            $ident    = $value['ident'];
            $recid_c  = $value['recid'];
            $nombre_c = $value['nombre'];
            $host_c   = $value['host'];
            return array($id_c, $ident, $nombre_c, $recid_c, $host_c);
        endforeach;
    endif;
}
function Rol_Recid($recid)
{
    $url   = host() . "/" . HOMEHOST . "/data/GetRoles.php?tk=" . token() . "&recid=" . $recid;
    $json  = file_get_contents($url);
    $array = json_decode($json, TRUE);
    $data  = $array[0]['roles'];
    if (is_array($data)) :
        // $r = array_filter($data, function ($e) {
        //     return $e['recid'] == $_GET['_c'];
        // });
        foreach ($data as $value) :
            $id_Rol           = $value['id'];
            $nombreRol        = $value['nombre'];
            $clienteRol       = $value['cliente'];
            $UsuariosRol      = $value['cant_roles'];
            $recid_clienteRol = $value['recid_cliente'];
            $idClienteRol     = $value['id_cliente'];
            return array($id_Rol, $nombreRol, $clienteRol, $UsuariosRol, $recid_clienteRol, $idClienteRol);
        endforeach;
    endif;
}
function modulo_cuentas()
{
    $r = array_filter($_SESSION['MODS_ROL'], function ($e) {
        return $e['modsrol'] == '1';
    });
    foreach ($r as $value) {
        return $value['modsrol'];
    }
}
function ExisteModRol($modulo)
{
    /**
     * verificamos si existe el modulo asociado a la session del rol de usuario. 
     * sino existe lo enviamos al incio.
     */
    define('ID_MODULO', $modulo);
    $r = array_filter($_SESSION["MODS_ROL"], function ($e) {
        return $e['modsrol'] === ID_MODULO;
    });
    if (!$r) {
        header("Location:/" . HOMEHOST . "/");
        exit;
    }
    /** redirect */
}
function errors($valor)
{
    return ini_set('display_errors', $valor);
}
function notif_ok_var($get, $texto)
{
    $ok = "";
    if (!empty($_GET[$get]) && (isset($_GET[$get]))) {
        $ok = '<div class="col-12">
        <div class="animate__animated animate__fadeInDown mt-3 radius p-3 fw5 fontq alert alert-success text-uppercase alert-dismissible fade show" role="alert">
			' . $texto . '
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
          </div>
          </div>';
    }
    return $ok;
}
function notif_error_var($get, $texto)
{
    $ok = "";
    if (isset($_GET[$get])) {
        $ok = '<div class="col-12">
        <div class="animate__animated animate__fadeInDown mt-1 p-3 radius-0 fw4 fontq alert alert-danger text-uppercase alert-dismissible fade show" role="alert">
			' . $texto . '
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
          </div>
          </div>';
    }
    return $ok;
}
function notif_error($GET, $msg, $texto)
{
    $ok = "";
    if ($_GET[$GET] == $msg) {
        $ok = '<div class="row"><div class="col-12 mt-4">
        <div class="animate__animated animate__fadeInDown p-3 radius-0 fw4 fontq alert alert-danger text-uppercase alert-dismissible fade show" role="alert">
			' . $texto . '
          </div>
          </div></div>';
    }
    return $ok;
}
function ListaRoles($Recid_C)
{
    $url          = host() . "/" . HOMEHOST . "/data/GetRoles.php?tk=" . token() . "&recid_c=" . $Recid_C;
    $json         = file_get_contents($url);
    $array        = json_decode($json, TRUE);
    $data         = $array[0]['roles'];
    if (is_array($array)) :
        foreach ($data as $value) :
            $nombre = $value['nombre'];
            $id     = $value['id'];
            echo '<option value="' . $id . '">' . $nombre . '</option>';
        endforeach;
    endif;
}
function sector_rol($recid_rol)
{
    $url   = host() . "/" . HOMEHOST . "/data/GetEstructRol.php?tk=" . token() . "&_r=" . $recid_rol;
    $json  = file_get_contents($url);
    $array = json_decode($json, TRUE);
    $sect_roles = (!$array[0]['error']) ? implode(",", $array[0]['sector']) : '';
    $rol = (!$array[0]['error']) ? "$sect_roles" : "";
    return $rol;
}
function estructura_rol($get_rol, $recid_rol, $e, $data)
{
    $url   = host() . "/" . HOMEHOST . "/data/$get_rol.php?tk=" . token() . "&_r=" . $recid_rol . "&e=" . $e;
    // echo $url; br();
    $json  = file_get_contents($url);
    $array = json_decode($json, TRUE);
    $data = $array[0][$data];
    if (is_array($data)) {
        $val_roles = (!$array[0]['error']) ? implode(",", $data) : '';
        $rol = (!$array[0]['error']) ? "$val_roles" : "";
        return $rol;
    }
}
function estructura_rol_count($get_rol, $recid_rol, $e, $data)
{
    $url   = host() . "/" . HOMEHOST . "/data/$get_rol.php?tk=" . token() . "&_r=" . $recid_rol . "&e=" . $e;
    $json  = file_get_contents($url);
    $array = json_decode($json, TRUE);
    $val_roles = (!$array[0]['error']) ? count($array[0][$data]) : '';
    $rol = (!$array[0]['error']) ? "$val_roles" : "";
    return $rol;
}
function count_estructura($_c, $e)
{
    $urls   = host() . "/" . HOMEHOST . "/data/GetEstructura.php?tk=" . token() . "&_c=" . $_c . "&count&e=" . $e;
    // echo $urls.PHP_EOL;
    // CountRegMySql("SELECT modulos.id AS 'id' FROM modulos WHERE modulos.id>'0' AND modulos.estado ='0'");    
    $jsons  = file_get_contents($urls);
    $arrays = json_decode($jsons, TRUE);
    if (is_array($arrays)) :
        $rowcount = ($arrays[0]['error']) ? ($arrays[0]['count_cod']) : '-';
        return $rowcount;
    endif;
}
function count_estructura2($_c, $tabla, $ColCodi)
{
    require_once __DIR__ . '/config/conect_mssql.php';
    $query = "SELECT COUNT($tabla.$ColCodi) AS count_cod
    FROM $tabla";
    $result = sqlsrv_query($link, $query);
    while ($row = sqlsrv_fetch_array($result)) :
        $count_cod = $row['count_cod'];
    endwhile;
    sqlsrv_free_stmt($result);
    sqlsrv_close($link);
}
function TipoNov($var)
{
    switch ($var) {
        case '0':
            $tipo = 'Llegada tarde';
            break;
        case '1':
            $tipo = 'Incumplimiento';
            break;
        case '2':
            $tipo = 'Salida anticipada';
            break;
        case '3':
            $tipo = 'Ausencia';
            break;
        case '4':
            $tipo = 'Licencia';
            break;
        case '5':
            $tipo = 'Accidente';
            break;
        case '6':
            $tipo = 'Vacaciones';
            break;
        case '7':
            $tipo = 'Suspensión';
            break;
        case '8':
            $tipo = 'ART';
            break;
        default:
            $tipo = $var;
            break;
    }
    return $tipo;
}
function TipoONov($var)
{
    $var = $var == 1 ? 'En Hora' : 'En Valor';
    return $var;
}
function CateNov($var)
{
    switch ($var) {
        case '0':
            $tipo = 'Primaria';
            break;
        case '1':
            $tipo = 'Adicional';
            break;
        case '2':
            $tipo = 'Secundaria';
            break;
        default:
            $tipo = $var;
            break;
    }
    return $tipo;
}
function JustNov($var)
{
    switch ($var) {
        case '0':
            $tipo = '';
            break;
        case '1':
            $tipo = '<span data-icon="&#xe10e;"></span>';
            /** icono de check */
            break;
        default:
            $tipo = $var;
            break;
    }
    return $tipo;
}
function TipoDoc($var)
{
    switch ($var) {
        case '0':
            $tipo = 'DU';
            break;
        case '1':
            $tipo = 'DNI';
            break;
        case '2':
            $tipo = 'CI';
            break;
        case '3':
            $tipo = 'LC';
            break;
        case '4':
            $tipo = 'LE';
            break;
        case '5':
            $tipo = 'PAS';
            break;
        default:
            $tipo = $var;
            break;
    }
    return $tipo;
}
function total_pers()
{
    require __DIR__ . '/config/conect_mssql.php';
    $query = "SELECT COUNT(PERSONAL.LegNume) AS cantidad FROM PERSONAL WHERE PERSONAL.LegFeEg = '17530101' AND PERSONAL.LegNume > '0'";
    // print_r($query).PHP_EOL;
    // exit;
    $result = sqlsrv_query($link, $query);
        // $data  = array();
    ;
    while ($row = sqlsrv_fetch_array($result)) {
        $cantidad = $row['cantidad'];
    }
    return $cantidad;
    sqlsrv_free_stmt($result);
    sqlsrv_close($link);
}
function tot_pers_pres($fecha)
{
    require __DIR__ . '/config/conect_mssql.php';
    $query = "SELECT COUNT(PERSONAL.LegNume) AS cantidad FROM PERSONAL
    INNER JOIN FICHAS ON PERSONAL.LegNume = FICHAS.FicLega
    WHERE PERSONAL.LegFeEg = '17530101'
    AND PERSONAL.LegNume > '0'
    AND FICHAS.FicDiaL ='1' AND FICHAS.FicFech='$fecha'";
    // print_r($query).PHP_EOL;
    // exit;
    $result = sqlsrv_query($link, $query);
        // $data  = array();
    ;
    while ($row = sqlsrv_fetch_array($result)) {
        $cantidad = $row['cantidad'];
    }
    return $cantidad;
    sqlsrv_free_stmt($result);
    sqlsrv_close($link);
}
function porcentaje($valor, $cantidad)
{
    $percent = round($valor / $cantidad * 100, 1);
    $percent = ($percent <= 100) ? $percent : '100';
    return $percent;
}
function imgIcon($var, $title, $width)
{
    $src = "/" . HOMEHOST . "/img/" . $var . ".png";
    return '<img loading="lazy" src="' . $src . '" class="' . $width . '" "alt="' . $title . '" title="' . $title . '">';
}
function imgFoto($face_url, $title, $width)
{
    $src = "https://server.xenio.uy/" . $face_url;
    return '<img loading="lazy"  src="' . $src . '" class="' . $width . '" title="' . $title . '">';
}
function Foto($face_url, $title, $width)
{
    $src = $face_url;
    return '<img loading="lazy" src="' . $src . '" class="' . $width . '" title="' . $title . '">';
}
function color_fichada($array)
{
    foreach ($array as $valor) {
        switch ($valor['Tipo']) {
            case 'Manual':
                $fichada = array(
                    'Fic'    => '<span class="text-primary fw4 contentd" title="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '">' . $valor['Fic'] . '</span>',
                    'Estado' => $valor['Estado'],
                    'Tipo'   => $valor['Tipo']
                );
                break;
            default:
                $fichada = array(
                    'Fic'    => '<span class="fw4 contentd" title="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '">' . $valor['Fic'] . '</span>',
                    'Estado' => $valor['Estado'],
                    'Tipo'   => $valor['Tipo']
                );
                break;
        }
        switch ($valor['Estado']) {
            case 'Modificada':
                $fichada = array(
                    'Fic'    => '<span class="text-danger fw4 contentd" title="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '" >' . $valor['Fic'] . '</span>',
                    'Estado' => $valor['Estado'],
                    'Tipo'   => $valor['Tipo']
                );
                break;
        }
    }
    return $fichada;
}
function color_fichada3($array)
{
    foreach ($array as $valor) {
        switch ($valor['Tipo']) {
            case 'Manual':
                $fichada = array(
                    'Fic'    => '<span class="text-primary fw4 contentd" title="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '. Fecha de registro: ' . $valor['RegFeRe'] . '">' . $valor['Fic'] . '</span>',
                    'Estado' => $valor['Estado'],
                    'Tipo'   => $valor['Tipo']
                );
                break;
            default:
                $fichada = array(
                    'Fic'    => '<span class="fw4 contentd" title="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '. Fecha de registro: ' . $valor['RegFeRe'] . '">' . $valor['Fic'] . '</span>',
                    'Estado' => $valor['Estado'],
                    'Tipo'   => $valor['Tipo']
                );
                break;
        }
        switch ($valor['Estado']) {
            case 'Modificada':
                $fichada = array(
                    'Fic'    => '<span class="text-danger fw4 contentd" title="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '. Fecha de registro: ' . $valor['RegFeRe'] . '">' . $valor['Fic'] . '</span>',
                    'Estado' => $valor['Estado'],
                    'Tipo'   => $valor['Tipo']
                );
                break;
        }
    }
    return $fichada;
}

function color_Fichada2($tipo, $estado, $hora)
{

    switch ($tipo) {
        case 'Manual':
            $fichada = '<span class="text-primary fw5" title="Fichada: ' . $tipo . '. Estado: ' . $estado . '">' . $hora . '</span>';
            break;
        default:
            $fichada =  '<span class="text-dark fw5" title="Fichada: ' . $tipo . '. Estado: ' . $estado . '">' . $hora . '</span>';
            break;
    }
    switch ($estado) {
        case 'Modificada':
            $fichada = '<span class="text-danger fw5" title="Fichada: ' . $tipo . '. Estado: ' . $estado . '" >' . $hora . '</span>';
            break;
        default:
            $fichada =  '<span class="text-dark fw5" title="Fichada: ' . $tipo . '. Estado: ' . $estado . '">' . $hora . '</span>';
            break;
    }
    if ($tipo == 'Manual' && $estado == 'Normal') {
        $fichada = '<span class="text-primary fw5" title="Fichada: ' . $tipo . '. Estado: ' . $estado . '">' . $hora . '</span>';
    }

    return $fichada;
}

function estruct_rol($estructura)
{
    return $_SESSION["ESTRUC_ROL"][$estructura];
}
/** Actual month last day **/
function _data_last_month_day($y, $m)
{
    $month = $m;
    $year = $y;
    $day = date("d", mktime(0, 0, 0, $month + 1, 0, $year));

    return date('Ymd', mktime(0, 0, 0, $month, $day, $year));
};
// echo _data_last_month_day('2020','04').PHP_EOL;

/** Actual month first day **/
function _data_first_month_day($y, $m)
{
    $month = $m;
    $year = $y;
    return date('Ymd', mktime(0, 0, 0, $month, 1, $year));
}
function peri_min_max()
{
    require __DIR__ . '/config/conect_mssql.php';
    $query = "SELECT MIN(CTANOVE.CTA2Peri) AS peri_min, MAX(CTANOVE.CTA2Peri) AS peri_max FROM CTANOVE";
    $rs    = sqlsrv_query($link, $query);
    while ($fila = sqlsrv_fetch_array($rs)) {
        $peri_min = $fila['peri_min'];
        $peri_max = $fila['peri_max'];
    }

    sqlsrv_free_stmt($rs);
    sqlsrv_close($link);
    $array = array(
        'min' => $peri_min,
        'max' => $peri_max
    );
    return $array;
    exit;
}
function PeriLiq()
{
    require __DIR__ . '/config/conect_mssql.php';
    $query = "SELECT PARACONT.ParPeMeD AS 'MensDesde', PARACONT.ParPeMeH AS 'MensHasta', PARACONT.ParPeJ1D AS 'Jor1Desde', PARACONT.ParPeJ1H AS 'Jor1Hasta', PARACONT.ParPeJ2D AS 'Jor2Desde', PARACONT.ParPeJ2H AS 'Jor2Hasta' FROM PARACONT WHERE PARACONT.ParCodi='0'";
    $rs    = sqlsrv_query($link, $query);
    while ($fila = sqlsrv_fetch_array($rs)) {

        $MensDesde = $fila['MensDesde'];
        $MensHasta = $fila['MensHasta'];
        $Jor1Desde = $fila['Jor1Desde'];
        $Jor1Hasta = $fila['Jor1Hasta'];
        $Jor2Desde = $fila['Jor2Desde'];
        $Jor2Hasta = $fila['Jor2Hasta'];
    }

    sqlsrv_free_stmt($rs);
    sqlsrv_close($link);
    $array = array(
        'MensDesde' => $MensDesde,
        'MensHasta' => $MensHasta,
        'Jor1Desde' => $Jor1Desde,
        'Jor1Hasta' => $Jor1Hasta,
        'Jor2Desde' => $Jor2Desde,
        'Jor2Hasta' => $Jor2Hasta
    );
    return $array;
    exit;
}

function audito_ch($AudTipo, $AudDato)
{
    $ipCliente = $_SERVER['REMOTE_ADDR'];
    switch ($ipCliente) {
        case '::1':
            $ipCliente = ('127.0.0.1');
            break;
        default:
            $ipCliente = ($_SERVER['REMOTE_ADDR']);
            break;
    }
    require __DIR__ . '/config/conect_mssql.php';
    $usuario    = explode("-", $_SESSION["user"]);

    // $AudUser   = substr($_SESSION["user"], 4, 10);
    $AudUser   = substr(ucfirst($usuario[1]), 0, 10);
    // $AudTerm   = gethostname();
    $AudTerm   = $ipCliente;
    $AudModu   = 21;
    $FechaHora = date('Ymd H:i:s');
    $AudFech   = date('Ymd');
    $AudHora   = date('H:i:s');

    $procedure_params = array(
        array(&$AudFech),
        array(&$AudHora),
        array(&$AudUser),
        array(&$AudTerm),
        array(&$AudModu),
        array(&$AudTipo),
        array(&$AudDato),
        array(&$FechaHora),
    );

    $sql = "exec DATA_AUDITORInsert @AudFech=?,@AudHora=?,@AudUser=?,@AudTerm=?,@AudModu=?,@AudTipo=?,@AudDato=?,@FechaHora=?";
    $stmt = sqlsrv_prepare($link, $sql, $procedure_params);

    if (!$stmt) {
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                // $dataAud = array("auditor" => "error", "dato" => $error['message']);
            }
        }
    }

    if (sqlsrv_execute($stmt)) {
        /** ejecuto la sentencia */
        $dataAud = array("auditor" => "ok");
        // echo json_encode($dataAud); 
    } else {

        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $mensaje = explode(']', $error['message']);
                $dataAud[] = array("auditor" => "error", "dato" => $mensaje[3]);
            }
        }

        echo json_encode($dataAud);
    }
    sqlsrv_execute($stmt);
    sqlsrv_close($link);
}
function audito_ch2($AudTipo, $AudDato)
{
    $ipCliente = $_SERVER['REMOTE_ADDR'];
    switch ($ipCliente) {
        case '::1':
            $ipCliente = ('127.0.0.1');
            break;
        default:
            $ipCliente = ($_SERVER['REMOTE_ADDR']);
            break;
    }
    require __DIR__ . '/config/conect_mssql.php';
    $usuario    = explode("-", $_SESSION["user"]);

    // $AudUser   = substr($_SESSION["user"], 4, 10);
    $AudUser   = substr(ucfirst($usuario[1]), 0, 10);
    // $AudTerm   = gethostname();
    $AudTerm   = $ipCliente . '-' . recid2(4);
    $AudModu   = 21;
    $FechaHora = date('Ymd H:i:s');
    $AudFech   = date('Ymd');
    $AudHora   = date('H:i:s');

    $procedure_params = array(
        array(&$AudFech),
        array(&$AudHora),
        array(&$AudUser),
        array(&$AudTerm),
        array(&$AudModu),
        array(&$AudTipo),
        array(&$AudDato),
        array(&$FechaHora),
    );

    $sql = "exec DATA_AUDITORInsert @AudFech=?,@AudHora=?,@AudUser=?,@AudTerm=?,@AudModu=?,@AudTipo=?,@AudDato=?,@FechaHora=?";
    $stmt = sqlsrv_prepare($link, $sql, $procedure_params);

    if (!$stmt) {
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                // $dataAud = array("auditor" => "error", "dato" => $error['message']);
            }
        }
    }

    if (sqlsrv_execute($stmt)) {
        /** ejecuto la sentencia */
        $dataAud = array("auditor" => "ok");
        // echo json_encode($dataAud); 
    } else {

        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $mensaje = explode(']', $error['message']);
                $dataAud[] = array("auditor" => "error", "dato" => $mensaje[3]);
            }
        }

        echo json_encode($dataAud);
    }
    sqlsrv_execute($stmt);
    sqlsrv_close($link);
}
function set_query_store($db)
{
    require __DIR__ . '/config/conect_mssql.php';
    $query = "ALTER DATABASE [$db] SET QUERY_STORE = ON   
    (MAX_STORAGE_SIZE_MB = 1024)";
    // print_r($query);
    $rs    = sqlsrv_query($link, $query);
    sqlsrv_free_stmt($rs);
    sqlsrv_close($link);
    // exit;
}
function fecha_min_max($tabla, $ColFech)
{
    require __DIR__ . '/config/conect_mssql.php';
    $param = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $query = "SELECT MIN($ColFech) AS 'min', MAX($ColFech) AS 'max' FROM $tabla  WHERE $ColFech !='17530101' AND $ColFech < GETDATE()";
    // print_r($query);
    $rs = sqlsrv_query($link, $query, $param, $options);
    while ($fila = sqlsrv_fetch_array($rs)) {
        $min = ($fila['min'] != null) ? $fila['min']->format('Y-m-d') : '';
        $max = ($fila['max'] != null) ? $fila['max']->format('Y-m-d') : '';
    }
    $array = array(
        'min' => $min,
        'max' => $max
    );
    return $array;
    sqlsrv_free_stmt($rs);
    sqlsrv_close($link);
    exit;
}
function fecha_min_max2($tabla, $ColFech)
{
    require __DIR__ . '/config/conect_mssql.php';
    $param = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $query = "SELECT MIN($ColFech) AS 'min', MAX($ColFech) AS 'max' FROM $tabla  WHERE $ColFech !='17530101'";
    // print_r($query);
    $rs = sqlsrv_query($link, $query, $param, $options);
    while ($fila = sqlsrv_fetch_array($rs)) {
        $min = ($fila['min'] != null) ? $fila['min']->format('Y-m-d') : '';
        $max = ($fila['max'] != null) ? $fila['max']->format('Y-m-d') : '';
    }
    $array = array(
        'min' => $min,
        'max' => $max
    );
    return $array;
    sqlsrv_free_stmt($rs);
    sqlsrv_close($link);
    exit;
}
function nov_cta_cte()
{
    require __DIR__ . '/config/conect_mssql.php';
    $params = array();
    $options =  array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $query = "SELECT DISTINCT CTANOVE.CTA2Nove, NOVEDAD.NovDesc, CTANOVE.CTA2Peri FROM CTANOVE JOIN NOVEDAD ON CTANOVE.CTA2Nove = NOVEDAD.NovCodi ORDER BY CTANOVE.CTA2Peri DESC";
    $rs    = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($rs) > 0) {
        while ($fila = sqlsrv_fetch_array($rs)) {
            $CTA2Nove = $fila['CTA2Nove'];
            $NovDesc  = $fila['NovDesc'];
            $CTA2Peri  = $fila['CTA2Peri'];
            $array[] = array(
                'cod' => $CTA2Nove,
                'desc' => $NovDesc,
                'peri' => $CTA2Peri
            );
        }
    } else {
        $array[] = array(
            'cod' => false,
            'desc' => false,
            'peri' => false
        );
    }

    sqlsrv_free_stmt($rs);
    sqlsrv_close($link);
    return ($array);
    // exit;
}
function nacionalidades()
{
    require __DIR__ . '/config/conect_mssql.php';
    $params = array();
    $options =  array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $query = "SELECT NACIONES.NacCodi, NACIONES.NacDesc FROM NACIONES";
    $rs    = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($rs) > 0) {
        while ($fila = sqlsrv_fetch_array($rs)) {
            $NacCodi = $fila['NacCodi'];
            $NacDesc  = empty($fila['NacDesc']) ? '-' : $fila['NacDesc'];
            $array[] = array(
                'cod' => $NacCodi,
                'desc' => $NacDesc,
            );
        }
    } else {
        $array[] = array(
            'cod' => false,
            'desc' => false
        );
    }

    sqlsrv_free_stmt($rs);
    sqlsrv_close($link);
    return ($array);
    // exit;
}
function super_unique($array, $key)
{
    $temp_array = [];
    foreach ($array as &$v) {
        if (!isset($temp_array[$v[$key]]))
            $temp_array[$v[$key]] = &$v;
    }
    $array = array_values($temp_array);
    return $array;
}

/** validar campos formmularios */

function ValNumerico($val)
{
    $val = is_numeric($val) ? true : false;
    return $val;
}
function ValInt($val)
{
    $val = is_int($val) ? true : false;
    return $val;
}
function ValString($val)
{
    $val = is_string($val) ? true : false;
    return $val;
}
function ValFloat($val)
{
    $val = is_float($val) ? true : false;
    return $val;
}
function DeleteRegistro($query)
{
    $params    = array();
    $options   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    require __DIR__ . '/config/conect_mssql.php';
    $stmt  = sqlsrv_query($link, $query, $params, $options);
    if (($stmt)) {
        // print_r($query);
        // echo json_encode($query);
        // exit;
        return true;
        sqlsrv_close($link);
    } else {
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $mensaje = explode(']', $error['message']);
                $data[] = array("status" => "error", "dato" => $mensaje[3]);
            }
        }
        echo json_encode($data[0]);
        exit;
        sqlsrv_close($link);
    }
}
/** Query MYSQL */
function CountRegMySql($query)
{
    require __DIR__ . '/config/conect_mysql.php';
    $stmt = mysqli_query($link, $query);
    // print_r($query); exit;
    if ($stmt) {
        $num=mysqli_num_rows($stmt);
        return $num;
        mysqli_free_result($stmt);
        mysqli_close($link);
    } else {
        return 'Error';
        mysqli_close($link);
        exit;
    }
}
/** Query MYSQL */
function CountRegMayorCeroMySql($query)
{
    require __DIR__ . '/config/conect_mysql.php';
    $stmt = mysqli_query($link, $query);
    // print_r($query); exit;
    if ($stmt) {
        if (mysqli_num_rows($stmt) > 0) {
            return true;
        } else {
            return false;
        }
        mysqli_free_result($stmt);
        mysqli_close($link);
    } else {

        mysqli_close($link);
        exit;
    }
}
function CountModRol()
{
    require __DIR__ . '/config/conect_mysql.php';
    $stmt = mysqli_query($link, "SELECT mod_roles.modulo AS modsrol FROM mod_roles WHERE mod_roles.recid_rol ='$_SESSION[RECID_ROL]'");
    if ($stmt) {
        return mysqli_num_rows($stmt);
        mysqli_free_result($stmt);
        mysqli_close($link);
    } else {
        mysqli_close($link);
        exit;
    }
}
function InsertRegistroMySql($query)
{
    require __DIR__ . '/config/conect_mysql.php';
    // print_r($query); exit;
    $stmt = mysqli_query($link, $query);
    if (($stmt)) {
        return true;
        mysqli_close($link);
    } else {
        statusData('error', mysqli_error($link));
        mysqli_close($link);
        exit;
    }
}
function UpdateRegistroMySql($query)
{
    require __DIR__ . '/config/conect_mysql.php';
    $stmt = mysqli_query($link, $query);
    // print_r($query); exit;
    if (($stmt)) {
        return true;
        mysqli_close($link);
    } else {
        statusData('error', mysqli_error($link));
        mysqli_close($link);
        exit;
    }
}
/** Fin Query MYSQL */
/**Query MS-SQL */
function DateFirst($numerodia)
{
    require __DIR__ . '/config/conect_mssql.php';
    $stmt  = sqlsrv_query($link, "SET DATEFIRST $numerodia");
    if (($stmt)) {
        return true;
        // sqlsrv_close($link);
    } else {
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $mensaje = explode(']', $error['message']);
                $data[] = array("status" => "error", "dato" => $mensaje[3]);
                exit;
            }
        }
        echo json_encode($data[0]);
        exit;
        sqlsrv_close($link);
    }
}
function InsertRegistro($query)
{
    $params    = array();
    $options   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    require __DIR__ . '/config/conect_mssql.php';
    // print_r($query);
    $stmt  = sqlsrv_query($link, $query, $params, $options);
    if (($stmt)) {
        return true;
        sqlsrv_close($link);
    } else {
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $mensaje = explode(']', $error['message']);
                $data[] = array("status" => "error", "dato" => $mensaje[3]);
                exit;
            }
        }
        echo json_encode($data[0]);
        exit;
        sqlsrv_close($link);
    }
}
function UpdateRegistro($query)
{
    $params    = array();
    $options   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    require __DIR__ . '/config/conect_mssql.php';
    $stmt  = sqlsrv_query($link, $query, $params, $options);
    // print_r($query);
    if (($stmt)) {
        return true;
        sqlsrv_close($link);
    } else {
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                // $mensaje = explode(']', $error['message']);
                // $data[] = array("status" => "error", "dato" => $mensaje[3]);
                $data[] = array("status" => "error", "dato" => 'Ya existe Novedad.');
                exit;
            }
        }
        echo json_encode($data[0]);
        exit;
        sqlsrv_close($link);
    }
}
function CountRegistrosMayorCero($queryy)
{
    $params    = array();
    $options   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    require __DIR__ . '/config/conect_mssql.php';
    $stmt  = sqlsrv_query($link, $queryy, $params, $options);
    // print_r($query);
    if (($stmt)) {
        if (sqlsrv_num_rows($stmt) > 0) {
            return true;
        } else {
            return false;
        }
        sqlsrv_free_stmt($stmt);
        sqlsrv_close($link);
    } else {
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $mensaje = explode(']', $error['message']);
                $data[] = array("status" => "error", "dato" => $mensaje[3]);
                //$data[] = array("status" => "error", "dato" => $query);
            }
        }
        sqlsrv_free_stmt($stmt);
        echo json_encode($data[0]);
        sqlsrv_close($link);
        exit;
    }
}
function NumRowsQueryMSQL($query)
{
    $params    = array();
    $options   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    require __DIR__ . '/config/conect_mssql.php';
    $stmt  = sqlsrv_query($link, $query, $params, $options);
    $num_rows = sqlsrv_num_rows($stmt);
    return $num_rows;
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($link);
    exit;
}
/** Fin Querys MS-SQL */
function PerCierre($FechaStr, $Legajo)
{
    $params    = array();
    $options   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    require __DIR__ . '/config/conect_mssql.php';
    $query = "SELECT TOP 1 CierreFech FROM PERCIERRE WHERE PERCIERRE.CierreLega = '$Legajo'";
    // print_r($query);exit;
    $stmt  = sqlsrv_query($link, $query, $params, $options);
    while ($row = sqlsrv_fetch_array($stmt)) {
        $perCierre = $row['CierreFech']->format('Ymd');
    }
    $perCierre = !empty($perCierre) ? $perCierre : '17530101';
    sqlsrv_free_stmt($stmt);
    if ($FechaStr <= $perCierre) {
        return true;
    } else {
        $query = "SELECT ParCierr FROM PARACONT WHERE ParCodi = 0 ORDER BY ParCodi";
        $stmt  = sqlsrv_query($link, $query, $params, $options);
        while ($row = sqlsrv_fetch_array($stmt)) {
            $ParCierr = $row['ParCierr']->format('Ymd');
        }
        $ParCierr = !empty($ParCierr) ? $ParCierr : '17530101';
        sqlsrv_free_stmt($stmt);
        if ($FechaStr <= $ParCierr) {
            return true;
        } else {
            return false;
        }
    }
    sqlsrv_close($link);
}
function PerCierreFech($FechaStr, $Legajo)
{
    $params    = array();
    $options   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    require __DIR__ . '/config/conect_mssql.php';
    $query = "SELECT TOP 1 CierreFech FROM PERCIERRE WHERE PERCIERRE.CierreLega = '$Legajo'";
    $stmt  = sqlsrv_query($link, $query, $params, $options);
    // print_r($query); exit;
    while ($row = sqlsrv_fetch_array($stmt)) {
        $perCierre = $row['CierreFech']->format('Ymd');
    }
    $perCierre = !empty($perCierre) ? $perCierre : '17530101';
    sqlsrv_free_stmt($stmt);

    if ($FechaStr <= $perCierre) {
        return $perCierre;
    } else {
        $query = "SELECT ParCierr FROM PARACONT WHERE ParCodi = 0 ORDER BY ParCodi";
        // print_r($query); exit;
        $stmt  = sqlsrv_query($link, $query, $params, $options);
        while ($row = sqlsrv_fetch_array($stmt)) {
            $ParCierr = $row['ParCierr']->format('Ymd');
        }
        $ParCierr = !empty($ParCierr) ? $ParCierr : '17530101';
        sqlsrv_free_stmt($stmt);
        if ($FechaStr <= $ParCierr) {
            return $ParCierr;
        } else {
            return false;
        }
    }
    sqlsrv_close($link);
}
function rutaWebService($Comando)
{
    return $_SESSION["WEBSERVICE"] . "/RRHHWebService/" . $Comando;
}
/** PARA EL WEBSERVICE CH*/
function respuestaWebService($respuesta)
{
    $respuesta = substr($respuesta, 1, -1);
    $respuesta = explode("=", $respuesta);
    return ($respuesta[1]);
}
function EstadoProceso($url)
{
    do {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta = curl_exec($ch);
    } while (respuestaWebService($respuesta) == 'Pendiente');
    curl_close($ch);
    return respuestaWebService($respuesta);
}
function PingWebServiceRRHH()
{
    $url = rutaWebService("Ping?");
    do {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta = curl_exec($ch);
    } while (($respuesta) == 'Pendiente');
    curl_close($ch);
}
function procesar_legajo($legajo, $FechaDesde, $FechaHasta)
{
    // PingWebServiceRRHH();
    $FechaDesde = Fech_Format_Var($FechaDesde, 'd/m/Y');
    $FechaHasta = Fech_Format_Var($FechaHasta, 'd/m/Y');
    $ruta = rutaWebService("Procesar");
    $post_data = "{Usuario=Supervisor,TipoDePersonal=0,LegajoDesde='$legajo',LegajoHasta='$legajo',FechaDesde='$FechaDesde',FechaHasta='$FechaHasta',Empresa=0,Planta=0,Sucursal=0,Grupo=0,Sector=0,Seccion=0}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ruta);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $respuesta = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($curl_errno > 0) {
        return "Error";
        exit;
    }
    curl_close($ch);
    if ($httpCode == 404) {
        return $respuesta;
        exit;
    }
    $processID = respuestaWebService($respuesta);
    $url = rutaWebService("EstadoProceso?ProcesoId=" . $processID);

    if ($httpCode == 201) {
        // usleep(1000000); /** un segundo */
        // sleep(2);
        return EstadoProceso($url);
    }
}
function procesar_Todo($FechaDesde, $FechaHasta, $LegajoDesde, $LegajoHasta)
{
    $FechaDesde = Fech_Format_Var($FechaDesde, 'd/m/Y');
    $FechaHasta = Fech_Format_Var($FechaHasta, 'd/m/Y');
    $ruta = rutaWebService("Procesar");
    $post_data = "{Usuario=Supervisor,TipoDePersonal=0,LegajoDesde=$LegajoDesde,LegajoHasta=$LegajoHasta,FechaDesde=$FechaDesde,FechaHasta=$FechaHasta,Empresa=0,Planta=0,Sucursal=0,Grupo=0,Sector=0,Seccion=0}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ruta);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $respuesta = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($curl_errno > 0) {
        $data = array('status' => 'error', 'dato' => 'No hay Conexión');
        echo json_encode($data);
        exit;
    }
    curl_close($ch);
    if ($httpCode == 404) {
        echo $respuesta;
        exit;
    }
    $processID = respuestaWebService($respuesta);
    $url = rutaWebService("EstadoProceso?ProcesoId=" . $processID);

    if ($httpCode == 201) {
        // sleep(2);
        echo EstadoProceso($url);
    }
}
function FicharHorario($FechaDesde, $FechaHasta, $LegajoDesde, $LegajoHasta, $TipoDePersonal, $Empresa, $Planta, $Sucursal, $Grupo, $Sector, $Seccion, $TipoDeFichada, $Laboral)
{
    $FechaDesde = Fech_Format_Var($FechaDesde, 'd/m/Y');
    $FechaHasta = Fech_Format_Var($FechaHasta, 'd/m/Y');
    $ruta = rutaWebService("FicharHorario");
    $post_data = "{Usuario=Supervisor,TipoDePersonal=$TipoDePersonal,LegajoDesde=$LegajoDesde,LegajoHasta=$LegajoHasta,FechaDesde=$FechaDesde,FechaHasta=$FechaHasta,Empresa=$Empresa,Planta=$Planta,Sucursal=$Sucursal,Grupo=$Grupo,Sector=$Sector,Seccion=$Seccion,TipoDeFichada=$TipoDeFichada,Laboral=$Laboral}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ruta);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $respuesta = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($curl_errno > 0) {
        $data = array('status' => 'error', 'dato' => 'No hay Conexión');
        echo json_encode($data);
        exit;
    }
    curl_close($ch);
    if ($httpCode == 404) {
        echo $respuesta;
        exit;
    }
    $processID = respuestaWebService($respuesta);
    $url = rutaWebService("EstadoFicharHorario?ProcesoId=" . $processID);

    if ($httpCode == 201) {
        return EstadoProceso($url);
        exit;
    }
}
function Liquidar($FechaDesde, $FechaHasta, $LegajoDesde, $LegajoHasta, $TipoDePersonal, $Empresa, $Planta, $Sucursal, $Grupo, $Sector, $Seccion)
{
    $FechaDesde = Fech_Format_Var($FechaDesde, 'd/m/Y');
    $FechaHasta = Fech_Format_Var($FechaHasta, 'd/m/Y');
    $ruta = rutaWebService("Liquidar");
    $post_data = "{Usuario=Supervisor,TipoDePersonal=$TipoDePersonal,LegajoDesde=$LegajoDesde,LegajoHasta=$LegajoHasta,FechaDesde=$FechaDesde,FechaHasta=$FechaHasta,Empresa=$Empresa,Planta=$Planta,Sucursal=$Sucursal,Grupo=$Grupo,Sector=$Sector,Seccion=$Seccion}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ruta);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $respuesta = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($curl_errno > 0) {
        $data = array('status' => 'error', 'dato' => 'No hay Conexión');
        echo json_encode($data);
        exit;
    }
    curl_close($ch);
    if ($httpCode == 404) {
        echo $respuesta;
        exit;
    }
    $processID = respuestaWebService($respuesta);
    $url = rutaWebService("EstadoFicharHorario?ProcesoId=" . $processID);

    if ($httpCode == 201) {
        return EstadoProceso($url);
        exit;
    }
}
function Procesar($FechaDesde, $FechaHasta, $LegajoDesde, $LegajoHasta, $TipoDePersonal, $Empresa, $Planta, $Sucursal, $Grupo, $Sector, $Seccion)
{
    $FechaDesde = Fech_Format_Var($FechaDesde, 'd/m/Y');
    $FechaHasta = Fech_Format_Var($FechaHasta, 'd/m/Y');
    $ruta = rutaWebService("Procesar");
    $post_data = "{Usuario=Supervisor,TipoDePersonal=$TipoDePersonal,LegajoDesde=$LegajoDesde,LegajoHasta=$LegajoHasta,FechaDesde=$FechaDesde,FechaHasta=$FechaHasta,Empresa=$Empresa,Planta=$Planta,Sucursal=$Sucursal,Grupo=$Grupo,Sector=$Sector,Seccion=$Seccion}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ruta);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $respuesta = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($curl_errno > 0) {
        $data = array('status' => 'error', 'dato' => 'No hay Conexión..');
        echo json_encode($data);
        exit;
    }
    curl_close($ch);
    if ($httpCode == 404) {
        $data = array('status' => 'error', 'dato' => $respuesta);
        echo json_encode($data);
        exit;
    }
    $processID = respuestaWebService($respuesta);
    $url = rutaWebService("EstadoProceso?ProcesoId=" . $processID);
    // echo $processID.PHP_EOL; 
    // echo EstadoProceso($url); exit;

    if ($httpCode == 201) {
        // return EstadoProceso($url);
        return array('ProcesoId'=>$processID, 'EstadoProceso'=>EstadoProceso($url));
        exit;
    }
}
function IngresarNovedad($TipoDePersonal, $LegajoDesde, $LegajoHasta, $FechaDesde, $FechaHasta, $Empresa, $Planta, $Sucursal, $Grupo, $Sector, $Seccion, $Laboral, $Novedad, $Justifica, $Observacion, $Horas, $Causa, $Categoria)
{
    $FechaDesde = Fech_Format_Var($FechaDesde, 'd/m/Y');
    $FechaHasta = Fech_Format_Var($FechaHasta, 'd/m/Y');
    $ruta = rutaWebService("Novedades");
    $post_data = "{Usuario=Supervisor,TipoDePersonal=$TipoDePersonal,LegajoDesde=$LegajoDesde,LegajoHasta=$LegajoHasta,FechaDesde=$FechaDesde,FechaHasta=$FechaHasta,Empresa=$Empresa,Planta=$Planta,Sucursal=$Sucursal,Grupo=$Grupo,Sector=$Sector,Seccion=$Seccion,Laboral=$Laboral,Novedad=$Novedad,Justifica=$Justifica,Observacion=$Observacion,Horas=$Horas,Causa=$Causa,Categoria=$Categoria}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ruta);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $respuesta = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($curl_errno > 0) {
        $data = array('status' => 'error', 'dato' => 'No hay Conexión');
        echo json_encode($data);
        exit;
    }
    curl_close($ch);
    if ($httpCode == 404) {
        echo $respuesta;
        exit;
    }
    $processID = respuestaWebService($respuesta);
    $url = rutaWebService("EstadoNovedades?ProcesoId=" . $processID);

    if ($httpCode == 201) {
        return EstadoProceso($url);
        exit;
    }
}
/** FIN EL WEBSERVICE CH*/

function FusNuloPOST($dato, $result)
{
    $_POST[$dato] = ($_POST[$dato]) ?? $result;
    return $_POST[$dato];
}
function FusNuloGET($dato, $result)
{
    $_GET[$dato] = ($_GET[$dato]) ?? $result;
    return $_GET[$dato];
}
function dr_fecha($ddmmyyyy)
{
    $fecha = date("Ymd", strtotime((str_replace("/", "-", $ddmmyyyy))));
    return $fecha;
}
function datosGetIn($Get, $Col)
{
    if (!empty($Get)) {
        $Get = implode(',', $Get);
        $texto = !empty($Get) ? "AND " . $Col . " IN (" . $Get . ") " : '';
        return $texto;
    }
};
function datosGet($Get, $Col)
{
    $texto = !empty($Get) ? "AND " . $Col . " = '" . $Get . "' " : '';
    return $texto;
};
function MinHora($Min)
{
    $segundos = $Min * 60;
    $horas = floor($segundos / 3600);
    $minutos = floor(($segundos - ($horas * 3600)) / 60);
    $segundos = $segundos - ($horas * 3600) - ($minutos * 60);
    $horas = str_pad($horas, 2, "0", STR_PAD_LEFT);
    $minutos = str_pad($minutos, 2, "0", STR_PAD_LEFT);
    $hora = $horas . ':' . $minutos;
    switch ($hora) {
        case '00:00':
            $hora = "-";
            break;
    }
    return $hora;
}
function MinHora2($Min)
{
    $segundos = $Min * 60;
    $horas = floor($segundos / 3600);
    $minutos = floor(($segundos - ($horas * 3600)) / 60);
    $segundos = $segundos - ($horas * 3600) - ($minutos * 60);
    $horas = str_pad($horas, 2, "0", STR_PAD_LEFT);
    $minutos = str_pad($minutos, 2, "0", STR_PAD_LEFT);
    $hora = $horas . '' . $minutos;
    return $hora;
}
function HoraMin($var)
{
    $var = explode(":", $var);
    $MinHora = $var[0] * 60;
    $Min = $var[1];
    $Minutos = $MinHora + $Min;
    return $Minutos;
}
function ValidarHora($Hora)
{
    $Hora =  HoraMin($Hora);
    $Hora = ($Hora > '1439' || $Hora < '1') ? true : false;
    return $Hora;
}
function ValidaFormatoHora($valor)
{
    $countValor = strlen($valor);
    if (($countValor < 5)) {
        return true;
    } else if (($countValor > 5)) {
        return true;
    } else {
        return false;
    }
}
function ArrayFechas($start, $end)
{
    $range = array();

    if (is_string($start) === true) $start = strtotime($start);
    if (is_string($end) === true) $end = strtotime($end);

    if ($start > $end) return createDateRangeArray($end, $start);

    do {
        $range[] = date('Ymd', $start);
        $start = strtotime("+ 1 day", $start);
    } while ($start <= $end);

    return $range;
}
function CantDiasFech($Fecha)
{
    //defino fecha 1
    $ano1 = 1900;
    $mes1 = 01;
    $dia1 = 01;
    $Date = explode('-', $Fecha);
    //defino fecha 2
    $ano2 = $Date[0];
    $mes2 = $Date[1];
    $dia2 = $Date[2];

    //calculo timestamp de las dos fechas
    $timestamp1 = mktime(0, 0, 0, $mes1, $dia1, $ano1);
    $timestamp2 = mktime(4, 12, 0, $mes2, $dia2, $ano2);

    //resto a una fecha la otra
    $segundos_diferencia = $timestamp1 - $timestamp2;
    //echo $segundos_diferencia;

    //convierto segundos en días
    $dias_diferencia = $segundos_diferencia / (60 * 60 * 24);

    //obtengo el valor absoulto de los días (quito el posible signo negativo)
    $dias_diferencia = abs($dias_diferencia);

    //quito los decimales a los días de diferencia
    $dias_diferencia = floor($dias_diferencia);

    return $dias_diferencia + 2;
}
function PrintError($TituloError, $Mensaje)
{
    $data = array('status' => $TituloError, 'Mensaje' => $Mensaje);
    echo json_encode($data);
    /** Imprimo json con resultado */
}
function PrintOK($Mensaje)
{
    $data = array('status' => 'ok', 'Mensaje' => $Mensaje);
    echo json_encode($data);
    /** Imprimo json con resultado */
}
function PrintRespuestaJson($status, $Mensaje)
{
    $data = array('status' => $status, 'Mensaje' => $Mensaje);
    echo json_encode($data);
    /** Imprimo json con resultado */
}
function getRemoteFile($url, $timeout = 10)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $file_contents = curl_exec($ch);
    curl_close($ch);
    return ($file_contents) ? $file_contents : FALSE;
}
function mod_roles($recid_rol)
{
    require 'config/conect_mysql.php';
    $query = "SELECT mod_roles.id AS 'id', mod_roles.recid_rol AS 'recid_rol', modulos.nombre AS 'nombre', modulos.id AS 'id_mod', modulos.idtipo AS 'idtipo' FROM mod_roles INNER JOIN modulos ON mod_roles.modulo=modulos.id WHERE mod_roles.id>'0' AND modulos.estado='0' AND mod_roles.recid_rol='$recid_rol' ORDER BY modulos.orden";
    $result = mysqli_query($link, $query);
    $data  = array();
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) :
            $id        = $row['id'];
            $id_mod    = $row['id_mod'];
            $recid_rol = $row['recid_rol'];
            $nombre    = $row['nombre'];
            $idtipo    = $row['idtipo'];
            $data[] = array(
                'id'        => $id,
                'recid_rol' => $recid_rol,
                'id_mod'    => $id_mod,
                'modulo'    => $nombre,
                'idtipo'    => $idtipo
            );
        endwhile;
        mysqli_free_result($result);
        mysqli_close($link);
        $respuesta = array('success' => 'YES', 'error' => '0', 'mod_roles' => $data);
        return $respuesta;
    }
}
function TokenMobile($token, $data)
{
    /** data = "appcode" devuelve Aplication Code, "token" = devuelve el token */
    if (!empty($token)) {
        $t = explode('@', $token);
        switch ($data) {
            case 'appcode':
                return $t[1];
                break;
            case 'token':
                return $t[0];
                break;
            default:
                return '';
                break;
        }
    } else {
        return '';
    }
}
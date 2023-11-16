<?php

require __DIR__ . '/vendor/autoload.php';
$routeEnv = __DIR__ . '../../../config_chweb/';
$dotenv = Dotenv\Dotenv::createImmutable($routeEnv);
$dotenv->safeLoad();

function version()
{
    return 'v0.4.19'; // Version de la aplicación
}
function verDBLocal()
{
    return 20230404; // Version de la base de datos local
}
function checkDBLocal()
{
    $v = simple_pdoQuery("SELECT valores as 'a' FROM params WHERE modulo = 0 AND cliente = 0 LIMIT 1");
    if ($v['a'] != verDBLocal()) {
        session_destroy();
        header("location:/" . HOMEHOST . "/login/"); // Redirecciona a login si la version de la base de datos es distinta a la version del servidor
    }
}
function E_ALL()
{
    if ($_SERVER['SERVER_NAME'] == 'localhost') { // Si es localhost
        error_reporting(E_ALL); // Muestra todos los errores
        ini_set('display_errors', '1'); // Muestra todos los errores
    } else {
        error_reporting(E_ALL);
        ini_set('display_errors', '0');
    }
}
// Funcion para validar si esta autenticado
function secure_auth_ch()
{
    timeZone();
    timeZone_lang();
    $_SESSION["secure_auth_ch"] = $_SESSION["secure_auth_ch"] ?? ''; // Si no existe la variable la crea
    $_SESSION['VER_DB_LOCAL'] = $_SESSION['VER_DB_LOCAL'] ?? ''; // Si no existe la variable la crea
    if (
        $_SESSION["secure_auth_ch"] !== true // Si no esta autenticado
        || (empty($_SESSION['UID']) || is_int($_SESSION['UID'])) // Si no existe el UID
        // || ($_SESSION['IP_CLIENTE'] !== $_SERVER['REMOTE_ADDR']) // Si la IP no es la misma
        // || ($_SESSION['USER_AGENT'] !== $_SERVER['HTTP_USER_AGENT']) // Si el USER_AGENT no es el mismo
        || (!$_SESSION['VER_DB_LOCAL']) // Si no existe la variable de la version de la base de datos local
        // || ($_SESSION['DIA_ACTUAL'] !== hoy()) // Si eliminar dia actual no es el mismo
    ) {
        // echo '<script>window.location.href="/' . HOMEHOST . '/login/"</script>';
        // PrintRespuestaJson('error', 'Sesión Expirada');
        if (isset($_SERVER['HTTP_REFERER'])) {
            header("location:/" . HOMEHOST . "/login/?l=" . urlencode($_SERVER['HTTP_REFERER'])); // Redirecciona a login
        } else {
            header("location:/" . HOMEHOST . "/login/"); // Redirecciona a login
        }
        exit;
    } else {
        // chequeamos si el usuario y la password son iguales. si se cumple la condición, lo redirigimos a cambiar la clave
        (password_verify($_SESSION["user"], $_SESSION["HASH_CLAVE"])) ? header('Location:/' . HOMEHOST . '/usuarios/perfil/') : '';
        $fechaGuardada = $_SESSION["ultimoAcceso"]; // Fecha de ultimo acceso
        $ahora = date("Y-m-d H:i:s"); // Fecha actual
        $tiempo_transcurrido = (strtotime($ahora) - strtotime($fechaGuardada)); // Tiempo transcurrido
        /** comparamos el tiempo transcurrido */
        if ($tiempo_transcurrido >= $_SESSION["LIMIT_SESION"]) { // Si el tiempo transcurrido es mayor a la variable LIMIT_SESION
            /** Si pasaron 60 minutos o más */
            session_destroy(); // Destruye la sesión
            /** destruyo la sesión */
            header("location:/" . HOMEHOST . "/login/?sesion&l=" . urlencode($_SERVER['HTTP_REFERER'])); // Redirecciona a login
            /** envío al usuario a la pag. de autenticación */
            exit(); // Fin del script
            /** sino, actualizo la fecha de la sesión */
        } else {
            $_SESSION["ultimoAcceso"] = $ahora; // Actualizo la fecha de la sesión
        }
        checkDBLocal();
    }
    session_regenerate_id(); // Regenera la sesión
    E_ALL(); // Funciones de error
}
function secure_auth_ch_json()
{
    timeZone();
    timeZone_lang();
    $_SESSION["secure_auth_ch"] = $_SESSION["secure_auth_ch"] ?? '';
    if (
        $_SESSION["secure_auth_ch"] !== true
        || (empty($_SESSION['UID']) || is_int($_SESSION['UID']))
        // || ($_SESSION['IP_CLIENTE'] !== $_SERVER['REMOTE_ADDR'])
        // || ($_SESSION['USER_AGENT'] !== $_SERVER['HTTP_USER_AGENT'])
        // || ($_SESSION['DIA_ACTUAL'] !== hoy())
    ) {
        $_SERVER['HTTP_REFERER'] = $_SERVER['HTTP_REFERER'] ?? '';
        $f = 'Sesi&oacute;n Expirada. Incie sesi&oacute;n nuevamente<br><a class="btn btn-sm fontq btn-info mt-2" href="/' . HOMEHOST . '/login/?l=' . urlencode($_SERVER['HTTP_REFERER']) . '">Iniciar sesi&oacute;n </a>';
        PrintRespuestaJson('sesion', $f);
        exit;
    } else {
        /** chequeamos si el usuario y la password son iguales. si se cumple la condición, lo redirigimos a cambiar la clave */
        (password_verify($_SESSION["user"], $_SESSION["HASH_CLAVE"])) ? header('Location:/' . HOMEHOST . '/usuarios/perfil/') : '';
        /** */
        $fechaGuardada = $_SESSION["ultimoAcceso"];
        $ahora = date("Y-m-d H:i:s");
        $tiempo_transcurrido = (strtotime($ahora) - strtotime($fechaGuardada));
        /** comparamos el tiempo transcurrido */
        if ($tiempo_transcurrido >= $_SESSION["LIMIT_SESION"]) {
            /** Si pasaron 60 minutos o más */
            session_destroy();
            /** destruyo la sesión */
            header("location:/" . HOMEHOST . "/login/?sesion&l=" . urlencode($_SERVER['HTTP_REFERER']));
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
function secure_auth_ch2()
{
    timeZone();
    timeZone_lang();
    if (
        $_SESSION["secure_auth_ch"] !== true
        || (empty($_SESSION['UID']) || is_int($_SESSION['UID']))
        // || ($_SESSION['IP_CLIENTE'] !== $_SERVER['REMOTE_ADDR'])
        // || ($_SESSION['USER_AGENT'] !== $_SERVER['HTTP_USER_AGENT'])
        // || ($_SESSION['DIA_ACTUAL'] !== hoy())
    ) {
        // PrintRespuestaJson('error', 'Session Expirada');
        echo '<div class="p-3 fw5 text-danger">Sesión Expirada</div>';
        exit;
    } else {
        /** chequeamos si el usuario y la password son iguales. si se cumple la condición, lo redirigimos a cambiar la clave */
        (password_verify($_SESSION["user"], $_SESSION["HASH_CLAVE"])) ? header('Location:/' . HOMEHOST . '/usuarios/perfil/') : '';
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
        checkDBLocal();
    }
    session_regenerate_id();
    E_ALL();
}
/** ultimaacc */
// Funcion para obtener la fecha hora del ultimo acceso
function ultimoacc()
{
    return $_SESSION["ultimoAcceso"] = date("Y-m-d H:i:s"); // Actualizo la fecha de la sesión
}
/** Seguridad injections SQL */
/**
 * @param $key string
 * @return string
 */
function secureVar($key)
{
    $key = htmlspecialchars(stripslashes($key)); // Limpio la variable
    $key = str_ireplace("script", "blocked", $key); // Remplazo el script por una palabra bloqueada
    $key = htmlentities($key, ENT_QUOTES); // Codifico la variable
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
function version_file($pathFile)
{
    if ($pathFile) {
        return filesize(dirname(__FILE__) . $pathFile) . '.' . filemtime(dirname(__FILE__) . $pathFile);
    }
    return time();
}
function API_KEY_MAPS()
{
    return 'AIzaSyCFs9lj9k7WZAyuwzDJwOiSiragUA9Xwg0';
}
$params = array();
if (!defined('SQLSRV_CURSOR_KEYSET')) {
    define('SQLSRV_CURSOR_KEYSET', 2); // El valor correcto puede variar según tu aplicación
}
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET, 'keyset');
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
    $cadena = "ABCDEFGHIJKLMNOPQRSTUVWXYZ"; //abcdefghijklmnopqrstuvwxyz
    $longitudCadena = strlen($cadena);
    $Ident = ""; // Variable para almacenar la cadena generada
    $longitudIdent = 3; // Longitud de la cadena a generar
    for ($i = 1; $i <= $longitudIdent; $i++) { // Ciclo para generar la cadena
        $pos = rand(0, $longitudCadena - 1);
        $Ident .= substr($cadena, $pos, 1);
    }
    return $Ident; // Retorno de la cadena generada
}
function statusData($status, $dato)
{
    $data = array('status' => $status, 'Mensaje' => $dato);
    echo json_encode($data);
    exit;
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
function token()
{
    return sha1('ie&%$sg@dQdtW@!""#');
}
function encabezado_mod($bgc, $colortexto, $img, $titulo, $imgclass)
{
    $QueryString = empty($_SERVER['QUERY_STRING']) ? '' : '?' . $_SERVER['QUERY_STRING'];
    $VER_DB_CH = $_SESSION['VER_DB_CH'] ?? '';
    $VER_DB_LOCAL = $_SESSION['VER_DB_LOCAL'] ?? '';

    echo '
    <div class="row d-print-none text-' . $colortexto . ' ' . $bgc . ' radius-0">
    <div class="col-12 d-inline-flex h6 fw3 py-2 m-0">
        <div class="d-flex align-items-center w-100">
            <div>
                <a href="' . $_SERVER['PHP_SELF'] . $QueryString . '">
                <img src="/' . HOMEHOST . '/img/' . $img . '?v=' . vjs() . '" alt="' . $titulo . '"class="mr-2 img-fluid ' . $imgclass . ' bg-light radius w30">
                </a>
            </div>
            <div class="w-100 d-inline-flex h30">
                <div class="d-flex justify-content-strat align-items-center text-nowrap ml-1 fonth" id="Encabezado">' . $titulo . '</div>
                <div class="fontpp d-flex justify-content-end align-items-top w-100" style="color:#efefef" title="Version DB CH: ' . $VER_DB_CH . ' - Version DB Local: ' . $VER_DB_LOCAL . '">' . version() . '</div>
            </div>
        </div>
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
        // $version = '<span class="float-right fontpp" style="color:#efefef;margin-top:-10px; padding-right:10px" title="Version DB CH: ' . $_SESSION['VER_DB_CH'] . '">' . version() . '</span>';
        $version = '<span class="float-right fontpp" style="color:#efefef;margin-top:-10px; padding-right:10px" title="Version DB CH: ' . $_SESSION['VER_DB_CH'] . ' - Version DB Local: ' . $_SESSION['VER_DB_LOCAL'] . '"">' . version() . '</span>';
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
            <div class="h6 fw4 py-2 m-0 d-inline-flex">
                <a href="' . $_SERVER['PHP_SELF'] . $QueryString . '">
                    ' . $svg . '
                </a>
                <div class="text-nowrap d-flex align-items-center fonth" id="Encabezado">' . $titulo . '</div>
            </div>
        </div>
        <div class="col-4 d-flex align-items-center justify-content-end pr-1">
            ' . $userLogout . $version . '
        </div>
    </div>';
}
function encabezado_mod3($bgc, $colortexto, $svg, $titulo, $style, $class)
{

    $countModRol = (count($_SESSION['MODS_ROL']));
    $userLogout = $version = $icon_clock_history = $icon_person_circle = '';

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
    $icon_box_arrow_right = '';

    if ($countModRol == '1') {
        $userLogout = '
<span class="p-2 float-right" >' . $icono . '
<span data-toggle="tooltip" data-placement="top" data-html="true" data-original-title="<b>SALIR</b>" aria-describedby="tooltip" >
<a href="/' . HOMEHOST . '/logout.php" title="Salir" class="btn btn-sm btn-custom">' . $icon_box_arrow_right . '</a></span>
</span>';
    }
    if ($countModRol != '1') {
        $version = '<span class="float-right fontpp" style="color:#efefef;margin-top:-10px; padding-right:10px" title="Version DB CH: ' . $_SESSION['VER_DB_CH'] . ' - Version DB Local: ' . $_SESSION['VER_DB_LOCAL'] . '"">' . version() . '</span>';
    }
    $QueryString = empty($_SERVER['QUERY_STRING']) ? '' : '?' . $_SERVER['QUERY_STRING'];
    if ($_SERVER['SCRIPT_NAME'] == '/' . HOMEHOST . '/mishoras/index.php') {
    } elseif ($_SERVER['SCRIPT_NAME'] == '/' . HOMEHOST . '/usuarios/perfil/index.php') {
    } else {
        $svg = '<img class="img-fluid ' . $class . '" style="' . $style . '" src="' . $svg . '"></img>';
    }
    echo '
    <div class="row text-' . $colortexto . ' ' . $bgc . ' radius-0">
        <div class="col-8 d-flex align-items-center">
            <div class="h6 fw4 py-2 m-0 d-inline-flex">
                <a href="' . $_SERVER['PHP_SELF'] . $QueryString . '">
                    ' . $svg . '
                </a>
                <div class="text-nowrap d-flex align-items-center" style="font-size: 0.9rem;margin-top: 3px;" id="Encabezado">' . $titulo . '</div>
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
function FormatHora($var)
{
    // if($var <= -60){ /** Si el valor es menor o igual a menos -60 */
    $segundos = $var * 60;
    $horas = intval($segundos / 3600);
    $minutos = floor(($segundos - ($horas * 3600)) / 60);
    $segundos = $segundos - ($horas * 3600) - ($minutos * 60);
    $horas = str_pad($horas, 2, "0", STR_PAD_LEFT);
    // $minutos  = ($minutos < 0) ? str_replace("-", "", $minutos) : $minutos;
    $minutos = str_pad($minutos, 2, "0", STR_PAD_LEFT);
    $hora = $horas . ':' . $minutos;
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
    $horas = intval($segundos / 3600);
    $minutos = floor(($segundos - ($horas * 3600)) / 60);
    $segundos = $segundos - ($horas * 3600) - ($minutos * 60);
    $horas = str_pad($horas, 2, "0", STR_PAD_LEFT);
    $minutos = ($minutos < 0) ? str_replace("-", "", $minutos) : $minutos;
    $minutos = str_pad($minutos, 2, "0", STR_PAD_LEFT);
    $hora = $horas . ':' . $minutos;
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
    $min = ($min / 24);
    return $min;
}
function test_input($data)
{
    if (!$data)
        return '';
    $data = $data ?? '';
    $data = trim($data);
    // $data = stripslashes($data);
    // $data = htmlspecialchars($data);
    $data = utf8str($data);
    $data = htmlspecialchars(stripslashes($data), ENT_QUOTES);
    $data = str_ireplace("script", '', $data);
    $data = htmlentities($data, ENT_QUOTES);
    return ($data);
}
function hoy()
{
    timeZone();
    $hoy = date('Y-m-d');
    return rtrim($hoy);
}
function FechaString($var)
{
    $date = date_create($var);
    $FormatFecha = date_format($date, "Ymd");
    return $FormatFecha;
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
    timeZone();
    setlocale(LC_TIME, "spanish");
    $scheduled_day = $Ymd;
    $days = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado'];
    $day = date('w', strtotime($scheduled_day));
    $scheduled_day = "$days[$day] " . date('d/m/Y', strtotime($scheduled_day));
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
function DiaSemana4($Ymd)
{
    timeZone();
    setlocale(LC_TIME, "spanish");
    $scheduled_day = $Ymd;
    $days = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado'];
    $day = date('w', strtotime($scheduled_day));
    $scheduled_day = "$days[$day]" . '&nbsp;' . date('d/m/Y', strtotime($scheduled_day));
    return $scheduled_day;
}
function DiaSemana3($Ymd)
{
    timeZone();
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
    $var = date_format($dato, 'Y-m-d');
    return $var;
}
function FechaFormatH($FechaHora)
{
    if (!$FechaHora) {
        return false;
    }
    $dato = date_create($FechaHora);
    $var = date_format($dato, "d/m/Y H:i");
    return $var;
}
function HoraFormat($FechaHora, $second = true)
{
    if ($second) {
        $dato = date_create($FechaHora);
        $var = date_format($dato, "H:i:s");
    } else {
        $dato = date_create($FechaHora);
        $var = date_format($dato, "H:i");
    }
    return $var;
}
function FechaFormatVar($FechaHora, $var)
{
    if ($FechaHora != '0000-00-00 00:00:00') {
        $dato = date_create($FechaHora);
        $var = date_format($dato, $var);
        return $var;
    } else {
        return '';
    }
}
function Fecha_String($var)
{
    $dato = date_create($var);
    $var = date_format($dato, 'Ymd');
    return $var;
}
function inicio()
{
    $sql = "SELECT usuarios.id FROM usuarios";
    $rs = array_pdoQuery($sql);
    $numrows = count($rs);
    return ($numrows);
}
function principal($rol)
{
    if ($rol) {
        $sql = "SELECT usuarios.id FROM usuarios JOIN roles ON usuarios.rol=roles.id WHERE usuarios.principal='1' AND roles.recid='$rol' LIMIT 1";
        $rs = simple_pdoQuery($sql);
        return ($rs) ? true : false;
    }
}
function token_exist($recid_cliente)
{
    if (!$recid_cliente)
        return false;

    $sql = "SELECT clientes.id FROM clientes
    WHERE clientes.tkmobile !='' AND clientes.recid = '$recid_cliente'";
    $rs = simple_pdoQuery($sql);
    return ($rs) ? true : false;
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
    $query = "SELECT clientes.recid as recid, clientes.nombre as nombre FROM clientes WHERE clientes.id >'0' AND clientes.recid='$recid' LIMIT 1";
    $result = simple_pdoQuery($query);
    if ($result) {
        $nombre = $result['nombre'];
        return $nombre;
    } else {
        header("Location: /" . HOMEHOST . "/usuarios/clientes/");
    }
}
function ExisteRol3($recid, $id)
{
    /** Verificamos el recid de rol para ver si existe. 
     * Sino existe redirigimos a Clientes*/
    $q = "SELECT roles.nombre, roles.id, roles.recid FROM roles WHERE roles.recid='$recid' AND roles.id = '$id' LIMIT 1";
    $result = simple_pdoQuery($q);
    if ($result) {
        return array('nombre' => $result['nombre'], 'id' => $result['id'], 'recid' => $result['recid']);
    } else {
        header("Location: /" . HOMEHOST . "/usuarios/clientes/");
    }
}
function ExisteRol4($recid, $id)
{
    /** Verificamos si existe el rol */
    $q = "SELECT 1 FROM roles WHERE roles.recid='$recid' AND roles.id = '$id' LIMIT 1";
    $result = simple_pdoQuery($q);
    if ($result) {
        return true;
    } else {
        return false;
    }
}
function ExisteUser($cliente_recid, $uid)
{
    /** Verificamos si existe el usuario */
    $q = "SELECT 1 FROM usuarios u INNER JOIN clientes c ON u.cliente=c.id WHERE u.id='$uid' AND c.recid='$cliente_recid' LIMIT 1";
    $result = simple_pdoQuery($q);
    if ($result) {
        return true;
    } else {
        return false;
    }
}
function ExisteRol2($recid)
{
    /** Verificamos el recid de cliente para ver si existe. 
     * Sino existe redirigimos a Clientes*/
    $q = "SELECT roles.recid as 'recid', roles.nombre as 'nombre', roles.id as 'id_rol' FROM roles WHERE roles.recid='$recid' LIMIT 1";
    $r = simple_pdoQuery($q);
    if ($r) {
        return array($r['nombre'], $r['id_rol'], $r['recid']);
    } else {
        header("Location: /" . HOMEHOST . "/usuarios/clientes/");
    }
}
function ExisteRol($recid)
{
    /** Verificamos el recid de cliente para ver si existe. 
     * Sino existe redirigimos a Clientes*/
    $url = host() . "/" . HOMEHOST . "/data/GetRoles.php?tk=" . token() . "&recid=" . $recid;
    // $json  = file_get_contents($url);
    // $array = json_decode($json, TRUE);
    $array = json_decode(getRemoteFile($url), true);
    $count = (count($array[0]['roles']));
    ($count) ? '' : header("Location: /" . HOMEHOST . "/usuarios/clientes/");
    /** redirect */
    /** 
     *  
     */
}
function Cliente_c($recid)
{
    $url = host() . "/" . HOMEHOST . "/data/GetClientes.php?tk=" . token() . "&recid=" . $recid;
    $array = json_decode(getRemoteFile($url), true);
    $data = $array[0]['clientes'];
    if (is_array($data)):
        // $r = array_filter($data, function ($e) {
        //     return $e['recid'] == $_GET['_c'];
        // });
        foreach ($data as $value):
            $id_c = $value['id'];
            $ident = $value['ident'];
            $recid_c = $value['recid'];
            $nombre_c = $value['nombre'];
            $host_c = $value['host'];
            return array($id_c, $ident, $nombre_c, $recid_c, $host_c);
        endforeach;
    endif;
}
function Rol_Recid($recid)
{
    $url = host() . "/" . HOMEHOST . "/data/GetRoles.php?tk=" . token() . "&recid=" . $recid;
    //$json  = file_get_contents($url);
    // $array = getRemoteFile($url);
    $array = json_decode(getRemoteFile($url), true);
    $data = $array[0]['roles'];
    if (is_array($data)):
        // $r = array_filter($data, function ($e) {
        //     return $e['recid'] == $_GET['_c'];
        // });
        foreach ($data as $value):
            $id_Rol = $value['id'];
            $nombreRol = $value['nombre'];
            $clienteRol = $value['cliente'];
            $UsuariosRol = $value['cant_roles'];
            $recid_clienteRol = $value['recid_cliente'];
            $idClienteRol = $value['id_cliente'];
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
     * 
     * verificamos si existe el modulo asociado a la session del rol de usuario. 
     * sino existe lo enviamos al incio.
     */
    $_SESSION['ID_MODULO'] = $modulo ?? '';
    define('ID_MODULO', $modulo);
    if (intval($modulo) > 0) {
        $r = array_filter($_SESSION["MODS_ROL"], function ($e) {
            return $e['modsrol'] === ID_MODULO;
        });
        $modulo_actual = (filtrarObjeto($_SESSION['MODULOS'], 'id', $modulo))['modulo']; // Nombre del modulo actual
        access_log($modulo_actual);
        if (!$r) {
            header("Location:/" . HOMEHOST . "/");
            exit;
        }
    }
    /** redirect */
}
// verifica si existe conexion a MSSQL
function existConnMSSQL()
{
    require_once __DIR__ . '/config/conect_mssql.php'; // conexion a MSSQL
    (!$_SESSION['CONECT_MSSQL']) ? header("Location:/" . HOMEHOST . "/inicio/?e=errorConexionMSSQL") . exit : ''; // si no existe conexion a MSSQL redirigimos al inicio
}
function ListaRoles($Recid_C)
{
    $url = host() . "/" . HOMEHOST . "/data/GetRoles.php?tk=" . token() . "&recid_c=" . $Recid_C;
    // $json         = file_get_contents($url);
    // $array        = json_decode($json, TRUE);
    $array = json_decode(getRemoteFile($url), true);
    $data = $array[0]['roles'];
    if (is_array($array)):
        foreach ($data as $value):
            $nombre = $value['nombre'];
            $id = $value['id'];
            echo '<option value="' . $id . '">' . $nombre . '</option>';
        endforeach;
    endif;
}
function estructura_rol($get_rol, $recid_rol, $e, $data)
{
    $url = host() . "/" . HOMEHOST . "/data/$get_rol.php?tk=" . token() . "&_r=" . $recid_rol . "&e=" . $e;
    // echo $url; br();
    // $json  = file_get_contents($url);
    // $array = json_decode($json, TRUE);
    // print_r($url);exit;
    $array = json_decode(getRemoteFile($url), true);
    $data = $array[0][$data];
    if (is_array($data)) {
        $val_roles = (!$array[0]['error']) ? implode(",", $data) : '';
        $rol = (!$array[0]['error']) ? "$val_roles" : "";
        return $rol;
    }
}
function estructura_rol_count($get_rol, $recid_rol, $e, $data)
{
    $url = host() . "/" . HOMEHOST . "/data/$get_rol.php?tk=" . token() . "&_r=" . $recid_rol . "&e=" . $e;
    // $json  = file_get_contents($url);
    // $array = json_decode($json, TRUE);
    // print_r($url);exit;
    $array = json_decode(getRemoteFile($url), true);
    $val_roles = (!$array[0]['error']) ? count($array[0][$data]) : '';
    $rol = (!$array[0]['error']) ? "$val_roles" : "";
    return $rol;
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
function imgIcon($var, $title, $width)
{
    $src = "/" . HOMEHOST . "/img/" . $var . ".png?v=" . vjs();
    return '<img loading="lazy" src="' . $src . '" class="' . $width . '" "alt="' . $title . '" title="' . $title . '">';
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
                    'Fic' => '<span class="text-primary fw4 contentd" data-titler="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '">' . $valor['Fic'] . '</span>',
                    'Estado' => $valor['Estado'],
                    'Tipo' => $valor['Tipo']
                );
                break;
            default:
                $fichada = array(
                    'Fic' => '<span class="fw4 contentd" data-titler="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '">' . $valor['Fic'] . '</span>',
                    'Estado' => $valor['Estado'],
                    'Tipo' => $valor['Tipo']
                );
                break;
        }
        switch ($valor['Estado']) {
            case 'Modificada':
                $fichada = array(
                    'Fic' => '<span class="text-danger fw4 contentd" data-titler="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '" >' . $valor['Fic'] . '</span>',
                    'Estado' => $valor['Estado'],
                    'Tipo' => $valor['Tipo']
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
                    'Fic' => '<span class="text-primary fw4 contentd" data-titler="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '. Fecha de registro: ' . $valor['RegFeRe'] . '">' . $valor['Fic'] . '</span>',
                    'Estado' => $valor['Estado'],
                    'Tipo' => $valor['Tipo']
                );
                break;
            default:
                $fichada = array(
                    'Fic' => '<span class="fw4 contentd" data-titler="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '. Fecha de registro: ' . $valor['RegFeRe'] . '">' . $valor['Fic'] . '</span>',
                    'Estado' => $valor['Estado'],
                    'Tipo' => $valor['Tipo']
                );
                break;
        }
        switch ($valor['Estado']) {
            case 'Modificada':
                $fichada = array(
                    'Fic' => '<span class="text-danger fw4 contentd" title="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '. Fecha de registro: ' . $valor['RegFeRe'] . '">' . $valor['Fic'] . '</span>',
                    'Estado' => $valor['Estado'],
                    'Tipo' => $valor['Tipo']
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
            $fichada = '<span class="text-primary" data-titler="Fichada: ' . $tipo . '. Estado: ' . $estado . '">' . $hora . '</span>';
            break;
        default:
            $fichada = '<span class="text-dark fw5" data-titler="Fichada: ' . $tipo . '. Estado: ' . $estado . '">' . $hora . '</span>';
            break;
    }
    switch ($estado) {
        case 'Modificada':
            $fichada = '<span class="text-danger fw5" data-titler="Fichada: ' . $tipo . '. Estado: ' . $estado . '" >' . $hora . '</span>';
            break;
        default:
            $fichada = '<span class="text-dark fw5" data-titler="Fichada: ' . $tipo . '. Estado: ' . $estado . '">' . $hora . '</span>';
            break;
    }
    if ($tipo == 'Manual' && $estado == 'Normal') {
        $fichada = '<span class="text-primary fw5" data-titler="Fichada: ' . $tipo . '. Estado: ' . $estado . '">' . $hora . '</span>';
    }

    return $fichada;
}
/** Actual month last day **/
function _data_last_month_day($y, $m)
{
    $month = $m;
    $year = $y;
    $day = date("d", mktime(0, 0, 0, $month + 1, 0, $year));

    return date('Ymd', mktime(0, 0, 0, $month, $day, $year));
}
;
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
    $rs = sqlsrv_query($link, $query);
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
    // exit;
}
function audito_ch($AudTipo, $AudDato, $modulo = '')
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
    // $usuario    = explode("-", $_SESSION["user"]);

    // $AudUser   = substr($_SESSION["user"], 4, 10);
    // if (isset($usuario[1])) {
    //     $AudUser   = substr(ucfirst($usuario[1]), 0, 10);
    // } else {
    //     $AudUser   = $_SESSION["user"];
    // }
    $AudUser = substr($_SESSION["NOMBRE_SESION"], 0, 10);
    $ipCliente = substr($ipCliente, 0, 20);
    // $AudTerm   = gethostname();
    $AudTerm = $ipCliente;
    $AudModu = 21;
    $FechaHora = fechaHora();
    $AudFech = fechaHora();
    $AudHora = date('H:i:s');

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
        auditoria($AudDato, $AudTipo, '', $modulo);
    } else {
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $mensaje = explode(']', $error['message']);
                $dataAud[] = array("auditor" => "error", "Mensaje" => $mensaje[3]);
            }
        }
        // echo json_encode($procedure_params);
        // exit;
    }
    sqlsrv_execute($stmt);
    sqlsrv_close($link);
}
function audito_ch2($AudTipo, $AudDato, $modulo = '')
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
    // $usuario    = explode("-", $_SESSION["user"]);

    $AudUser = substr($_SESSION["NOMBRE_SESION"], 0, 10);
    // $AudUser   = substr(ucfirst($usuario[1]), 0, 10);
    // $AudTerm   = gethostname();
    // $AudTerm   = $ipCliente . '-' . recid2(4);
    $ipCliente = substr($ipCliente, 0, 20);
    $AudTerm = $ipCliente;
    $AudModu = 21;
    // $FechaHora = date('Ymd H:i:s.u');
    $AudFech = date('Ymd');
    $AudHora = date('H:i:s');
    // $now = DateTime::createFromFormat('U.u', number_format(microtime(true), 6, '.', ''));
    // $local = $now->setTimeZone(new DateTimeZone('America/Argentina/Buenos_Aires'));
    // $FechaHora = $local->format("Y-m-d H:i:s.u");
    $FechaHora = fechaHora();

    $procedure_params = array(
        array(&$FechaHora),
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
                $pathLog = __DIR__ . '/logs/error/' . date('Ymd') . '_errorQueryMS.log';
                fileLog($_SERVER['REQUEST_URI'] . "\n" . sqlsrv_errors($link), $pathLog); // escribir en el log
                return false;
            }
        }
    }

    if (sqlsrv_execute($stmt)) {
        /** ejecuto la sentencia */
        $dataAud = array("auditor" => "ok");
        // echo json_encode($dataAud); 
        auditoria($AudDato, $AudTipo, '', $modulo);
    } else {

        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $mensaje = explode(']', $error['message']);
                $dataAud[] = array("auditor" => "error", "dato" => 'Error Auditor');
                $pathLog = __DIR__ . './logs/error/' . date('Ymd') . '_errorQueryMS.log';
                fileLog($_SERVER['REQUEST_URI'] . "\n" . $mensaje[3], $pathLog); // escribir en el log
                return false;
            }
        }

        echo json_encode($dataAud);
    }
    sqlsrv_execute($stmt);
    sqlsrv_close($link);
}
/**
 * @param {$dato} string texto de auditoria
 * @param {$tipo} string a:insert, b:update, m:delete; p: proceso
 * @param {$audcuenta} integer id de cuenta
 * @param {$modulo} modulo de la aplicacion
 */
function auditoria($dato, $tipo, $audcuenta = '', $modulo = '')
{
    timeZone();
    require __DIR__ . '/config/conect_pdo.php'; //Conexion a la base de datos
    $connpdo->beginTransaction();
    try {
        $sql = 'INSERT INTO auditoria( id_sesion, usuario, nombre, cuenta, audcuenta, fecha, hora, tipo, dato, modulo ) VALUES( :id_sesion, :usuario, :nombre, :cuenta, :audcuenta, :fecha, :hora, :tipo, :dato, :modulo )';
        $stmt = $connpdo->prepare($sql); // prepara la consulta
        $data = [
            'id_sesion' => $_SESSION['ID_SESION'],
            // $_SESSION['ID_SESION'],
            'usuario' => ($_SESSION["user"]) ? $_SESSION["user"] : 'Sin usuario',
            'nombre' => ($_SESSION["NOMBRE_SESION"]) ? $_SESSION["NOMBRE_SESION"] : 'Sin nombre',
            'cuenta' => ($_SESSION["ID_CLIENTE"]) ? $_SESSION["ID_CLIENTE"] : '',
            'audcuenta' => ($audcuenta) ? $audcuenta : $_SESSION["ID_CLIENTE"],
            'fecha' => date("Y-m-d "),
            'hora' => date("H:i:s"),
            'tipo' => ($tipo) ? $tipo : 'Null',
            // a:insert, b:update, m:delete; p: proceso
            'dato' => ($dato) ? trim($dato) : 'No se especificaron datos',
            'modulo' => ($modulo) ? $modulo : ''
        ];
        $stmt->bindParam(':id_sesion', $data['id_sesion']);
        $stmt->bindParam(':usuario', $data['usuario']);
        $stmt->bindParam(':nombre', $data['nombre']);
        $stmt->bindParam(':cuenta', $data['cuenta']);
        $stmt->bindParam(':audcuenta', $data['audcuenta']);
        $stmt->bindParam(':fecha', $data['fecha']);
        $stmt->bindParam(':hora', $data['hora']);
        $stmt->bindParam(':tipo', $data['tipo']);
        $stmt->bindParam(':dato', $data['dato']);
        $stmt->bindParam(':modulo', $data['modulo']);
        $stmt->execute();
        $connpdo->commit(); // si todo salio bien, confirma la transaccion
    } catch (\Throwable $th) { // si hay error
        $message = "Error -> auditoria. Usuario : \"$data[usuario]\" Dato: \"$data[dato]\"  Tipo: \"$data[tipo]\"  Fecha: \"$data[fecha]\"  Hora: \"$data[hora]\" Cuenta (\"$data[audcuenta]\")"; // mensaje de exito
        $connpdo->rollBack(); // revierte la transaccion
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorAudito.log'; // ruta del archivo de Log
        fileLog($th->getMessage() . "\n $message", $pathLog); // escribir en el log de errores
    }
    $connpdo = null; // cierra la conexion
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
    sqlsrv_free_stmt($rs);
    sqlsrv_close($link);
    return $array;
    // exit;
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
    sqlsrv_free_stmt($rs);
    sqlsrv_close($link);
    return $array;
}
function nov_cta_cte()
{
    require __DIR__ . '/config/conect_mssql.php';
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $query = "SELECT DISTINCT CTANOVE.CTA2Nove, NOVEDAD.NovDesc, CTANOVE.CTA2Peri FROM CTANOVE JOIN NOVEDAD ON CTANOVE.CTA2Nove = NOVEDAD.NovCodi ORDER BY CTANOVE.CTA2Peri DESC";
    $rs = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($rs) > 0) {
        while ($fila = sqlsrv_fetch_array($rs)) {
            $CTA2Nove = $fila['CTA2Nove'];
            $NovDesc = $fila['NovDesc'];
            $CTA2Peri = $fila['CTA2Peri'];
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
function ValString($val)
{
    $val = is_string($val) ? true : false;
    return $val;
}
function DeleteRegistro($query)
{
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    require __DIR__ . '/config/conect_mssql.php';
    $stmt = sqlsrv_query($link, $query, $params, $options);
    if (($stmt)) {
        // print_r($query);
        // echo json_encode($query);
        // exit;
        sqlsrv_close($link);
        return true;
    } else {
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $mensaje = explode(']', $error['message']);
                $data[] = array("status" => "error", "dato" => $mensaje[3]);
            }
        }
        echo json_encode($data[0]);
        sqlsrv_close($link);
        exit;
    }
}
/** Query MYSQL */
function CountRegMayorCeroMySql($query)
{
    $stmt = simple_pdoQuery($query);
    if ($stmt) {
        return true;
    } else {
        return false;
    }
}
function checkKey($fk, $table)
{
    $db = $_ENV['DB_CHWEB_NAME'] ?? '';
    $check_schema = "SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = '$table' AND CONSTRAINT_NAME = '$fk' AND TABLE_SCHEMA = '$db'";
    $stmt = simple_pdoQuery($check_schema);
    if ($stmt) {
        return true;
    } else {
        return false;
    }
}
function checkColumn($table, $col)
{
    $db = $_ENV['DB_CHWEB_NAME'] ?? '';
    $check_schema = "SELECT information_schema.COLUMNS.COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='$db' AND TABLE_NAME='$table' AND COLUMN_NAME='$col'";
    $stmt = simple_pdoQuery($check_schema);
    if ($stmt) {
        return true;
    } else {
        return false;
    }
}
function checkTable($table)
{
    $db = $_ENV['DB_CHWEB_NAME'] ?? '';

    $check_schema = "SELECT distinct(TABLE_NAME) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='$db' AND TABLE_NAME='$table'";
    $stmt = simple_pdoQuery($check_schema);
    if ($stmt) {
        return true;
    } else {
        return false;
    }
}
function InsertRegistroMySql($query)
{
    $stmt = pdoQuery($query);
    if ($stmt) {
        return true;
    } else {
        return false;
    }
}
function simpleQueryDataMS($query)
{
    require __DIR__ . './config/conect_mssql.php';
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $stmt = sqlsrv_query($link, $query, $params, $options);
    if ($stmt) {
        $a = sqlsrv_fetch_array($stmt);
        sqlsrv_free_stmt($stmt);
        return $a;
    } else {
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $mensaje = explode(']', $error['message']);
            }
        }

        $pathLog = __DIR__ . './logs/error/' . date('Ymd') . '_errorQuery.log';
        fileLog($_SERVER['REQUEST_URI'] . "\n" . $mensaje, $pathLog); // escribir en el log
        return false;
    }
}
function arrayQueryDataMS($query)
{
    require __DIR__ . './config/conect_mssql.php';
    $data = array();
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $stmt = sqlsrv_query($link, $query, $params, $options);
    if ($stmt) {
        while ($a = sqlsrv_fetch_array($stmt)) {
            $data[] = $a;
        }
        sqlsrv_free_stmt($stmt);
        return $data;
    } else {
        $pathLog = __DIR__ . './logs/error/' . date('Ymd') . '_errorQuery.log';
        fileLog($_SERVER['REQUEST_URI'] . "\n", $pathLog); // escribir en el log
        return false;
    }
}
function UpdateRegistroMySql($query)
{
    $stmt = pdoQuery($query);
    if (($stmt)) {
        return true;
    } else {
        statusData('error', 'Error');
        return false;
    }
}
function deleteRegistroMySql($query)
{
    $stmt = pdoQuery($query);
    if (($stmt)) {
        return true;
    } else {
        statusData('error', 'Error');
        return false;
    }
}
function dataLista2($lista, $rol)
{
    require __DIR__ . '/config/conect_mysql.php';
    $stmt = mysqli_query($link, "SELECT datos FROM lista_roles where id_rol = '$rol' AND lista = '$lista'");
    // print_r($query); exit;
    if (($stmt)) {
        if (mysqli_num_rows($stmt) > 0) {
            while ($row = mysqli_fetch_assoc($stmt)) {
                return array($row['datos']);
            }
        } else {
            return array('-');
        }

        mysqli_free_result($stmt);
        mysqli_close($link);
    } else {
        statusData('error', mysqli_error($link));
        mysqli_close($link);
        exit;
    }
}
function dataLista($lista, $rol)
{

    $q = ("SELECT datos FROM lista_roles where id_rol = '$rol' AND lista = '$lista'");
    $stmt = simple_pdoQuery($q);
    if ($stmt) {
        return array($stmt['datos']);
    } else {
        return array('-');
    }
}
function dataListaEstruct($lista, $uid)
{
    $q = "SELECT lista_estruct.datos FROM lista_estruct where lista_estruct.uid = '$uid' AND lista_estruct.lista = '$lista'";

    $stmt = simple_pdoQuery($q);

    if ($stmt) {
        return array($stmt['datos']);
    } else {
        return array('-');
    }

    // require __DIR__ . '/config/conect_mysql.php';
    // $stmt = mysqli_query($link, "SELECT lista_estruct.datos FROM lista_estruct where lista_estruct.uid = '$uid' AND lista_estruct.lista = '$lista'");

    // if ($stmt) {
    //     if (mysqli_num_rows($stmt) > 0) {
    //         while ($row = mysqli_fetch_assoc($stmt)) {
    //             print_r(array($row['datos'])).exit;
    //             return array($row['datos']);
    //         }
    //         mysqli_free_result($stmt);
    //     } else {
    //         return array('-');
    //     }
    // } else {
    //     $pathLog = __DIR__ . './logs/error/' . date('Ymd') . '_errorQuery.log';
    //     fileLog($_SERVER['REQUEST_URI'] . "\n" . mysqli_error($link), $pathLog); // escribir en el log
    //     return false;
    // }
}
function InsertRegistro($query)
{
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    require __DIR__ . '/config/conect_mssql.php';
    // print_r($query);
    $stmt = sqlsrv_query($link, $query, $params, $options);
    if (($stmt)) {
        sqlsrv_close($link);
        return true;
    } else {
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $mensaje = explode(']', $error['message']);
                $data[] = array("status" => "error", "dato" => $mensaje[3]);
                exit;
            }
        }
        echo json_encode($data[0]);
        sqlsrv_close($link);
        exit;
    }
}
function InsertRegistroMS($query)
{
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    require __DIR__ . '/config/conect_mssql.php';
    // print_r($query);
    $stmt = sqlsrv_query($link, $query, $params, $options);
    if (($stmt)) {
        sqlsrv_close($link);
        return true;
    } else {
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $mensaje = explode(']', $error['message']);
                // print_r($error);
                // $data[] = array("status" => "error", "Mensaje" => $mensaje['SQLSTATE']);
                if ($error['SQLSTATE'] == '23000') {
                    PrintRespuestaJson('error', 'Ya existe en tabla Fichadas');
                    exit;
                } else {
                    PrintRespuestaJson('error', $mensaje);
                    exit;
                }
            }
        }
        echo json_encode($data[0]);
        sqlsrv_close($link);
        exit;
    }
}
function UpdateRegistro($query)
{
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    require __DIR__ . '/config/conect_mssql.php';
    $stmt = sqlsrv_query($link, $query, $params, $options);
    // print_r($query);
    if (($stmt)) {
        sqlsrv_close($link);
        return true;
    } else {
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $mensaje = explode(']', $error['message']);
                PrintRespuestaJson('error', $mensaje[3]);
                // $data[] = array("status" => "error", "dato" => $mensaje[3]);
                // $data[] = array("status" => "error", "dato" => 'Ya existe Novedad.');
                exit;
            }
        }
        echo json_encode($data);
        sqlsrv_close($link);
        exit;
    }
}
function CountRegistrosMayorCero($query)
{
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    require __DIR__ . '/config/conect_mssql.php';
    $stmt = sqlsrv_query($link, $query, $params, $options);
    // print_r($query);
    if (($stmt)) {
        if (sqlsrv_num_rows($stmt) > 0) {
            sqlsrv_free_stmt($stmt);
            sqlsrv_close($link);
            return true;
        } else {
            sqlsrv_free_stmt($stmt);
            sqlsrv_close($link);
            return false;
        }
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
/** Fin Querys MS-SQL */
function PerCierre($FechaStr, $Legajo)
{
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    require __DIR__ . '/config/conect_mssql.php';
    $query = "SELECT TOP 1 CierreFech FROM PERCIERRE WHERE PERCIERRE.CierreLega = '$Legajo'";
    // print_r($query);exit;
    $stmt = sqlsrv_query($link, $query, $params, $options);
    while ($row = sqlsrv_fetch_array($stmt)) {
        $perCierre = $row['CierreFech']->format('Ymd');
    }
    $perCierre = !empty($perCierre) ? $perCierre : '17530101';
    sqlsrv_free_stmt($stmt);
    if (intval($FechaStr) <= intval($perCierre)) {
        return true;
    } else {
        $query = "SELECT ParCierr FROM PARACONT WHERE ParCodi = 0 ORDER BY ParCodi";
        $stmt = sqlsrv_query($link, $query, $params, $options);
        while ($row = sqlsrv_fetch_array($stmt)) {
            $ParCierr = $row['ParCierr']->format('Ymd');
        }
        $ParCierr = !empty($ParCierr) ? $ParCierr : '17530101';
        sqlsrv_free_stmt($stmt);
        if (intval($FechaStr) <= intval($ParCierr)) {
            sqlsrv_close($link);
            return true;
        } else {
            sqlsrv_close($link);
            return false;
        }
    }
}
function PerCierreFech($FechaStr, $Legajo)
{
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    require __DIR__ . '/config/conect_mssql.php';
    $query = "SELECT TOP 1 CierreFech FROM PERCIERRE WHERE PERCIERRE.CierreLega = '$Legajo'";
    $stmt = sqlsrv_query($link, $query, $params, $options);
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
        $stmt = sqlsrv_query($link, $query, $params, $options);
        while ($row = sqlsrv_fetch_array($stmt)) {
            $ParCierr = $row['ParCierr']->format('Ymd');
        }
        $ParCierr = !empty($ParCierr) ? $ParCierr : '17530101';
        sqlsrv_free_stmt($stmt);
        if ($FechaStr <= $ParCierr) {
            sqlsrv_close($link);
            return $ParCierr;
        } else {
            sqlsrv_close($link);
            return false;
        }
    }
    // sqlsrv_close($link);
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
    return ($respuesta[0]);
}
function EstadoProceso($url)
{
    do {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $respuesta = curl_exec($ch);
        curl_close($ch);
    } while (respuestaWebService($respuesta) == 'Pendiente');
    return respuestaWebService($respuesta);
}
function pingWebService($textError) // Funcion para validar que el Webservice de Control Horario esta disponible
{
    $url = rutaWebService("Ping?");
    $ch = curl_init(); // Inicializar el objeto curl
    curl_setopt($ch, CURLOPT_URL, $url); // Establecer la URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Establecer que retorne el contenido del servidor
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // The number of seconds to wait while trying to connect
    curl_setopt($ch, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
    $response = curl_exec($ch); // extract information from response
    $curl_errno = curl_errno($ch); // get error code
    $curl_error = curl_error($ch); // get error information
    if ($curl_errno > 0) { // si hay error
        $text = "Error Ping WebService. \"Cod: $curl_errno: $curl_error\""; // set error message
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        PrintRespuestaJson('Error', $textError);
        exit; // salimos del script
    }
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // get http response code
    //return curl_getinfo($ch, CURLINFO_HTTP_CODE); // retornar el codigo de respuesta
    curl_close($ch); // close curl handle
    return ($http_code == 201) ? true : PrintRespuestaJson('Error', $textError) . exit; // escribir en el log
}
// Funcion para validar que el Webservice de Control Horario esta disponible
function pingWS()
{
    $url = rutaWebService("Ping?");
    $ch = curl_init(); // Inicializar el objeto curl
    curl_setopt($ch, CURLOPT_URL, $url); // Establecer la URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Establecer que retorne el contenido del servidor
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); // The number of seconds to wait while trying to connect
    curl_setopt($ch, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
    $response = curl_exec($ch); // extract information from response
    $curl_errno = curl_errno($ch); // get error code
    $curl_error = curl_error($ch); // get error information
    if ($curl_errno > 0) { // si hay error
        $text = "Error Ping WebService. \"Cod: $curl_errno: $curl_error\""; // set error message
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
    }
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // get http response code
    //return curl_getinfo($ch, CURLINFO_HTTP_CODE); // retornar el codigo de respuesta
    curl_close($ch); // close curl handle
    return ($http_code == 201) ? true : false; // escribir en el log
}
function procesar_legajo($legajo, $FechaDesde, $FechaHasta)
{
    if (pingWS()) {
        $FechaDesde = Fech_Format_Var($FechaDesde, 'd/m/Y');
        $FechaHasta = Fech_Format_Var($FechaHasta, 'd/m/Y');
        $ruta = rutaWebService("Procesar");
        $post_data = "{Usuario=Supervisor,TipoDePersonal=0,LegajoDesde='$legajo',LegajoHasta='$legajo',FechaDesde='$FechaDesde',FechaHasta='$FechaHasta',Empresa=0,Planta=0,Sucursal=0,Grupo=0,Sector=0,Seccion=0}";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $ruta);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $respuesta = curl_exec($ch);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($curl_errno > 0) {
            $text = "Error al procesar. Legajo \"$legajo\" desde \"$FechaDesde\" a \"$FechaHasta\""; // set error message
            fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
            return "Error";
            // exit;
        }
        curl_close($ch);
        if ($httpCode == 404) {
            fileLog("Error al procesar. Legajo \"$legajo\" desde \"$FechaDesde\" a \"$FechaHasta\"", __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
            return $respuesta;
            // exit;
        }
        $processID = respuestaWebService($respuesta);
        $url = rutaWebService("Estado?ProcesoId=" . $processID);

        if ($httpCode == 201) {
            return EstadoProceso($url);
        }
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
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $respuesta = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($curl_errno > 0) {
        $text = "Error al ingresar fichadas \"$LegajoDesde\" a \"$LegajoHasta\". Fecha \"$FechaDesde\" a \"$FechaHasta\""; // set error message
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        fileLog('No hay conexión con WebService CH', __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        PrintRespuestaJson('error', 'No hay Conexión..');
        exit;
    }
    curl_close($ch);
    if ($httpCode == 404) {
        $text = "Error al ingresar fichadas \"$LegajoDesde\" a \"$LegajoHasta\". Fecha \"$FechaDesde\" a \"$FechaHasta\""; // set error message
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        fileLog($respuesta, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        // echo $respuesta;
        PrintRespuestaJson('error', $respuesta);
        exit;
    }
    $processID = respuestaWebService($respuesta);
    $url = rutaWebService("Estado?ProcesoId=" . $processID);

    if ($httpCode == 201) {
        return EstadoProceso($url);
        // exit;
    } else {
        $text = "Error al ingresar fichadas \"$LegajoDesde\" a \"$LegajoHasta\". Fecha \"$FechaDesde\" a \"$FechaHasta\""; // set error message
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        fileLog($respuesta, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
    }
}
function Liquidar($FechaDesde, $FechaHasta, $LegajoDesde, $LegajoHasta, $TipoDePersonal, $Empresa, $Planta, $Sucursal, $Grupo, $Sector, $Seccion)
{
    // $FechaDesde = Fech_Format_Var($FechaDesde, 'd/m/Y');
    // $FechaHasta = Fech_Format_Var($FechaHasta, 'd/m/Y');
    $ruta = rutaWebService("Liquidar");
    $post_data = "{Usuario=Supervisor,TipoDePersonal=$TipoDePersonal,LegajoDesde=$LegajoDesde,LegajoHasta=$LegajoHasta,FechaDesde=$FechaDesde,FechaHasta=$FechaHasta,Empresa=$Empresa,Planta=$Planta,Sucursal=$Sucursal,Grupo=$Grupo,Sector=$Sector,Seccion=$Seccion}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ruta);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $respuesta = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($curl_errno > 0) {
        $text = "Error al Liquidar. Legajo \"$LegajoDesde\" a \"$LegajoHasta\". Fecha \"$FechaDesde\" a \"$FechaHasta\""; // set error message
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        fileLog('No hay conexión con WebService CH', __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        PrintRespuestaJson('error', 'No hay Conexión..');
        exit;
    }
    curl_close($ch);
    if ($httpCode == 404) {
        $text = "Error al Liquidar. Legajo \"$LegajoDesde\" a \"$LegajoHasta\". Fecha \"$FechaDesde\" a \"$FechaHasta\""; // set error message
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        fileLog($respuesta, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        $data = array('status' => 'error', 'Mensaje' => $respuesta);
        echo json_encode($data);
        exit;
    }
    $processID = respuestaWebService($respuesta);
    $url = rutaWebService("Estado?ProcesoId=" . $processID);

    if ($httpCode == 201) {
        return EstadoProceso($url);
        // exit;
    } else {
        $text = "Error al Liquidar. Legajo \"$LegajoDesde\" a \"$LegajoHasta\". Fecha \"$FechaDesde\" a \"$FechaHasta\""; // set error message
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        fileLog($respuesta, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
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
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $respuesta = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $text = "Error al procesar. Legajo \"$LegajoDesde\" a \"$LegajoHasta\". Fecha \"$FechaDesde\" a \"$FechaHasta\""; // set error message
    if ($curl_errno > 0) {
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        fileLog('No hay conexión con WebService CH', __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        PrintRespuestaJson('error', 'No hay Conexión..');
        exit;
    }
    curl_close($ch);
    if ($httpCode == 404) {
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        fileLog($respuesta, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        // $data = array('status' => 'error', 'Mensaje' => $respuesta);
        PrintRespuestaJson('error', $respuesta);
        // echo json_encode($data);
        exit;
    }
    $processID = respuestaWebService($respuesta);
    $url = rutaWebService("Estado?ProcesoId=" . $processID);
    // echo $processID.PHP_EOL; 
    // echo EstadoProceso($url); exit;

    if ($httpCode == 201) {
        // return EstadoProceso($url);
        return array('ProcesoId' => $processID, 'EstadoProceso' => EstadoProceso($url));
        // exit;
    } else {
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        fileLog($respuesta, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
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
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $respuesta = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $text = "Error al ingresar Novedad. Legajo \"$LegajoDesde\" a \"$LegajoHasta\". Fecha \"$FechaDesde\" a \"$FechaHasta\""; // set error message
    if ($curl_errno > 0) {
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        PrintRespuestaJson('error', 'No hay Conexión..');
        exit;
    }
    curl_close($ch);
    if ($httpCode == 404) {
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        echo $respuesta;
        exit;
    }
    $processID = respuestaWebService($respuesta);
    $url = rutaWebService("Estado?ProcesoId=" . $processID);

    if ($httpCode == 201) {
        return EstadoProceso($url);
        // exit;
    } else {
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
    }
}
function getHorario($FechaDesde, $FechaHasta, $LegajoDesde, $LegajoHasta, $TipoDePersonal, $Empresa, $Planta, $Sucursal, $Grupo, $Sector, $Seccion)
{
    $FechaDesde = Fech_Format_Var($FechaDesde, 'd/m/Y');
    $FechaHasta = Fech_Format_Var($FechaHasta, 'd/m/Y');
    $ruta = rutaWebService("Horarios");
    $post_data = "{Usuario=CHWEB,TipoDePersonal=$TipoDePersonal,LegajoDesde=$LegajoDesde,LegajoHasta=$LegajoHasta,FechaDesde=$FechaDesde,FechaHasta=$FechaHasta,Empresa=$Empresa,Planta=$Planta,Sucursal=$Sucursal,Grupo=$Grupo,Sector=$Sector,Seccion=$Seccion}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ruta);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $respuesta = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $text = "Error al obtener Horario. Legajo \"$LegajoDesde\" a \"$LegajoHasta\". Fecha \"$FechaDesde\" a \"$FechaHasta\""; // set error message
    if ($curl_errno > 0) {
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        PrintRespuestaJson('error', 'No hay Conexión..');
        exit;
    }
    curl_close($ch);
    if ($httpCode == 404) {
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        $data = array('status' => 'error', 'dato' => $respuesta);
        echo json_encode($data);
        exit;
    }
    $respuesta = substr($respuesta, 1, -1);
    $respuesta = explode("=", $respuesta);
    $processID = ($respuesta[1]);
    $url = rutaWebService("Estado?ProcesoId=" . $processID);
    if ($httpCode == 201) {
        return array('ProcesoId' => $processID, 'Estado' => EstadoProceso($url));
        // exit;
    } else {
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
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
function dr_fecha($ddmmyyyy, $format = 'Ymd')
{
    $fecha = date($format, strtotime((str_replace("/", "-", $ddmmyyyy))));
    return $fecha;
}
function dr_($ddmmyyyy)
{
    $fecha = date("Y-m-d", strtotime((str_replace("/", "-", $ddmmyyyy))));
    return $fecha;
}
function datosGetIn($Get, $Col)
{
    $v = ($Get >= '0') ? true : false;
    if ($v) {
        $Get = implode(',', $Get);
        $t = ($v) ? "AND " . $Col . " IN (" . (test_input($Get)) . ") " : '';
        return test_input($t);
    }
}
;
function datosGet($Get, $Col)
{
    $texto = !empty($Get) ? "AND " . $Col . " = '" . $Get . "' " : '';
    return $texto;
}
;
function MinHora($Min)
{
    if (!$Min || !is_int($Min)) {
        return false;
    }
    ;
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
function HoraMin($var)
{
    $var = explode(":", $var);
    $MinHora = intval($var[0]) * 60;
    $Min = intval($var[1] ?? '');
    $Minutos = $MinHora + $Min;
    return $Minutos;
}
function ValidarHora($Hora)
{
    if ($Hora == '00:00')
        return false;
    $Hora = HoraMin($Hora);
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
function PrintError($TituloError, $Mensaje)
{
    $data = array('status' => $TituloError, 'Mensaje' => $Mensaje);
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
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $file_contents = curl_exec($ch);
    $curl_errno = curl_errno($ch); // get error code
    $curl_error = curl_error($ch); // get error information
    if ($curl_errno > 0) { // si hay error
        $text = "cURL Error ($curl_errno): $curl_error"; // set error message
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorCurl.log'; // ruta del archivo de Log de errores
        fileLog($text, $pathLog); // escribir en el log de errores el error
    }
    curl_close($ch);
    if ($file_contents) {
        return $file_contents;
    } else {
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorCurl.log'; // ruta del archivo de Log de errores
        fileLog('Error al obtener datos', $pathLog); // escribir en el log de errores el error
    }
    exit;
}
function implodeArrayByKey(array $array, $key, $separator = ',')
{
    if ($array && $key) {
        $i = array_unique(array_column($array, $key));
        $i = implode("$separator", $i);
        return $i;
    }
    return false;
}
function sendRemoteData($url, $payload, $timeout = 10)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    $file_contents = curl_exec($ch);
    $curl_errno = curl_errno($ch); // get error code
    $curl_error = curl_error($ch); // get error information
    $payload = json_encode($payload);
    if ($curl_errno > 0) { // si hay error
        $text = "cURL Error ($curl_errno) : $curl_error $url $payload"; // set error message
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorCurl.log'; // ruta del archivo de Log de errores
        fileLog($text, $pathLog); // escribir en el log de errores el error
    }
    curl_close($ch);
    if ($file_contents) {
        return $file_contents;
    } else {
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorCurl.log'; // ruta del archivo de Log de errores
        fileLog('Error al obtener datos', $pathLog); // escribir en el log de errores el error
    }
    exit;
}
function sendApiData($url, $payload, $method = 'POST', $timeout = 10)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    $file_contents = curl_exec($ch);
    $curl_errno = curl_errno($ch); // get error code
    $curl_error = curl_error($ch); // get error information
    $payload = json_encode($payload);
    if ($curl_errno > 0) { // si hay error
        $text = "cURL Error ($curl_errno) : $curl_error $url $payload"; // set error message
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorCurl.log'; // ruta del archivo de Log de errores
        fileLog($text, $pathLog); // escribir en el log de errores el error
    }
    curl_close($ch);
    if ($file_contents) {
        return $file_contents;
    } else {
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorCurl.log'; // ruta del archivo de Log de errores
        fileLog('Error al obtener datos', $pathLog); // escribir en el log de errores el error
    }
    exit;
}
function curlAPI($url, $payload, $method, $token, $timeout = 10)
{
    $method = $method ?? 'POST';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: */*", 'Content-Type: application/json', "Token: $token", ));
    $file_contents = curl_exec($ch);
    $curl_errno = curl_errno($ch); // get error code
    $curl_error = curl_error($ch); // get error information
    $payload = json_encode($payload);
    if ($curl_errno > 0) { // si hay error
        $text = "cURL Error ($curl_errno) : $curl_error $url $payload"; // set error message
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorCurl.log'; // ruta del archivo de Log de errores
        fileLog($text, $pathLog); // escribir en el log de errores el error
    }
    curl_close($ch);
    if ($file_contents) {
        return $file_contents;
    } else {
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorCurl.log'; // ruta del archivo de Log de errores
        fileLog('Error al obtener datos', $pathLog); // escribir en el log de errores el error
    }
    exit;
}
function mod_roles($recid_rol)
{
    $query = "SELECT mod_roles.id AS 'id', mod_roles.recid_rol AS 'recid_rol', modulos.nombre AS 'nombre', modulos.id AS 'id_mod', modulos.idtipo AS 'idtipo' FROM mod_roles INNER JOIN modulos ON mod_roles.modulo=modulos.id WHERE mod_roles.id>'0' AND modulos.estado='0' AND mod_roles.recid_rol='$recid_rol' ORDER BY modulos.orden";
    $result = array_pdoQuery($query);
    $data = array();

    if (($result)) {
        foreach ($result as $key => $row) {

            $id = $row['id'];
            $id_mod = $row['id_mod'];
            $recid_rol = $row['recid_rol'];
            $nombre = $row['nombre'];
            $idtipo = $row['idtipo'];

            $data[] = array(
                'id' => $id,
                'recid_rol' => $recid_rol,
                'id_mod' => $id_mod,
                'modulo' => $nombre,
                'idtipo' => $idtipo
            );
        }
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
                $t[1] = $t[1] ?? '';
                return $t[1];
            // break;
            case 'token':
                $t[0] = $t[0] ?? '';
                return $t[0];
            // break;
            default:
                return '';
            // break;
        }
    } else {
        return '';
    }
}
function utf8str($cadena)
{
    return str_replace([
        "&Aacute;",
        "&Eacute;",
        "&Iacute;",
        "&Oacute;",
        "&Uacute;",
        "&Ntilde;",
        "&aacute;",
        "&eacute;",
        "&iacute;",
        "&oacute;",
        "&uacute;",
        "&ntilde;",
        "&amp;",
        "&#039;",
        "&#39;",
        "&quot;",
        "&hellip;",
    ], [
        "Á",
        "É",
        "Í",
        "Ó",
        "Ú",
        "Ñ",
        "á",
        "é",
        "í",
        "ó",
        "ú",
        "ñ",
        "&",
        "'",
        "'",
        "'",
        "...",
    ], $cadena);
}
function horarioCH($HorCodi)
{
    require __DIR__ . '/config/conect_mssql.php';
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $query = "SELECT HorCodi, HorDesc FROM HORARIOS WHERE HorCodi = $HorCodi";
    $stmt = sqlsrv_query($link, $query, $params, $options);
    // print_r($query);
    while ($a = sqlsrv_fetch_array($stmt)) {
        $data = array(
            'cod' => $a['HorCodi'],
            'desc' => $a['HorDesc'],
        );
        return $data;
    }
    sqlsrv_close($link);
}
function listaRol($idLista = '0')
{
    $idLista = intval($idLista);
    switch ($idLista) {
        case 0:
            return 'Todos';
        // break;
        case 1:
            return 'Novedades';
        // break;
        case 2:
            return 'Otras Novedades';
        // break;
        case 3:
            return 'Horarios';
        // break;
        case 4:
            return 'Rotaciones';
        // break;
        case 5:
            return 'Tipos de Hora';
        // break;
        default:
            return 'Todos';
        // break;
    }
}
function listaEstruct($idLista = '0')
{
    $idLista = intval($idLista);
    switch ($idLista) {
        case 0:
            return 'Todos';
        // break;
        case 1:
            return 'Empresas';
        // break;
        case 2:
            return 'Plantas';
        // break;
        case 3:
            return 'Convenios';
        // break;
        case 4:
            return 'Sectores';
        // break;
        case 5:
            return 'Secciones';
        // break;
        case 6:
            return 'Grupos';
        // break;
        case 7:
            return 'Sucursales';
        // break;
        case 8:
            return 'Personal';
        // break;
        default:
            return 'Todos';
        // break;
    }
}
function totalDiasFechas($fecha_inicial, $fecha_final)
{
    $dias = (strtotime($fecha_inicial) - strtotime($fecha_final)) / 86400;
    $dias = abs($dias);
    $dias = floor($dias);
    return $dias;
}
function fechaIniFinDias($fecha_inicial, $fecha_final, $dias)
{
    $TotalDias = totalDiasFechas($fecha_inicial, $fecha_final);
    $arrayTotalMeses[] = array();
    for ($i = 0; $i < intval($TotalDias / $dias); $i++) {
        $arrayTotalMeses[] = array($i);
    }
    foreach ($arrayTotalMeses as $value) {
        $fecha1 = $fecha_inicial;
        $fecha2 = date("Ymd", strtotime($fecha1 . "+ " . $dias . " days"));
        $fecha2 = ($fecha2 > date('Ymd')) ? date('Ymd') : $fecha2;
        $arrayFechas[] = array('FechaIni' => FechaFormatVar($fecha1, 'd-m-Y'), 'FechaFin' => FechaFormatVar($fecha2, 'd-m-Y'));
        $fecha_inicial = date("Ymd", strtotime($fecha2 . "+ 1 days"));
    }
    return $arrayFechas;
}
// Obtiene la version de la base de datos
function getVerDBCH($link)
{
    $query = "SELECT TOP 1 PARACONT.ParPath1 FROM PARACONT WHERE PARACONT.ParCodi = 10"; // Query
    $stmt = sqlsrv_query($link, $query); // Ejecucion del query
    $path = '';
    while ($a = sqlsrv_fetch_array($stmt)) { // Recorre el resultado
        $path = $a['ParPath1']; // Asigna el valor a la variable
    }
    sqlsrv_free_stmt($stmt); // Libera el query
    return $path; // Retorna el valor
}
function fileLog($text, $ruta_archivo, $type = false)
{
    $log = fopen($ruta_archivo, 'a');
    $date = fechaHora2();
    $text = ($type == 'export') ? $text . "\n" : $date . ' ' . $text . "\n";
    fwrite($log, $text);
    fclose($log);
}
function fileLogJson($text, $ruta_archivo, $date = true)
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
// diferencia en días entre dos fechas
function dateDifference($date_1, $date_2, $differenceFormat = '%a')
{
    $datetime1 = date_create($date_1); // creo la fecha 1
    $datetime2 = date_create($date_2); // creo la fecha 2
    $interval = date_diff($datetime1, $datetime2); // obtengo la diferencia de fechas
    return $interval->format($differenceFormat); // devuelvo el número de días
}
// borra los logs a partir de una cantidad de días
function borrarLogs($path, $dias, $ext)
{
    $files = glob($path . '*' . $ext); //obtenemos el nombre de todos los ficheros
    foreach ($files as $file) { // recorremos todos los ficheros.
        $lastModifiedTime = filemtime($file); // obtenemos la fecha de modificación del fichero
        $currentTime = time(); // obtenemos la fecha actual
        $dateDiff = dateDifference(date('Ymd', $lastModifiedTime), date('Ymd', $currentTime)); // obtenemos la diferencia de fechas
        ($dateDiff >= $dias) ? unlink($file) : ''; //elimino el fichero
    }
}
function fechaHora()
{
    timeZone();
    $t = explode(" ", microtime());
    $t = date("Ymd H:i:s", $t[1]) . substr((string) $t[0], 1, 4);
    return $t;
}
function fechaHora2()
{
    timeZone();
    $t = date("Y-m-d H:i:s");
    return $t;
}
function timeZone()
{
    return date_default_timezone_set('America/Argentina/Buenos_Aires');
}
function timeZone_lang()
{
    return setlocale(LC_TIME, "es_ES");
}
function simple_pdoQuery($sql)
{
    require __DIR__ . '/config/conect_pdo.php';
    try {
        $stmt = $connpdo->prepare($sql);
        $stmt->execute();
        // $result = $stmt->fetch(PDO::FETCH_ASSOC);
        while (($row = $stmt->fetch(PDO::FETCH_ASSOC)) !== false) {
            return $row;
        }
    } catch (\Throwable $th) { // si hay error en la consulta
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorPdoQuery.log'; // ruta del archivo de Log de errores
        fileLog($th->getMessage(), $pathLog); // escribir en el log de errores el error
    }
    $stmt = null;
}
function count_pdoQuery($sql)
{
    require __DIR__ . '/config/conect_pdo.php';
    try {
        $stmt = $connpdo->prepare($sql);
        $stmt->execute();
        return ($stmt->fetchColumn() > 0) ? true : false;
    } catch (\Throwable $th) { // si hay error en la consulta
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorPdoQuery.log'; // ruta del archivo de Log de errores
        fileLog($th->getMessage(), $pathLog); // escribir en el log de errores el error
    }
    $stmt = null;
}
function array_pdoQuery($sql)
{
    require __DIR__ . '/config/conect_pdo.php';
    try {
        $stmt = $connpdo->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    } catch (\Throwable $th) { // si hay error en la consulta
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorPdoQuery.log'; // ruta del archivo de Log de errores
        fileLog($th->getMessage(), $pathLog); // escribir en el log de errores el error
    }
    $stmt = null;
}
function insert_pdoQuery($sql)
{
    require __DIR__ . '/config/conect_pdo.php';
    try {
        $stmt = $connpdo->prepare($sql);
        return ($stmt->execute()) ? true : false;
    } catch (\Throwable $th) { // si hay error en la consulta
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorPdoQuery.log'; // ruta del archivo de Log de errores
        fileLog($th->getMessage(), $pathLog); // escribir en el log de errores el error
    }
    $stmt = null;
}
function pdoQuery($sql)
{
    require __DIR__ . '/config/conect_pdo.php';
    try {
        $stmt = $connpdo->prepare($sql);
        return ($stmt->execute()) ? true : false;
    } catch (\Throwable $th) { // si hay error en la consulta
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorPdoQuery.log'; // ruta del archivo de Log de errores
        fileLog($th->getMessage(), $pathLog); // escribir en el log de errores el error
    }
    $stmt = null;
}
function rowCount_pdoQuery($sql)
{
    require __DIR__ . '/config/conect_pdo.php';
    try {
        $stmt = $connpdo->prepare($sql);
        $stmt->execute();
        return $stmt->rowCount();
    } catch (\Throwable $th) { // si hay error en la consulta
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorPdoQuery.log'; // ruta del archivo de Log de errores
        fileLog($th->getMessage(), $pathLog); // escribir en el log de errores el error
    }
    $stmt = null;
}
function filtrarObjeto($array, $key, $valor) // Funcion para filtrar un objeto
{
    $r = array_filter($array, function ($e) use ($key, $valor) {
        return $e[$key] === $valor;
    });
    foreach ($r as $key => $value) {
        return ($value);
    }
}
function filtrarObjetoArr($array, $key, $valor) // Funcion para filtrar un objeto
{
    $r = array_filter($array, function ($e) use ($key, $valor) {
        return $e[$key] === $valor;
    });
    foreach ($r as $key => $value) {
        $v[] = $value;
    }
    return $v;
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
function tipoAud($tipo)
{
    switch ($tipo) {
        case 'P':
            $tipo = 'Proceso';
            break;
        case 'M':
            $tipo = 'Modificación';
            break;
        case 'A':
            $tipo = 'Alta';
            break;
        case 'B':
            $tipo = 'Baja';
            break;
        default:
            $tipo = '';
            break;
    }
    return $tipo;
}
function login_logs($estado, $usuario = '')
{
    // estado = 1: Login correcto; 2: Login incorrecto
    require __DIR__ . '/config/conect_pdo.php'; //Conexion a la base de datos
    $connpdo->beginTransaction();
    try {
        $sql = 'INSERT INTO login_logs(usuario,uid,estado,rol,cliente,ip,agent,fechahora) VALUES(:usuario, :uid, :estado, :rol, :cliente, :ip, :agent, :fechahora)';
        $stmt = $connpdo->prepare($sql); // prepara la consulta

        $usuario = ($usuario == '') ? filter_input(INPUT_POST, 'user', FILTER_DEFAULT) : $usuario;

        $data = [
            // array asociativo con los parametros a pasar a la consulta preparada (:usuario, :uid, :estado, :rol, :cliente, :ip, :agent, :fechahora)
            'usuario' => $usuario,
            'uid' => $_SESSION["UID"] ?? '',
            'estado' => $estado,
            'rol' => $_SESSION["ID_ROL"] ?? '',
            'cliente' => $_SESSION["ID_CLIENTE"] ?? '',
            'ip' => ($_SERVER['REMOTE_ADDR'] == '::1') ? ip2long('127.0.0.1') : ip2long($_SERVER['REMOTE_ADDR']),
            'agent' => $_SERVER['HTTP_USER_AGENT'],
            'fechahora' => fechaHora2()
        ];

        $stmt->bindParam(':usuario', $data['usuario']);
        $stmt->bindParam(':uid', $data['uid']);
        $stmt->bindParam(':estado', $data['estado']); // 1: Login correcto; 2: Login incorrecto
        $stmt->bindParam(':rol', $data['rol']);
        $stmt->bindParam(':cliente', $data['cliente']);
        $stmt->bindParam(':ip', $data['ip']);
        $stmt->bindParam(':agent', $data['agent']);
        $stmt->bindParam(':fechahora', $data['fechahora']);
        if ($stmt->execute()) { // ejecuta la consulta
            $_SESSION['UID'] = $_SESSION['UID'] ?? '';
            $_SESSION['ID_SESION'] = $connpdo->lastInsertId();
            $message = "Sesion correcta \"($_SESSION[UID]) $data[usuario]\""; // mensaje de exito
            if ($_SERVER['SERVER_NAME'] == 'localhost') { // Si es localhost
                $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_successSesion.log'; // ruta del archivo de log
                fileLog($message, $pathLog); // escribir en el log de errores
            }
        }
        $connpdo->commit(); // si todo salio bien, confirma la transaccion
    } catch (\Throwable $th) { // si hay error
        $connpdo->rollBack(); // revierte la transaccion
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorLogSesion.log'; // ruta del archivo de Log
        fileLog($th->getMessage(), $pathLog); // escribir en el log de errores
    }
    $connpdo = null; // cierra la conexion
}
function escape_sql_wild($s)
{
    $result = array();
    foreach (str_split($s) as $ch) {
        if ($ch == "\\" || $ch == "%" || $ch == "_") {
            $result[] = "\\";
        } /*if*/
        $result[] = $ch;
    } /*foreach*/
    return
        implode("", $result);
}
function write_apiKeysFile()
{
    $q = "SELECT `c`.`host` AS 'hostDB', `c`.`user` AS 'userDB',`c`.`pass` AS 'passDB', `c`.`db` AS 'DB', `c`.`auth` AS 'authDB', `c`.`id` as 'idCompany', `c`.`nombre` as 'nameCompany', `c`.`recid` as 'recidCompany', 'key' as 'key', `c`.`urlAppMobile` AS 'urlAppMobile', `c`.`localCH` as 'localCH', (SELECT `valores` FROM `params` `p` WHERE `p`.`modulo` = 1 AND `p`.`descripcion` = 'host' AND `p`.`cliente` = `c`.`id` LIMIT 1) AS 'hostCHWeb', `c`.`WebService` AS 'WebService', `c`.`ApiMobileHRP` AS 'apiMobileHRP' FROM `clientes` `c`";
    $assoc_arr = array_pdoQuery($q);
    // $assoc = $assoc_arr;

    foreach ($assoc_arr as $key => $value) {
        $assoc[] = (
            array(
                'idCompany' => $value['idCompany'],
                'nameCompany' => $value['nameCompany'],
                'recidCompany' => $value['recidCompany'],
                'urlAppMobile' => $value['urlAppMobile'],
                'apiMobileHRP' => $value['apiMobileHRP'],
                'localCH' => ($value['localCH'] == '') ? "0" : $value['localCH'],
                'hostCHWeb' => $value['hostCHWeb'],
                'homeHost' => HOMEHOST,
                'DBHost' => $value['hostDB'],
                'DBUser' => $value['userDB'],
                'DBPass' => $value['passDB'],
                'DBName' => $value['DB'],
                'DBAuth' => $value['authDB'],
                'Token' => sha1($value['recidCompany']),
                'WebServiceCH' => ($value['WebService']),
            )
        );
    }
    $content = "; <?php exit; ?> <-- ¡No eliminar esta línea! --> \n";
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
    $path = __DIR__ . '/mobileApikey.php';
    if (!$handle = fopen($path, 'w')) {
        return false;
    }
    $success = fwrite($handle, $content);
    fclose($handle);
    return $success;
}
function getIniCuenta($recidCuenta, $key = false)
{
    $urlHost = '';
    $d = (getDataIni(__DIR__ . '/mobileApikey.php'));
    foreach ($d as $k => $value) {
        if ($value['recidCompany'] == $recidCuenta) {
            $urlHost = array($k => $value);
            return $urlHost[$k];
            // break;
        }
    }
    return ($key) ? $urlHost[0][$key] : $urlHost[0];
}
function getDataIni($url) // obtiene el json de la url
{
    try {
        if (!file_exists($url))
            return false; // Si no existe el archivo
        $data = file_get_contents($url); // obtenemos el contenido del archivo
        if ($data) { // si el contenido no está vacío
            $data = parse_ini_file($url, true); // Obtenemos los datos del data.php
            return $data; // devolvemos el json
        } else { // si el contenido está vacío
            fileLog("No hay informacion en el archivo \"$url\"", __DIR__ . "/logs/" . date('Ymd') . "_getDataIni.log", ''); // escribimos en el log
        }
    } catch (Exception $e) {
        fileLog($e->getMessage(), __DIR__ . "/logs/" . date('Ymd') . "_getDataIni.log", ''); // escribimos en el log
    }
}
function gethostCHWeb()
{
    $token = sha1($_SESSION['RECID_CLIENTE']);
    $iniData = (getDataIni(__DIR__ . './mobileApikey.php'));

    foreach ($iniData as $v) {
        if ($v['Token'] == $token) {
            $data = array(
                $v
            );
            return $data[0]['hostCHWeb'];
            // break;
        }
    }
}
;
/**
 * @param $str = string a escapar
 * @param $length = cantidad de caracteres a devolver
 * @param $pad  = caracteres de autocompletado
 */
function padLeft($str, $length, $pad = ' ')
{
    return str_pad($str, $length, $pad, STR_PAD_LEFT);
}
/**
 * Function that groups an array of associative arrays by some key.
 * 
 * @param {String} $key Property to sort by.
 * @param {Array} $data Array that stores multiple associative arrays.
 */
function _group_by_keys($array, $keys = array())
{
    if (($array)) {
        $return = array();
        $append = (count($keys) > 1 ? "_" : null);
        foreach ($array as $val) {
            $final_key = "";
            foreach ($keys as $theKey) {
                $final_key .= $val[$theKey] . $append;
            }
            $return[$final_key][] = $val;
        }
        // return $return;
        foreach ($return as $key => $value) {
            $arrGroup2[] = array_map("unserialize", array_unique(array_map("serialize", $value)));
        }

        foreach ($arrGroup2 as $key => $value2) {
            $arrGroup3[] = $value2[0];
        }
    } else {
        $arrGroup3[] = array();
    }
    return $arrGroup3;
}
function pingApiMobileHRP($urlAppMobile)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $urlAppMobile . '/attention/api/test/ping');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $headers = [
        'Content-Type: application/json',
        'Authorization: 7BB3A26C25687BCD56A9BAF353A78',
        'Connection' => 'keep-alive'
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $file_contents = curl_exec($ch);
    curl_close($ch);
    return ($file_contents) ? $file_contents : false;
    // exit;
}
function sendApiMobileHRP($payload, $urlApp, $paramsUrl, $idCompany, $post = true)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $urlApp . '/' . $paramsUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    $headers = [
        'Content-Type: application/json',
        'Authorization: 7BB3A26C25687BCD56A9BAF353A78',
        'Connection' => 'keep-alive'
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    if ($post) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($payload));
    }
    $file_contents = curl_exec($ch);
    $curl_errno = curl_errno($ch); // get error code
    $curl_error = curl_error($ch); // get error information
    if ($curl_errno > 0) { // si hay error
        $MESSAGE = 'Error al enviar al servidor. (' . $curl_error . ')';
        (response(0, 1, $MESSAGE, '', 0, 1, $idCompany));
        exit; // salimos del script
    }
    curl_close($ch);
    return ($file_contents) ? $file_contents : false;
}
function confidenceFaceStr($confidence, $id_api, $threshold)
{
    $i = intval($id_api) ?? 0;
    if ($i > 1) {
        switch ($i) {
            case $confidence == -99:
                $c = 'Registro Sin Foto';
                break;
            case $confidence == -3:
                $c = 'Foto Inválida';
                break;
            case $confidence == -2:
                $c = 'No Enrolado';
                break;
            case $confidence == -1:
                $c = 'Entrenamiento Inválido';
                break;
            case $confidence == 0:
                $c = 'No Identificado';
                break;
            case $confidence >= $threshold:
                $c = 'Identificado';
                break;
            case $confidence < $threshold:
                $c = 'No Identificado';
                break;
            default:
                $c = 'No Disponible';
                break;
        }
    }
    return $c ?? 'No Disponible';
}
function access_log($Modulo)
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $pathLog = __DIR__ . '/logs/access/' . date('Ymd') . '_access_log.log'; // ruta del archivo de log
        borrarLogs(__DIR__ . '/logs/access/', 30, '.log');
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $_SESSION['NOMBRE_SESION'] = $_SESSION['NOMBRE_SESION'] ?? '';
        $_SESSION['CLIENTE'] = $_SESSION['CLIENTE'] ?? '';
        $_SERVER['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'] ?? '';
        $_SESSION['secure_auth_ch'] = $_SESSION['secure_auth_ch'] ?? false;
        $_REQUEST['state'] = $_REQUEST['state'] ?? false;

        switch ($_REQUEST['state']) {
            case '1':
                $state = 'visible tab';
                break;
            case '2':
                $state = 'hidden tab';
                break;
            default:
                $state = 'none tab';
                break;
        }

        $log = fopen($pathLog, 'a');
        $t = (new DateTime())->format('Y-m-d H:i:s.v');
        $text = "$t - u: \"$_SESSION[NOMBRE_SESION]\" c: \"$_SESSION[CLIENTE]\" ip: \"$_SERVER[REMOTE_ADDR]\"  m: \"$Modulo\" a: \"$_SESSION[secure_auth_ch]\" state: \"$state\"\n";
        fwrite($log, $text);
        fclose($log);
        // exit;
    }
}
function access_log_proy($Modulo)
{
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $pathLog = __DIR__ . '/logs/access/' . date('Ymd') . '_access_log.log'; // ruta del archivo de log
        borrarLogs(__DIR__, 30, $pathLog);
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $_SESSION['NOMBRE_SESION'] = $_SESSION['NOMBRE_SESION'] ?? '';
        $_SESSION['CLIENTE'] = $_SESSION['CLIENTE'] ?? '';
        $_SERVER['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'] ?? '';
        $_SESSION['secure_auth_ch'] = $_SESSION['secure_auth_ch'] ?? false;
        $_REQUEST['state'] = $_REQUEST['state'] ?? false;

        switch ($_REQUEST['state']) {
            case '1':
                $state = 'visible tab';
                break;
            case '2':
                $state = 'hidden tab';
                break;
            default:
                $state = 'none tab';
                break;
        }

        $log = fopen($pathLog, 'a');
        $t = (new DateTime())->format('Y-m-d H:i:s.v');
        $text = "$t - u: \"$_SESSION[NOMBRE_SESION]\" c: \"$_SESSION[CLIENTE]\" ip: \"$_SERVER[REMOTE_ADDR]\"  m: \"$Modulo\" a: \"$_SESSION[secure_auth_ch]\" state: \"$state\"\n";
        fwrite($log, $text);
        fclose($log);
        // exit;
    }
}
function createDir($path, $gitignore = true)
{
    $dirname = dirname($path . '/index.php');
    if (!is_dir($dirname)) {
        mkdir($dirname, 0755, true);

        if ($gitignore) {
            $git = dirname($path . '/.gitignore');
            if (!file_exists($git)) {
                mkdir($path, 0700);
                mkdir($git, 0755, true);
                $logGit = fopen($git . '/.gitignore', 'a');
                $textGit = '*';
                fwrite($logGit, $textGit);
                fclose($logGit);
            }
        }

        $log = fopen($dirname . '/index.php', 'a');
        $text = '<?php exit;';
        fwrite($log, $text);
        fclose($log);
    }
}

require __DIR__ . '/vendor/autoload.php';

use Carbon\Carbon;

function tareDiff($start, $end)
{
    Carbon::setLocale('es');
    setlocale(LC_TIME, 'es_ES.UTF-8');
    $f = Carbon::parse($start);
    $f2 = Carbon::parse($end);
    $d2 = $f->diffForHumans(null, false, false, 2);
    $totalDuration = $f2->diffInSeconds($f);
    $totalDuration = gmdate("H:i:s", $totalDuration);
    $totalDuration = ($end == '0000-00-00 00:00:00') ? '' : $totalDuration;
    $tareDiff = trim(str_replace('hace', '', $d2));
    return $tareDiff;
}
function diffStartEnd($start, $end)
{
    Carbon::setLocale('es');
    setlocale(LC_TIME, 'es_ES.UTF-8');
    $f = Carbon::parse($start);
    $f2 = Carbon::parse($end);
    $d2 = $f->diffForHumans(null, false, false, 2);
    $diffInSeconds = $f2->diffInSeconds($f);
    $diffInMinutes = $f2->diffInMinutes($f);
    $totalDuration = $diffInSeconds;
    $totalDuration = gmdate("H:i:s", $totalDuration);
    $totalDuration = ($end == '0000-00-00 00:00:00') ? '' : $totalDuration;
    $tareDiff = trim(str_replace('hace', '', $d2));
    $t = array(
        'diffIni' => $tareDiff,
        'duration' => $totalDuration,
        'diffInSeconds' => $diffInSeconds,
        'diffInMinutes' => $diffInMinutes
    );
    return $t;
}
function implode_keys_values($key, $separator, $array = array())
{
    if (!$array):
        return $array;
    endif;
    $key = array_column($array, $key);
    $key = array_unique($key);
    $key = implode($separator, $key);
    return $key;
}
function simple_MSQuery($query)
{
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    require __DIR__ . '/config/conect_mssql.php';
    $stmt = sqlsrv_query($link, $query, $params, $options);
    // print_r($query);
    // exit;
    if (($stmt)) {
        while ($r = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $registros = $r;
        }
        sqlsrv_free_stmt($stmt);
        sqlsrv_close($link);
        return $registros ?? false;
    } else {
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $mensaje = explode(']', $error['message']);
                $data[] = array("status" => "error", "dato" => $mensaje[3]);
            }
        }
        sqlsrv_free_stmt($stmt);
        echo json_encode($data[0]);
        sqlsrv_close($link);
        exit;
    }
}
function MSQuery($query)
{
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    require __DIR__ . '/config/conect_mssql.php';
    $stmt = sqlsrv_query($link, $query, $params, $options);
    if (($stmt)) {
        return true;
    } else {
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $mensaje = explode(']', $error['message']);
                $data[] = array("status" => "error", "dato" => $mensaje[3]);
            }
        }
        sqlsrv_free_stmt($stmt);
        echo json_encode($data[0]);
        sqlsrv_close($link);
        return false;
        // exit;
    }
}
function arrMSQuery($query)
{
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    require __DIR__ . '/config/conect_mssql.php';
    $stmt = sqlsrv_query($link, $query);
    if (($stmt)) {
        while ($r = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $registros[] = $r;
        }
        sqlsrv_free_stmt($stmt);
        sqlsrv_close($link);
        return $registros ?? false;
    } else {
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $mensaje = explode(']', $error['message']);
                $data[] = array("status" => "error", "dato" => $mensaje[3]);
                $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorMSQuery.log'; // ruta del archivo de Log de errores
                fileLog(PHP_EOL . 'Message: ' . json_encode($mensaje, JSON_UNESCAPED_UNICODE) . PHP_EOL . 'Source: ' . '"' . $_SERVER['REQUEST_URI'] . '"', $pathLog); // escribir en el log de errores el error
            }
        }
        // sqlsrv_free_stmt($stmt);
        echo json_encode($data[0]);
        sqlsrv_close($link);
        exit;
    }
}
function confTar($assoc, $path)
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
    if (!$handle = fopen($path, 'w')) {
        return false;
    }
    $content .= "## REFERENCIAS: ##\n";
    $content .= ";ProcPendTar : Procesar Tareas Pendientes. \"1\" = Si, \"0\" = No \n";
    $content .= ";ProcDescTar : Procesar Tiempo de descanso. \"1\" = Si, \"0\" = No \n";
    $content .= ";HoraCierre  : Hora de cierre del día para cerrar tareas pendientes. De \"00:00\" a \"23:59\"\n";
    $content .= ";MinimoDesc  : Tiempo Minimo de horario de descanso.\n";
    $content .= ";LimitTar    : Hora límite para cerrar tareas pendientes. De \"0\" a \"9999\"\n";
    $content .= ";ProcRedTar  : Redondear finalizacion de tarea. De \"0\" a \"9999\"\n";
    $content .= ";RecRedTar   : Recorte y redondeo de finalizacion de tarea.";
    $success = fwrite($handle, $content);
    fclose($handle);
    return $success;
}
function getConfTar()
{
    $confRequest['conf'] = 1;
    $confRequest['getConf'] = 1;
    $confTar = (getDataIni(__DIR__ . '\proy\op\confTarea.php'));
    return $confTar;
}
/**
 * @param {String} Start Fecha y hora de incio de la tarea
 * @param {String} End Fecha y hora de finalización de la tarea
 * @return {Array} Devuelve un arregle con la diferencia de tiempo entre las dos fechas, status = 0 si hay error, status = 1 si no hay error, limitMin = límite de la tarea en minutos, limitHor = límite de la tarea en Horas (18:00), diffMin = diferencia de tiempo en minutos, diffHor = diferencia de tiempo en Horas (18:00), confTar = arregle con la configuración de las tareas
 */
function calcLimitTar($start, $end)
{
    $getConfTar = ((getConfTar()['confTar'])); // Obtenemos configuración de confTarea.php en una array
    $getlimitTar = (($getConfTar['LimitTar'])); // Obtenemos el limite de tiempo de la tarea en horas
    $limitTar = intval($getlimitTar) * 60; // Convertimos el limite de tiempo a minutos
    $diffStartEnd = intval(diffStartEnd($start, $end)['diffInMinutes']); // Calculamos la diferencia de tiempo de la tarea
    $obj = array(
        'status' => ($diffStartEnd > $limitTar) ? 1 : 0,
        'limitMin' => $limitTar,
        'limitHor' => MinHora($limitTar),
        'diffMin' => $diffStartEnd,
        'diffHor' => MinHora($diffStartEnd),
        'confTar' => $getConfTar
    );
    return $obj;
}
function requestApi($url, $token, $authBasic, $payload, $timeout = 10)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    // curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            "Accept: */*",
            'Content-Type: application/json',
            "Token: $token",
        )
    );

    // $verbose = fopen('php://temp', 'w+');
    // curl_setopt($ch, CURLOPT_STDERR, $verbose);


    $file_contents = curl_exec($ch);
    $curl_errno = curl_errno($ch); // get error code
    $curl_error = curl_error($ch); // get error information

    // Obtener la salida VERBOSE del archivo temporal
    // rewind($verbose);
    // $verbose_output = stream_get_contents($verbose);

    // Cerrar el recurso de archivo temporal
    // fclose($verbose);

    // Puedes imprimir la salida VERBOSE o guardarla en un archivo para revisarla
    // echo $verbose_output;
    $pathLog = __DIR__ . '/' . date('Ymd') . '_errorCurl.log'; // ruta del archivo de Log de errores

    if ($curl_errno > 0) { // si hay error
        $text = "cURL Error ($curl_errno): $curl_error"; // set error message
        fileLog($text, $pathLog); // escribir en el log de errores el error
    }
    curl_close($ch);

    if ($file_contents) {
        return $file_contents;
    } else {
        fileLog('Error al obtener datos', $pathLog); // escribir en el log de errores el error
        return false;
    }
}
function horarioApi($ent, $sal, $labo, $Feri)
{
    $h = $ent . ' a ' . $sal;
    $h = ($labo == '0') ? 'Franco' : $h;
    $h = ($Feri == '1') ? 'Feriado' : $h;
    return $h;
}
function mergeArrayIfValue($arr1, $arr2, $key)
{
    if ($arr1 && $arr2) {
        $d = (array_merge($arr1, $arr2));
        foreach ($d as $i)
            $n[$i[$key]] = $i;
        return $n;
    }
    return false;
}
function checkenroll($recid, $userID, $apimobile, $sleep)
{
    sleep($sleep);
    $paramsCheck = array(
        'key' => $recid,
        'userID' => $userID,
    );
    $apiCheck = "api/v1/enroll/check/";
    $urlCheck = $apimobile . "/" . HOMEHOST . "/mobile/hrp/" . $apiCheck;
    sendApiData($urlCheck, $paramsCheck, 'POST');
}
/**
 * Convierte bytes en tamaño de archivo legible por humanos.
 *
 * @param $bytes
 * @return // legible por humanos tamaño de archivo (2,87 Мб)
 */
function FileSizeConvert($bytes)
{
    $result = '';
    $bytes = floatval($bytes);
    $arBytes = array(
        0 => array(
            "UNIT" => "TB",
            "VALUE" => pow(1024, 4)
        ),
        1 => array(
            "UNIT" => "GB",
            "VALUE" => pow(1024, 3)
        ),
        2 => array(
            "UNIT" => "MB",
            "VALUE" => pow(1024, 2)
        ),
        3 => array(
            "UNIT" => "KB",
            "VALUE" => 1024
        ),
        4 => array(
            "UNIT" => "B",
            "VALUE" => 1
        ),
    );

    foreach ($arBytes as $arItem) {
        if ($bytes >= $arItem["VALUE"]) {
            $result = $bytes / $arItem["VALUE"];
            $result = str_replace(".", ",", strval(round($result, 2))) . " " . $arItem["UNIT"];
            break;
        }
    }
    return $result;
}
function get_client_ip(): string
{
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    } else if (isset($_SERVER['HTTP_FORWARDED'])) {
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    } else if (isset($_SERVER['REMOTE_ADDR'])) {
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    } else {
        $ipaddress = 'UNKNOWN';
    }
    return $ipaddress;
}

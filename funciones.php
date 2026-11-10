<?php

use Symfony\Component\Translation\Provider\NullProvider;

require __DIR__ . '/config/function.php';
require __DIR__ . '/vendor/autoload.php';

class ValidationException extends \Exception
{
}

$routeEnv = getConfigPath();
$dotenv = Dotenv\Dotenv::createImmutable($routeEnv);
$dotenv->safeLoad();

function version($html = false)
{
    try {
        $fileVersion = __DIR__ . '/_ver';

        if (file_exists($fileVersion)) {
            $version = file_get_contents($fileVersion);
            $version = str_replace("\n", '', $version);
            $version = str_replace("\r", '', $version);
            $version = str_replace(" ", '', $version);
            $version = str_replace("'", '', $version);
            $version = str_replace('"', '', $version);
        } else {
            $version = '';
        }

        // $version = shell_exec('git log -1 --pretty=%B');
        // $fecha = shell_exec('git log -1 --format=%ci');
        // $fecha_ultimo_commit = new DateTime($fecha);
        // $formato_fecha = $fecha_ultimo_commit->format('d/m/Y H:i');
        // $version = $html ? "<span class='text-white text-right'>$version - $formato_fecha</span>" : trim($version);
        return trim($version); // Version de la aplicación
    } catch (\Throwable $th) {
        error_log($th->getMessage());
        return '';
    }
}

function verDBLocal(): int
{
    return 20261012; // Version de la base de datos local
}
function checkDBIntegrity()
{
    $v = simple_pdoQuery("SELECT valores as 'a' FROM params WHERE modulo = 0 AND cliente = 0 LIMIT 1");
    $dbLocal = intval($v['a'] ?? 0);

    if ($dbLocal !== verDBLocal()) {
        $msq = "Integridad de base de datos comprometida. DB Local: $dbLocal, DB Esperada: " . verDBLocal();
        $path = __DIR__ . '/logs/info/' . date('Ymd') . '_cambios_db.log';
        fileLog($msq, $path); // escribir en el Log
        redirectToLogin();
    }
}
function configureErrorReporting(): void
{
    $isLocal = ($_SERVER['SERVER_NAME'] ?? '') === 'chweb.local';
    error_reporting(E_ALL);
    ini_set('display_errors', $isLocal ? '1' : '0');
}
function E_ALL()
{
    configureErrorReporting();
}
// Función para validar si esta autenticado
function secure_auth_ch()
{

    if (!$_SESSION) {
        redirectToLogin();
    }

    timeZone();
    timeZone_lang();
    $_SESSION["secure_auth_ch"] ??= '';
    $_SESSION['VER_DB_LOCAL'] ??= '';

    // Si no existe la variable de la version de la base de datos local
    if (($_SESSION["secure_auth_ch"] ?? '') !== true || empty($_SESSION['UID'] ?? '') || (!$_SESSION['VER_DB_LOCAL'] ?? null)) {
        redirectToLogin();
    }

    checkDBIntegrity();
    // -------- Chequeamos si el usuario y la password son iguales ---------- //
    // Si la clave por defecto no ha sido verificada aún, la verificamos y almacenamos el resultado en la sesión
    if (!isset(($_SESSION['CLAVE_POR_DEFECTO']))) {
        $_SESSION['CLAVE_POR_DEFECTO'] = password_verify($_SESSION['user'], $_SESSION['HASH_CLAVE']);
    }

    // Si la clave por defecto ha sido verificada y es la clave por defecto, redirigimos al usuario a cambiar la clave
    if ($_SESSION['CLAVE_POR_DEFECTO']) {
        header('Location:/' . HOMEHOST . '/usuarios/perfil/');
        exit;
    }
    // -------- //

    $fechaGuardada = $_SESSION["ultimoAcceso"]; // Fecha de ultimo acceso
    $ahora = date("Y-m-d H:i:s"); // Fecha actual
    $tiempo_transcurrido = (strtotime($ahora) - strtotime($fechaGuardada)); // Tiempo transcurrido
    $HTTP_REFERER = $_SERVER['HTTP_REFERER'] ?? '';
    // comparamos el tiempo transcurrido 
    if ($tiempo_transcurrido >= $_SESSION["LIMIT_SESION"]) { // Si el tiempo transcurrido es mayor a la variable LIMIT_SESION
        // Si pasaron 60 minutos o más
        session_destroy(); // Destruye la sesión
        // destruyo la sesión
        // envío al usuario a la pag. de autenticación
        header("location:/" . HOMEHOST . "/login/?sesion&l=" . urlencode($HTTP_REFERER));
        exit();
    } else {
        // sino, actualizo la fecha de la sesión
        $_SESSION["ultimoAcceso"] = $ahora; // Actualizo la fecha de la sesión
    }
    // session_regenerate_id(); // Regenera la sesión
    configureErrorReporting(); // Funciones de error
}
function secure_auth_ch_json()
{
    timeZone();
    timeZone_lang();

    $secure_auth_ch = $_SESSION["secure_auth_ch"] ?? '';
    $HTTP_REFERER = $_SERVER['HTTP_REFERER'] ?? '';
    if ($secure_auth_ch !== true || empty($_SESSION['UID'])) {
        error_log($HTTP_REFERER);
        $f = 'Sesión Expirada. Inicie sesión nuevamente<br><a class="btn btn-sm fontq btn-info mt-2" href="/' . HOMEHOST . '/login/?l=' . urlencode($HTTP_REFERER) . '">Iniciar sesión </a>';
        PrintRespuestaJson('sesion', $f);
        exit;
    }

    // -------- Chequeamos si el usuario y la password son iguales ---------- //
    // Si la clave por defecto no ha sido verificada aún, la verificamos y almacenamos el resultado en la sesión
    if (!isset(($_SESSION['CLAVE_POR_DEFECTO']))) {
        $_SESSION['CLAVE_POR_DEFECTO'] = password_verify($_SESSION['user'], $_SESSION['HASH_CLAVE']);
    }

    // Si la clave por defecto ha sido verificada y es la clave por defecto, redirigimos al usuario a cambiar la clave
    if ($_SESSION['CLAVE_POR_DEFECTO']) {
        header('Location:/' . HOMEHOST . '/usuarios/perfil/');
        exit;
    }
    // -------- //

    $fechaGuardada = $_SESSION["ultimoAcceso"];
    $ahora = date("Y-m-d H:i:s");
    $tiempo_transcurrido = (strtotime($ahora) - strtotime($fechaGuardada));
    // comparamos el tiempo transcurrido
    if ($tiempo_transcurrido >= $_SESSION["LIMIT_SESION"]) {
        // Si pasaron 60 minutos o más
        // destruyo la sesión
        // envío al usuario a la pag. de autenticación
        session_destroy();
        header("location:/" . HOMEHOST . "/login/?sesion&l=" . urlencode($HTTP_REFERER));
        exit();
    } else {
        // sino, actualizo la fecha de la sesión
        $_SESSION["ultimoAcceso"] = $ahora;
    }
    // session_regenerate_id(); // Regenera la sesión para prevenir ataques de fijación de sesión
    configureErrorReporting(); // Funciones de error
}
function secure_auth_ch2()
{
    timeZone();
    timeZone_lang();
    if (
        $_SESSION["secure_auth_ch"] !== true
        || empty($_SESSION['UID'])
    ) {
        echo '<div class="p-3 fw5 text-danger">Sesión Expirada</div>';
        exit;
    }
    // -------- Chequeamos si el usuario y la password son iguales ---------- //
    // Si la clave por defecto no ha sido verificada aún, la verificamos y almacenamos el resultado en la sesión
    if (!isset(($_SESSION['CLAVE_POR_DEFECTO']))) {
        $_SESSION['CLAVE_POR_DEFECTO'] = password_verify($_SESSION['user'], $_SESSION['HASH_CLAVE']);
    }

    // Si la clave por defecto ha sido verificada y es la clave por defecto, redirigimos al usuario a cambiar la clave
    if ($_SESSION['CLAVE_POR_DEFECTO']) {
        header('Location:/' . HOMEHOST . '/usuarios/perfil/');
        exit;
    }
    // -------- //

    $fechaGuardada = $_SESSION["ultimoAcceso"];
    $ahora = date("Y-m-d H:i:s");
    $tiempo_transcurrido = (strtotime($ahora) - strtotime($fechaGuardada));
    // Comparamos el tiempo transcurrido
    if ($tiempo_transcurrido >= $_SESSION["LIMIT_SESION"]) {
        // Si pasaron 60 minutos o más
        // destruyo la sesión
        // envío al usuario a la pag. de autenticación
        session_destroy();
        header("location:/" . HOMEHOST . "/login/?sesion");
        exit();
    } else {
        // sino, actualizo la fecha de la sesión
        $_SESSION["ultimoAcceso"] = $ahora;
    }
    checkDBIntegrity();
    configureErrorReporting();
}
// Función para obtener la fecha hora del ultimo acceso
function ultimoacc()
{
    return $_SESSION["ultimoAcceso"] = date("Y-m-d H:i:s"); // Actualizo la fecha de la sesión
}
function vjs()
{
    switch ($_SERVER['SERVER_NAME']) {
        case 'localhost':
        case 'chweb.local':
            return time();
        default:
            return version();
    }
}
/** Seguridad injection SQL */
/**
 * Summary of secureVar
 * Escapa y sanitiza una variable para prevenir inyecciones SQL y XSS.
 * @param mixed $key
 * @return string
 */
function secureVar($key)
{
    $key = htmlspecialchars(stripslashes($key));
    $key = str_ireplace("script", "blocked", $key);
    #$key = mysql_real_escape_string($key);
    $key = htmlentities($key, ENT_QUOTES);
    return $key;
}
function version_file(string $pathFile)
{
    try {
        if (!file_exists(dirname(__FILE__) . $pathFile)) {
            throw new \Exception("El archivo {$pathFile} no existe.");
        }

        if ($pathFile) {
            return filesize(dirname(__FILE__) . $pathFile) . '.' . filemtime(dirname(__FILE__) . $pathFile);
        }

        return time();
    } catch (\Exception $e) {
        error_log("Error al obtener la versión del archivo {$pathFile}: " . $e->getMessage());
        return time();
    }
}
function API_KEY_MAPS()
{
    return 'AIzaSyCFs9lj9k7WZAyuwzDJwOiSiragUA9Xwg0';
}
$params = [];
if (!defined('SQLSRV_CURSOR_KEYSET')) {
    define('SQLSRV_CURSOR_KEYSET', 2); // El valor correcto puede variar según tu aplicación
}
$options = ["Scrollable" => SQLSRV_CURSOR_KEYSET, 'keyset'];
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
/**
 * Summary of statusData
 * @param mixed $status
 * @param mixed $dato
 * @return never
 */
function statusData($status, $dato)
{
    $data = ['status' => $status, 'Mensaje' => $dato];
    echo json_encode($data);
    exit;
}
/**
 * Summary of getBrowser
 * @param mixed $user_agent
 * @return string
 */
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
/**
 * Summary of AttrDisabled
 * Devuelve el atributo 'disabled' si $ValTrue no es igual a 1
 * @param mixed $ValTrue
 * @return string
 */
function attrDisabled($ValTrue)
{
    return $ValTrue == 1 ? '' : 'disabled';
}
function BrowserIE()
{
    return getBrowser($_SERVER['HTTP_USER_AGENT']) == 'Internet explorer' ? true : false;
}
/**
 * Summary of BorrarArchivosPDF
 * Borra archivos PDF en la ruta especificada que tengan más de 1 hora de antigüedad
 * @param mixed $RutaFiles
 */
function BorrarArchivosPDF($RutaFiles)
{
    $files = glob($RutaFiles); //obtenemos el nombre de todos los ficheros
    foreach ($files as $file) {
        $lastModifiedTime = filemtime($file);
        $currentTime = time();
        $timeDiff = abs($currentTime - $lastModifiedTime) / (60 * 60);
        /** Genera el resulta en Horas decimal */
        if (is_file($file) && $timeDiff > 1)
            /** borra archivos con diferencia de horas mayor a 1 */
            unlink($file); //elimino el fichero
    }
}
function token()
{
    return sha1('ie&%$sg@dQdtW@!""#');
}
/**
 * Summary of encabezado_mod
 * @param mixed $bgc
 * @param mixed $colortexto
 * @param mixed $img
 * @param mixed $titulo
 * @param mixed $imgclass
 */
function encabezado_mod($bgc, $colortexto, $img, $titulo, $imgclass = '')
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
                <img src="/' . HOMEHOST . '/img/' . $img . '?v=' . vjs() . '" alt="' . $titulo . '" class="mr-2 img-fluid ' . $imgclass . ' bg-light radius w30">
                </a>
            </div>
            <div class="w-100 d-inline-flex h30">
                <div class="d-flex justify-content-start align-items-center text-nowrap ml-1 fonth" id="Encabezado">' . $titulo . '</div>
                <div class="fontpp d-flex justify-content-end align-items-top w-100" style="color:#efefef" title="Version DB CH: ' . $VER_DB_CH . ' - Version DB Local: ' . $VER_DB_LOCAL . '">' . version() . '</div>
            </div>
        </div>
    </div>
    </div>';
}
/**
 * Summary of encabezado_mod_svgIcon
 * @param mixed $bgc
 * @param mixed $colortexto
 * @param mixed $svg
 * @param mixed $titulo
 * @param mixed $imgclass
 */
function encabezado_mod_svgIcon($bgc, $colortexto, $svg, $titulo, $imgclass = '')
{
    $QueryString = empty($_SERVER['QUERY_STRING']) ? '' : '?' . $_SERVER['QUERY_STRING'];
    $VER_DB_CH = $_SESSION['VER_DB_CH'] ?? '';
    $VER_DB_LOCAL = $_SESSION['VER_DB_LOCAL'] ?? '';

    echo '
    <div class="row d-print-none text-' . $colortexto . ' ' . $bgc . ' radius-0">
    <div class="col-12 d-inline-flex h6 fw3 py-2 m-0">
        <div class="d-inline-flex align-items-center w-100">
            <div>
                ' . $svg . '
            </div>
            <div class="w-100 d-inline-flex h30">
                <div class="d-flex justify-content-start align-items-center text-nowrap ml-1 fonth" id="Encabezado">' . $titulo . '</div>
                <div class="fontpp d-flex justify-content-end align-items-top w-100" style="color:#efefef" title="Version DB CH: ' . $VER_DB_CH . ' - Version DB Local: ' . $VER_DB_LOCAL . '">' . version() . '</div>
            </div>
        </div>
    </div>
    </div>';
}
/**
 * Summary of encabezado_mod2
 * Displays a custom header with the specified background color, text color, SVG icon, title, width, and additional CSS class.
 * @param mixed $bgc
 * @param mixed $colortexto
 * @param mixed $svg
 * @param mixed $titulo
 * @param mixed $width
 * @param mixed $class
 */
function encabezado_mod2($bgc, $colortexto, $svg, $titulo, $width, $class)
{

    $countModRol = (count($_SESSION['MODS_ROL'] ?? []));
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
        $version = '<span class="float-right fontpp" style="color:#efefef;margin-top:-10px; padding-right:10px" title="Version DB CH: ' . ($_SESSION['VER_DB_CH'] ?? '') . ' - Version DB Local: ' . ($_SESSION['VER_DB_LOCAL'] ?? '') . '"">' . version() . '</span>';
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
/**
 * Summary of encabezado_mod3
 * @param mixed $bgc
 * @param mixed $colortexto
 * @param mixed $svg
 * @param mixed $titulo
 * @param mixed $style
 * @param mixed $class
 * Displays a custom header with the specified background color, text color, SVG icon, title, inline style, and additional CSS class.
 */
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
function host(): string
{
    // Headers a verificar para detectar HTTPS (en orden de prioridad)
    $httpsHeaders = [
        'HTTP_X_FORWARDED_PROTO' => 'https',  // Estándar de facto
        'HTTP_X_FORWARDED_SCHEME' => 'https', // Alternativa común
        'HTTP_X_FORWARDED_SSL' => 'on',       // Usado por algunos proxies
        'HTTPS' => ['on', '1']                // Nativo (sin proxy)
    ];

    $isHttps = false;

    foreach ($httpsHeaders as $header => $expectedValues) {
        if (!isset($_SERVER[$header])) {
            continue;
        }

        $serverValue = $_SERVER[$header];
        $expectedValues = is_array($expectedValues) ? $expectedValues : [$expectedValues];

        // Verificar si el valor del servidor coincide con alguno de los valores esperados
        if (
            in_array($serverValue, $expectedValues, true) ||
            ($header === 'HTTPS' && $serverValue !== 'off')
        ) {
            $isHttps = true;
            break;
        }
    }

    $protocol = $isHttps ? 'https://' : 'http://';

    return $protocol . $_SERVER['HTTP_HOST'];
}
/**
 * Summary of valida_campo
 * @param mixed $name
 * @return bool
 */
function valida_campo($name)
{
    switch ($name) {
        case '':
            return true;
        default:
            return false;
    }
}
/**
 * Summary of pagina
 * @param mixed $pagina
 * @return mixed
 */
function pagina($pagina)
{
    $pag = (!isset($_GET['p']) or $_GET['p'] == "index.php") ? $pagina : $_GET['p'];
    return $pag;
}
/**
 * Summary of UnsetGet
 * Esta función obtiene el valor de una variable GET y la inicializa si no está definida.
 * @param mixed $variable
 * @return mixed|string
 */
function UnsetGet($variable)
{
    $_GET[$variable] = isset($_GET[$variable]) ? $_GET[$variable] : '';
    return $_GET[$variable];
}
/**
 * Summary of UnsetPost
 * Esta función obtiene el valor de una variable POST y la inicializa si no está definida.
 * @param mixed $variable
 * @return mixed|string
 */
function UnsetPost($variable)
{
    $_POST[$variable] = ($_POST[$variable]) ?? '';
    return $_POST[$variable];
}
/**
 * Summary of FormatHora
 * Convierte un valor en minutos a formato de hora (HH:MM).
 * @param mixed $var
 * @return string
 */
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
/**
 * Summary of FormatHoraR
 * Convierte un valor en minutos a formato de hora (HH:MM) y maneja valores negativos.
 * @param mixed $var
 * @return string
 */
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
/**
 * Summary of MinExcel
 * Convierte minutos a formato compatible con Excel (días).
 * @param mixed $min
 * @return float
 */
function MinExcel($min)
{
    $min = $min / 60;
    $min = ($min / 24);
    return $min;
}
/**
 * Normaliza/limpia datos de entrada de forma recursiva.
 *
 * Contextos:
 *   - 'json' (por defecto): normaliza entidades legadas a UTF-8, NO escapa HTML.
 *                           Pensado para alimentar json_encode().
 *   - 'html':               normaliza entidades legadas y escapa UNA vez con
 *                           htmlspecialchars() para imprimir en HTML.
 *   - 'raw':                solo normaliza UTF-8 y trim. Sin tocar entidades
 *                           ni escapar. Útil para hashes, tokens, binarios
 *                           convertidos, etc.
 *
 * NO sustituye a prepared statements para SQL ni al escapado en la salida.
 *
 * @param mixed  $data
 * @param string $context 'json' | 'html' | 'raw'
 * @return mixed
 */
function test_input(mixed $data, string $context = 'json'): mixed
{
    static $allowed = ['json' => true, 'html' => true, 'raw' => true];
    if (!isset($allowed[$context])) {
        $context = 'json';
    }

    if (is_array($data)) {
        $clean = [];
        foreach ($data as $k => $value) {
            // Las claves también se normalizan para evitar claves binarias/rotas.
            $cleanKey = is_string($k) ? test_input($k, 'raw') : $k;
            $clean[$cleanKey] = test_input($value, $context);
        }
        return $clean;
    }

    if ($data === null) {
        return '';
    }
    // error_log('test_input received: ' . var_export($data, true));

    if (is_bool($data)) {
        return $data ? '1' : '0';
    }

    if (!is_scalar($data)) {
        // Objetos, recursos, closures... no son datos válidos de entrada.
        return '';
    }

    $data = (string) $data;

    // 1) UTF-8 válido (sustituye secuencias inválidas por U+FFFD).
    if (!mb_check_encoding($data, 'UTF-8')) {
        $data = mb_convert_encoding($data, 'UTF-8', 'UTF-8');
    }

    // 2) Normaliza entidades HTML legadas a caracteres reales.
    if ($context !== 'raw') {
        $data = html_entity_decode($data, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // nbsp real -> espacio normal; "…" -> "..."
        $data = str_replace(
            ["\xC2\xA0", "\xE2\x80\xA6"],
            [' ', '...'],
            $data
        );
    }

    // 3) Recorta antes de escapar para no dejar entidades a medias.
    $data = trim($data);

    // 4) Escapado HTML una sola vez, solo si el destino es HTML.
    if ($context === 'html') {
        $data = htmlspecialchars($data, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    // error_log('test_input returning: ' . var_export($data, true) . ' for context: ' . $context);
    return $data;
}
function hoy()
{
    timeZone();
    $hoy = date('Y-m-d');
    return rtrim($hoy);
}
/**
 * Summary of FechaString
 * Convierte una fecha a formato "Ymd" (año, mes, día) sin separadores.
 * @param mixed $var
 * @return string
 */
function FechaString($var)
{
    $date = date_create($var);
    $FormatFecha = date_format($date, "Ymd");
    return $FormatFecha;
}
/**
 * Summary of ceronull
 * Convierte valores nulos o ceros a un guion ('-').
 * @param mixed $var
 * @return string
 */
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
/**
 * Summary of nombre_dia
 * Devuelve el nombre del día de la semana correspondiente a un número (1-7, donde 1 es Lunes y 7 es Domingo).
 * @param mixed $var
 * @return string
 */
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
/**
 * Summary of dayString
 * Devuelve el nombre del día de la semana correspondiente a un número (1-7, donde 1 es Lunes y 7 es Domingo), abreviado o completo.
 * @param mixed $var
 * @param bool $abreviado
 * @return string
 */
function dayString($var, bool $abreviado)
{
    if ($abreviado) {
        $var = match ($var) {
            '7' => "Dom",
            '1' => "Lun",
            '2' => "Mar",
            '3' => "Mié",
            '4' => "Jue",
            '5' => "Vie",
            '6' => "Sáb",
            default => $var
        };
        return $var;
    }
    
    $var = match ($var) {
        '7' => "Domingo",
        '1' => "Lunes",
        '2' => "Martes",
        '3' => "Miércoles",
        '4' => "Jueves",
        '5' => "Viernes",
        '6' => "Sábado",
        default => $var
    };

    return $var;
}
/**
 * Summary of DiaSemana
 * Devuelve el nombre del día de la semana y la fecha correspondiente a una fecha dada.
 * @param mixed $Ymd
 * @return string
 */
function DiaSemana($Ymd)
{
    timeZone();
    setlocale(LC_TIME, "spanish");
    $scheduled_day = $Ymd;
    $days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    $day = date('w', strtotime($scheduled_day));
    $scheduled_day = "$days[$day] " . date('d/m/Y', strtotime($scheduled_day));
    return $scheduled_day;
}
/**
 * Summary of Nombre_Mes
 * Devuelve el nombre abreviado del mes correspondiente a una fecha dada.
 * @param mixed $Ymd
 * @return string
 */
function Nombre_Mes($Ymd)
{
    $scheduled_month = $Ymd;
    $Month = ['', 'Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
    $mes = date('n', strtotime($scheduled_month));
    $scheduled_month = $Month[$mes];
    return $scheduled_month;
}
/**
 * Summary of Nombre_MesNum
 * Devuelve el nombre completo del mes correspondiente a un número (1-12).
 * @param mixed $NumeroMes
 * @return string
 */
function Nombre_MesNum($NumeroMes)
{
    $text = match ($NumeroMes) {
        '1' => 'Enero',
        '2' => 'Febrero',
        '3' => 'Marzo',
        '4' => 'Abril',
        '5' => 'Mayo',
        '6' => 'Junio',
        '7' => 'Julio',
        '8' => 'Agosto',
        '9' => 'Septiembre',
        '10' => 'Octubre',
        '11' => 'Noviembre',
        '12' => 'Diciembre',
        default => 'No hay Mes',
    };
    return $text;
}
/**
 * Summary of DiaSemana4
 * Devuelve el nombre del día de la semana y la fecha correspondiente a una fecha dada, con un espacio no separable entre ellos.
 * @param mixed $Ymd
 * @return string
 */
function DiaSemana4($Ymd)
{
    timeZone();
    setlocale(LC_TIME, "spanish");
    $scheduled_day = $Ymd;
    $days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    $day = date('w', strtotime($scheduled_day));
    $scheduled_day = "$days[$day]" . '&nbsp;' . date('d/m/Y', strtotime($scheduled_day));
    return $scheduled_day;
}
/**
 * Summary of DiaSemana3
 * Devuelve el nombre del día de la semana correspondiente a una fecha dada.
 * @param mixed $Ymd
 * @return string
 */
function DiaSemana3($Ymd)
{
    timeZone();
    setlocale(LC_TIME, "spanish");
    $scheduled_day = $Ymd;
    $days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    $day = date('w', strtotime($scheduled_day));
    $scheduled_day = $days[$day];
    return $scheduled_day;
}
/**
 * Summary of fechformat
 * Devuelve la fecha en formato 'd/m/Y'.
 * @param mixed $fecha
 * @return string
 */
function fechformat($fecha)
{
    $fecha = date_create($fecha);
    $fecha = date_format($fecha, 'd/m/Y');
    return $fecha;
}
/**
 * Summary of Fech_Format_Var
 * Devuelve la fecha formateada según el formato especificado en $var.
 * @param mixed $fecha
 * @param mixed $var
 * @return string
 */
function Fech_Format_Var($fecha, $var)
{
    $fecha = date_create($fecha);
    $fecha = date_format($fecha, $var);
    return $fecha;
}
/**
 * Summary of fechformat2
 * Devuelve la fecha en formato 'Y-m-d'.
 * @param mixed $var
 * @return string
 */
function fechformat2($var)
{
    $dato = date_create($var);
    $var = date_format($dato, 'Y-m-d');
    return $var;
}
/**
 * Summary of FechaFormatH
 * Devuelve la fecha y hora en formato 'd/m/Y H:i'.
 * @param mixed $FechaHora
 * @return bool|string
 */
function FechaFormatH($FechaHora)
{
    if (!$FechaHora) {
        return false;
    }
    $dato = date_create($FechaHora);
    $var = date_format($dato, "d/m/Y H:i");
    return $var;
}
/**
 * Summary of HoraFormat
 * @param mixed $FechaHora
 * @param mixed $second
 * @return string
 */
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
/**
 * Summary of FechaFormatVar
 * Devuelve la fecha formateada según el formato especificado en $var.
 * @param mixed $FechaHora
 * @param mixed $var
 * @return string
 */
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
/**
 * Summary of Fecha_String
 * @param mixed $var
 * @return string
 * Devuelve la fecha en formato 'Ymd'.
 */
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
/**
 * Summary of existeUsuarioPrincipal
 * Devuelve true si existe un usuario principal para el rol especificado, de lo contrario false.
 * @param mixed $rol
 * @return bool
 * 
 * @example
 * existeUsuarioPrincipal(1); // Devuelve true si existe un usuario principal para el rol con recid 1.
 */
function existeUsuarioPrincipal($rol)
{
    if ($rol) {
        $sql = "SELECT usuarios.id FROM usuarios JOIN roles ON usuarios.rol=roles.id WHERE usuarios.principal='1' AND roles.recid='$rol' LIMIT 1";
        $rs = simple_pdoQuery($sql);
        return ($rs) ? true : false;
    }
    return false;
}
/**
 * Summary of token_exist
 * Devuelve true si existe un token para el cliente especificado, de lo contrario false.
 * @param mixed $recid_cliente
 * @return bool
 */
function token_exist($recid_cliente)
{
    if (!$recid_cliente)
        return false;

    $sql = "SELECT clientes.id FROM clientes
    WHERE clientes.tkmobile !='' AND clientes.recid = '$recid_cliente'";
    $rs = simple_pdoQuery($sql);
    return ($rs) ? true : false;
}
/**
 * Summary of classDnone
 * @param mixed $var
 * @return string
 * Devuelve "d-none" si $var coincide con ciertos valores predeterminados, de lo contrario devuelve $var.
 */
function classDnone($var)
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
function ExisteCliente(string $recid)
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
/**
 * Verifica si existe un rol con el recid y id especificados.
 * @param mixed $recid
 * @param mixed $id
 * @return array{id: mixed, nombre: mixed, recid: mixed}
 * @throws Exception Si el rol no existe, redirige a la página de clientes.
 */
function ExisteRol3($recid, $id)
{
    $q = "SELECT roles.nombre, roles.id, roles.recid FROM roles WHERE roles.recid='$recid' AND roles.id = '$id' LIMIT 1";
    $result = simple_pdoQuery($q);
    if ($result) {
        return ['nombre' => $result['nombre'], 'id' => $result['id'], 'recid' => $result['recid']];
    } else {
        header("Location: /" . HOMEHOST . "/usuarios/clientes/");
        return ['nombre' => null, 'id' => null, 'recid' => null];
    }
}
/**
 * Verifica si existe un rol con el recid y id especificados.
 * @param mixed $recid
 * @param mixed $id
 * @return bool
 * Devuelve true si el rol existe, de lo contrario false.
 */
function ExisteRol4($recid, $id)
{
    $q = "SELECT 1 FROM roles WHERE roles.recid='$recid' AND roles.id = '$id' LIMIT 1";
    $result = simple_pdoQuery($q);
    return $result ? true : false;
}
/**
 * Verifica si existe un usuario con el cliente recid y uid especificados.
 * @param mixed $cliente_recid
 * @param mixed $uid
 * @return bool
 * Devuelve true si el usuario existe, de lo contrario false.
 */
function ExisteUser($cliente_recid, $uid)
{
    $q = "SELECT 1 FROM usuarios u INNER JOIN clientes c ON u.cliente=c.id WHERE u.id='$uid' AND c.recid='$cliente_recid' LIMIT 1";
    $result = simple_pdoQuery($q);
    return $result ? true : false;
}
/**
 * Summary of ExisteRol2
 * @param mixed $recid
 * @return array{nombre: mixed, id_rol: mixed, recid: mixed}
 */
function ExisteRol2($recid)
{
    $q = "SELECT roles.recid as 'recid', roles.nombre as 'nombre', roles.id as 'id_rol' FROM roles WHERE roles.recid='$recid' LIMIT 1";
    $r = simple_pdoQuery($q);
    if ($r) {
        return [$r['nombre'], $r['id_rol'], $r['recid']];
    } else {
        header("Location: /" . HOMEHOST . "/usuarios/clientes/");
        return [];
    }
}
/**
 * Verifica si existe un rol con el recid especificado.
 * @param mixed $recid
 * @return void
 * Redirige a la página de clientes si no existe el rol.
 */
function ExisteRol($recid)
{
    $url = host() . "/" . HOMEHOST . "/data/GetRoles.php?tk=" . token() . "&recid=" . $recid;
    $array = json_decode(getRemoteFile($url), true);
    $count = (count($array[0]['roles']));
    ($count) ? '' : header("Location: /" . HOMEHOST . "/usuarios/clientes/");
}
/**
 * Obtiene la información del cliente con el recid especificado.
 * @param mixed $recid
 * @return array{0: mixed, 1: mixed, 2: mixed, 3: mixed, 4: mixed}
 */
function Cliente_c($recid)
{
    $url = host() . "/" . HOMEHOST . "/data/GetClientes.php?tk=" . token() . "&recid=" . $recid;
    $array = json_decode(getRemoteFile($url), true);
    $data = $array[0]['clientes'];
    if (is_array($data)):
        foreach ($data as $value):
            $id_c = $value['id'];
            $ident = $value['ident'];
            $recid_c = $value['recid'];
            $nombre_c = $value['nombre'];
            $host_c = $value['host'];
            return [$id_c, $ident, $nombre_c, $recid_c, $host_c];
        endforeach;
    endif;
    return [];
}
/**
 * Obtiene la información del rol con el recid especificado.
 * @param mixed $recid
 * @return array{0: mixed, 1: mixed, 2: mixed, 3: mixed, 4: mixed, 5: mixed}
 */
function Rol_Recid($recid)
{
    $url = host() . "/" . HOMEHOST . "/data/GetRoles.php?tk=" . token() . "&recid=" . $recid;
    $array = json_decode(getRemoteFile($url), true);
    $data = $array[0]['roles'];
    if (is_array($data)):
        foreach ($data as $value):
            $id_Rol = $value['id'];
            $nombreRol = $value['nombre'];
            $clienteRol = $value['cliente'];
            $UsuariosRol = $value['cant_roles'];
            $recid_clienteRol = $value['recid_cliente'];
            $idClienteRol = $value['id_cliente'];
            return [$id_Rol, $nombreRol, $clienteRol, $UsuariosRol, $recid_clienteRol, $idClienteRol];
        endforeach;
    endif;
    return [];
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
/**
 * Busca el primer elemento de un array cuyo $key coincida con $valor.
 * Devuelve el elemento completo o null si no se encuentra.
 */
function buscarPorClave(array $array, string $key, mixed $valor): ?array
{
    foreach ($array as $item) {
        if (isset($item[$key]) && $item[$key] == $valor) {
            return $item;
        }
    }
    return null;
}

/**
 * Verifica si el módulo actual está asignado al rol del usuario en sesión.
 * Redirige al inicio si no tiene acceso.
 */
function ExisteModRol(?string $modulo): void
{
    $_SESSION['ID_MODULO'] = $modulo;

    if (!defined('ID_MODULO')) {
        define('ID_MODULO', $modulo ?? '');
    }

    $moduloId = (int) $modulo;
    if ($moduloId <= 0) {
        return;
    }

    $tieneAcceso = false;
    foreach ($_SESSION['MODS_ROL'] ?? [] as $modRol) {
        if ((int) $modRol['modsrol'] === $moduloId) {
            $tieneAcceso = true;
            break;
        }
    }

    if (!$tieneAcceso) {
        header('Location: /' . HOMEHOST . '/');
        exit;
    }

    $moduloActual = buscarPorClave($_SESSION['MODULOS'] ?? [], 'id', $moduloId);
    if ($moduloActual !== null) {
        access_log($moduloActual['modulo']);
    }
}
function ExisteModRol_old(string $modulo)
{
    /**
     * 
     * verificamos si existe el modulo asociado a la session del rol de usuario. 
     * sino existe lo enviamos al inicio.
     */
    $_SESSION['ID_MODULO'] = $modulo ?? null;
    define('ID_MODULO', $modulo);
    if (intval($modulo) > 0) {
        $r = array_filter($_SESSION["MODS_ROL"], function ($e) {
            return $e['modsrol'] == ID_MODULO;
        });
        $modulo_actual = (filtrarObjeto(($_SESSION['MODULOS'] ?? []), 'id', $modulo))['modulo']; // Nombre del modulo actual
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
    if (empty($_SESSION['CONECT_MSSQL'])) {
        header("Location:/" . HOMEHOST . "/inicio/?e=errorConexionMSSQL");
        exit;
    } // si no existe conexion a MSSQL redirigimos al inicio
}
function ListaRoles(string $Recid_C)
{
    $url = host() . "/" . HOMEHOST . "/data/GetRoles.php?tk=" . token() . "&recid_c=" . $Recid_C;
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
/**
 * Obtiene la estructura del rol especificado.
 * @param mixed $get_rol
 * @param mixed $recid_rol
 * @param mixed $e
 * @param mixed $data
 * @return string
 */
function estructura_rol($get_rol, $recid_rol, $e, $data)
{
    $url = host() . "/" . HOMEHOST . "/data/$get_rol.php?tk=" . token() . "&_r=" . $recid_rol . "&e=" . $e;
    $array = json_decode(getRemoteFile($url), true);
    $data = $array[0][$data];
    if (is_array($data)) {
        $val_roles = (!$array[0]['error']) ? implode(",", $data) : '';
        $rol = (!$array[0]['error']) ? "$val_roles" : "";
        return $rol;
    }
    return '';
}
/**
 * Summary of estructura_rol_count
 * @param mixed $get_rol
 * @param mixed $recid_rol
 * @param mixed $e
 * @param mixed $data
 * @return string
 */
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
/**
 * Summary of TipoNov
 * @param mixed $var
 * @return string La descripción del tipo de novedad correspondiente al valor proporcionado.
 */
function TipoNov($var)
{
    $tipo = match ($var) {
        '0' => 'Llegada tarde',
        '1' => 'Incumplimiento',
        '2' => 'Salida anticipada',
        '3' => 'Ausencia',
        '4' => 'Licencia',
        '5' => 'Accidente',
        '6' => 'Vacaciones',
        '7' => 'Suspensión',
        '8' => 'ART',
        default => $var,
    };
    return $tipo;
}
/**
 * Obtiene la descripción del tipo de novedad en función del valor proporcionado.
 * @param mixed $var
 * @return string
 */
function TipoONov($var)
{
    $var = $var == 1 ? 'En Hora' : 'En Valor';
    return $var;
}
/**
 * Summary of CateNov
 * @param mixed $var
 */
function CateNov($var)
{
    $tipo = match ($var) {
        '0' => 'Primaria',
        '1' => 'Adicional',
        '2' => 'Forzada',
        default => $var,
    };
    return $tipo;
}
/**
 * Summary of JustNov
 * @param mixed $var
 * @return string La representación de la justificación de la novedad correspondiente al valor proporcionado.
 */
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
/**
 * Summary of TipoDoc
 * @param mixed $var
 * @return string La representación del tipo de documento correspondiente al valor proporcionado.
 */
function TipoDoc($var)
{
    $tipo = match ($var) {
        '0' => 'DU',
        '1' => 'DNI',
        '2' => 'CI',
        '3' => 'LC',
        '4' => 'LE',
        '5' => 'PAS',
        default => $var,
    };
    return $tipo;
}
/**
 * Summary of imgIcon
 * @param string $var
 * @param string $title
 * @param string $width
 * @return string HTML de la imagen con el icono correspondiente.
 */
function imgIcon($var, $title, $width)
{
    $src = "/" . HOMEHOST . "/img/" . $var . ".png?v=" . vjs();
    return "<img loading=\"lazy\" src=\"$src\" class=\"$width\" alt=\"$title\" title=\"$title\">";
}
/**
 * Summary of Foto
 * @param string $face_url
 * @param string $title
 * @param string $width
 * @return string HTML de la imagen con la foto correspondiente.
 */
function Foto($face_url, $title, $width)
{
    $src = $face_url;
    return "<img loading=\"lazy\" src=\"$src\" class=\"$width\" title=\"$title\">";
}
/**
 * Summary of color_fichada
 * @param mixed $array
 * @return array|array{Estado: mixed, Fic: string, Tipo: mixed}
 */
function color_fichada($array)
{
    $fichada = [];
    foreach ($array as $valor) {

        $fichada = match ($valor['Tipo']) {
            'Manual' => [
                'Fic' => '<span class="text-primary fw4 contentd" data-titler="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '">' . $valor['Fic'] . '</span>',
                'Estado' => $valor['Estado'],
                'Tipo' => $valor['Tipo']
            ],
            default => [
                'Fic' => '<span class="fw4 contentd" data-titler="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '">' . $valor['Fic'] . '</span>',
                'Estado' => $valor['Estado'],
                'Tipo' => $valor['Tipo']
            ],
        };
        $fichada = match ($valor['Estado']) {
            'Modificada' => [
                'Fic' => '<span class="text-danger fw4 contentd" data-titler="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '" >' . $valor['Fic'] . '</span>',
                'Estado' => $valor['Estado'],
                'Tipo' => $valor['Tipo']
            ],
            'Normal' => [
                'Fic' => '<span class="fw4 contentd" data-titler="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '" >' . $valor['Fic'] . '</span>',
                'Estado' => $valor['Estado'],
                'Tipo' => $valor['Tipo']
            ],
            default => [
                'Fic' => '<span class="fw4 contentd" data-titler="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '">' . $valor['Fic'] . '</span>',
                'Estado' => $valor['Estado'],
                'Tipo' => $valor['Tipo']
            ]
        };
    }
    return $fichada;
}
/**
 * Summary of color_fichada3
 * Colors the fichada based on its type and state, adding appropriate HTML span elements with classes and data attributes for display purposes.
 * @param array $array
 * @return array|array{Estado: mixed, Fic: string, Tipo: mixed}
 */
function color_fichada3(array $array)
{
    $fichada = [];
    foreach ($array as $valor) {
        switch ($valor['Tipo']) {
            case 'Manual':
                $fichada = [
                    'Fic' => '<span class="text-primary fw4 contentd" data-titler="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '. Fecha de registro: ' . $valor['RegFeRe'] . '">' . $valor['Fic'] . '</span>',
                    'Estado' => $valor['Estado'],
                    'Tipo' => $valor['Tipo']
                ];
                break;
            default:
                $fichada = [
                    'Fic' => '<span class="fw4 contentd" data-titler="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '. Fecha de registro: ' . $valor['RegFeRe'] . '">' . $valor['Fic'] . '</span>',
                    'Estado' => $valor['Estado'],
                    'Tipo' => $valor['Tipo']
                ];
                break;
        }
        switch ($valor['Estado']) {
            case 'Modificada':
                $fichada = [
                    'Fic' => '<span class="text-danger fw4 contentd" title="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '. Fecha de registro: ' . $valor['RegFeRe'] . '">' . $valor['Fic'] . '</span>',
                    'Estado' => $valor['Estado'],
                    'Tipo' => $valor['Tipo']
                ];
                break;
        }
    }
    return $fichada;
}
/**
 * Colors the fichada based on its type and state, adding appropriate HTML span elements with classes and data attributes for display purposes.
 * Summary of color_Fichada2
 * @param mixed $tipo
 * @param mixed $estado
 * @param mixed $hora
 * @return string
 */
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
/**
 * Summary of _data_last_month_day
 * Returns the last day of the given month and year in 'Ymd' format.
 * @param mixed $y
 * @param mixed $m
 * @return string
 */
function _data_last_month_day($y, $m)
{
    $month = $m;
    $year = $y;
    $day = date("d", mktime(0, 0, 0, $month + 1, 0, $year));

    return date('Ymd', mktime(0, 0, 0, $month, $day, $year));
}
;
// echo _data_last_month_day('2020','04').PHP_EOL;

/**
 * Summary of _data_first_month_day
 * Returns the first day of the given month and year in 'Ymd' format.
 * @param mixed $y
 * @param mixed $m
 * @return string
 */
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
    $array = [
        'min' => $peri_min,
        'max' => $peri_max
    ];
    return $array;
}
/**
 * Summary of audito_ch
 * Logs an audit entry into the database.
 * @param mixed $AudTipo
 * @param mixed $AudDato
 * @param mixed $modulo
 */
function audito_ch($AudTipo, $AudDato, $modulo = '')
{
    $ipCliente = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    $ipCliente = match ($ipCliente) {
        '::1' => '127.0.0.1',
        default => ($_SERVER['REMOTE_ADDR']),
    };
    require __DIR__ . '/config/conect_mssql.php';

    $AudUser = substr($_SESSION["NOMBRE_SESION"], 0, 10);
    $ipCliente = substr($ipCliente, 0, 20);
    // $AudTerm   = gethostname();
    $AudTerm = $ipCliente;
    $AudModu = 21;
    $FechaHora = fechaHora();
    $AudFech = fechaHora();
    $AudHora = date('H:i:s');
    $AudZonaHoraria = '(UTC-03:00) Ciudad de Buenos Aires';

    $VER_DB_CH = $_SESSION['VER_DB_CH']; // Ver_61_20230622
    $VER_DB_CH = explode('_', $VER_DB_CH);
    $VER_DB_CH = $VER_DB_CH[1] ?? 60;
    // file_put_contents(__DIR__ . '/' . date('Ymd') . '_errorQueryMS.log', print_r($VER_DB_CH, true));

    $procedure_params = [
        [&$AudFech],
        [&$AudHora],
        [&$AudUser],
        [&$AudTerm],
        [&$AudModu],
        [&$AudTipo],
        [&$AudDato],
        [&$FechaHora],
        ($VER_DB_CH >= 70) ? [&$AudZonaHoraria] : '',
    ];

    $execAuditor70 = "exec DATA_AUDITORInsert @AudFech=?,@AudHora=?,@AudUser=?,@AudTerm=?,@AudModu=?,@AudTipo=?,@AudDato=?,@FechaHora=?, @AudZonaHoraria=?";
    $execAuditor60 = "exec DATA_AUDITORInsert @AudFech=?,@AudHora=?,@AudUser=?,@AudTerm=?,@AudModu=?,@AudTipo=?,@AudDato=?,@FechaHora=?";
    $sql = ($VER_DB_CH >= 70) ? $execAuditor70 : $execAuditor60;

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
        auditoria($AudDato, $AudTipo, '', $modulo); // insertamos en la tabla de auditoria de la base de datos mysql
    } else {
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $mensaje = explode(']', $error['message']);
                $dataAud[] = array("auditor" => "error", "Mensaje" => $mensaje[3]);
            }
        }
    }
    sqlsrv_execute($stmt);
    sqlsrv_close($link);
}
/**
 * Summary of audito_ch2
 * @param mixed $AudTipo
 * @param mixed $AudDato
 * @param mixed $modulo
 * @return bool
 */
function audito_ch2($AudTipo, $AudDato, $modulo = '')
{
    $ipCliente = $_SERVER['REMOTE_ADDR'];
    $ipCliente = match ($ipCliente) {
        '::1' => '127.0.0.1',
        default => ($_SERVER['REMOTE_ADDR']),
    };
    require __DIR__ . '/config/conect_mssql.php';

    $AudUser = substr($_SESSION["NOMBRE_SESION"], 0, 10);
    $ipCliente = substr($ipCliente, 0, 20);
    $AudTerm = $ipCliente;
    $AudModu = 21;
    $AudFech = date('Ymd');
    $AudHora = date('H:i:s');
    $FechaHora = fechaHora();

    $procedure_params = [
        [&$FechaHora],
        [&$AudHora],
        [&$AudUser],
        [&$AudTerm],
        [&$AudModu],
        [&$AudTipo],
        [&$AudDato],
        [&$FechaHora],
    ];

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
        return false;
    }

    if (sqlsrv_execute($stmt)) {
        $dataAud = ["auditor" => "ok"];
        auditoria($AudDato, $AudTipo, '', $modulo);
    } else {

        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $mensaje = explode(']', $error['message']);
                $dataAud[] = ["auditor" => "error", "dato" => 'Error Auditor'];
                $pathLog = __DIR__ . '/logs/error/' . date('Ymd') . '_errorQueryMS.log';
                fileLog($_SERVER['REQUEST_URI'] . "\n" . $mensaje[3], $pathLog); // escribir en el log
                return false;
            }
        }

        echo json_encode($dataAud);
        return false;
    }
    sqlsrv_execute($stmt);
    sqlsrv_close($link);
    return false;
}
/**
 * Summary of auditoria function
 * Inserts an audit record into the database with the provided details.
 * @param mixed $dato The data to be recorded in the audit.
 * @param mixed $tipo The type of audit action (e.g., insert, update, delete, process).
 * @param mixed $audcuenta The account ID related to the audit.
 * @param mixed $modulo The module of the application where the action occurred.
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
        $connpdo->commit(); // si todo salio bien, confirma la transacción
    } catch (\Throwable $th) { // si hay error
        $message = "Error -> auditoria. Usuario : \"$data[usuario]\" Dato: \"$data[dato]\"  Tipo: \"$data[tipo]\"  Fecha: \"$data[fecha]\"  Hora: \"$data[hora]\" Cuenta (\"$data[audcuenta]\")"; // mensaje de éxito
        $connpdo->rollBack(); // revierte la transacción
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorAudito.log'; // ruta del archivo de Log
        fileLog($th->getMessage() . "\n $message", $pathLog); // escribir en el log de errores
    }
    $connpdo = null; // cierra la conexion
}
/**
 * Summary of auditoria_multiple
 * @param mixed $array
 * @param mixed $AudMod
 * @return bool
 */
function auditoria_multiple($array, $AudMod)
{
    timeZone();

    $values = [];
    $params = [];
    $index = 0;
    foreach ($array as $value) {
        $values[] = "(:id_sesion{$index} , :usuario{$index} , :nombre{$index} , :cuenta{$index} , :audcuenta{$index} , :fecha{$index} , :hora{$index} , :tipo{$index} , :dato{$index} , :modulo{$index})";

        $params[":id_sesion{$index}"] = $_SESSION['ID_SESION'];
        $params[":usuario{$index}"] = $_SESSION["user"] ?? 'Sin usuario';
        $params[":nombre{$index}"] = $_SESSION["NOMBRE_SESION"] ?? 'Sin nombre';
        $params[":cuenta{$index}"] = $_SESSION["ID_CLIENTE"] ?? '';
        $params[":audcuenta{$index}"] = $_SESSION["ID_CLIENTE"] ?? '';
        $params[":fecha{$index}"] = date("Y-m-d");
        $params[":hora{$index}"] = date("H:i:s");
        $params[":tipo{$index}"] = $value['AudTipo'] ?? 'Null';
        $params[":dato{$index}"] = trim($value['AudDato']) ?? 'No se especificaron datos';
        $params[":modulo{$index}"] = $AudMod ?? '';
        $index++;
    }

    $valuesList = implode(',', $values);

    // return $valuesList;

    require __DIR__ . '/config/conect_pdo.php'; //Conexion a la base de datos
    $connpdo->beginTransaction();
    try {
        $sql = "INSERT INTO auditoria( id_sesion, usuario, nombre, cuenta, audcuenta, fecha, hora, tipo, dato, modulo) VALUES $valuesList";
        $stmt = $connpdo->prepare($sql); // prepara la consulta
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value, is_int($value) ? \PDO::PARAM_INT : \PDO::PARAM_STR);
        }
        $stmt->execute();
        $connpdo->commit(); // si todo salio bien, confirma la transacción
        return true;
    } catch (\Throwable $th) { // si hay error
        $message = "Error -> auditoria. Usuario : \"$data[usuario]\" Dato: \"$data[dato]\"  Tipo: \"$data[tipo]\"  Fecha: \"$data[fecha]\"  Hora: \"$data[hora]\" Cuenta (\"$data[audcuenta]\")"; // mensaje de éxito
        $connpdo->rollBack(); // revierte la transacción
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorAudito.log'; // ruta del archivo de Log
        fileLog($th->getMessage() . "\n $message", $pathLog); // escribir en el log de errores
    }
    $connpdo = null; // cierra la conexion
    return false;
}
/**
 * Summary of fecha_min_max
 * @param mixed $tabla
 * @param mixed $ColFech
 * @return array
 */
function fecha_min_max($tabla, $ColFech)
{
    require __DIR__ . '/config/conect_mssql.php';
    $param = [];
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
/**
 * Summary of fecha_min_max2
 * @param mixed $tabla
 * @param mixed $ColFech
 * @return array
 */
function fecha_min_max2($tabla, $ColFech)
{
    require __DIR__ . '/config/conect_mssql.php';
    $param = [];
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
/**
 * Summary of nov_cta_cte
 * @return array<array{cod: bool, desc: bool, peri: bool}|array{cod: mixed, desc: mixed, peri: mixed}>
 */
function nov_cta_cte()
{
    $query = "SELECT DISTINCT CTANOVE.CTA2Nove, NOVEDAD.NovDesc, CTANOVE.CTA2Peri FROM CTANOVE JOIN NOVEDAD ON CTANOVE.CTA2Nove = NOVEDAD.NovCodi ORDER BY CTANOVE.CTA2Peri DESC";
    $rs = arrMSQuery($query);
    if ($rs) {
        foreach ($rs as $fila) {
            $CTA2Nove = $fila['CTA2Nove'] ?? false;
            $NovDesc = $fila['NovDesc'] ?? false;
            $CTA2Peri = $fila['CTA2Peri'] ?? false;
            $array[] = [
                'cod' => $CTA2Nove,
                'desc' => $NovDesc,
                'peri' => $CTA2Peri
            ];
        }
    } else {
        $array[] = [
            'cod' => false,
            'desc' => false,
            'peri' => false
        ];
    }
    return $array ?? [];
}
/**
 * Summary of super_unique
 * @param array $array
 * @param string $key
 * @return array
 */
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
/**
 * Summary of siEsNumero
 * @param mixed $val
 * @return bool
 * Valida si un valor es un número.
 */
function siEsNumero($val)
{
    $val = is_numeric($val) ? true : false;
    return $val;
}
/**
 * Summary of siEsString
 * @param mixed $val
 * @return bool
 * Valida si un valor es una cadena de texto.
 */
function siEsString($val)
{
    $val = is_string($val) ? true : false;
    return $val;
}
/**
 * Summary of DeleteRegistro
 * Elimina un registro de la base de datos según la consulta proporcionada.
 * @param mixed $query
 * @return bool
 */
function DeleteRegistro($query)
{
    $params = [];
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
/**
 * Summary of CountRegMayorCeroMySql
 * @param string $query
 * @return bool
 * Ejecuta una consulta MySQL y devuelve true si hay registros mayores a cero.
 */
function CountRegMayorCeroMySql($query)
{
    $stmt = simple_pdoQuery($query);
    if ($stmt) {
        return true;
    } else {
        return false;
    }
}
/**
 * Summary of checkKey
 * Verifica si una clave foránea existe en la tabla especificada.
 * @param mixed $fk
 * @param mixed $table
 * @return bool
 */
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
/**
 * Summary of checkColumn
 * @param mixed $table
 * @param mixed $col
 * @return bool
 */
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
/**
 * Summary of checkTable
 * @param mixed $table
 * @return bool
 */
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
/**
 * Summary of InsertRegistroMySql
 * @param mixed $query
 * @return bool
 */
function InsertRegistroMySql($query)
{
    $stmt = pdoQuery($query);
    if ($stmt) {
        return true;
    } else {
        return false;
    }
}
/**
 * Summary of simpleQueryDataMS
 * @param mixed $query
 * @return array|bool
 */
function simpleQueryDataMS($query)
{
    require __DIR__ . '/config/conect_mssql.php';
    $params = [];
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $stmt = sqlsrv_query($link, $query, $params, $options);
    if ($stmt) {
        $a = sqlsrv_fetch_array($stmt);
        sqlsrv_free_stmt($stmt);
        return $a;
    } else {
        $mensaje = '';
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $mensaje = explode(']', $error['message']);
            }
        }

        $pathLog = __DIR__ . '/logs/error/' . date('Ymd') . '_errorQuery.log';
        fileLog($_SERVER['REQUEST_URI'] . "\n" . $mensaje, $pathLog); // escribir en el log
        return false;
    }
}
/**
 * Summary of arrayQueryDataMS
 * @param mixed $query
 * @return array[]|bool
 * Ejecuta una consulta en SQL Server y devuelve los resultados como un array de arrays.
 * @param mixed $query
 * @return array[]|bool
 */
function arrayQueryDataMS($query)
{
    require __DIR__ . '/config/conect_mssql.php';
    $data = [];
    $params = [];
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $stmt = sqlsrv_query($link, $query, $params, $options);
    if ($stmt) {
        while ($a = sqlsrv_fetch_array($stmt)) {
            $data[] = $a;
        }
        sqlsrv_free_stmt($stmt);
        return $data;
    } else {
        $pathLog = __DIR__ . '/logs/error/' . date('Ymd') . '_errorQuery.log';
        fileLog($_SERVER['REQUEST_URI'] . "\n", $pathLog); // escribir en el log
        return false;
    }
}
/**
 * Summary of UpdateRegistroMySql
 * @param mixed $query
 * @return bool
 * Actualiza un registro en la base de datos según la consulta proporcionada.
 */
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
/**
 * Summary of deleteRegistroMySql
 * @param mixed $query
 * @return bool
 * Elimina un registro de la base de datos según la consulta proporcionada.
 */
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
/**
 * Summary of dataLista
 * @param mixed $lista
 * @param mixed $rol
 * @return array
 * Obtiene los datos de la lista de roles según el rol y la lista proporcionados.
 */
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
/**
 * Summary of dataListaEstruct
 * @param mixed $lista
 * @param mixed $uid
 * @return array
 * Obtiene los datos de la lista estructurada según el UID y la lista proporcionados.
 */
function dataListaEstruct($lista, $uid)
{
    $q = "SELECT lista_estruct.datos FROM lista_estruct where lista_estruct.uid = ? AND lista_estruct.lista = ?";

    $stmt = pdoQuerySeguro($q, [$uid, $lista]);
    if ($stmt) {
        return [$stmt['datos']];
    } else {
        return ['-'];
    }
}
/**
 * Summary of InsertRegistro
 * @param string $query
 * @return bool
 */
function InsertRegistro(string $query)
{
    $params = [];
    $options = ["Scrollable" => SQLSRV_CURSOR_KEYSET];
    require __DIR__ . '/config/conect_mssql.php';
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
/**
 * Summary of InsertRegistroMS
 * @param mixed $query
 * @return bool
 * Inserta un registro en la base de datos SQL Server según la consulta proporcionada.
 */
function InsertRegistroMS($query)
{
    $params = [];
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
/**
 * Summary of UpdateRegistro
 * @param mixed $query
 * @return bool
 * Actualiza un registro en la base de datos SQL Server según la consulta proporcionada.
 */
function UpdateRegistro($query)
{
    $params = [];
    $options = ["Scrollable" => SQLSRV_CURSOR_KEYSET];
    require __DIR__ . '/config/conect_mssql.php';
    $stmt = sqlsrv_query($link, $query, $params, $options);

    if (($stmt)) {
        sqlsrv_close($link);
        return true;
    } else {
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $mensaje = explode(']', $error['message']);
                PrintRespuestaJson('error', $mensaje[3]);
                exit;
            }
        }
        echo json_encode($data);
        sqlsrv_close($link);
        return false;
    }
}
/**
 * Summary of CountRegistrosMayorCero
 * @param mixed $query
 * @return bool
 */
function CountRegistrosMayorCero($query)
{
    $params = [];
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
/**
 * Summary of PerCierre
 * Verifica si la fecha proporcionada es menor o igual a la fecha de cierre del legajo.
 * @param mixed $FechaStr
 * @param mixed $Legajo
 * @return bool
 */
function PerCierre($FechaStr, $Legajo)
{
    $params = [];
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
/**
 * Summary of PerCierreFech
 * @param mixed $FechaStr
 * @param mixed $Legajo
 * @return int|mixed|string
 */
function PerCierreFech($FechaStr, $Legajo)
{
    try {

        $params = [];
        $options = ["Scrollable" => SQLSRV_CURSOR_KEYSET];
        require __DIR__ . '/config/conect_mssql.php';
        $query = "SELECT TOP 1 CierreFech FROM PERCIERRE WHERE PERCIERRE.CierreLega = '$Legajo'";
        $stmt = sqlsrv_query($link, $query, $params, $options);
        while ($row = sqlsrv_fetch_array($stmt)) {
            $perCierre = $row['CierreFech']->format('Ymd');
        }

        $perCierre = !empty($perCierre) ? $perCierre : '17530101';
        sqlsrv_free_stmt($stmt);

        if ($FechaStr <= $perCierre) {
            return $perCierre;
        } else {
            $query = "SELECT ParCierr FROM PARACONT WHERE ParCodi = 0 ORDER BY ParCodi";
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
                // error_log(print_r($ParCierr, true));
                return $ParCierr;
                // return false;
            }
        }

    } catch (\Throwable $th) {
        error_log($th->getMessage());
    }
    return false;
    // sqlsrv_close($link);
}
/**
 * Summary of rutaWebService
 * Devuelve la URL completa del WebService de RRHH para el comando especificado.
 * @param mixed $Comando
 * @return string
 */
function rutaWebService($Comando)
{
    return $_SESSION["WEBSERVICE"] . "/RRHHWebService/" . $Comando;
}
/**
 * Summary of respuestaWebService
 * Procesa la respuesta del WebService y devuelve el valor antes del signo "=".
 * @param mixed $respuesta La respuesta del WebService.
 * @param mixed $split Indica si se debe dividir la respuesta por el signo "=".
 * @return string El valor antes del signo "=" si $split es true, de lo contrario la respuesta completa.
 */
function respuestaWebService($respuesta, $split = true)
{
    if (!$split) {
        return $respuesta;
    }
    $respuesta = substr($respuesta, 1, -1);
    $respuesta = explode("=", $respuesta);
    return $respuesta[0];
}
/**
 * Summary of EstadoProceso
 * Verifica el estado de un proceso a través de su URL.
 * @param mixed $url
 * @return string
 */
function EstadoProceso($url)
{
    // CRÍTICO: Cerrar sesión para no bloquear otras peticiones
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }

    $maxRetries = 30; // Máximo 30 intentos (30 segundos aprox)
    $retryCount = 0;

    do {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // TIMEOUTS CRÍTICOS
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2); // Timeout de conexión: 2 segundos
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); // Timeout total: 3 segundos

        $respuesta = curl_exec($ch);
        $curl_errno = curl_errno($ch);

        unset($ch);

        // Si hay error de curl, retornar error
        if ($curl_errno > 0) {
            return 'Error';
        }

        $retryCount++;

        // Si excede el máximo de reintentos, retornar timeout
        if ($retryCount >= $maxRetries) {
            return 'Timeout';
        }

        if ($respuesta === '{Pendiente}') {
            usleep(300000); // Esperar 0.3 segundos
        }

    } while (respuestaWebService($respuesta) == 'Pendiente');

    return respuestaWebService($respuesta);
}
/**
 * Summary of pingWebService
 * Valida que el WebService de Control Horario esté disponible.
 * @param mixed $textError Variable para almacenar el mensaje de error en caso de fallo.
 * @return bool Devuelve true si el WebService está disponible, false en caso contrario.
 */
function pingWebService($textError) // Función para validar que el Webservice de Control Horario esta disponible
{
    // CRÍTICO: Cerrar sesión para no bloquear otras peticiones
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }

    $url = rutaWebService("Ping?");

    $ch = curl_init(); // Inicializar el objeto curl
    curl_setopt($ch, CURLOPT_URL, $url); // Establecer la URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Establecer que retorne el contenido del servidor
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    // TIMEOUTS CRÍTICOS - Evitar esperas largas
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2); // Timeout de conexión: 2 segundos
    curl_setopt($ch, CURLOPT_TIMEOUT, 1); // Timeout total de ejecución: 1 segundo
    curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 60); // Cache DNS por 60 segundos

    curl_setopt($ch, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, false); // Reutilizar conexiones existentes
    curl_setopt($ch, CURLOPT_FORBID_REUSE, false); // Permitir reutilización de conexiones
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3); // Máximo 3 redirecciones

    // No verificar SSL en desarrollo (comentar en producción si usas HTTPS válido)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch); // extract information from response
    $curl_errno = curl_errno($ch); // get error code
    $curl_error = curl_error($ch); // get error information
    $textError = '';

    if ($curl_errno > 0) { // si hay error
        $text = "WebService. \"Cod: $curl_errno: $curl_error\""; // set error message
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        unset($ch); // Cerrar curl antes de salir
        PrintRespuestaJson('Error', $text);
        exit; // salimos del script
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // get http response code
    unset($ch); // close curl handle

    switch ($http_code) {
        case 201:
            return true;
        default:
            PrintRespuestaJson('Error', $textError);
            exit;
    } // escribir en el log
}
// Función para validar que el Webservice de Control Horario esta disponible
function pingWS()
{
    // CRÍTICO: Cerrar sesión para no bloquear otras peticiones
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }

    $url = rutaWebService("Ping?");

    $ch = curl_init(); // Inicializar el objeto curl
    curl_setopt($ch, CURLOPT_URL, $url); // Establecer la URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Establecer que retorne el contenido del servidor
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    // TIMEOUTS CRÍTICOS - Evitar esperas largas
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2); // Timeout de conexión: 2 segundos
    curl_setopt($ch, CURLOPT_TIMEOUT, 3); // Timeout total de ejecución: 3 segundos
    curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 60); // Cache DNS por 60 segundos

    curl_setopt($ch, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, false); // Reutilizar conexiones existentes
    curl_setopt($ch, CURLOPT_FORBID_REUSE, false); // Permitir reutilización de conexiones
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3); // Máximo 3 redirecciones

    // No verificar SSL en desarrollo (comentar en producción si usas HTTPS válido)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    $response = curl_exec($ch); // extract information from response
    $curl_errno = curl_errno($ch); // get error code
    $curl_error = curl_error($ch); // get error information

    if ($curl_errno > 0) { // si hay error
        $text = "Error Ping WebService. \"Cod: $curl_errno: $curl_error\""; // set error message
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // get http response code
    unset($ch); // close curl handle

    return ($http_code == 201) ? true : false; // escribir en el log
}
/**
 * Summary of procesar_legajo
 * @param mixed $legajo
 * @param mixed $FechaDesde
 * @param mixed $FechaHasta
 * @return bool|string
 */
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
        $respuesta = curl_exec($ch) ?? '';
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($curl_errno > 0) {
            $text = "Error al procesar. Legajo \"$legajo\" desde \"$FechaDesde\" a \"$FechaHasta\""; // set error message
            fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
            return "Error";
        }

        unset($ch);

        if ($httpCode == 404) {
            fileLog("Error al procesar. Legajo \"$legajo\" desde \"$FechaDesde\" a \"$FechaHasta\"", __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
            return $respuesta;
        }

        $processID = respuestaWebService($respuesta);
        $url = rutaWebService("Estado?ProcesoId=$processID");

        if ($httpCode == 201) {
            return EstadoProceso($url);
        }
        return "Error"; // si no se cumple ninguna condición, retornar error
    }
    return "Error"; // si no hay conexión con el WebService, retornar error
}
/**
 * Summary of FicharHorario
 * Ingresa fichadas para un rango de legajos y fechas especificadas.
 * @param mixed $FechaDesde
 * @param mixed $FechaHasta
 * @param mixed $LegajoDesde
 * @param mixed $LegajoHasta
 * @param mixed $TipoDePersonal
 * @param mixed $Empresa
 * @param mixed $Planta
 * @param mixed $Sucursal
 * @param mixed $Grupo
 * @param mixed $Sector
 * @param mixed $Seccion
 * @param mixed $TipoDeFichada
 * @param mixed $Laboral
 * @return string
 */
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
    unset($ch);
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

    switch ($httpCode) {
        case 201:
            return EstadoProceso($url);
        default:
            $text = "Error al ingresar fichadas \"$LegajoDesde\" a \"$LegajoHasta\". Fecha \"$FechaDesde\" a \"$FechaHasta\""; // set error message
            fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
            fileLog($respuesta, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log');
            break;
    }
    return 'error';
}
/**
 * Summary of Liquidar
 * @param mixed $FechaDesde
 * @param mixed $FechaHasta
 * @param mixed $LegajoDesde
 * @param mixed $LegajoHasta
 * @param mixed $TipoDePersonal
 * @param mixed $Empresa
 * @param mixed $Planta
 * @param mixed $Sucursal
 * @param mixed $Grupo
 * @param mixed $Sector
 * @param mixed $Seccion
 * @return string
 * Ingresa liquidaciones para un rango de legajos y fechas especificadas.
 */
function Liquidar($FechaDesde, $FechaHasta, $LegajoDesde, $LegajoHasta, $TipoDePersonal, $Empresa, $Planta, $Sucursal, $Grupo, $Sector, $Seccion)
{
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
    unset($ch);
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

    switch ($httpCode) {
        case 201:
            return EstadoProceso($url);
        default:
            $text = "Error al Liquidar. Legajo \"$LegajoDesde\" a \"$LegajoHasta\". Fecha \"$FechaDesde\" a \"$FechaHasta\""; // set error message
            fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
            fileLog($respuesta, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log');
            break;
    }
    return 'error';
}
/**
 * Summary of Procesar
 * Ingresa procesos para un rango de legajos y fechas especificadas.
 * @param mixed $FechaDesde
 * @param mixed $FechaHasta
 * @param mixed $LegajoDesde
 * @param mixed $LegajoHasta
 * @param mixed $TipoDePersonal
 * @param mixed $Empresa
 * @param mixed $Planta
 * @param mixed $Sucursal
 * @param mixed $Grupo
 * @param mixed $Sector
 * @param mixed $Seccion
 * @return array{EstadoProceso: string, ProcesoId: string, error: string|array{EstadoProceso: string, ProcesoId: string}}
 */
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
    unset($ch);

    if ($httpCode == 404) {
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        fileLog($respuesta, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        PrintRespuestaJson('error', $respuesta);
        exit;
    }

    $processID = respuestaWebService($respuesta, $httpCode !== 201 ? false : true) ?? '';
    $url = rutaWebService("Estado?ProcesoId=$processID");

    return match ($httpCode) {
        201 => ['ProcesoId' => $processID, 'EstadoProceso' => EstadoProceso($url)],
        default => ['error' => $processID, 'ProcesoId' => $processID, 'EstadoProceso' => ''],
    };
}
/**
 * Summary of IngresarNovedad
 * Ingresa una novedad para un rango de legajos y fechas especificadas.
 * @param mixed $TipoDePersonal
 * @param mixed $LegajoDesde
 * @param mixed $LegajoHasta
 * @param mixed $FechaDesde
 * @param mixed $FechaHasta
 * @param mixed $Empresa
 * @param mixed $Planta
 * @param mixed $Sucursal
 * @param mixed $Grupo
 * @param mixed $Sector
 * @param mixed $Seccion
 * @param mixed $Laboral
 * @param mixed $Novedad
 * @param mixed $Justifica
 * @param mixed $Observacion
 * @param mixed $Horas
 * @param mixed $Causa
 * @param mixed $Categoria
 * @return string
 */
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
    unset($ch);
    if ($httpCode == 404) {
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        echo $respuesta;
        exit;
    }
    $processID = respuestaWebService($respuesta);
    $url = rutaWebService("Estado?ProcesoId=" . $processID);

    switch ($httpCode) {
        case 201:
            return EstadoProceso($url);
        default:
            fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log');
            break;
    }
    return '';
}
/**
 * Summary of getHorario
 * @param mixed $FechaDesde
 * @param mixed $FechaHasta
 * @param mixed $LegajoDesde
 * @param mixed $LegajoHasta
 * @param mixed $TipoDePersonal
 * @param mixed $Empresa
 * @param mixed $Planta
 * @param mixed $Sucursal
 * @param mixed $Grupo
 * @param mixed $Sector
 * @param mixed $Seccion
 * @return array{Estado: string, ProcesoId: string}
 */
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
    unset($ch);
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
    switch ($httpCode) {
        case 201:
            return ['ProcesoId' => $processID, 'Estado' => EstadoProceso($url)];
        default:
            fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log');
            break;
    }
    return ['ProcesoId' => $processID, 'Estado' => 'error'];
}
/** FIN EL WEBSERVICE CH*/
/**
 * Summary of FusNuloPOST
 * @param mixed $dato
 * @param mixed $result
 */
function FusNuloPOST($dato, $result)
{
    $_POST[$dato] = ($_POST[$dato]) ?? $result;
    return $_POST[$dato];
}
/**
 * Summary of FusNuloGET
 * @return mixed
 * @param mixed $dato
 * @param mixed $result
 */
function FusNuloGET($dato, $result)
{
    $_GET[$dato] = ($_GET[$dato]) ?? $result;
    return $_GET[$dato];
}
/**
 * Summary of dr_fecha
 * @param mixed $ddmmyyyy
 * @param mixed $format
 * @return string
 */
function dr_fecha($ddmmyyyy, $format = 'Ymd')
{
    $fecha = date($format, strtotime((str_replace("/", "-", $ddmmyyyy))));
    return $fecha;
}
/**
 * Summary of dr_
 * @param mixed $ddmmyyyy
 * @return string
 */
function dr_($ddmmyyyy)
{
    $fecha = date("Y-m-d", strtotime((str_replace("/", "-", $ddmmyyyy))));
    return $fecha;
}
/**
 * Summary of datosGetIn
 * @param mixed $Get
 * @param mixed $Col
 * @return string|null
 */
function datosGetIn($Get, $Col)
{
    $v = ($Get >= '0') ? true : false;
    if ($v) {
        $Get = implode(',', $Get);
        $t = ($v) ? "AND " . $Col . " IN (" . (test_input($Get)) . ") " : '';
        return test_input($t);
    }
}
/**
 * Summary of datosGet
 * @param mixed $Get
 * @param mixed $Col
 * @return string
 */
function datosGet($Get, $Col)
{
    $texto = !empty($Get) ? "AND " . $Col . " = '" . $Get . "' " : '';
    return $texto;
}
/**
 * Summary of MinHora
 * @param mixed $Min
 * @return string|false
 */
function MinHora($Min)
{
    if ($Min == 0) {
        return '00:00';
    }
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
    $hora = "$horas:$minutos";
    $hora = match ($hora) {
        '00:00' => "-",
        default => $hora
    };
    return $hora;
}
/**
 * Summary of HoraMin
 * @param mixed $var
 * @return int
 */
function HoraMin($var)
{
    $var = explode(":", $var);
    $MinHora = intval($var[0]) * 60;
    $Min = intval($var[1] ?? '');
    $Minutos = $MinHora + $Min;
    return $Minutos;
}
/**
 * Summary of ValidarHora
 * @param mixed $Hora
 * @return bool
 */
function ValidarHora($Hora)
{
    if ($Hora == '00:00')
        return false;
    $Hora = HoraMin($Hora);
    $Hora = ($Hora > '1439' || $Hora < '1') ? true : false;
    return $Hora;
}
/**
 * Summary of ValidaFormatoHora
 * @param mixed $valor
 * @return bool
 */
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
/**
 * Summary of PrintError
 * @param mixed $TituloError
 * @param mixed $Mensaje
 */
function PrintError($TituloError, $Mensaje)
{
    $data = array('status' => $TituloError, 'Mensaje' => $Mensaje);
    echo json_encode($data);
    /** Imprimo json con resultado */
}
/**
 * Summary of PrintRespuestaJson
 * @param mixed $status
 * @param mixed $Mensaje
 * @return void
 */
function PrintRespuestaJson($status, $Mensaje)
{
    $data = [
        'status' => $status,
        'Mensaje' => $Mensaje
    ];
    echo json_encode($data);
}
/**
 * Summary of getRemoteFile
 * @param mixed $url
 * @param mixed $timeout
 * @return bool|string
 */
function getRemoteFile($url, $timeout = 10)
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2); // Timeout de conexión: 2 segundos
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout total de ejecución: 10 segundos
    curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 60); // Cache DNS por 60 segundos
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
        error_log(" cURL failed for URL $url with error $text");
    }
    unset($ch);
    if ($file_contents) {
        return $file_contents;
    } else {
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorCurl.log';
        return false;
    }
}
/**
 * Summary of parseApiResponse
 * @param mixed $response
 * @return array
 */
function parseApiResponse($response): array
{
    if (!$response || !is_string($response)) {
        return ['error' => 'Error al conectar con el servidor'];
    }

    $decoded = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['error' => 'Error al decodificar respuesta del servidor'];
    }

    return $decoded;
}
/**
 * Summary of implodeArrayByKey
 * @param array $array
 * @param mixed $key
 * @param mixed $separator
 * @return bool|string
 * @param array $array
 * @param mixed $key
 * @param mixed $separator
 * @return bool|string
 */
function implodeArrayByKey(array $array, $key, $separator = ',')
{
    if (!empty($array) && !empty($key)) {
        $i = array_unique(array_column($array, $key));
        $i = implode($separator, $i);
        return $i;
    }
    return false;
}
/**
 * Summary of sendRemoteData
 * @param mixed $url
 * @param mixed $payload
 * @param mixed $timeout
 * @return bool|string
 */
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
    unset($ch);
    if ($file_contents) {
        return $file_contents;
    } else {
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorCurl.log'; // ruta del archivo de Log de errores
        fileLog('Error al obtener datos', $pathLog); // escribir en el log de errores el error
    }
    return '';
}
/**
 * Summary of sendApiData
 * @param mixed $url
 * @param mixed $payload
 * @param mixed $method
 * @param mixed $timeout
 * @return bool|string
 */
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
    unset($ch);
    if ($file_contents) {
        return $file_contents;
    } else {
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorCurl.log'; // ruta del archivo de Log de errores
        fileLog('Error al obtener datos', $pathLog); // escribir en el log de errores el error
    }
    exit;
}
/**
 * Summary of curlAPI
 * @param mixed $url
 * @param mixed $payload
 * @param mixed $method
 * @param mixed $token
 * @param mixed $timeout
 * @return bool|string
 */
function curlAPI($url, $payload, $method, $token, $timeout = 10)
{
    $method ??= 'POST';
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
    unset($ch);
    if ($file_contents) {
        return $file_contents;
    } else {
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorCurl.log'; // ruta del archivo de Log de errores
        fileLog('Error al obtener datos', $pathLog); // escribir en el log de errores el error
    }
    return '';
}
/**
 * Summary of mod_roles
 * @param mixed $recid_rol
 * @return array{error: int|string, "mod_roles": array, success: string}
 */
function mod_roles($recid_rol)
{
    $query = "SELECT mod_roles.id AS 'id', mod_roles.recid_rol AS 'recid_rol', modulos.nombre AS 'nombre', modulos.id AS 'id_mod', modulos.idtipo AS 'idtipo' FROM mod_roles INNER JOIN modulos ON mod_roles.modulo=modulos.id WHERE mod_roles.id>'0' AND modulos.estado='0' AND mod_roles.recid_rol='$recid_rol' ORDER BY modulos.orden";
    $result = array_pdoQuery($query);
    $data = [];

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
        $respuesta = ['success' => 'YES', 'error' => '0', 'mod_roles' => $data];
        return $respuesta;
    }
    return ['error' => '1', 'mod_roles' => [], 'success' => 'NO'];
}

/**
 * Extrae una parte del token móvil con formato "token@appcode".
 *
 * @param string $token  Token completo (formato: "token@appcode").
 * @param string $campo  "appcode" devuelve la parte tras el '@',
 *                       "token"   devuelve la parte antes del '@'.
 * @return string        La parte solicitada, o '' si no existe / es inválida.
 */
function tokenMobile(string $token, string $campo): string
{
    if ($token === '') {
        return '';
    }

    [$tokenParte, $appCodeParte] = array_pad(explode('@', $token, 2), 2, '');

    return match ($campo) {
        'appcode' => $appCodeParte,
        'token' => $tokenParte,
        default => '',
    };
}
/**
 * Decodifica entidades HTML a caracteres reales (UTF-8).
 * NO es un sanitizador: no protege de XSS, solo normaliza texto ya escapado.
 *
 * @param string|null $cadena
 * @return string
 */
function utf8str(?string $cadena): string
{
    if ($cadena === null || $cadena === '') {
        return '';
    }

    // Decodifica entidades con y sin punto y coma, numéricas y nombradas (HTML5).
    $decoded = html_entity_decode($cadena, ENT_QUOTES | ENT_HTML5, 'UTF-8');

    // Normaliza nbsp real a espacio normal.
    $decoded = str_replace("\xC2\xA0", ' ', $decoded);

    // Colapsa "..." tipográfico si lo quieres mantener como texto plano.
    $decoded = str_replace("\xE2\x80\xA6", '...', $decoded); // U+2026

    return $decoded;
}
/**
 * Summary of horarioCH
 * Obtiene la información de un horario por su código.
 * @param mixed $HorCodi
 * @return array{cod: mixed, desc: mixed}
 */
function horarioCH($HorCodi)
{
    require __DIR__ . '/config/conect_mssql.php';
    $params = [];
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $query = "SELECT HorCodi, HorDesc FROM HORARIOS WHERE HorCodi = $HorCodi";
    $stmt = sqlsrv_query($link, $query, $params, $options);
    // print_r($query);
    while ($a = sqlsrv_fetch_array($stmt)) {
        $data = [
            'cod' => $a['HorCodi'],
            'desc' => $a['HorDesc'],
        ];
        return $data;
    }
    sqlsrv_close($link);
    return ['cod' => null, 'desc' => null]; // Devuelve un valor por defecto si no se encuentra el horario.
}
/**
 * Summary of listaRol
 * Devuelve el nombre del rol según su ID.
 * @param string $idLista
 * @return string
 */
/**
 * Summary of listaRol
 * Devuelve el nombre del rol según su ID.
 * @param string $idLista
 * @return string
 */
function listaRol(string $idLista = '0')
{
    $idLista = intval($idLista);
    return match ($idLista) {
        0 => 'Todos',
        1 => 'Novedades',
        2 => 'Otras Novedades',
        3 => 'Horarios',
        4 => 'Rotaciones',
        5 => 'Tipos de Hora',
        default => 'Todos',
    };
}
function listaEstruct($idLista = '0')
{
    $idLista = intval($idLista);
    return match ($idLista) {
        0 => 'Todos',
        1 => 'Empresas',
        2 => 'Plantas',
        3 => 'Convenios',
        4 => 'Sectores',
        5 => 'Secciones',
        6 => 'Grupos',
        7 => 'Sucursales',
        8 => 'Personal',
        default => 'Todos',
    };
}
/**
 * Summary of totalDiasFechas
 * Calcula el total de días entre dos fechas.
 * @param mixed $fecha_inicial
 * @param mixed $fecha_final
 * @return float
 */
function totalDiasFechas($fecha_inicial, $fecha_final)
{
    $days = (strtotime($fecha_inicial) - strtotime($fecha_final)) / 86400;
    $days = abs($days);
    $days = floor($days);
    return $days;
}
/**
 * Summary of fechaIniFinDias
 * @param mixed $fecha_inicial
 * @param mixed $fecha_final
 * @param mixed $days
 * @return array{FechaFin: string, FechaIni: string[]}
 */
function fechaIniFinDias($fecha_inicial, $fecha_final, $days)
{
    $TotalDias = totalDiasFechas($fecha_inicial, $fecha_final);
    $arrayTotalMeses[] = [];
    for ($i = 0; $i < intval($TotalDias / $days); $i++) {
        $arrayTotalMeses[] = [$i];
    }
    foreach ($arrayTotalMeses as $value) {
        $fecha1 = $fecha_inicial;
        $fecha2 = date("Ymd", strtotime($fecha1 . "+ " . $days . " days"));
        $fecha2 = ($fecha2 > date('Ymd')) ? date('Ymd') : $fecha2;
        $arrayFechas[] = ['FechaIni' => FechaFormatVar($fecha1, 'd-m-Y'), 'FechaFin' => FechaFormatVar($fecha2, 'd-m-Y')];
        $fecha_inicial = date("Ymd", strtotime($fecha2 . "+ 1 days"));
    }
    return $arrayFechas ?? [];
}
/**
 * Summary of getVerDBCH
 * Obtiene la versión de la base de datos CHWeb.
 * @param mixed $link
 * @return string|false
 */
function getVerDBCH($link)
{
    // Primero, verificamos si la tabla 'DBData' existe
    $checkTableQuery = "IF EXISTS (SELECT * FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME = 'DBData') SELECT 1 ELSE SELECT 0";
    $checkTableStmt = sqlsrv_query($link, $checkTableQuery);

    if ($checkTableStmt === false) {
        // Error al ejecutar la consulta
        return false;
    }

    $tableExists = sqlsrv_fetch_array($checkTableStmt, SQLSRV_FETCH_NUMERIC)[0];
    sqlsrv_free_stmt($checkTableStmt);

    if (!$tableExists) {
        // La tabla no existe
        return false;
    }

    // Si la tabla existe, procedemos con la consulta original
    $query = "SELECT * FROM DBData";
    $stmt = sqlsrv_query($link, $query);

    if ($stmt === false) {
        // Error al ejecutar la consulta
        return false;
    }

    $path = '';
    while ($a = sqlsrv_fetch_array($stmt)) {
        $path = $a['BDVersion'];
    }
    sqlsrv_free_stmt($stmt);
    return $path;
}
/**
 * Summary of fileLog
 * Escribe un mensaje en un archivo de log.
 * @param mixed $text El texto a escribir en el log.
 * @param mixed $ruta_archivo La ruta del archivo de log.
 * @param mixed $type El tipo de log (opcional).
 * @param mixed $text
 * @param mixed $ruta_archivo
 * @param mixed $type
 */
function fileLog($text, $ruta_archivo, $type = false)
{
    $log = fopen($ruta_archivo, 'a');
    $date = fechaHora2();
    $text = ($type == 'export') ? $text . "\n" : $date . ' ' . $text . "\n";
    fwrite($log, $text);
    fclose($log);
    log_local($text);
}
/**
 * Summary of log_local
 * Escribe un mensaje en el log de errores local si el host es localhost o chweb.local.
 * @param string $msg El mensaje a escribir en el log.
 */
function log_local(string $msg)
{
    switch ($_SERVER['HTTP_HOST'] ?? '') {
        case 'localhost':
        case 'chweb.local':
            error_log($msg);
            break;
    }
}
/**
 * Summary of dateDifference
 * Calcula la diferencia entre dos fechas.
 * @param mixed $date_1
 * @param mixed $date_2
 * @param mixed $differenceFormat
 * @return string
 */
function dateDifference($date_1, $date_2, $differenceFormat = '%a')
{
    $datetime1 = date_create($date_1); // creo la fecha 1
    $datetime2 = date_create($date_2); // creo la fecha 2
    $interval = date_diff($datetime1, $datetime2); // obtengo la diferencia de fechas
    return $interval->format($differenceFormat); // devuelvo el número de días
}
/**
 * Summary of borrarLogs
 * Borra los logs a partir de una cantidad de días.
 * @param string $path La ruta del directorio donde se encuentran los logs.
 * @param int $days La cantidad de días a partir de los cuales se eliminarán los logs.
 * @param string $ext La extensión de los archivos de log.
 */
function borrarLogs(string $path, int $days, string $ext)
{

    // si el directorio $path no existe throw error
    if (!is_dir($path)) {
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorBorrarLogs.log'; // ruta del archivo de Log de errores
        // fileLog('El directorio ' . $path . ' no existe', $pathLog); // escribir en el log de errores el error
        return false;
    }
    $files = glob("$path*$ext"); //obtenemos el nombre de todos los ficheros
    try {
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_successBorrarLogs.log';
        foreach ($files as $file) { // recorremos todos los ficheros.
            $lastModifiedTime = filemtime($file); // obtenemos la fecha de modificación del fichero
            $currentTime = time(); // obtenemos la fecha actual
            $dateDiff = dateDifference(date('Ymd', $lastModifiedTime), date('Ymd', $currentTime)); // obtenemos la diferencia de fechas
            if ($dateDiff >= $days) {
                unlink($file); //elimino el fichero
                // fileLog("Se eliminó log {$file}", $pathLog);
            }
        }

    } catch (\Throwable $th) {
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorBorrarLogs.log'; // ruta del archivo de Log de errores
        fileLog($th->getMessage(), $pathLog); // escribir en el log de errores el error
    }

}
/**
 * Summary of fechaHora
 * Devuelve la fecha y hora actual con microsegundos.
 * @return string
 */
function fechaHora()
{
    timeZone();
    $t = explode(" ", microtime());
    $t = date("Ymd H:i:s", $t[1]) . substr((string) $t[0], 1, 4);
    return $t;
}
/**
 * Summary of fechaHora2
 * Devuelve la fecha y hora actual sin microsegundos.
 * @return string
 */
function fechaHora2()
{
    timeZone();
    $t = date("Y-m-d H:i:s");
    return $t;
}
function timeZone(): bool
{
    return date_default_timezone_set('America/Argentina/Buenos_Aires');
}
function timeZone_lang()
{
    return setlocale(LC_TIME, "es_ES");
}
/**
 * Summary of simple_pdoQuery
 * Ejecuta una consulta PDO simple y devuelve la primera fila como un array asociativo.
 * @param mixed $sql
 * @return array|null
 * @param mixed $sql
 */
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
function count_pdoQuery(string $sql)
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
function array_pdoQuery(string $sql)
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
function insert_pdoQuery(string $sql)
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
function pdoQuery(string $sql)
{
    require __DIR__ . '/config/conect_pdo.php';
    try {
        $stmt = $connpdo->prepare($sql);
        return ($stmt->execute()) ? true : false;
    } catch (\Throwable $th) { // si hay error en la consulta
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorPdoQuery.log'; // ruta del archivo de Log de errores
        error_log($th->getMessage()); // escribir en el log de errores el error
        fileLog($th->getMessage(), $pathLog); // escribir en el log de errores el error
    }
    $stmt = null;
}
function rowCount_pdoQuery(string $sql)
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
/**
 * Summary of filtrarObjeto
 * Filtra un array de objetos por una clave y valor específico.
 * @param array $array El array de objetos a filtrar.
 * @param string $key La clave por la cual filtrar.
 * @param mixed $valor El valor que debe tener la clave para ser incluido en el resultado.
 * @return object|null El primer objeto que cumpla con el criterio de filtrado, o null si no se encuentra ninguno.
 */
function filtrarObjeto(array $array, string $key, $valor) // Función para filtrar un objeto
{
    $r = array_filter($array, function ($e) use ($key, $valor) {
        return $e[$key] == $valor;
    });
    foreach ($r as $k => $value) {
        return ($value);
    }
}
/**
 * Summary of filtrarObjetoArr
 * Filtra un array de objetos por una clave y valor específico.
 * @param array $array
 * @param string $key
 * @param mixed $valor
 * @return array
 */
function filtrarObjetoArr(array $array, string $key, $valor) // Función para filtrar un objeto
{
    $r = array_filter($array, function ($e) use ($key, $valor) {
        return $e[$key] == $valor;
    });
    foreach ($r as $k => $value) {
        $v[] = $value;
    }
    return $v ?? [];
}
/**
 * Summary of filtrarObjetoArr2
 * Filtra un array de objetos por dos claves y valores específicos.
 * @param array $array
 * @param string $key
 * @param string $key2
 * @param mixed $valor
 * @param mixed $valor2
 * @return array
 */
function filtrarObjetoArr2(array $array, string $key, string $key2, $valor, $valor2) // Función para filtrar un objeto 
{
    $a = [];
    if ($array && $key && $key2 && $valor && $valor2) {
        foreach ($array as $k => $v) {
            if ($v[$key] == $valor && $v[$key2] == $valor2) {
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
/**
 * Summary of tipoAud
 * Devuelve una descripción legible del tipo de auditoría basado en un código.
 * @param mixed $tipo
 * @return string
 */
function tipoAud($tipo)
{
    $tipo = match ($tipo) {
        'P' => 'Proceso',
        'M' => 'Modificación',
        'A' => 'Alta',
        'B' => 'Baja',
        default => '',
    };
    return $tipo;
}
/**
 * Summary of login_logs
 * Registra los intentos de inicio de sesión en la base de datos.
 * @param mixed $estado Estado del login (1: correcto, 2: incorrecto)
 * @param mixed $usuario Nombre de usuario (opcional, se obtiene de POST si no se proporciona)
 * @param mixed $estado
 * @param mixed $usuario
 */
function login_logs($estado, $usuario = null)
{
    // estado = 1: Login correcto; 2: Login incorrecto
    require __DIR__ . '/config/conect_pdo.php'; //Conexion a la base de datos
    $connpdo->beginTransaction();
    try {
        $sql = 'INSERT INTO login_logs(usuario,uid,estado,rol,cliente,ip,agent,fechahora) VALUES(:usuario, :uid, :estado, :rol, :cliente, :ip, :agent, :fechahora)';
        $stmt = $connpdo->prepare($sql); // prepara la consulta

        $usuario = ($usuario === null) ? filter_input(INPUT_POST, 'user', FILTER_DEFAULT) : $usuario;

        $data = [
            // array asociativo con los parametros a pasar a la consulta preparada (:usuario, :uid, :estado, :rol, :cliente, :ip, :agent, :fechahora)
            'usuario' => $usuario,
            'uid' => !empty($_SESSION["UID"]) ? $_SESSION["UID"] : 0,
            'estado' => $estado,
            'rol' => !empty($_SESSION["ID_ROL"]) ? $_SESSION["ID_ROL"] : 0,
            'cliente' => !empty($_SESSION["ID_CLIENTE"]) ? $_SESSION["ID_CLIENTE"] : 0,
            'ip' => ($_SERVER['REMOTE_ADDR'] == '::1') ? ip2long('127.0.0.1') : ip2long($_SERVER['REMOTE_ADDR']),
            'agent' => $_SERVER['HTTP_USER_AGENT'],
            'fechahora' => fechaHora2()
        ];

        $stmt->bindParam(':usuario', $data['usuario']);
        $stmt->bindParam(':uid', $data['uid'], PDO::PARAM_INT);
        $stmt->bindParam(':estado', $data['estado'], PDO::PARAM_INT); // 1: Login correcto; 2: Login incorrecto
        $stmt->bindParam(':rol', $data['rol'], PDO::PARAM_INT);
        $stmt->bindParam(':cliente', $data['cliente'], PDO::PARAM_INT);
        $stmt->bindParam(':ip', $data['ip']);
        $stmt->bindParam(':agent', $data['agent']);
        $stmt->bindParam(':fechahora', $data['fechahora']);
        if ($stmt->execute()) { // ejecuta la consulta
            $_SESSION['UID'] ??= '';
            $_SESSION['ID_SESION'] = $connpdo->lastInsertId();
            $message = "Sesion correcta \"($_SESSION[UID]) $data[usuario]\""; // mensaje de éxito
            if ($_SERVER['SERVER_NAME'] == 'localhost') { // Si es localhost
                $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_successSesion.log'; // ruta del archivo de log
                fileLog($message, $pathLog); // escribir en el log de errores
            }
        }
        $connpdo->commit(); // si todo salio bien, confirma la transacción
    } catch (\Throwable $th) { // si hay error
        $connpdo->rollBack(); // revierte la transacción
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorLogSesion.log'; // ruta del archivo de Log
        error_log($th->getMessage()); // escribir en el log de errores
        fileLog($th->getMessage(), $pathLog); // escribir en el log de errores
    }
    $connpdo = null; // cierra la conexion
}
/**
 * Escapa los caracteres especiales de SQL para su uso en consultas con comodines.
 * @param mixed $s
 * @return string
 */
function escape_sql_wild($s)
{
    $result = [];
    foreach (str_split($s) as $ch) {
        if ($ch == "\\" || $ch == "%" || $ch == "_") {
            $result[] = "\\";
        } /*if*/
        $result[] = $ch;
    } /*foreach*/
    return
        implode("", $result);
}
/**
 * Genera un archivo de claves API para las aplicaciones móviles.
 * @return bool|int Retorna false si no se puede abrir el archivo, o el número de bytes escritos si tiene éxito.
 * @return bool|int
 */
function write_apiKeysFile()
{
    $q = "SELECT `c`.`host` AS 'hostDB', `c`.`user` AS 'userDB',`c`.`pass` AS 'passDB', `c`.`db` AS 'DB', `c`.`auth` AS 'authDB', `c`.`id` as 'idCompany', `c`.`nombre` as 'nameCompany', `c`.`recid` as 'recidCompany', 'key' as 'key', `c`.`urlAppMobile` AS 'urlAppMobile', `c`.`localCH` as 'localCH', (SELECT `valores` FROM `params` `p` WHERE `p`.`modulo` = 1 AND `p`.`descripcion` = 'host' AND `p`.`cliente` = `c`.`id` LIMIT 1) AS 'hostCHWeb', `c`.`WebService` AS 'WebService', `c`.`ApiMobileHRP` AS 'apiMobileHRP' FROM `clientes` `c`";
    $assoc_arr = array_pdoQuery($q);
    // $assoc = $assoc_arr;

    foreach ($assoc_arr as $key => $value) {
        $assoc[] = [
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
        ];
    }
    $content = "; <?php exit; ?> <-- ¡No eliminar esta línea! --> \n";
    foreach ($assoc ?? [] as $key => $elem) {
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
function getIniCuenta(string $recidCuenta, $key = false)
{
    $urlHost = '';
    $d = (getDataIni(__DIR__ . '/mobileApikey.php'));
    foreach ($d as $k => $value) {
        if ($value['recidCompany'] == $recidCuenta) {
            $urlHost = [$k => $value];
            return $urlHost[$k];
        }
    }
    return ($key) ? $urlHost[0][intval($key)] : $urlHost[0];
}
/**
 * Summary of getDataIni
 * Obtiene los datos de un archivo INI y los devuelve como un array asociativo.
 * Si el archivo no existe o está vacío, retorna false.
 * @param mixed $url
 * @return array|bool
 */
function getDataIni(string $url) // obtiene el json de la url
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
    return false; // Retornar false si ocurre algún error o el archivo está vacío
}
function gethostCHWeb_old()
{
    $token = sha1(($_SESSION['RECID_CLIENTE'] ?? ''));
    if (!file_exists(__DIR__ . '/mobileApikey.php')) {
        return false;
    }
    $iniData = getDataIni(__DIR__ . '/mobileApikey.php');
    foreach ($iniData as $v) {
        if ($v['Token'] == $token) {
            $data = [$v];
            return $data[0]['hostCHWeb'];
        }
    }
}
function getHomeHost_old()
{
    $token = sha1(($_SESSION['RECID_CLIENTE'] ?? ''));
    if (!file_exists(__DIR__ . '/mobileApikey.php')) {
        return false;
    }
    $iniData = getDataIni(__DIR__ . '/mobileApikey.php');
    foreach ($iniData as $v) {
        if ($v['Token'] == $token) {
            $data = [$v];
            return $data[0]['homeHost'];
        }
    }
}
/**
 * Busca en mobileApikey.php el registro que coincide con el token del cliente
 * y retorna el valor de la clave solicitada.
 *
 * @param  string      $key  Clave a retornar ('hostCHWeb', 'homeHost', etc.)
 * @return string|false      El valor, o false si no se encontró
 */
function getClientHostData(string $key)
{
    $apiKeyFile = __DIR__ . '/mobileApikey.php';

    if (!file_exists($apiKeyFile)) {
        return false;
    }

    $token = sha1((string) ($_SESSION['RECID_CLIENTE'] ?? ''));
    $iniData = getDataIni($apiKeyFile);

    foreach ($iniData as $entry) {
        if (($entry['Token'] ?? '') === $token) {
            return $entry[$key] ?? false;
        }
    }

    return false;
}

function gethostCHWeb()
{
    return getClientHostData('hostCHWeb');
}

function getHomeHost()
{
    return getClientHostData('homeHost');
}
/**
 * Summary of padLeft
 * Rellena una cadena a la izquierda hasta alcanzar la longitud especificada con el carácter dado.
 * @param mixed $str
 * @param mixed $length
 * @param mixed $pad
 * @return string
 */
function padLeft($str, $length, $pad = ' ')
{
    return str_pad($str, $length, $pad, STR_PAD_LEFT);
}

function _group_by_keys(array $array, array $keys = [])
{
    if ($array) {
        $return = [];
        $append = (count($keys) > 1) ? "_" : null;
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

        foreach (($arrGroup2 ?? []) as $key => $value2) {
            $arrGroup3[] = $value2[0] ?? [];
        }
    } else {
        $arrGroup3[] = [];
    }
    return $arrGroup3 ?? [];
}
function pingApiMobileHRP(string $urlAppMobile)
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
    unset($ch);
    return ($file_contents) ? $file_contents : false;
    // exit;
}
/**
 * Summary of sendApiMobileHRP
 * Envía una solicitud a la API móvil HRP con los datos proporcionados.
 * @param mixed $payload Los datos a enviar en la solicitud.
 * @param mixed $urlApp La URL base de la aplicación móvil HRP.
 * @param mixed $paramsUrl Los parámetros de la URL para la solicitud.
 * @param mixed $idCompany El ID de la empresa.
 * @param mixed $post Indica si la solicitud es POST (true) o GET (false).
 * @param mixed $payload
 * @param mixed $urlApp
 * @param mixed $paramsUrl
 * @param mixed $idCompany
 * @param mixed $post
 * @return bool|string
 */
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
    unset($ch);
    return ($file_contents) ? $file_contents : false;
}
/**
 * Summary of confidenceFaceStr
 * Devuelve una cadena descriptiva basada en el valor de confianza, el ID de la API y el umbral proporcionados.
 * @param mixed $confidence El valor de confianza obtenido.
 * @param mixed $id_api El ID de la API utilizada.
 * @param mixed $threshold El umbral de confianza para determinar si la identificación es válida.
 * @return string Una cadena descriptiva que indica el estado de la identificación.
 * @return string
 */
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
/**
 * Summary of access_log
 * @param mixed $Modulo
 * @return string
 */
function access_log($Modulo)
{
    return '';
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        timeZone();
        $pathLog = __DIR__ . '/logs/access/' . date('Ymd') . '_access_log.log'; // ruta del archivo de log
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
        exit;
    }
    return '';
}
/**
 * Summary of access_log_proy
 * Envía un registro de acceso para la aplicación de proyecto.
 * @param mixed $Modulo
 * @return string
 */
function access_log_proy($Modulo)
{
    return '';
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        timeZone();
        $pathLog = __DIR__ . '/logs/access/' . date('Ymd') . '_access_log.log'; // ruta del archivo de log
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

use Carbon\Carbon;

/**
 * Summary of tareDiff
 * @param mixed $start
 * @param mixed $end
 * @return string La diferencia de tiempo en formato legible.
 */
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
/**
 * Summary of diffStartEnd
 * Calcula la diferencia entre dos fechas y devuelve información detallada sobre la duración y la diferencia inicial.
 * @param mixed $start
 * @param mixed $end
 * @return array{diffInMinutes: int, diffInSeconds: int, diffIni: string, duration: string}
 */
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
/**
 * Summary of implode_keys_values
 * Combina los valores únicos de una columna específica de un array en una cadena separada por un separador.
 * @param mixed $key La clave de la columna a combinar.
 * @param mixed $separator El separador a utilizar entre los valores.
 * @param mixed $array El array de entrada.
 * @return string La cadena resultante con los valores combinados.
 */
function implode_keys_values($key, $separator, $array = [])
{
    if (!$array):
        return $array;
    endif;
    $key = array_column($array, $key);
    $key = array_unique($key);
    $key = implode($separator, $key);
    return $key;
}
/**
 * Summary of simple_MSQuery
 * Ejecuta una consulta simple en Microsoft SQL Server y devuelve el primer registro encontrado o false si no hay resultados.
 * @param mixed $query
 * @return array|bool
 */
function simple_MSQuery($query)
{
    $params = [];
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
/**
 * Summary of MSQuery
 * Ejecuta una consulta en Microsoft SQL Server y devuelve true si se ejecuta correctamente, o false en caso de error.
 * @param mixed $query La consulta SQL a ejecutar.
 * @return bool Verdadero si la consulta se ejecuta correctamente, falso en caso de error.
 */
function MSQuery($query)
{
    $params = [];
    $options = ["Scrollable" => SQLSRV_CURSOR_KEYSET];
    require __DIR__ . '/config/conect_mssql.php';
    $stmt = sqlsrv_query($link, $query, $params, $options);
    if ($stmt) {
        return true;
    } else {
        if (($errors = sqlsrv_errors()) != null) {
            foreach ($errors as $error) {
                $mensaje = explode(']', $error['message']);
                $data[] = ["status" => "error", "dato" => $mensaje[3]];
            }
        }
        error_log(json_encode($data[0]));
        sqlsrv_free_stmt($stmt ?? null);
        echo json_encode($data[0]);
        sqlsrv_close($link);
        return false;
        // exit;
    }
}
function arrMSQuery(string $query, array $opt = [])
{

    static $link = null;
    static $connectionCount = 0;
    static $queryCount = 0;

    $stmt = null;

    $insert = $opt['insert'] ?? false;
    $update = $opt['update'] ?? false;

    try {

        if ($link === null) {
            // Solo log cuando se crea la conexión
            // error_log('[MSSQL CACHE] Nueva conexión creada (#' . ($connectionCount + 1) . ')');

            require __DIR__ . '/config/conect_mssql.php';

            if (!$link) {
                throw new Exception('No se pudo establecer la conexión a la base de datos');
            }
            $connectionCount++;
        } else {
            // Log opcional para verificar que usa caché
            // error_log('[MSSQL CACHE] Reutilizando conexión #' . $connectionCount . ' - Consulta #' . ($queryCount + 1));
        }

        $stmt = sqlsrv_query($link, $query);

        // error_log('[MSSQL CACHE] Consulta #' . ($queryCount + 1) . ' ejecutada: ' . $query);
        $queryCount++;

        if ($stmt === false) {
            $errors = sqlsrv_errors();
            $mensaje = $errors[0]['message'] ?? 'Error desconocido en sqlsrv_query';
            throw new Exception($mensaje);
        }

        if ($insert) {
            return true;
        }

        if ($update) {
            return true;
        }

        while ($r = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $registros[] = $r;
        }

        return $registros ?? false;

    } catch (Throwable $e) {
        error_log(
            PHP_EOL . '[MSSQL ERROR] ' . $e->getMessage() .
            PHP_EOL . '[MSSQL ERROR] Query: ' . $query
        );
        throw new Exception($e->getMessage());
    } finally {
        if ($stmt) {
            sqlsrv_free_stmt($stmt);
        }
    }
}

function arrMSQueryData(string $query)
{
    static $link = null;
    static $connectionCount = 0;
    static $queryCount = 0;
    $stmt = null;

    try {
        if ($link === null) {
            require __DIR__ . '/config/conect_mssql.php';

            if (!$link) {
                throw new Exception('No se pudo establecer la conexión a la base de datos');
            }
            $connectionCount++;
        }

        $queryCount++;

        $stmt = sqlsrv_query($link, $query);

        if ($stmt === false) {
            $errors = sqlsrv_errors();
            $mensaje = $errors[0]['message'] ?? 'Error desconocido en sqlsrv_query';
            throw new Exception($mensaje);
        }

        $registros = [];
        while ($r = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $registros[] = $r;
        }

        return $registros;

    } catch (Throwable $e) {
        error_log(
            PHP_EOL . '[MSSQL ERROR] ' . $e->getMessage() .
            PHP_EOL . '[MSSQL ERROR] Query: ' . $query
        );
        throw new Exception($e->getMessage());
    } finally {
        if ($stmt) {
            sqlsrv_free_stmt($stmt);
        }
    }
}
/**
 * Summary of confTar
 * Genera un archivo de configuración de tareas a partir de un array asociativo y lo guarda en la ruta especificada.
 * @param mixed $assoc El array asociativo con la configuración de tareas.
 * @param mixed $path La ruta donde se guardará el archivo de configuración.
 * @return bool|int Devuelve false si no se pudo abrir el archivo, o el número de bytes escritos si se guardó correctamente.
 */
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
    $content .= ";MinimoDesc  : Tiempo Mínimo de horario de descanso.\n";
    $content .= ";LimitTar    : Hora límite para cerrar tareas pendientes. De \"0\" a \"9999\"\n";
    $content .= ";ProcRedTar  : Redondear finalización de tarea. De \"0\" a \"9999\"\n";
    $content .= ";RecRedTar   : Recorte y redondeo de finalización de tarea.";
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
 * Summary of calcLimitTar
 * Calcula el límite de tiempo de una tarea y la diferencia entre el inicio y el fin.
 * @param mixed $start
 * @param mixed $end
 * @return array{confTar: mixed, diffHor: bool|string, diffMin: int, limitHor: bool|string, limitMin: int, status: int}
 */
function calcLimitTar($start, $end)
{
    $getConfTar = ((getConfTar()['confTar'])); // Obtenemos configuración de confTarea.php en una array
    $getLimitTar = (($getConfTar['LimitTar'])); // Obtenemos el límite de tiempo de la tarea en horas
    $limitTar = intval($getLimitTar) * 60; // Convertimos el limite de tiempo a minutos
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
/**
 * Summary of requestApi
 * @param mixed $url
 * @param mixed $token
 * @param mixed $authBasic
 * @param mixed $payload
 * @param mixed $timeout
 * @param mixed $method
 * @return bool|string
 */
function requestApi($url, $token, $authBasic, $payload, $timeout = 10, $method = 'POST')
{

    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_ENCODING, '');

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Establecer que retorne el contenido del servidor
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    // TIMEOUTS CRÍTICOS - Evitar esperas largas
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout); // Timeout de conexión: 2 segundos
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout); // Timeout total de ejecución: 10 segundo
    curl_setopt($ch, CURLOPT_DNS_CACHE_TIMEOUT, 60); // Cache DNS por 60 segundos

    curl_setopt($ch, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, false); // Reutilizar conexiones existentes
    curl_setopt($ch, CURLOPT_FORBID_REUSE, false); // Permitir reutilización de conexiones
    curl_setopt($ch, CURLOPT_MAXREDIRS, 3); // Máximo 3 redirecciones

    // No verificar SSL en desarrollo (comentar en producción si usas HTTPS válido)
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    switch ($method) {
        case 'POST':
            // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTREDIR, CURL_REDIR_POST_ALL); // Mantener POST y body en 301/302
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            break;
        case "GET":
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            break;
    }

    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        [
            "Accept: */*",
            'Content-Type: application/json',
            "Token: $token",
        ]
    );

    $file_contents = curl_exec($ch);

    $curl_errno = curl_errno($ch); // get error code
    $curl_error = curl_error($ch); // get error information

    $pathLog = __DIR__ . '/' . date('Ymd') . '_errorCurl.log'; // ruta del archivo de Log de errores

    if ($curl_errno > 0) { // si hay error
        $text = "cURL Error ($curl_errno): $curl_error"; // set error message
        fileLog($text, $pathLog); // escribir en el log de errores el error
    }
    unset($ch);

    if ($file_contents) {
        return $file_contents;
    } else {
        fileLog('Error al obtener datos', $pathLog); // escribir en el log de errores el error
        return false;
    }
}
/**
 * Summary of horarioApi
 * @param mixed $ent
 * @param mixed $sal
 * @param mixed $labo
 * @param mixed $Feri
 * @return string
 * 
 * Devuelve el horario de trabajo en formato legible según los parámetros proporcionados.
 */
function horarioApi($ent, $sal, $labo, $Feri)
{
    $h = "$ent a $sal"; // Horario de trabajo
    $h = ($labo == '0') ? 'Franco' : $h;
    $h = ($Feri == '1') ? 'Feriado' : $h;
    $h = ($Feri == '1' && $labo == '1') ? $ent . ' a ' . $sal : $h;
    return $h;
}
/**
 * Summary of checkEnroll
 * Verifica la inscripción de un usuario en la API móvil después de un tiempo de espera especificado.
 * @param mixed $recid
 * @param mixed $userID
 * @param mixed $apimobile
 * @param mixed $sleep
 * @return void
 */
function checkEnroll($recid, $userID, $apimobile, $sleep)
{
    sleep($sleep);
    $paramsCheck = array(
        'key' => $recid,
        'userID' => $userID,
    );
    $apiCheck = "api/v1/enroll/check/";
    $urlCheck = "$apimobile/" . HOMEHOST . "/mobile/hrp/" . $apiCheck;
    sendApiData($urlCheck, $paramsCheck, 'POST');
}
/**
 * Summary of FileSizeConvert
 * Convierte bytes en un tamaño de archivo legible por humanos.
 * @param mixed $bytes
 * @return string
 */
function FileSizeConvert($bytes)
{
    $result = '';
    $bytes = floatval($bytes);
    $arBytes = [
        0 => [
            "UNIT" => "TB",
            "VALUE" => pow(1024, 4)
        ],
        1 => [
            "UNIT" => "GB",
            "VALUE" => pow(1024, 3)
        ],
        2 => [
            "UNIT" => "MB",
            "VALUE" => pow(1024, 2)
        ],
        3 => [
            "UNIT" => "KB",
            "VALUE" => 1024
        ],
        4 => [
            "UNIT" => "B",
            "VALUE" => 1
        ],
    ];

    foreach ($arBytes as $arItem) {
        if ($bytes >= $arItem["VALUE"]) {
            $result = $bytes / $arItem["VALUE"];
            $result = str_replace(".", ",", strval(round($result, 2))) . " " . $arItem["UNIT"];
            break;
        }
    }
    return $result;
}
/**
 * Summary of get_client_ip
 * Obtiene la dirección IP del cliente.
 * @return string
 */
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
/**
 * Summary of setParamsOrSession
 * Obtiene los parámetros de la solicitud o de la sesión.
 * @param mixed $params
 * @param mixed $session
 * @return array
 */
function setParamsOrSession($params, $session)
{
    if ($params) {
        return $params;
    }

    if (empty($session)) {
        return [];
    }

    return explodeSession($session);
}
/**
 * Summary of explodeSession
 * Convierte una cadena de sesión separada por comas en un array.
 * @param string $session
 * @return array
 */
function explodeSession($session)
{
    if (empty($session)) {
        return [];
    }

    return explode(',', $session);
}
/**
 * Summary of actualizar_cache
 * Actualiza la caché para un endpoint dado.
 * @param string $endpoint
 * @throws Exception
 */
function actualizar_cache(string $endpoint)
{
    try {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);

        curl_exec($ch);

        $curl_errno = curl_errno($ch); // get error code
        $curl_error = curl_error($ch); // get error information
        if ($curl_errno > 0) { // si hay error
            $text = "cURL Error ($curl_errno): $curl_error"; // set error message
            throw new Exception($text);
        }
        unset($ch);
    } catch (Exception $e) {
        filelog($e->getMessage(), 'error.log', false);
    }
}
/**
 * Summary of createDateRangeArray
 * @param mixed $strDateFrom
 * @param mixed $strDateTo
 * @return array
 */
function createDateRangeArray($strDateFrom, $strDateTo)
{
    //Crea un array de fechas (excluyendo el fin y el inicio)
    $aryRange = [];

    $iDateFrom = mktime(1, 0, 0, substr($strDateFrom, 5, 2), substr($strDateFrom, 8, 2), substr($strDateFrom, 0, 4));
    $iDateTo = mktime(1, 0, 0, substr($strDateTo, 5, 2), substr($strDateTo, 8, 2), substr($strDateTo, 0, 4));

    if ($iDateTo >= $iDateFrom) {
        array_push($aryRange, date('Y-m-d', $iDateFrom)); // first entry
        while ($iDateFrom < $iDateTo) {
            $iDateFrom += 86400; // add 24 hours
            array_push($aryRange, date('Y-m-d', $iDateFrom));
        }
    }
    return $aryRange;
}
function OS()
{
    $os = strtolower(PHP_OS);
    if (strpos($os, 'win') !== false) {
        return 'windows';
    } elseif (strpos($os, 'linux') !== false) {
        return 'linux';
    } elseif (strpos($os, 'darwin') !== false) {
        return 'mac';
    }
    return 'unknown';
}
/**
 * Summary of auth_ad
 * Autentica un usuario contra Active Directory usando los datos proporcionados.
 * @param mixed $passLogin
 * @param mixed $row
 * @return bool
 */
function auth_ad($passLogin, $row)
{
    $_SESSION['RECID_CLIENTE'] = $row['recid_cliente'] ?? '';
    $url = host() . '/' . HOMEHOST . '/app-data/_local/login_ad';

    $data = [
        'id_cliente' => $row['id_cliente'] ?? '',
        'recid_cliente' => $row['recid_cliente'] ?? '',
        'serviceUserAD' => $row['usuario'] ?? '',
        'servicePassAD' => $passLogin
    ];

    $options = [
        'http' => [
            'header' => "Content-Type: application/json\r\n",
            'method' => 'POST',
            'content' => json_encode($data),
            'timeout' => 10, // segundos
            'ignore_errors' => true // Para poder leer respuestas de error
        ],
        'ssl' => [
            'verify_peer' => false, // Solo para desarrollo local
            'verify_peer_name' => false
        ]
    ];

    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        // Error en la petición
        $pathLog = __DIR__ . '/../logs/' . date('Ymd') . '_errorAD.log';
        fileLog("Error al conectar con AD: " . error_get_last()['message'], $pathLog);
    } else {
        $responseData = json_decode($response, true);
        // Verificar si la autenticación AD fue exitosa
        if (isset($responseData['RESPONSE_CODE']) && $responseData['RESPONSE_CODE'] === '200 OK') {
            return true;
        } else {
            return false;
        }
    }
    // Si llegamos aquí, significa que hubo un error en la autenticación AD
    return false;
}

$LABEL_ESTRUCT = function ($struct, $plural = false) {
    $labels = $_SESSION['LABELS'] ?? [];
    if (empty($labels))
        return '';

    $map = [
        'Empr' => ['EmprSin', 'EmprPlu'],
        'Plan' => ['PlanSin', 'PlanPlu'],
        'Sucu' => ['SucuSin', 'SucuPlu'],
        'Grup' => ['GrupSin', 'GrupPlu'],
        'Sect' => ['SectSin', 'SectPlu'],
        'Secc' => ['SeccSin', 'SeccPlu'],
    ];
    $mapDefault = [
        'Empr' => [
            0 => 'Empresas',
            1 => 'Empresa',
        ],
        'Plan' => [
            0 => 'Plantas',
            1 => 'Planta'
        ],
        'Sucu' => [
            0 => 'Sucursales',
            1 => 'Sucursal'
        ],
        'Grup' => [
            0 => 'Grupos',
            1 => 'Grupo'
        ],
        'Sect' => [
            0 => 'Sectores',
            1 => 'Sector'
        ],
        'Secc' => [
            0 => 'Secciones',
            1 => 'Sección'
        ],
    ];

    if (!isset($map[$struct]))
        return '';

    $etiqueta = $labels[$map[$struct][(int) $plural]] ?: $mapDefault[$struct][(int) $plural];
    return $etiqueta;
};
function labelEstruct(string $struct, bool $plural = false): string
{
    $labels = $_SESSION['LABELS'] ?? [];
    if (empty($labels))
        return '';

    $map = [
        'Empr' => ['EmprSin', 'EmprPlu'],
        'Plan' => ['PlanSin', 'PlanPlu'],
        'Sucu' => ['SucuSin', 'SucuPlu'],
        'Grup' => ['GrupSin', 'GrupPlu'],
        'Sect' => ['SectSin', 'SectPlu'],
        'Secc' => ['SeccSin', 'SeccPlu'],
    ];
    $mapDefault = [
        'Empr' => [
            1 => 'Empresas',
            0 => 'Empresa',
        ],
        'Plan' => [
            1 => 'Plantas',
            0 => 'Planta'
        ],
        'Sucu' => [
            1 => 'Sucursales',
            0 => 'Sucursal'
        ],
        'Grup' => [
            1 => 'Grupos',
            0 => 'Grupo'
        ],
        'Sect' => [
            1 => 'Sectores',
            0 => 'Sector'
        ],
        'Secc' => [
            1 => 'Secciones',
            0 => 'Sección'
        ],
    ];

    if (!isset($map[$struct]))
        return '';

    $etiqueta = $labels[$map[$struct][(int) $plural]] ?: $mapDefault[$struct][(int) $plural];
    return $etiqueta;
}
function api_internal_base_url(): string
{
    return OS() === 'linux' ? 'http://localhost' : $_SESSION['HOST_CHWEB'] ?? '';
}

function redirectToLogin(): void
{
    $hostCHWeb = host();
    $homeHost = HOMEHOST;

    if (empty($hostCHWeb) || empty($homeHost)) {
        error_log('redirectToLogin: no se pudo resolver el host del cliente.');
        exit;
    }

    $host = "{$hostCHWeb}/{$homeHost}";
    $trace = buildTraceContext();
    $referer = sanitizeReferer($_SERVER['HTTP_REFERER'] ?? '');
    $location = "{$host}/login/";
    // error_log($referer);

    if ($referer) {
        $location .= '?l=' . urlencode($referer);
    }

    session_destroy();
    sleep(1); // Pequeña pausa para asegurar que la sesión se cierre antes de redirigir
    // error_log(buildRedirectLogMessage($trace, $referer));
    header('location:' . $location);
    exit;
}
/**
 * Extrae los datos relevantes del backtrace.
 *
 * @return array{file: string, line: string, callerFile: string, callerLine: string, callerFn: string}
 */
function buildTraceContext(): array
{
    $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);

    return [
        'file' => $trace[1]['file'] ?? 'unknown file',
        'line' => $trace[1]['line'] ?? 'unknown line',
        'callerFile' => $trace[2]['file'] ?? 'unknown file',
        'callerLine' => $trace[2]['line'] ?? 'unknown line',
        'callerFn' => $trace[2]['function'] ?? 'unknown function',
    ];
}

/**
 * @param array{file: string, line: string, callerFile: string, callerLine: string, callerFn: string} $trace
 */
function buildRedirectLogMessage(array $trace, string $referer = ''): string
{
    return sprintf(
        'Redirigiendo a login desde %s on line %s (called from %s on line %s in function %s()) Referer: %s IP: %s',
        $trace['file'],
        $trace['line'],
        $trace['callerFile'],
        $trace['callerLine'],
        $trace['callerFn'],
        $referer,
        $ip = get_client_ip()
    );
}

/**
 * Devuelve la URL saneada o cadena vacía si no es válida.
 */
function sanitizeReferer(string $referer): string
{
    if ($referer === '') {
        return '';
    }

    // FILTER_SANITIZE_URL está deprecado en 8.1+; validamos directo.
    $sanitized = filter_var($referer, FILTER_SANITIZE_URL);

    // Doble chequeo: que sea una URL estructuralmente válida.
    if ($sanitized === false || filter_var($sanitized, FILTER_VALIDATE_URL) === false) {
        return '';
    }

    // si $sanitized contiene login lo retornamos vacío para evitar loops de redirección
    if (stripos($sanitized, 'login') !== false) {
        return '';
    }

    return $sanitized;
}
function loadModulos(): array
{
    $path = __DIR__ . '/config/modulos.php';

    if (!file_exists($path)) {
        error_log("Archivo de módulos no encontrado: {$path}");
        return [];
    }

    $modulos = require $path;

    if (!is_array($modulos)) {
        error_log("El archivo de módulos no devuelve un array: {$path}");
        return [];
    }
    return $modulos;
}
function updateModulosCache(): void
{
    try {
        $pathModulos = __DIR__ . '/config/modulos.php';

        $dataMod = array_pdoQuery("SELECT nombre, descripcion FROM modulos") ?? [];
        $dataMod = array_column($dataMod, 'descripcion', 'nombre');

        $normalized = [];

        foreach ($dataMod as $key => $value) {
            $newKey = removeAccents((string) $key);
            $newKey = str_replace(' ', '_', strtolower($newKey));
            $normalized[$newKey] = $value;
        }

        $phpText = '<?php' . PHP_EOL . PHP_EOL . 'return ' . var_export($normalized, true) . ';' . PHP_EOL;
        file_put_contents($pathModulos, $phpText);
    } catch (\Throwable $th) {
        error_log("Error actualizando cache de módulos: " . $th->getMessage());
    }
}
function removeAccents(string $str): string
{
    if (function_exists('transliterator_transliterate')) {
        return transliterator_transliterate('Any-Latin; Latin-ASCII', $str);
    }

    $from = [
        'á',
        'é',
        'í',
        'ó',
        'ú',
        'à',
        'è',
        'ì',
        'ò',
        'ù',
        'ä',
        'ë',
        'ï',
        'ö',
        'ü',
        'â',
        'ê',
        'î',
        'ô',
        'û',
        'ã',
        'õ',
        'ñ',
        'ç',
        'ý',
        'ÿ',
        'Á',
        'É',
        'Í',
        'Ó',
        'Ú',
        'À',
        'È',
        'Ì',
        'Ò',
        'Ù',
        'Ä',
        'Ë',
        'Ï',
        'Ö',
        'Ü',
        'Â',
        'Ê',
        'Î',
        'Ô',
        'Û',
        'Ã',
        'Õ',
        'Ñ',
        'Ç',
        'Ý'
    ];

    $to = [
        'a',
        'e',
        'i',
        'o',
        'u',
        'a',
        'e',
        'i',
        'o',
        'u',
        'a',
        'e',
        'i',
        'o',
        'u',
        'a',
        'e',
        'i',
        'o',
        'u',
        'a',
        'o',
        'n',
        'c',
        'y',
        'y',
        'A',
        'E',
        'I',
        'O',
        'U',
        'A',
        'E',
        'I',
        'O',
        'U',
        'A',
        'E',
        'I',
        'O',
        'U',
        'A',
        'E',
        'I',
        'O',
        'U',
        'A',
        'O',
        'N',
        'C',
        'Y'
    ];

    return str_replace($from, $to, $str);
}
function validarTablaSeccion(string $recid_cliente)
{
    try {

        if (!$recid_cliente) {
            throw new InvalidArgumentException("El parámetro recid_cliente no puede estar vacío.");
        }

        $_GET['_c'] = $recid_cliente ?? '';

        $dt = new \DateTimeImmutable('now', new \DateTimeZone('America/Argentina/Buenos_Aires'));
        $FechaHora = substr($dt->format('Y-m-d\TH:i:s.u'), 0, 23);

        $query = "INSERT INTO SECCION (SecCodi, Se2Codi, Se2Desc, FechaHora) SELECT s.SecCodi, '0', '', '$FechaHora' FROM SECTORES s WHERE NOT EXISTS (SELECT 1 FROM SECCION sc WHERE sc.SecCodi = s.SecCodi);";
        require __DIR__ . '/config/conect_mssql.php';
        $stmt = sqlsrv_query($link, $query, [], ["Scrollable" => SQLSRV_CURSOR_KEYSET]);
        if ($stmt === false) {
            if (($errors = sqlsrv_errors()) != null) {
                foreach ($errors as $error) {
                    $mensaje = explode(']', $error['message']);
                    $data[] = ["status" => "error", "dato" => $mensaje[3]];
                }
            }
            sqlsrv_close($link);
            throw new RuntimeException("Error al ejecutar la consulta: " . json_encode($data[0]));
        }
        sqlsrv_free_stmt($stmt ?? null);
        sqlsrv_close($link);
    } catch (\Throwable $th) {
        error_log($th->getMessage());
    }
}
function iconEncabezados(string $modulo)
{

    $iconInformes = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="#e3e3e3" viewBox="0 -960 960 960"><path d="M280-280h280v-80H280zm0-160h400v-80H280zm0-160h400v-80H280zm-80 480q-33 0-56.5-23.5T120-200v-560q0-33 23.5-56.5T200-840h560q33 0 56.5 23.5T840-760v560q0 33-23.5 56.5T760-120zm0-80h560v-560H200zm0-560v560z"/></svg>';
    $iconInformesBlack = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 -960 960 960"><path d="M300-300h240v-40H300zm0-160h360v-40H300zm0-160h360v-40H300zm-75.38 460q-27.62 0-46.12-18.5T160-224.62v-510.76q0-27.62 18.5-46.12t46.12-18.5h510.76q27.62 0 46.12 18.5t18.5 46.12v510.76q0 27.62-18.5 46.12T735.38-160zm0-40h510.76q9.24 0 16.93-7.69t7.69-16.93v-510.76q0-9.24-7.69-16.93T735.38-760H224.62q-9.24 0-16.93 7.69T200-735.38v510.76q0 9.24 7.69 16.93t16.93 7.69M200-760v560z"/></svg>';

    $mapModulos = [
        'informes' => $iconInformes,
        'informesBlack' => $iconInformesBlack,
    ];

    return $mapModulos[$modulo] ?? '';
}
/**
 * Safely reduces an array using a callback function, returning an initial value if the array is empty.
 * @param array $array
 * @param mixed $callback
 * @param mixed $initial
 */
function array_reduce_safe(array $array, $callback, $initial = [])
{
    if (!is_array($array) || empty($array)) {
        return $initial;
    }
    return array_reduce($array, $callback, $initial);
}

/**
 * Ejecuta un SELECT parametrizado y devuelve la primera fila como array
 * asociativo, o null si no hay resultados.
 *
 * Sustituye a simple_pdoQuery() cuando necesites parámetros.
 *
 * @param string $sql    SQL con placeholders (? o :nombre).
 * @param array  $params Parámetros a bindear.
 * @return array|null
 */
function pdoQuerySeguro(string $sql, array $params = [], $fetchAll = false): ?array
{
    require __DIR__ . '/config/conect_pdo.php';

    try {
        $stmt = $connpdo->prepare($sql);
        $stmt->execute($params);
        $row = $fetchAll ? $stmt->fetchAll(PDO::FETCH_ASSOC) : $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $row === false ? null : $row;
    } catch (\Throwable $th) {
        $pathLog = __DIR__ . '/logs/' . date('Ymd') . '_errorPdoQuery.log';
        error_log($th->getMessage());
        return null;
    }
}

function pdoExec(string $sql, array $params = []): bool
{
    require __DIR__ . '/config/conect_pdo.php';
    try {
        $stmt = $connpdo->prepare($sql);
        foreach ($params as $i => $v) {
            $tipo = match (true) {
                is_int($v) => PDO::PARAM_INT,
                is_bool($v) => PDO::PARAM_BOOL,
                $v === null => PDO::PARAM_NULL,
                default => PDO::PARAM_STR,
            };
            // $i+1 para placeholders posicionales (?), o $i+1 si usas :nombre con array indexado
            $stmt->bindValue(is_int($i) ? $i + 1 : $i, $v, $tipo);
        }
        return $stmt->execute();
    } catch (\PDOException $th) {
        error_log("SQL: $sql | " . $th->getMessage());
        throw new Exception($th->getMessage(), (int) $th->getCode(), $th);
    }
}
/**
 * Summary of esRutaLocal
 * Determina si una URL es local al servidor actual.
 * Considera rutas relativas que comienzan con / y URLs absolutas con el mismo host.
 * @param string $url
 * @return bool
 */
function esRutaLocal(string $url): bool
{
    if ($url === '') {
        return false;
    }

    // Protocol-relative //evil.com
    if (str_starts_with($url, '//')) {
        return false;
    }

    // Ruta relativa: debe empezar con / y no contener backslashes
    if (str_starts_with($url, '/')) {
        return !str_contains($url, '\\');
    }

    // URL absoluta: host debe coincidir con el actual
    $parts = parse_url($url);
    if ($parts === false || empty($parts['host'])) {
        return false;
    }

    $hostActual = $_SERVER['HTTP_HOST'] ?? '';

    return strcasecmp($parts['host'], $hostActual) === 0;
}
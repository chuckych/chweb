<?php
// use PhpOffice\PhpSpreadsheet\Worksheet\Row;
function version()
{
    return 'v0.0.235'; // Version de la aplicación
}
function verDBLocal()
{
    return 20220517; // Version de la base de datos local
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
function secure_auth_ch() // Funcion para validar si esta autenticado
{
    timeZone();
    timeZone_lang();
    $_SESSION["secure_auth_ch"] = $_SESSION["secure_auth_ch"] ?? ''; // Si no existe la variable la crea
    if (
        $_SESSION["secure_auth_ch"] !== true // Si no esta autenticado
        || (empty($_SESSION['UID']) || is_int($_SESSION['UID'])) // Si no existe el UID
        || ($_SESSION['IP_CLIENTE'] !== $_SERVER['REMOTE_ADDR']) // Si la IP no es la misma
        || ($_SESSION['USER_AGENT'] !== $_SERVER['HTTP_USER_AGENT']) // Si el USER_AGENT no es el mismo
        || ($_SESSION['DIA_ACTUAL'] !== hoy()) // Si el dia actual no es el mismo
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
        || ($_SESSION['IP_CLIENTE'] !== $_SERVER['REMOTE_ADDR'])
        || ($_SESSION['USER_AGENT'] !== $_SERVER['HTTP_USER_AGENT'])
        || ($_SESSION['DIA_ACTUAL'] !== hoy())
    ) {
        $f = 'Sesión Expirada. Incie sesión nuevamente<br><a class="btn btn-sm fontq btn-info mt-2" href="/' . HOMEHOST . '/login/?l=' . urlencode($_SERVER['HTTP_REFERER']) . '">Iniciar sesión</a>';
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
        || ($_SESSION['IP_CLIENTE'] !== $_SERVER['REMOTE_ADDR'])
        || ($_SESSION['USER_AGENT'] !== $_SERVER['HTTP_USER_AGENT'])
        || ($_SESSION['DIA_ACTUAL'] !== hoy())
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
function ultimoacc() // Funcion para obtener la fecha hora del ultimo acceso
{
    return $_SESSION["ultimoAcceso"] = date("Y-m-d H:i:s"); // Actualizo la fecha de la sesión
}
/** Seguridad injections SQL */
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
        // return version();
    } else {
        return version();
    }
}
function API_KEY_MAPS()
{
    return 'AIzaSyCFs9lj9k7WZAyuwzDJwOiSiragUA9Xwg0';
}
// define("SQLSRV_CURSOR_KEYSET", '');
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
function GeneraClave() // Genera una contraseña aleatoria
{
    $cadena0 = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $cadena1 = "12345678901234567890";
    $cadena2 = "abcdefghijklmnopqrstuvwxyz";
    $cadena = ($cadena0) . ($cadena1) . ($cadena2); //concatenamos las cadenas
    $longitudCadena = strlen($cadena); //longitud de la cadena
    $pass = ""; //variable para almacenar la contraseña
    $longitudPass = 12; //longitud de la contraseña
    for ($i = 1; $i <= $longitudPass; $i++) { //ciclo para generar la contraseña
        $pos = rand(0, $longitudCadena - 1);
        $pass .= substr($cadena, $pos, 1);
    }
    return $pass; //retornamos la contraseña
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
    $VER_DB_CH = $_SESSION['VER_DB_CH'] ?? '';
    $VER_DB_LOCAL = $_SESSION['VER_DB_LOCAL'] ?? '';

    echo '
    <div class="row text-' . $colortexto . ' ' . $bgc . ' radius-0">
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
            timeZone();
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
    $data = $data ?? '';
    $data = trim($data);
    // $data = stripslashes($data);
    // $data = htmlspecialchars($data);
    $data = utf8str($data);
    $data = htmlspecialchars(stripslashes($data), ENT_QUOTES);
    $data = str_ireplace("script", '', $data);
    // $data = htmlentities($data, ENT_QUOTES);
    return ($data);
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
    timeZone();
    $hoy = date('Y-m-d');
    return rtrim($hoy);
}
function hoyStr()
{
    $hoy = date('Ymd');
    return rtrim($hoy);
}
function FechaString($var)
{
    $date        = date_create($var);
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
    timeZone();
    setlocale(LC_TIME, "spanish");
    $scheduled_day = $Ymd;
    $days = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado'];
    $day = date('w', strtotime($scheduled_day));
    $scheduled_day = "$days[$day] " . date('d/m/Y', strtotime($scheduled_day));
    return $scheduled_day;
}
function DiaSemana_Numero($Ymd)
{
    timeZone();
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
    timeZone();
    setlocale(LC_TIME, "spanish");
    $scheduled_day = $Ymd;
    $days = ['Domingo', 'Lunes', 'Martes', 'Miercoles', 'Jueves', 'Viernes', 'Sábado'];
    $day = date('w', strtotime($scheduled_day));
    $scheduled_day = "$days[$day] <br/>" . date('d.m.Y', strtotime($scheduled_day));
    return $scheduled_day;
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
    $var  = date_format($dato, 'Y-m-d');
    return $var;
}
function FechaFormatH($FechaHora)
{
    $dato = date_create($FechaHora);
    $var  = date_format($dato, "d/m/Y H:i");
    return $var;
}
function HoraFormat($FechaHora, $second = true)
{
    if ($second) {
        $dato = date_create($FechaHora);
        $var  = date_format($dato, "H:i:s");
    } else {
        $dato = date_create($FechaHora);
        $var  = date_format($dato, "H:i");
    }
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
    $dato = date_create($var);
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
    // $Get   = file_get_contents($url);
    /** Traemos el fichero completo de la url */
    // $array = json_decode($Get, TRUE);
    $array = json_decode(getRemoteFile($url), true);
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
    $query = "SELECT clientes.recid as recid, clientes.nombre as nombre FROM clientes WHERE clientes.id >'0' AND clientes.recid='$recid' LIMIT 1";
    require 'config/conect_mysql.php';
    $result = mysqli_query($link, $query);
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) :
            $nombre = $row['nombre'];
        endwhile;
        mysqli_free_result($result);
        mysqli_close($link);
        return $nombre;
    } else {
        header("Location: /" . HOMEHOST . "/usuarios/clientes/");
    }

    // CountRegMayorCeroMySql($query) ? '' : header("Location: /" . HOMEHOST . "/usuarios/clientes/");
    /** redirect */
}
function ExisteRol3($recid, $id)
{
    /** Verificamos el recid de rol para ver si existe. 
     * Sino existe redirigimos a Clientes*/
    $q = "SELECT roles.nombre, roles.id, roles.recid FROM roles WHERE roles.recid='$recid' AND roles.id = '$id' LIMIT 1";
    require 'config/conect_mysql.php';
    $rs = mysqli_query($link, $q);
    if (mysqli_num_rows($rs) > 0) {
        while ($row = mysqli_fetch_assoc($rs)) :
            $nombre = $row['nombre'];
            $id     = $row['id'];
            $recid  = $row['recid'];
        endwhile;
        return array('nombre' => $nombre, 'id' => $id, 'recid' => $recid);
    } else {
        header("Location: /" . HOMEHOST . "/usuarios/clientes/");
        // return false;
    }
    mysqli_free_result($rs);
    mysqli_close($link);
    exit;
}
function ExisteRol4($recid, $id)
{
    /** Verificamos si existe el rol */
    $q = "SELECT 1 FROM roles WHERE roles.recid='$recid' AND roles.id = '$id' LIMIT 1";
    require 'config/conect_mysql.php';
    $rs = mysqli_query($link, $q);
    if (mysqli_num_rows($rs) > 0) {
        return true;
    } else {
        return false;
    }
    mysqli_free_result($rs);
    mysqli_close($link);
    exit;
}
function ExisteUser($cliente_recid, $uid)
{
    /** Verificamos si existe el usuario */
    $q = "SELECT 1 FROM usuarios u INNER JOIN clientes c ON u.cliente=c.id WHERE u.id='$uid' AND c.recid='$cliente_recid' LIMIT 1";
    require 'config/conect_mysql.php';
    $rs = mysqli_query($link, $q);
    if (mysqli_num_rows($rs) > 0) {
        return true;
    } else {
        return false;
    }
    mysqli_free_result($rs);
    mysqli_close($link);
    exit;
}
function ExisteRol2($recid)
{
    /** Verificamos el recid de cliente para ver si existe. 
     * Sino existe redirigimos a Clientes*/
    $q = "SELECT roles.recid as 'recid', roles.nombre as 'nombre', roles.id as 'id_rol' FROM roles WHERE roles.recid='$recid' LIMIT 1";
    require 'config/conect_mysql.php';
    $rs = mysqli_query($link, $q);
    if (mysqli_num_rows($rs) > 0) {
        while ($r = mysqli_fetch_assoc($rs)) :
            $n = array($r['nombre'], $r['id_rol'], $r['recid']);
        endwhile;
        mysqli_free_result($rs);
        mysqli_close($link);
        return $n;
    } else {
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
    $url   = host() . "/" . HOMEHOST . "/data/GetClientes.php?tk=" . token() . "&recid=" . $recid;
    $array = json_decode(getRemoteFile($url), true);
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
    //$json  = file_get_contents($url);
    // $array = getRemoteFile($url);
    $array = json_decode(getRemoteFile($url), true);
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
    $_SESSION['ID_MODULO'] = $modulo;
    define('ID_MODULO', $modulo);
    $r = array_filter($_SESSION["MODS_ROL"], function ($e) {
        return $e['modsrol'] === ID_MODULO;
    });
    $modulo_actual = (filtrarObjeto($_SESSION['MODULOS'], 'id', $modulo))['modulo']; // Nombre del modulo actual
    access_log($modulo_actual);
    if (!$r) {
        header("Location:/" . HOMEHOST . "/");
        exit;
    }
    /** redirect */
}
function existConnMSSQL() // verifica si existe conexion a MSSQL
{
    require_once __DIR__ . '/config/conect_mssql.php'; // conexion a MSSQL
    (!$_SESSION['CONECT_MSSQL']) ? header("Location:/" . HOMEHOST . "/inicio/?e=errorConexionMSSQL") . exit : ''; // si no existe conexion a MSSQL redirigimos al inicio
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
    // $json         = file_get_contents($url);
    // $array        = json_decode($json, TRUE);
    $array = json_decode(getRemoteFile($url), true);
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
    // $json  = file_get_contents($url);
    // $array = json_decode($json, TRUE);
    $array = json_decode(getRemoteFile($url), true);
    $sect_roles = (!$array[0]['error']) ? implode(",", $array[0]['sector']) : '';
    $rol = (!$array[0]['error']) ? "$sect_roles" : "";
    return $rol;
}
function estructura_rol($get_rol, $recid_rol, $e, $data)
{
    $url   = host() . "/" . HOMEHOST . "/data/$get_rol.php?tk=" . token() . "&_r=" . $recid_rol . "&e=" . $e;
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
    $url   = host() . "/" . HOMEHOST . "/data/$get_rol.php?tk=" . token() . "&_r=" . $recid_rol . "&e=" . $e;
    // $json  = file_get_contents($url);
    // $array = json_decode($json, TRUE);
    // print_r($url);exit;
    $array = json_decode(getRemoteFile($url), true);
    $val_roles = (!$array[0]['error']) ? count($array[0][$data]) : '';
    $rol = (!$array[0]['error']) ? "$val_roles" : "";
    return $rol;
}
function count_estructura($_c, $e)
{
    $urls   = host() . "/" . HOMEHOST . "/data/GetEstructura.php?tk=" . token() . "&_c=" . $_c . "&count&e=" . $e;
    // echo $urls.PHP_EOL;
    // CountRegMySql("SELECT modulos.id AS 'id' FROM modulos WHERE modulos.id>'0' AND modulos.estado ='0'");    
    // $jsons  = file_get_contents($urls);
    // $arrays = json_decode($jsons, TRUE);
    $arrays = json_decode(getRemoteFile($urls), true);
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
    $src = "/" . HOMEHOST . "/img/" . $var . ".png?v=" . vjs();
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
                    'Fic'    => '<span class="text-primary fw4 contentd" data-titler="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '">' . $valor['Fic'] . '</span>',
                    'Estado' => $valor['Estado'],
                    'Tipo'   => $valor['Tipo']
                );
                break;
            default:
                $fichada = array(
                    'Fic'    => '<span class="fw4 contentd" data-titler="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '">' . $valor['Fic'] . '</span>',
                    'Estado' => $valor['Estado'],
                    'Tipo'   => $valor['Tipo']
                );
                break;
        }
        switch ($valor['Estado']) {
            case 'Modificada':
                $fichada = array(
                    'Fic'    => '<span class="text-danger fw4 contentd" data-titler="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '" >' . $valor['Fic'] . '</span>',
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
                    'Fic'    => '<span class="text-primary fw4 contentd" data-titler="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '. Fecha de registro: ' . $valor['RegFeRe'] . '">' . $valor['Fic'] . '</span>',
                    'Estado' => $valor['Estado'],
                    'Tipo'   => $valor['Tipo']
                );
                break;
            default:
                $fichada = array(
                    'Fic'    => '<span class="fw4 contentd" data-titler="Fichada: ' . $valor['Tipo'] . '. Estado: ' . $valor['Estado'] . '. Fecha de registro: ' . $valor['RegFeRe'] . '">' . $valor['Fic'] . '</span>',
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
            $fichada = '<span class="text-primary" data-titler="Fichada: ' . $tipo . '. Estado: ' . $estado . '">' . $hora . '</span>';
            break;
        default:
            $fichada =  '<span class="text-dark fw5" data-titler="Fichada: ' . $tipo . '. Estado: ' . $estado . '">' . $hora . '</span>';
            break;
    }
    switch ($estado) {
        case 'Modificada':
            $fichada = '<span class="text-danger fw5" data-titler="Fichada: ' . $tipo . '. Estado: ' . $estado . '" >' . $hora . '</span>';
            break;
        default:
            $fichada =  '<span class="text-dark fw5" data-titler="Fichada: ' . $tipo . '. Estado: ' . $estado . '">' . $hora . '</span>';
            break;
    }
    if ($tipo == 'Manual' && $estado == 'Normal') {
        $fichada = '<span class="text-primary fw5" data-titler="Fichada: ' . $tipo . '. Estado: ' . $estado . '">' . $hora . '</span>';
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
    $AudUser   = substr($_SESSION["NOMBRE_SESION"], 0, 10);
    $ipCliente = substr($ipCliente, 0, 20);
    // $AudTerm   = gethostname();
    $AudTerm   = $ipCliente;
    $AudModu   = 21;
    $FechaHora = fechaHora();
    $AudFech   = fechaHora();
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

    $AudUser   = substr($_SESSION["NOMBRE_SESION"], 0, 10);
    // $AudUser   = substr(ucfirst($usuario[1]), 0, 10);
    // $AudTerm   = gethostname();
    // $AudTerm   = $ipCliente . '-' . recid2(4);
    $ipCliente = substr($ipCliente, 0, 20);
    $AudTerm   = $ipCliente;
    $AudModu   = 21;
    // $FechaHora = date('Ymd H:i:s.u');
    $AudFech   = date('Ymd');
    $AudHora   = date('H:i:s');
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
                $pathLog = __DIR__ . './logs/error/' . date('Ymd') . '_errorQueryMS.log';
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
function auditoria($dato, $tipo, $audcuenta = '', $modulo = '')
{
    timeZone();
    require __DIR__ . '/config/conect_pdo.php'; //Conexion a la base de datos
    $connpdo->beginTransaction();
    try {
        $sql = 'INSERT INTO auditoria( id_sesion, usuario, nombre, cuenta, audcuenta, fecha, hora, tipo, dato, modulo ) VALUES( :id_sesion, :usuario, :nombre, :cuenta, :audcuenta, :fecha, :hora, :tipo, :dato, :modulo )';
        $stmt = $connpdo->prepare($sql); // prepara la consulta
        $data = [
            'id_sesion' => $_SESSION['ID_SESION'],  // $_SESSION['ID_SESION'],
            'usuario'   => ($_SESSION["user"]) ? $_SESSION["user"] : 'Sin usuario',
            'nombre'    => ($_SESSION["NOMBRE_SESION"]) ? $_SESSION["NOMBRE_SESION"] : 'Sin nombre',
            'cuenta'    => ($_SESSION["ID_CLIENTE"]) ? $_SESSION["ID_CLIENTE"]  : '',
            'audcuenta' => ($audcuenta) ? $audcuenta : $_SESSION["ID_CLIENTE"],
            'fecha'     => date("Y-m-d "),
            'hora'      => date("H:i:s"),
            'tipo'      => ($tipo) ? $tipo : 'Null', // a:insert, b:update, m:delete; p: proceso
            'dato'      => ($dato) ? trim($dato) : 'No se especificaron datos',
            'modulo'    => ($modulo) ? $modulo : ''
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
function fecha_min_max_mysql($tabla, $ColFech)
{
    require __DIR__ . '/config/conect_mysql.php';
    $query = "SELECT MIN($ColFech) AS 'min', MAX($ColFech) AS 'max' FROM $tabla";
    // print_r($query);exit;
    $rs = mysqli_query($link, $query);
    while ($r = mysqli_fetch_assoc($rs)) {
        $min = ($r['min'] != null) ? $r['min'] : '';
        $max = ($r['max'] != null) ? $r['max'] : '';
    }
    $array = array(
        'min' => $min,
        'max' => $max
    );
    return $array;
    mysqli_free_result($rs);
    mysqli_close($link);
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
        $num = mysqli_num_rows($stmt);
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
            mysqli_free_result($stmt);
            return true;
        } else {
            mysqli_free_result($stmt);
            return false;
        }
    } else {
        $pathLog = __DIR__ . './logs/error/' . date('Ymd') . '_errorQuery_count.log';
        fileLog($_SERVER['REQUEST_URI'] . "\n" . mysqli_error($link), $pathLog); // escribir en el log
    }
}
function checkKey($fk, $table)
{
    require __DIR__ . '/config/conect_mysql.php';
    // $check_schema = "SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE information_schema.TABLE_CONSTRAINTS.CONSTRAINT_TYPE = 'FOREIGN KEY' AND information_schema.TABLE_CONSTRAINTS.TABLE_SCHEMA = '$db' AND information_schema.TABLE_CONSTRAINTS.TABLE_NAME = $fk";
    $check_schema = "SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME = '$table' AND CONSTRAINT_NAME = '$fk' AND TABLE_SCHEMA = '$db'";
    $stmt = mysqli_query($link, $check_schema);
    // print_r($query); exit;
    if ($stmt) {
        if (mysqli_num_rows($stmt) > 0) {
            mysqli_free_result($stmt);
            return true;
        } else {
            mysqli_free_result($stmt);
            return false;
        }
    } else {
        $pathLog = __DIR__ . './logs/error/' . date('Ymd') . '_errorQuery_fk.log';
        fileLog($_SERVER['REQUEST_URI'] . "\n" . $check_schema . "\n" . mysqli_error($link), $pathLog); // escribir en el log
    }
}
function checkColumn($table, $col)
{
    require __DIR__ . '/config/conect_mysql.php';
    $check_schema = "SELECT information_schema.COLUMNS.COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='$db' AND TABLE_NAME='$table' AND COLUMN_NAME='$col'";
    $stmt = mysqli_query($link, $check_schema);
    // print_r($query); exit;
    if ($stmt) {
        if (mysqli_num_rows($stmt) > 0) {
            mysqli_free_result($stmt);
            return true;
        } else {
            mysqli_free_result($stmt);
            return false;
        }
    } else {
        $pathLog = __DIR__ . './logs/error/' . date('Ymd') . '_errorQuery_col.log';
        fileLog($_SERVER['REQUEST_URI'] . "\n" . mysqli_error($link), $pathLog); // escribir en el log
    }
}
function checkTable($table)
{
    require __DIR__ . '/config/conect_mysql.php';
    $check_schema = "SELECT distinct(TABLE_NAME) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA='$db' AND TABLE_NAME='$table'";
    $stmt = mysqli_query($link, $check_schema);
    if ($stmt) {
        if (mysqli_num_rows($stmt) > 0) {
            mysqli_free_result($stmt);
            return true;
        } else {
            mysqli_free_result($stmt);
            return false;
        }
    } else {
        $pathLog = __DIR__ . './logs/error/' . date('Ymd') . '_errorQuery_table.log';
        fileLog($_SERVER['REQUEST_URI'] . "\n" . mysqli_error($link), $pathLog); // escribir en el log
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
function simpleQuery($query, $link)
{
    $stmt = mysqli_query($link, $query);
    if ($stmt) {
        return true;
    } else {
        $pathLog = __DIR__ . './logs/error/' . date('Ymd') . '_errorQuery.log';
        fileLog($_SERVER['REQUEST_URI'] . "\n" . mysqli_error($link), $pathLog); // escribir en el log
        return false;
    }
}
function simpleQueryData($query, $link)
{
    $stmt = mysqli_query($link, $query);
    if ($stmt) {
        if (mysqli_num_rows($stmt) > 0) {
            $a  = mysqli_fetch_assoc($stmt);
            mysqli_free_result($stmt);
            return $a;
        } else {
            return false;
        }
    } else {
        $pathLog = __DIR__ . './logs/error/' . date('Ymd') . '_errorQuery.log';
        fileLog($_SERVER['REQUEST_URI'] . "\n" . mysqli_error($link), $pathLog); // escribir en el log
        return false;
    }
}
function simpleQueryDataMS($query)
{
    require __DIR__ . './config/conect_mssql.php';
    $params    = array();
    $options   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $stmt  = sqlsrv_query($link, $query, $params, $options);
    if ($stmt) {
        $a  = sqlsrv_fetch_array($stmt);
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
    $params    = array();
    $options   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $stmt  = sqlsrv_query($link, $query, $params, $options);
    if ($stmt) {
        while ($a  = sqlsrv_fetch_array($stmt)) {
            $data[] = $a;
        }
        sqlsrv_free_stmt($stmt);
        return $data;
    } else {
        $pathLog = __DIR__ . './logs/error/' . date('Ymd') . '_errorQuery.log';
        fileLog($_SERVER['REQUEST_URI'] . "\n" . mysqli_error($link), $pathLog); // escribir en el log
        return false;
    }
}
function arrayQueryData($query, $link)
{
    $stmt = mysqli_query($link, $query);
    if ($stmt) {
        if (mysqli_num_rows($stmt) > 0) {
            while ($a = mysqli_fetch_assoc($stmt)) {
                $array[] = $a;
            }
            // $a = mysqli_fetch_assoc($stmt);
            mysqli_free_result($stmt);
            return $array;
        } else {
            return false;
        }
    } else {
        $pathLog = __DIR__ . './logs/error/' . date('Ymd') . '_errorQuery.log';
        fileLog($_SERVER['REQUEST_URI'] . "\n" . mysqli_error($link), $pathLog); // escribir en el log
        return false;
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
function deleteRegistroMySql($query)
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
function dataLista($lista, $rol)
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
function dataListaEstruct($lista, $uid)
{
    require __DIR__ . '/config/conect_mysql.php';
    $stmt = mysqli_query($link, "SELECT lista_estruct.datos FROM lista_estruct where lista_estruct.uid = '$uid' AND lista_estruct.lista = '$lista'");

    if ($stmt) {
        if (mysqli_num_rows($stmt) > 0) {
            while ($row = mysqli_fetch_assoc($stmt)) {
                return array($row['datos']);
            }
            mysqli_free_result($stmt);
        } else {
            return array('-');
        }
    } else {
        $pathLog = __DIR__ . './logs/error/' . date('Ymd') . '_errorQuery.log';
        fileLog($_SERVER['REQUEST_URI'] . "\n" . mysqli_error($link), $pathLog); // escribir en el log
        return false;
    }
    // print_r($query); exit;
    // if (($stmt)) {
    //     if (mysqli_num_rows($stmt) > 0) {
    //         while ($row = mysqli_fetch_assoc($stmt)) {
    //             return array($row['datos']);
    //         }
    //     } else {
    //         return array('-');
    //     }

    //     mysqli_free_result($stmt);
    //     mysqli_close($link);
    // } else {
    //     statusData('error', mysqli_error($link));
    //     mysqli_close($link);
    //     exit;
    // }
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
function InsertRegistroMS($query)
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
        exit;
        sqlsrv_close($link);
    }
}
function InsertRegistroCH($query)
{
    $params    = array();
    $options   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    require __DIR__ . '/config/conect_mssql.php';
    $stmt  = sqlsrv_query($link, $query, $params, $options);
    if (($stmt)) {
        return true;
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
                $mensaje = explode(']', $error['message']);
                PrintRespuestaJson('error', $mensaje[3]);
                // $data[] = array("status" => "error", "dato" => $mensaje[3]);
                // $data[] = array("status" => "error", "dato" => 'Ya existe Novedad.');
                exit;
            }
        }
        echo json_encode($data);
        exit;
        sqlsrv_close($link);
    }
}
function CountRegistrosMayorCero($query)
{
    $params    = array();
    $options   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    require __DIR__ . '/config/conect_mssql.php';
    $stmt  = sqlsrv_query($link, $query, $params, $options);
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
    if (intval($FechaStr) <= intval($perCierre)) {
        return true;
    } else {
        $query = "SELECT ParCierr FROM PARACONT WHERE ParCodi = 0 ORDER BY ParCodi";
        $stmt  = sqlsrv_query($link, $query, $params, $options);
        while ($row = sqlsrv_fetch_array($stmt)) {
            $ParCierr = $row['ParCierr']->format('Ymd');
        }
        $ParCierr = !empty($ParCierr) ? $ParCierr : '17530101';
        sqlsrv_free_stmt($stmt);
        if (intval($FechaStr) <= intval($ParCierr)) {
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
    return ($respuesta[0]);
}
function EstadoProceso($url)
{
    do {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $respuesta = curl_exec($ch);
        curl_close($ch);
    } while (respuestaWebService($respuesta) == 'Pendiente');
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
        curl_close($ch);
    } while (($respuesta) == 'Pendiente');
}

function pingWebService($textError) // Funcion para validar que el Webservice de Control Horario esta disponible
{
    $url = rutaWebService("Ping?");
    $ch = curl_init(); // Inicializar el objeto curl
    curl_setopt($ch, CURLOPT_URL, $url); // Establecer la URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Establecer que retorne el contenido del servidor
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // The number of seconds to wait while trying to connect
    curl_setopt($ch, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
    $response   = curl_exec($ch); // extract information from response
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
    return ($http_code == 201) ? true : PrintRespuestaJson('Error', $textError) . exit; // escribir en el log
    curl_close($ch); // close curl handle
}
function pingWS() // Funcion para validar que el Webservice de Control Horario esta disponible
{
    $url = rutaWebService("Ping?");
    $ch = curl_init(); // Inicializar el objeto curl
    curl_setopt($ch, CURLOPT_URL, $url); // Establecer la URL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Establecer que retorne el contenido del servidor
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3); // The number of seconds to wait while trying to connect
    curl_setopt($ch, CURLOPT_HEADER, 0); // set to 0 to eliminate header info from response
    $response   = curl_exec($ch); // extract information from response
    $curl_errno = curl_errno($ch); // get error code
    $curl_error = curl_error($ch); // get error information
    if ($curl_errno > 0) { // si hay error
        $text = "Error Ping WebService. \"Cod: $curl_errno: $curl_error\""; // set error message
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
    }
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); // get http response code
    //return curl_getinfo($ch, CURLINFO_HTTP_CODE); // retornar el codigo de respuesta
    return ($http_code == 201) ? true : false; // escribir en el log
    curl_close($ch); // close curl handle
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
        $respuesta = curl_exec($ch);
        $curl_errno = curl_errno($ch);
        $curl_error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($curl_errno > 0) {
            $text = "Error al procesar. Legajo \"$legajo\" desde \"$FechaDesde\" a \"$FechaHasta\""; // set error message
            fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
            return "Error";
            exit;
        }
        curl_close($ch);
        if ($httpCode == 404) {
            fileLog("Error al procesar. Legajo \"$legajo\" desde \"$FechaDesde\" a \"$FechaHasta\"", __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
            return $respuesta;
            exit;
        }
        $processID = respuestaWebService($respuesta);
        $url = rutaWebService("Estado?ProcesoId=" . $processID);

        if ($httpCode == 201) {
            return EstadoProceso($url);
        }
    }
}
function procesar_lega($legajo, $FechaDesde, $FechaHasta)
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
        $text = "Error al procesar. Legajo \"$legajo\" desde \"$FechaDesde\" a \"$FechaHasta\""; // set error message
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        return $ruta;
        exit;
    }
    curl_close($ch);
    if ($httpCode === 404) {
        $text = "Error al procesar. Legajo \"$legajo\" desde \"$FechaDesde\" a \"$FechaHasta\""; // set error message
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        return $ruta;
        exit;
    }
    $processID = respuestaWebService($respuesta);
    $url = rutaWebService("Estado?ProcesoId=" . $processID);
    return ($url);
    exit;
    if ($httpCode == 201) {
        // if (EstadoProceso($url) == 'Terminado') {
        //     return true;
        // }else{
        //     return false;
        // }
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
        PrintRespuestaJson('error', 'No hay Conexión..');
        exit;
    }
    curl_close($ch);
    if ($httpCode == 404) {
        $text = "Error al procesar. Legajo \"$LegajoDesde\" a \"$LegajoHasta\". Fecha \"$FechaDesde\" a \"$FechaHasta\""; // set error message
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
        echo $respuesta;
        exit;
    }
    $processID = respuestaWebService($respuesta);
    $url = rutaWebService("Estado?ProcesoId=" . $processID);

    if ($httpCode == 201) {
        // sleep(2);
        echo EstadoProceso($url);
    } else {
        $text = "Error al procesar. Legajo \"$LegajoDesde\" a \"$LegajoHasta\". Fecha \"$FechaDesde\" a \"$FechaHasta\""; // set error message
        fileLog($text, __DIR__ . '/logs/' . date('Ymd') . '_errorWebService.log'); // escribir en el log
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
        exit;
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
        exit;
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
        exit;
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
        exit;
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
        exit;
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
function dr_fecha($ddmmyyyy)
{
    $fecha = date("Ymd", strtotime((str_replace("/", "-", $ddmmyyyy))));
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
    $MinHora = intval($var[0]) * 60;
    $Min = intval($var[1] ?? '');
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

    // if ($start > $end) return createDateRangeArray($end, $start);

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
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
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
function sendRemoteData($url, $payload, $timeout = 10)
{

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    // curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //     'Content-Type: application/json',
    //     'Content-Length: ' . strlen($payload)
    // ));
    // curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
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
                $t[1] = $t[1] ?? '';
                return $t[1];
                break;
            case 'token':
                $t[0] = $t[0] ?? '';
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
function NombreLegajo($NumLega)
{
    $params    = array();
    $options   = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    require __DIR__ . '/config/conect_mssql.php';
    $query = "SELECT TOP 1 PERSONAL.LegApNo FROM PERSONAL WHERE PERSONAL.LegNume = '$NumLega'";
    $stmt  = sqlsrv_query($link, $query, $params, $options);
    while ($row = sqlsrv_fetch_array($stmt)) {
        $LegApNo = $row['LegApNo'];
    }
    return $LegApNo;
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($link);
}
function regid_legajo($legajo)
{
    require __DIR__ . '../config/conect_mysql.php';
    $sql = "SELECT regid FROM reg_user_ WHERE id_user ='$legajo' LIMIT 1";
    $rs = mysqli_query($link, $sql);
    if (mysqli_num_rows($rs) > 0) {
        while ($a = mysqli_fetch_assoc($rs)) {
            $regid = $a['regid'];
        }
    } else {
        $regid = '';
    }
    mysqli_free_result($rs);
    mysqli_close($link);
    return $regid;
}
function horarioCH($HorCodi)
{
    require __DIR__ . '/config/conect_mssql.php';
    $params  = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $query   = "SELECT HorCodi, HorDesc FROM HORARIOS WHERE HorCodi = $HorCodi";
    $stmt    = sqlsrv_query($link, $query, $params, $options);
    // print_r($query);
    while ($a = sqlsrv_fetch_array($stmt)) {
        $data = array(
            'cod'  => $a['HorCodi'],
            'desc' => $a['HorDesc'],
        );
        return $data;
    }
    sqlsrv_close($link);
}
function listaRol($idLista  = '0')
{
    $idLista = intval($idLista);
    switch ($idLista) {
        case 0:
            return 'Todos';
            break;
        case 1:
            return 'Novedades';
            break;
        case 2:
            return 'Otras Novedades';
            break;
        case 3:
            return 'Horarios';
            break;
        case 4:
            return 'Rotaciones';
            break;
        case 5:
            return 'Tipos de Hora';
            break;
        default:
            return 'Todos';
            break;
    }
}
function listaEstruct($idLista  = '0')
{
    $idLista = intval($idLista);
    switch ($idLista) {
        case 0:
            return 'Todos';
            break;
        case 1:
            return 'Empresas';
            break;
        case 2:
            return 'Plantas';
            break;
        case 3:
            return 'Convenios';
            break;
        case 4:
            return 'Sectores';
            break;
        case 5:
            return 'Secciones';
            break;
        case 6:
            return 'Grupos';
            break;
        case 7:
            return 'Sucursales';
            break;
        case 8:
            return 'Personal';
            break;
        default:
            return 'Todos';
            break;
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
        $fecha2 = ($fecha2 > date('Ymd')) ?  date('Ymd') : $fecha2;
        $arrayFechas[] = array('FechaIni' => FechaFormatVar($fecha1, 'd-m-Y'),  'FechaFin' => FechaFormatVar($fecha2, 'd-m-Y'));
        $fecha_inicial = date("Ymd", strtotime($fecha2 . "+ 1 days"));
    }
    return $arrayFechas;
}
function getVerDBCH($link) // Obtiene la version de la base de datos
{
    $query = "SELECT TOP 1 PARACONT.ParPath1 FROM PARACONT WHERE PARACONT.ParCodi = 10"; // Query
    $stmt  = sqlsrv_query($link, $query); // Ejecucion del query
    $path = '';
    while ($a = sqlsrv_fetch_array($stmt)) { // Recorre el resultado
        $path = $a['ParPath1']; // Asigna el valor a la variable
    }
    sqlsrv_free_stmt($stmt); // Libera el query
    return $path; // Retorna el valor
}
function fileLog($text, $ruta_archivo)
{
    $log    = fopen($ruta_archivo, 'a');
    $date   = fechaHora2();
    $text   = $date . ' ' . $text . "\n";
    fwrite($log, $text);
    fclose($log);
}
function fileLogJson($text, $ruta_archivo, $date = true)
{
    if ($date) {
        $log    = fopen(date('YmdHis') . '_' . $ruta_archivo, 'w');
    } else {
        $log    = fopen($ruta_archivo, 'w');
    }
    $text   = json_encode($text, JSON_PRETTY_PRINT) . "\n";
    fwrite($log, $text);
    fclose($log);
}
function dateDifference($date_1, $date_2, $differenceFormat = '%a') // diferencia en días entre dos fechas
{
    $datetime1 = date_create($date_1); // creo la fecha 1
    $datetime2 = date_create($date_2); // creo la fecha 2
    $interval = date_diff($datetime1, $datetime2); // obtengo la diferencia de fechas
    return $interval->format($differenceFormat); // devuelvo el número de días
}
function borrarLogs($path, $dias, $ext) // borra los logs a partir de una cantidad de días
{
    $files = glob($path . '*' . $ext); //obtenemos el nombre de todos los ficheros
    foreach ($files as $file) { // recorremos todos los ficheros.
        $lastModifiedTime = filemtime($file); // obtenemos la fecha de modificación del fichero
        $currentTime      = time(); // obtenemos la fecha actual
        $dateDiff         = dateDifference(date('Ymd', $lastModifiedTime), date('Ymd', $currentTime)); // obtenemos la diferencia de fechas
        ($dateDiff >= $dias) ? unlink($file) : ''; //elimino el fichero
    }
}
function fechaHora()
{
    timeZone();
    $t = explode(" ", microtime());
    $t = date("Ymd H:i:s", $t[1]) . substr((string)$t[0], 1, 4);
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

function hostName()
{
    $nombre_host = gethostbyaddr($_SERVER['REMOTE_ADDR']);
    return $nombre_host;
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
            $tipo = $tipo;
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

        $data = [ // array asociativo con los parametros a pasar a la consulta preparada (:usuario, :uid, :estado, :rol, :cliente, :ip, :agent, :fechahora)
            'usuario'   => $usuario,
            'uid'       => $_SESSION["UID"],
            'estado'    => $estado,
            'rol'       => $_SESSION["ID_ROL"],
            'cliente'   => $_SESSION["ID_CLIENTE"],
            'ip'        => ($_SERVER['REMOTE_ADDR'] == '::1') ? ip2long('127.0.0.1') : ip2long($_SERVER['REMOTE_ADDR']),
            'agent'     => $_SERVER['HTTP_USER_AGENT'],
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
} /*escape_sql_wild*/

function defaultConfigData() // default config data

{
    $datos = array(
        'mssql' => array('srv' => '', 'db' => '', 'user' => '', 'pass' => ''), 'logConexion' => array('success' => false, 'error' => true), 'api' => array('url' => "https://hr-process.com/hrctest/api/novedades/", 'user' => 'admin', 'pass' => 'admin'), 'webService' => array('url' => "http://localhost:6400/RRHHWebService/"), 'logNovedades' => array('success' => true, 'error' => true), 'proxy' => array('ip' => '', 'port' => '', 'enabled' => false), 'borrarLogs' => array('estado' => true, 'dias' => 31), // 'interrumpirSolicitud'=>array('carga'=>true, 'anulacion'=>true)
    );
    return $datos;
}

function write_apiKeysFile()
{
    $q = "SELECT id as 'idCompany', nombre as 'nameCompany', recid as 'recidCompany', 'key' as 'key', urlAppMobile AS 'urlAppMobile', localCH as 'localCH' FROM clientes";
    $assoc_arr = array_pdoQuery($q);

    foreach ($assoc_arr as $key => $value) {
        $assoc[] = (array(
            'idCompany'    => $value['idCompany'],
            'nameCompany'  => $value['nameCompany'],
            'recidCompany' => $value['recidCompany'],
            'urlAppMobile' => $value['urlAppMobile'],
            'localCH'      => ($value['localCH'])
        ));
    }
    // $assoc[] = (array('idCompany' => '100', 'nameCompany' => 'prueba', 'recidCompany' => 'das4ds5'));
    // $assoc[] = (array('idCompany' => '300', 'nameCompany' => 'prueba', 'recidCompany' => 'das4ds5'));

    $content = "; <?php exit; ?> <-- ¡No eliminar esta línea! --> \n";
    foreach ($assoc as $key => $elem) {
        $content .= "[" . $key . "]\n";
        foreach ($elem as $key2 => $elem2) {
            if (is_array($elem2)) {
                for ($i = 0; $i < count($elem2); $i++) {
                    $content .= $key2 . "[] =\"" . $elem2[$i] . "\"\n";
                }
            } else if ($elem2 == "") $content .= $key2 . " =\n";
            else $content .= $key2 . " = \"" . $elem2 . "\"\n";
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
function getDataIni($url) // obtiene el json de la url
{
    if (file_exists($url)) { // si existe el archivo
        $data = file_get_contents($url); // obtenemos el contenido del archivo
        if ($data) { // si el contenido no está vacío
            $data = parse_ini_file($url, true); // Obtenemos los datos del archivo
            return $data; // devolvemos el json
        } else { // si el contenido está vacío
            fileLog("No hay informacion en el archivo \"$url\"", __DIR__ . "/logs/" . date('Ymd') . "_getDataIni.log", ''); // escribimos en el log
        }
    } else { // si no existe el archivo
        fileLog("No existe archivo \"$url\"", __DIR__ . "/logs/" . date('Ymd') . "_getDataIni.log", ''); // escribimos en el log
        return false; // devolvemos false
    }
}
function padLeft($str, $len, $pad = ' ')
{
    return str_pad($str, $len, $pad, STR_PAD_LEFT);
}
/**
 * Function that groups an array of associative arrays by some key.
 * 
 * @param {String} $key Property to sort by.
 * @param {Array} $data Array that stores multiple associative arrays.
 */
function group_by($key, $data)
{
    $result = array();

    foreach ($data as $val) {
        if (array_key_exists($key, $val)) {
            $result[$val[$key]][] = $val;
        } else {
            $result[""][] = $val;
        }
    }

    return $result;
}
function _group_by_keys($array, $keys = array())
{
    $return = array();
    $append = (sizeof($keys) > 1 ? "_" : null);
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
    return $arrGroup3;
}
function pingApiMobileHRP($urlAppMobile)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $urlAppMobile . '/attention/api/test/ping');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
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
    exit;
}
function sendApiMobileHRP($payload, $urlApp, $paramsUrl, $idCompany, $post = true)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $urlApp . '/' . $paramsUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
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
function confidenceFaceStr($confidence, $id_api)
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
            case $confidence >= 0 && $confidence <= 80:
                $c = 'Identificado';
                break;
            case $confidence > 80:
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
        borrarLogs(__DIR__, 30, $pathLog);
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $_SESSION['NOMBRE_SESION']  = $_SESSION['NOMBRE_SESION'] ?? '';
        $_SESSION['CLIENTE']        = $_SESSION['CLIENTE'] ?? '';
        $_SERVER['REMOTE_ADDR']     = $_SERVER['REMOTE_ADDR'] ?? '';
        $_SESSION['secure_auth_ch'] = $_SESSION['secure_auth_ch'] ?? false;
        $_REQUEST['state']             = $_REQUEST['state'] ?? false;

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

        $log  = fopen($pathLog, 'a');
        $t    = date("Y-m-d H:i:s");
        $text = "$t - u : \"$_SESSION[NOMBRE_SESION]\" c: \"$_SESSION[CLIENTE]\" ip: \"$_SERVER[REMOTE_ADDR]\"  m: \"$Modulo\" a: \"$_SESSION[secure_auth_ch]\" state: \"$state\"\n";
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

        if($gitignore){
            $git = dirname($path . '/.gitignore');
            mkdir($git, 0755, true);
            $logGit    = fopen($git . '/.gitignore', 'a');
            $textGit   = '*';
            fwrite($logGit, $textGit);
            fclose($logGit);
        }

        $log    = fopen($dirname . '/index.php', 'a');
        $text   = '<?php exit;';
        fwrite($log, $text);
        fclose($log);
    }
}

<?php

if ($_SERVER['SERVER_NAME'] == 'localhost') { // Si es localhost
    borrarLogs(__DIR__ . '/../logs/', 1, '.log');
    borrarLogs(__DIR__ . '/../logs/error/', 1, '.log');
    borrarLogs(__DIR__ . '/../logs/info/', 1, '.log');
} else {
    borrarLogs(__DIR__ . '/../logs/', 7, '.log');
    borrarLogs(__DIR__ . '/../logs/error/', 7, '.log');
    borrarLogs(__DIR__ . '/../logs/info/', 7, '.log');
}

//$_SESSION['VER_DB_LOCAL'] = $verDB; // Version de la DB local

/** chequeamos los módulos asociados al rol de usuarios 
 * y guardamos en una session el array de los mismos 
 * */
function sesionListas($id_rol, $lista, $nombreSesion)
{
    $dataLista = dataLista($lista, $id_rol);
    $dataLista = implode(',', $dataLista);
    $_SESSION[$nombreSesion] = $dataLista;
}
sesionListas($row['id_rol'], 1, 'ListaNov'); // Sesion lista de novedades
sesionListas($row['id_rol'], 2, 'ListaONov'); // Sesion lista de otras Novedades
sesionListas($row['id_rol'], 3, 'ListaHorarios'); // Sesion lista de horarios
sesionListas($row['id_rol'], 4, 'ListaRotaciones'); // Sesion lista de rotaciones
sesionListas($row['id_rol'], 5, 'ListaTipoHora'); // Sesion lista de tipos de horas

// $abm = simpleQueryData("SELECT * FROM abm_roles WHERE recid_rol = '$row[recid_rol]' LIMIT 1", $link); // Traigo los permisos del rol
$abm = simple_pdoQuery("SELECT * FROM abm_roles WHERE recid_rol = '$row[recid_rol]' LIMIT 1"); // Traigo los permisos del rol
$ABMRol = array(); // Array de permisos del rol
if ($abm) { // Si hay permisos
    $ABMRol = array('aFic' => $abm['aFic'], 'mFic' => $abm['mFic'], 'bFic' => $abm['bFic'], 'aNov' => $abm['aNov'], 'mNov' => $abm['mNov'], 'bNov' => $abm['bNov'], 'aHor' => $abm['aHor'], 'mHor' => $abm['mHor'], 'bHor' => $abm['bHor'], 'aONov' => $abm['aONov'], 'mONov' => $abm['mONov'], 'bONov' => $abm['bONov'], 'Proc' => $abm['Proc'], 'aCit' => $abm['aCit'], 'mCit' => $abm['mCit'], 'bCit' => $abm['bCit'], 'aTur' => $abm['aTur'], 'mTur' => $abm['mTur'], 'bTur' => $abm['bTur']);
} else { // Si no hay permisos
    $ABMRol = array('aFic' => '0', 'mFic' => '0', 'bFic' => '0', 'aNov' => '0', 'mNov' => '0', 'bNov' => '0', 'aHor' => '0', 'mHor' => '0', 'bHor' => '0', 'aONov' => '0', 'mONov' => '0', 'bONov' => '0', 'Proc' => '0', 'aCit' => '0', 'mCit' => '0', 'bCit' => '0', 'aTur' => '0', 'mTur' => '0', 'bTur' => '0');
}
//$data_mod = array_pdoQuery("SELECT mod_roles.modulo AS modsrol FROM mod_roles WHERE mod_roles.recid_rol ='$row[recid_rol]'"); // Traigo los módulos asociados al rol
//$_SESSION["MODS_ROL"] = $data_mod; // Guardo en la session los módulos asociados al rol
$_SESSION["ABM_ROL"] = $ABMRol; // Guardo en la session los permisos del rol

$arrMod = array_pdoQuery("SELECT 
`mod_roles`.`modulo` AS `modsrol`, `modulos`.`idtipo` AS `tipo`, `modulos`.`nombre` as `modulo`, `modulos`.`orden` as `orden`
FROM `mod_roles` 
INNER JOIN `modulos` ON `mod_roles`.`modulo` = `modulos`.`id`
WHERE `mod_roles`.`recid_rol` ='$row[recid_rol]'");
$arrModProy = array_pdoQuery("SELECT 
`mod_roles`.`modulo` AS `modsrol`, `modulos`.`idtipo` AS `tipo`, `modulos`.`nombre` as `modulo`, `modulos`.`orden` as `orden`
FROM `mod_roles` 
INNER JOIN `modulos` ON `mod_roles`.`modulo` = `modulos`.`id`
WHERE `mod_roles`.`recid_rol` ='$row[recid_rol]' and `modulos` .`idtipo` = 6");

$_SESSION["MODS_ROL"] = $arrMod; // Guardo en la session los módulos asociados al rol
if ($arrModProy) {
    $_SESSION["MODS_ROL_PROY"] = $arrModProy; // Guardo en la session los módulos asociados al rol
} else {
    $_SESSION["MODS_ROL_PROY"] = 'error'; // Guardo en la session los módulos asociados al rol
}

function estructura_recid_rol($recid_rol, $e, $data)
{
    E_ALL();
    require __DIR__ . '/../config/conect_mysql.php';
    $concat = '';
    switch ($e) {
        case 'sectores':
            $tabla = 'sect_roles';
            $ColEstr = 'sector';
            break;
        case 'plantas':
            $tabla = 'plan_roles';
            $ColEstr = 'planta';
            break;
        case 'grupos':
            $tabla = 'grup_roles';
            $ColEstr = 'grupo';
            break;
        case 'sucursales':
            $tabla = 'suc_roles';
            $ColEstr = 'sucursal';
            break;
        case 'empresas':
            $tabla = 'emp_roles';
            $ColEstr = 'empresa';
            break;
        case 'convenios':
            $tabla = 'conv_roles';
            $ColEstr = 'convenio';
            break;
        case 'secciones':
            $tabla = 'secc_roles';
            $ColEstr = 'seccion';
            $concat = ", CONCAT(secc_roles.sector,secc_roles.seccion) AS 'sect_secc'";
            break;
        case 'personal':
            break;
        default:
            $concat = '';
            // $respuesta = array('success' => 'NO', 'error' => '1', 'mensaje' => 'No se especifico parametro de estructura');
            // $datos = array($respuesta);
            // echo json_encode($datos);
            // exit;
            break;
    }

    $recidRol = (isset($e)) ? "WHERE $tabla.recid_rol = '$recid_rol'" : "";
    $query = "SELECT DISTINCT $tabla.$ColEstr AS id, $tabla.recid_rol AS recid_rol $concat FROM $tabla $recidRol";
    $result = mysqli_query($link, $query);
    // print_r($query);exit;

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $id = ($e == 'secciones') ? $row['sect_secc'] : $row['id'];
            // $recid_rol = $row['recid_rol'];
            $DataID[] = ($id);
        }

        $data = implode(",", $DataID);
    } else {
        $data = '';
    }
    mysqli_free_result($result);
    mysqli_close($link);
    return $data;
}
function estructUsuario($uid, $lista)
{
    $v = dataListaEstruct($lista, $uid);
    $v = implode(',', $v);
    $v = ($v == '-') ? '' : $v;
    return $v;
}

$_SESSION['EstrUser'] = estructUsuario(intval($row['id']), 8);
if ($row["recid_cliente"] == 'kxo7w2q-'): // solo para la cuenta de SKF 'kxo7w2q-'
    $_SESSION['EmprRol'] = (estructura_recid_rol($row['recid_rol'], 'empresas', 'empresa'));
    $_SESSION['PlanRol'] = (estructura_recid_rol($row['recid_rol'], 'plantas', 'planta'));
    $_SESSION['ConvRol'] = (estructura_recid_rol($row['recid_rol'], 'convenios', 'convenio'));
    $_SESSION['SectRol'] = (estructura_recid_rol($row['recid_rol'], 'sectores', 'sector'));
    $_SESSION['Sec2Rol'] = (estructura_recid_rol($row['recid_rol'], 'secciones', 'seccion'));
    $_SESSION['GrupRol'] = (estructura_recid_rol($row['recid_rol'], 'grupos', 'grupo'));
    $_SESSION['SucuRol'] = (estructura_recid_rol($row['recid_rol'], 'sucursales', 'sucursal'));
else:
    $_SESSION['EmprRol'] = estructUsuario(intval($row['id']), 1);
    $_SESSION['PlanRol'] = estructUsuario(intval($row['id']), 2);
    $_SESSION['ConvRol'] = estructUsuario(intval($row['id']), 3);
    $_SESSION['SectRol'] = estructUsuario(intval($row['id']), 4);
    $_SESSION['Sec2Rol'] = estructUsuario(intval($row['id']), 5);
    $_SESSION['GrupRol'] = estructUsuario(intval($row['id']), 6);
    $_SESSION['SucuRol'] = estructUsuario(intval($row['id']), 7);
endif;

$_SESSION["CONEXION_MS"] = array('host' => $row["host"], 'db' => $row["db"], 'user' => $row["user"], 'pass' => $row["pass"], 'auth' => $row['auth']);
$_SESSION["secure_auth_ch"] = true;
$_SESSION["user"] = strtolower($row['usuario']);
$_SESSION["ultimoAcceso"] = date("Y-m-d H:i:s");
$_SESSION["UID"] = $row["id"];
$_SESSION["NOMBRE_SESION"] = $row["nombre"];
$_SESSION["LEGAJO_SESION"] = $row["legajo"];
$_SESSION["RECID_USER"] = $row["recid_user"];
$_SESSION["ID_ROL"] = $row["id_rol"];
$_SESSION["ID_CLIENTE"] = $row["id_cliente"];
$_SESSION["CLIENTE"] = $row["cliente"];
$_SESSION["ROL"] = $row["rol"];
$_SESSION["RECID_ROL"] = $row["recid_rol"];
$_SESSION["RECID_CLIENTE"] = $row["recid_cliente"];
$_SESSION["TK_MOBILE"] = $row["tkmobile"];
$_SESSION["WEBSERVICE"] = $row["WebService"];
$_SESSION["HASH_CLAVE"] = ($row['clave']);
$_SESSION["LIMIT_SESION"] = 3600;
$_SESSION['USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
$_SESSION['IP_CLIENTE'] = $_SERVER['REMOTE_ADDR'];
$_SESSION['DIA_ACTUAL'] = hoy();
$_SESSION['VER_DB_CH'] = false;
$_SESSION['CONECT_MSSQL'] = false;
$_SESSION['PROY_SESION'] = true;
// $_SESSION["HOST_NAME"] = gethostbyaddr($_SERVER['REMOTE_ADDR']);
login_logs('1', $row['usuario']);
//session_start();    // inicia la sesion
$datos = array(
    'user' => $_SESSION["user"],
    'name' => $_SESSION["NOMBRE_SESION"],
    'lega' => $_SESSION["LEGAJO_SESION"],
    'last' => $_SESSION["ultimoAcceso"],
    'uuid' => $_SESSION["UID"],
    'urol' => $_SESSION["ROL"],
    'lses' => $_SESSION["LIMIT_SESION"],
    'addr' => $_SESSION["IP_CLIENTE"],
    'uday' => $_SESSION["DIA_ACTUAL"],
    'pses' => $_SESSION["PROY_SESION"],
    'reci' => $_SESSION["RECID_CLIENTE"]
);
$data = array('status' => 'ok', 'Mensaje' => $datos);
echo json_encode($data);
// session_regenerate_id();
exit;
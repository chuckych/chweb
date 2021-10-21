<?php
//InsertRegistroMySql("ALTER TABLE `clientes` ADD COLUMN IF NOT EXISTS `WebService` VARCHAR(30) NOT NULL AFTER `tkmobile`");
function login_logs($var)
{
	require __DIR__ . '../../config/conect_mysql.php';
	// $fechahora = date("Y/m/d H:i:s");
	$fechahora = fechaHora2();
	$ip        = $_SERVER['REMOTE_ADDR'];
	$agent     = secureVar($_SERVER['HTTP_USER_AGENT']);
	$uid       = $_SESSION["UID"];
	$rol       = $_SESSION["ID_ROL"];
	$cliente   = $_SESSION["ID_CLIENTE"];
	switch ($ip) {
		case '::1':
			$ip = ip2long('127.0.0.1');
			break;
		default:
			$ip = ip2long($_SERVER['REMOTE_ADDR']);
			break;
	}
	$sql = "INSERT INTO login_logs (usuario,uid,estado,rol,cliente,ip,agent,fechahora)
	VALUES('$_POST[user]','$uid','$var','$rol','$cliente','$ip','$agent','$fechahora')";
	$stmt = mysqli_query($link, $sql);
	if ($stmt) {
		return true;
	} else {
		$pathLog = __DIR__ . '../../logs/error/' . date('Ymd') . '_auditoLogin.log';
		fileLog($_SERVER['REQUEST_URI'] . "\n" . mysqli_error($link), $pathLog); // escribir en el log
		return false;
	}
	mysqli_close($link);
}

$_POST["guarda"] = empty($_POST["guarda"]) ? '' : $_POST["guarda"];

if ($_POST["guarda"] == "on") {
	##  GUARDAR COOKIE ## 
	setcookie("user", strtolower($_POST["user"]), time() + 3600 * 24 * 30);
	setcookie("clave", $_POST["clave"], time() + 3600 * 24 * 30);
} else {
	setcookie("user", "");
	setcookie("clave", "");
}
/** Consultamos el si el usuario y clave son correctos */
require __DIR__ . '../../config/conect_mysql.php';

$user = (isset($_GET['conf'])) ? $_GET['conf'] : strip_tags(strtolower($_POST['user']));
$pass = (isset($_GET['conf'])) ? $_GET['conf'] : strip_tags($_POST['clave']);
$user = test_input($user);
$sql = "SELECT usuarios.usuario AS 'usuario', usuarios.clave AS 'clave', usuarios.nombre AS 'nombre', usuarios.legajo AS 'legajo', usuarios.id AS 'id', usuarios.rol AS 'id_rol', usuarios.cliente AS 'id_cliente', clientes.nombre AS 'cliente', roles.nombre AS 'rol', roles.recid AS 'recid_rol', roles.id AS 'id_rol', clientes.host AS 'host', clientes.db AS 'db', clientes.user AS 'user', clientes.pass AS 'pass', clientes.auth AS 'auth', clientes.recid AS 'recid_cliente', clientes.tkmobile AS 'tkmobile', clientes.WebService AS 'WebService', usuarios.recid AS 'recid_user' FROM usuarios INNER JOIN clientes ON usuarios.cliente=clientes.id INNER JOIN roles ON usuarios.rol=roles.id WHERE usuarios.usuario='$user' AND usuarios.estado='0' LIMIT 1";

// print_r($sql); exit;
// $rs       = mysqli_query($link, $sql);
// $NumRows  = mysqli_num_rows($rs);
// $row      = mysqli_fetch_assoc($rs);
$row = simpleQueryData($sql, $link); // obtener los datos del usuario 
// $hash = $row['clave']; // obtener la clave del usuario
// print_r($row); exit;
/** Si es correcto */
if (($row) && (password_verify($pass, $row['clave']))) { // password_verify($pass, $hash)

	borrarLogs(__DIR__ . '../../logs/', 7, '.log');
	borrarLogs(__DIR__ . '../../logs/error/', 7, '.log');

	$pathLog = __DIR__ . '../../logs/info/' . date('Ymd') . '_cambios_db.log';

	if (!CountRegMayorCeroMySql("SELECT 1 FROM params WHERE modulo = 0 LIMIT 1")) { // si no existe el registro en la tabla params
		simpleQuery("INSERT INTO params (modulo, descripcion, valores, cliente) VALUES (0, 'Ver DB', 20210101, 0)", $link);
		fileLog("Se inserto el parametro: \"Ver DB\"", $pathLog); // escribir en el log
	} else { // Si existe el registro
		//fileLog("El parametro: \"Ver DB\" ya existe", $pathLog); // escribir en el log
	}

	$a = simpleQueryData("SELECT valores FROM params WHERE modulo = 0 and cliente = 0 LIMIT 1", $link); // Traigo el valor de la version de la DB mysql
	$verDB = intval($a['valores']); // valor de la version de la DB mysql

	require_once __DIR__ . './cambios.php'; // Cambios en la DB

	$_SESSION['VER_DB_LOCAL'] = $verDB; // Version de la DB local

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

	$abm = simpleQueryData("SELECT * FROM abm_roles WHERE recid_rol = '$row[recid_rol]' LIMIT 1", $link); // Traigo los permisos del rol
	$ABMRol = array(); // Array de permisos del rol

	if ($abm) { // Si hay permisos
		$ABMRol = array('aFic' => $abm['aFic'], 'mFic'  => $abm['mFic'], 'bFic'  => $abm['bFic'], 'aNov'  => $abm['aNov'], 'mNov'  => $abm['mNov'], 'bNov'  => $abm['bNov'], 'aHor'  => $abm['aHor'], 'mHor'  => $abm['mHor'], 'bHor'  => $abm['bHor'], 'aONov' => $abm['aONov'], 'mONov' => $abm['mONov'], 'bONov' => $abm['bONov'], 'Proc'  => $abm['Proc'], 'aCit'  => $abm['aCit'], 'mCit'  => $abm['mCit'], 'bCit'  =>  $abm['bCit'], 'aTur'  => $abm['aTur'], 'mTur'  => $abm['mTur'], 'bTur'  => $abm['bTur']);
	} else { // Si no hay permisos
		$ABMRol = array('aFic'  => '0', 'mFic'  => '0', 'bFic'  => '0', 'aNov'  => '0', 'mNov'  => '0', 'bNov'  => '0', 'aHor'  => '0', 'mHor'  => '0', 'bHor'  => '0', 'aONov' => '0', 'mONov' => '0', 'bONov' => '0', 'Proc'  => '0', 'aCit'  => '0', 'mCit'  => '0', 'bCit'  => '0', 'aTur'  => '0', 'mTur'  => '0', 'bTur'  => '0');
	}

	$query = "SELECT mod_roles.modulo AS modsrol FROM mod_roles WHERE mod_roles.recid_rol ='$row[recid_rol]'";
	$data_mod = arrayQueryData($query, $link); // Traigo los módulos asociados al rol

	$_SESSION["MODS_ROL"] = $data_mod; // Guardo en la session los módulos asociados al rol
	$_SESSION["ABM_ROL"] = $ABMRol; // Guardo en la session los permisos del rol


	function estructura_recid_rol($recid_rol, $e, $data)
	{
		E_ALL();
		require __DIR__ . '../../config/conect_mysql.php';
		$concat = '';
		switch ($e) {
			case 'sectores':
				$tabla     = 'sect_roles';
				$ColEstr   = 'sector';
				break;
			case 'plantas':
				$tabla     = 'plan_roles';
				$ColEstr   = 'planta';
				break;
			case 'grupos':
				$tabla     = 'grup_roles';
				$ColEstr   = 'grupo';
				break;
			case 'sucursales':
				$tabla     = 'suc_roles';
				$ColEstr   = 'sucursal';
				break;
			case 'empresas':
				$tabla     = 'emp_roles';
				$ColEstr   = 'empresa';
				break;
			case 'convenios':
				$tabla     = 'conv_roles';
				$ColEstr   = 'convenio';
				break;
			case 'secciones':
				$tabla     = 'secc_roles';
				$ColEstr   = 'seccion';
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
		$query    = "SELECT DISTINCT $tabla.$ColEstr AS id, $tabla.recid_rol AS recid_rol $concat FROM $tabla $recidRol";
		$result   = mysqli_query($link, $query);
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

	$_SESSION['EstrUser'] =  estructUsuario(intval($row['id']), 8);
	if ($row["recid_cliente"] == 'kxo7w2q-') : // solo para la cuenta de SKF 'kxo7w2q-'
		$_SESSION['EmprRol'] = (estructura_recid_rol($row['recid_rol'], 'empresas', 'empresa'));
		$_SESSION['PlanRol'] = (estructura_recid_rol($row['recid_rol'], 'plantas', 'planta'));
		$_SESSION['ConvRol'] = (estructura_recid_rol($row['recid_rol'], 'convenios', 'convenio'));
		$_SESSION['SectRol'] = (estructura_recid_rol($row['recid_rol'], 'sectores', 'sector'));
		$_SESSION['Sec2Rol'] = (estructura_recid_rol($row['recid_rol'], 'secciones', 'seccion'));
		$_SESSION['GrupRol'] = (estructura_recid_rol($row['recid_rol'], 'grupos', 'grupo'));
		$_SESSION['SucuRol'] = (estructura_recid_rol($row['recid_rol'], 'sucursales', 'sucursal'));
	else :
		$_SESSION['EmprRol'] = estructUsuario(intval($row['id']), 1);
		$_SESSION['PlanRol'] = estructUsuario(intval($row['id']), 2);
		$_SESSION['ConvRol'] = estructUsuario(intval($row['id']), 3);
		$_SESSION['SectRol'] = estructUsuario(intval($row['id']), 4);
		$_SESSION['Sec2Rol'] = estructUsuario(intval($row['id']), 5);
		$_SESSION['GrupRol'] = estructUsuario(intval($row['id']), 6);
		$_SESSION['SucuRol'] = estructUsuario(intval($row['id']), 7);
	endif;

	// $_SESSION['EmprRol'] = (estructura_rol('GetEstructRol', $row['recid_rol'], 'empresas', 'empresa'));
	// $_SESSION['PlanRol'] = (estructura_rol('GetEstructRol', $row['recid_rol'], 'plantas', 'planta'));
	// $_SESSION['ConvRol'] = (estructura_rol('GetEstructRol', $row['recid_rol'], 'convenios', 'convenio'));
	// $_SESSION['SectRol'] = (estructura_rol('GetEstructRol', $row['recid_rol'], 'sectores', 'sector'));
	// $_SESSION['Sec2Rol'] = (estructura_rol('GetEstructRol', $row['recid_rol'], 'secciones', 'seccion'));
	// $_SESSION['GrupRol'] = (estructura_rol('GetEstructRol', $row['recid_rol'], 'grupos', 'grupo'));
	// $_SESSION['SucuRol'] = (estructura_rol('GetEstructRol', $row['recid_rol'], 'sucursales', 'sucursal'));

    $_SESSION["CONEXION_MS"]    = array('host' => $row["host"], 'db' => $row["db"], 'user' => $row["user"], 'pass' => $row["pass"], 'auth' => $row['auth']);
    $_SESSION["secure_auth_ch"] = true;
    $_SESSION["user"]           = strtolower($row['usuario']);
    $_SESSION["ultimoAcceso"]   = date("Y-m-d H:i:s");
    $_SESSION["UID"]            = $row["id"];
    $_SESSION["NOMBRE_SESION"]  = $row["nombre"];
    $_SESSION["LEGAJO_SESION"]  = $row["legajo"];
    $_SESSION["RECID_USER"]     = $row["recid_user"];
    $_SESSION["ID_ROL"]         = $row["id_rol"];
    $_SESSION["ID_CLIENTE"]     = $row["id_cliente"];
    $_SESSION["CLIENTE"]        = $row["cliente"];
    $_SESSION["ROL"]            = $row["rol"];
    $_SESSION["RECID_ROL"]      = $row["recid_rol"];
    $_SESSION["RECID_CLIENTE"]  = $row["recid_cliente"];
    $_SESSION["TK_MOBILE"]      = $row["tkmobile"];
    $_SESSION["WEBSERVICE"]     = $row["WebService"];
    $_SESSION["HASH_CLAVE"]     = ($row['clave']);
    $_SESSION["LIMIT_SESION"]   = 3600;
    $_SESSION['USER_AGENT']     = $_SERVER['HTTP_USER_AGENT'];
    $_SESSION['IP_CLIENTE']     = $_SERVER['REMOTE_ADDR'];
    $_SESSION['DIA_ACTUAL']     = hoy();
    $_SESSION['VER_DB_CH']      = false;
    $_SESSION['CONECT_MSSQL']   = false;
	// $_SESSION["HOST_NAME"] = gethostbyaddr($_SERVER['REMOTE_ADDR']);

	session_regenerate_id();
	login_logs('1');
	// mysqli_close($link);
	if ($_POST['lasturl']) {
		header('Location:' . urldecode($_POST['lasturl']));
	} else if (CountRegMayorCeroMySql("SELECT mod_roles.modulo AS modsrol FROM mod_roles WHERE mod_roles.recid_rol ='$row[recid_rol]' AND mod_roles.modulo = '8'")) {
		header('Location:/' . HOMEHOST . '/dashboard/');
	} else if (CountRegMayorCeroMySql("SELECT mod_roles.modulo AS modsrol FROM mod_roles WHERE mod_roles.recid_rol ='$row[recid_rol]' AND mod_roles.modulo = '6'")) {
		header('Location:/' . HOMEHOST . '/mishoras/');
	} else if (CountRegMayorCeroMySql("SELECT mod_roles.modulo AS modsrol FROM mod_roles WHERE mod_roles.recid_rol ='$row[recid_rol]' AND mod_roles.modulo = '5'")) {
		header('Location:/' . HOMEHOST . '/mobile/');
	} else {
		header('Location:/' . HOMEHOST . '/inicio/');
	}
}
/** Si es incorrecto */
else {
	login_logs('2');
	header('Location:/' . HOMEHOST . '/login/?error');
}

mysqli_close($link);

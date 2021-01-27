<?php
//InsertRegistroMySql("ALTER TABLE `clientes` ADD COLUMN IF NOT EXISTS `WebService` VARCHAR(30) NOT NULL AFTER `tkmobile`");
function login_logs($var)
{
	require __DIR__ . '../../config/conect_mysql.php';
	$fechahora = date("Y/m/d H:i:s");
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
	$rs = mysqli_query($link, $sql);
	return $rs;
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

$sql = "SELECT usuarios.usuario AS 'usuario', usuarios.clave AS 'clave', usuarios.nombre AS 'nombre', usuarios.legajo AS 'legajo', usuarios.id AS 'id', usuarios.rol AS 'id_rol', usuarios.cliente AS 'id_cliente', clientes.nombre AS 'cliente', roles.nombre AS 'rol', roles.recid AS 'recid_rol', clientes.host AS 'host', clientes.db AS 'db', clientes.user AS 'user', clientes.pass AS 'pass', clientes.auth AS 'auth', clientes.recid AS 'recid_cliente', clientes.tkmobile AS 'tkmobile', clientes.WebService AS 'WebService', usuarios.recid AS 'recid_user' FROM usuarios INNER JOIN clientes ON usuarios.cliente=clientes.id INNER JOIN roles ON usuarios.rol=roles.id WHERE usuarios.usuario='" . test_input($user) . "' AND usuarios.estado='0' LIMIT 1";

// print_r($sql); exit;

$rs       = mysqli_query($link, $sql);
$NumRows  = mysqli_num_rows($rs);
$row      = mysqli_fetch_assoc($rs);
$hash     = $row['clave'];
// print_r($sql); exit;
/** Si es correcto */
if (($NumRows > '0') && (password_verify($pass, $hash))) {
	/** chequeamos los módulos asociados al rol de usuarios 
	 * y guardamos en una session el array de los mismos 
	 * */
	$query = "SELECT * FROM abm_roles WHERE recid_rol = '$row[recid_rol]' LIMIT 1";
	$result = mysqli_query($link, $query);
	// print_r($query); exit;
	$ABMRol = array();
	if (mysqli_num_rows($result) > 0) {
		while ($rowabm = mysqli_fetch_assoc($result)) :
			$aFic  = $rowabm['aFic'];
			$mFic  = $rowabm['mFic'];
			$bFic  = $rowabm['bFic'];
			$aNov  = $rowabm['aNov'];
			$mNov  = $rowabm['mNov'];
			$bNov  = $rowabm['bNov'];
			$aHor  = $rowabm['aHor'];
			$mHor  = $rowabm['mHor'];
			$bHor  = $rowabm['bHor'];
			$aONov = $rowabm['aONov'];
			$mONov = $rowabm['mONov'];
			$bONov = $rowabm['bONov'];
			$Proc  = $rowabm['Proc'];
			$aCit  = $rowabm['aCit'];
			$mCit  = $rowabm['mCit'];
			$bCit  = $rowabm['bCit'];
			$ABMRol = array(
				'aFic'  => $aFic,
				'mFic'  => $mFic,
				'bFic'  => $bFic,
				'aNov'  => $aNov,
				'mNov'  => $mNov,
				'bNov'  => $bNov,
				'aHor'  => $aHor,
				'mHor'  => $mHor,
				'bHor'  => $bHor,
				'aONov' => $aONov,
				'mONov' => $mONov,
				'bONov' => $bONov,
				'Proc'  => $Proc,
				'aCit'  => $aCit,
				'mCit'  => $mCit,
				'bCit'  => $bCit,
			);
		endwhile;
	} else {
		$ABMRol = array(
			'aFic'  => '0',
			'mFic'  => '0',
			'bFic'  => '0',
			'aNov'  => '0',
			'mNov'  => '0',
			'bNov'  => '0',
			'aHor'  => '0',
			'mHor'  => '0',
			'bHor'  => '0',
			'aONov' => '0',
			'mONov' => '0',
			'bONov' => '0',
			'Proc'  => '0',
			'aCit'  => '0',
			'mCit'  => '0',
			'bCit'  => '0',
		);
	}
	mysqli_free_result($result);

	$query = "SELECT mod_roles.modulo AS modsrol FROM mod_roles WHERE mod_roles.recid_rol ='$row[recid_rol]'";
	$result = mysqli_query($link, $query);
	// $data=array();
	while ($rows = mysqli_fetch_assoc($result)) :
		$modsrol = $rows['modsrol'];
		$data_mod[] = array(
			'modsrol' => $modsrol
		);
	endwhile;
	mysqli_free_result($result);
	/** 
	 * 
	 */

	$_SESSION["MODS_ROL"] = $data_mod;
	$_SESSION["ABM_ROL"] = $ABMRol;


	function estructura_recid_rol($recid_rol, $e, $data)
	{
		error_reporting(E_ALL);
		ini_set('display_errors', '1');
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
				$DataID[] = (
					$id
				);
			}
			
			$data = implode(",", $DataID);
		}else{
			$data = '';
		}
		mysqli_free_result($result);
		mysqli_close($link);
		return $data;
	}

	$_SESSION['EmprRol'] = (estructura_recid_rol($row['recid_rol'], 'empresas', 'empresa'));
	$_SESSION['PlanRol'] = (estructura_recid_rol($row['recid_rol'], 'plantas', 'planta'));
	$_SESSION['ConvRol'] = (estructura_recid_rol($row['recid_rol'], 'convenios', 'convenio'));
	$_SESSION['SectRol'] = (estructura_recid_rol($row['recid_rol'], 'sectores', 'sector'));
	$_SESSION['Sec2Rol'] = (estructura_recid_rol($row['recid_rol'], 'secciones', 'seccion'));
	$_SESSION['GrupRol'] = (estructura_recid_rol($row['recid_rol'], 'grupos', 'grupo'));
	$_SESSION['SucuRol'] = (estructura_recid_rol($row['recid_rol'], 'sucursales', 'sucursal'));
	
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
	$_SESSION['USER_AGENT']      = $_SERVER['HTTP_USER_AGENT'];
	$_SESSION['IP_CLIENTE']      = $_SERVER['REMOTE_ADDR'];
	$_SESSION['DIA_ACTUAL']      = hoy();
	// $_SESSION["HOST_NAME"]      = gethostbyaddr($_SERVER['REMOTE_ADDR']);

	session_regenerate_id();

	mysqli_free_result($rs);

	login_logs('1');

	if (CountRegMayorCeroMySql("SELECT mod_roles.modulo AS modsrol FROM mod_roles WHERE mod_roles.recid_rol ='$row[recid_rol]' AND mod_roles.modulo = '6'")) {
		header('Location:/' . HOMEHOST . '/mishoras/');
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

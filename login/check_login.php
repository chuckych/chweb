<?php
// try {
$authenticated = false;
$Server = $_SERVER['SERVER_NAME'] ?? '';

// Configuración de la cookie
$path = '/';
$domain = $Server;
$secure = true; // Establece a true si solo deseas que se envíe sobre HTTPS
$sameSite = 'None'; // Puedes configurar a 'Strict', 'Lax', o 'None'

// si la session no esta iniciada
if (session_status() == PHP_SESSION_NONE) {
	session_set_cookie_params([
		'samesite' => 'None',
		'secure' => true, // Asegúrate de usar solo HTTPS para este valor
		'httponly' => true // Esto evita que la cookie sea accesible a través de JavaScript
	]);
}

$guarda = $_POST["guarda"] ?? '';

if ($guarda == "on") {
	##  GUARDAR COOKIE ## 
	// setcookie("user", strtolower($_POST["user"]), time() + 3600 * 24 * 30);
	setcookie('user', strtolower($_POST["user"]), [
		'expires' => time() + 3600 * 24 * 30, // expira en 30 días
		'path' => $path, // disponible en todo el dominio
		'domain' => $domain,
		'secure' => $secure,
		'httponly' => true, // Esto evita que la cookie sea accesible a través de JavaScript
		'samesite' => $sameSite // Establece el valor de SameSite
	]);
	// setcookie("clave", $_POST["clave"], time() + 3600 * 24 * 30);
	setcookie('clave', $_POST["clave"], [
		'expires' => time() + 3600 * 24 * 30, // expira en 30 días
		'path' => $path, // disponible en todo el dominio
		'domain' => $domain,
		'secure' => $secure,
		'httponly' => true, // Esto evita que la cookie sea accesible a través de JavaScript
		'samesite' => $sameSite // Establece el valor de SameSite
	]);
}
if (!($_POST['user'] ?? '') || !($_POST['clave'] ?? '')) {
	header('Location:/' . HOMEHOST . '/login/?error');
	exit;
}
$userLogin = $_GET['conf'] ?? strip_tags(strtolower($_POST['user'] ?? ''));
$passLogin = $_GET['conf'] ?? strip_tags($_POST['clave'] ?? '');
$userLogin = test_input($userLogin);
$userLogin = filter_input(INPUT_POST, 'user', FILTER_DEFAULT);
$passLogin = filter_input(INPUT_POST, 'clave', FILTER_DEFAULT);

require_once __DIR__ . '/../config/conect_pdo.php'; //Conexión a la base de datos

try {

	$a = simple_pdoQuery("SELECT valores FROM params WHERE modulo = 0 and cliente = 0 LIMIT 1"); // Traigo el valor de la version de la DB mysql

	$verDB = intval($a['valores']); // valor de la version de la DB mysql

	$cols = [
		"usuarios.usuario AS 'usuario'",
		"usuarios.clave AS 'clave'",
		"usuarios.nombre AS 'nombre'",
		"usuarios.legajo AS 'legajo'",
		"usuarios.id AS 'id'",
		"usuarios.rol AS 'id_rol'",
		"usuarios.cliente AS 'id_cliente'",
		"clientes.nombre AS 'cliente'",
		"roles.nombre AS 'rol'",
		"roles.recid AS 'recid_rol'",
		"roles.id AS 'id_rol'",
		"clientes.host AS 'host'",
		"clientes.db AS 'db'",
		"clientes.user AS 'user'",
		"clientes.pass AS 'pass'",
		"clientes.auth AS 'auth'",
		"clientes.recid AS 'recid_cliente'",
		"clientes.tkmobile AS 'tkmobile'",
		"clientes.WebService AS 'WebService'",
		"usuarios.recid AS 'recid_user'"
	];

	if ($verDB >= 20251016) {
		$cols[] = "usuarios.user_ad AS 'user_ad'";
	}

	$cols = array_map('trim', $cols); // Asegura que no haya espacios en los elementos del array
	$cols = array_filter($cols); // Elimina elementos vacíos del array
	$cols = implode(", ", $cols); // Convierte el array de columnas en una cadena separada por comas

	$sql = "SELECT $cols FROM usuarios INNER JOIN clientes ON usuarios.cliente=clientes.id INNER JOIN roles ON usuarios.rol=roles.id WHERE usuarios.usuario= :user AND usuarios.estado = '0' LIMIT 1";
	$stmt = $connpdo->prepare($sql); // prepara la consulta
	$stmt->bindParam(':user', $userLogin, PDO::PARAM_STR); // enlaza el parámetro :user con el valor de $userLogin
	$stmt->execute(); // ejecuta la consulta
	$row = $stmt->fetch(PDO::FETCH_ASSOC) ?? []; // obtiene el resultado de la consulta
	$connpdo = null; // cierra la conexión con la base de datos

} catch (\Throwable $th) { // si hay error en la consulta
	$pathLog = __DIR__ . '/../logs/' . date('Ymd') . '_errorLogSesion.log'; // ruta del archivo de Log de errores
	fileLog($th->getMessage(), $pathLog); // escribir en el log de errores el error
	exit; // termina la ejecución
}

// Si el usuario no es de AD, se procede con la autenticación normal
$authenticated = (($row['user_ad'] ?? '0') === '1') ?
	auth_ad($passLogin, $row) :
	password_verify($passLogin ?? '', $row['clave'] ?? ''); // Verifica la contraseña utilizando password_verify

// Si la autenticación es correcta (true)
if ($authenticated) {

	$checkHost = count_pdoQuery("SELECT 1 FROM params WHERE modulo = 1 AND descripcion = 'host' AND cliente = $row[id_cliente] LIMIT 1");

	if (empty($checkHost)) {
		$linux = in_array(OS(), ['linux', 'mac'], true); // Si es linux o mac
		$windows = in_array(OS(), ['windows'], true); // Si es windows

		$OriginalHost = $_SERVER['HTTP_ORIGIN'] ?? 'http://localhost'; // Obtener el host original

		// si es linux o mac quitar el puerto del string $OriginaHost
		// $host = $linux ? 'localhost' : $OriginalHost;
		// $host = $linux ? preg_replace('/:[0-9]+$/', '', $OriginalHost) : $OriginalHost;

		pdoQuery("INSERT INTO params (modulo, descripcion, valores, cliente) VALUES (1, 'host', '$OriginalHost', $row[id_cliente])");
		write_apiKeysFile();
		access_log('Host registrado: ' . $OriginalHost);
	}

	$filepiKeysFile = __DIR__ . '/../mobileApikey.php'; // ruta del archivo de apiKeys
	if (!file_exists($filepiKeysFile)) {
		write_apiKeysFile();
	}

	borrarLogs(__DIR__ . '/../logs/', 7, '.log');
	borrarLogs(__DIR__ . '/../logs/error/', 7, '.log');
	borrarLogs(__DIR__ . '/../logs/access/', 7, '.log');
	borrarLogs(__DIR__ . '/../mobile/hrp/api/logs/', 7, '.log');

	$pathLog = __DIR__ . '/../logs/info/' . date('Ymd') . '_cambios_db.log';

	if (!count_pdoQuery("SELECT valores FROM params WHERE modulo = 0 and cliente = 0 LIMIT 1")) { // Si no existe el registro
		pdoQuery("INSERT INTO params (modulo, descripcion, valores, cliente) VALUES (0, 'Ver DB', 20210101, 0)");
		fileLog("Se inserto el parámetro: \"Ver DB\"", $pathLog); // escribir en el log
	}

	require_once __DIR__ . '/cambios.php'; // Cambios en la DB

	$_SESSION['VER_DB_LOCAL'] = $verDB; // Version de la DB local

	/** chequeamos los módulos asociados al rol de usuarios 
	 * y guardamos en una session el array de los mismos 
	 * */
	function sesionListas(int $id_rol, int $lista, string $nombreSesion)
	{
		$dataLista = dataLista($lista, $id_rol);
		$dataLista = implode(',', $dataLista);
		$_SESSION[$nombreSesion] = $dataLista;
	}
	sesionListas(intval($row['id_rol']), 1, 'ListaNov'); // Sesión lista de novedades
	sesionListas(intval($row['id_rol']), 2, 'ListaONov'); // Sesión lista de otras Novedades
	sesionListas(intval($row['id_rol']), 3, 'ListaHorarios'); // Sesión lista de horarios
	sesionListas(intval($row['id_rol']), 4, 'ListaRotaciones'); // Sesión lista de rotaciones
	sesionListas(intval($row['id_rol']), 5, 'ListaTipoHora'); // Sesión lista de tipos de horas

	// $abm = simpleQueryData("SELECT * FROM abm_roles WHERE recid_rol = '$row[recid_rol]' LIMIT 1", $link); // Traigo los permisos del rol
	$abm = simple_pdoQuery("SELECT * FROM abm_roles WHERE recid_rol = '$row[recid_rol]' LIMIT 1"); // Traigo los permisos del rol

	$keys = ['aFic', 'mFic', 'bFic', 'aNov', 'mNov', 'bNov', 'aHor', 'mHor', 'bHor', 'aONov', 'mONov', 'bONov', 'Proc', 'aCit', 'mCit', 'bCit', 'aTur', 'mTur', 'bTur'];

	$ABMRol = [];
	if ($abm) {
		foreach ($keys as $key) {
			$ABMRol[$key] = $abm[$key] ?? '0';
		}
	} else {
		$ABMRol = array_fill_keys($keys, '0');
	}

	$data_mod = array_pdoQuery("SELECT `mod_roles`.`modulo` AS `modsrol`, `modulos`.`idtipo` AS `tipo`, `modulos`.`nombre` as `modulo`, `modulos`.`orden` as `orden` FROM `mod_roles` INNER JOIN `modulos` ON `mod_roles`.`modulo` = `modulos`.`id` WHERE `mod_roles`.`recid_rol` ='$row[recid_rol]'"); // Traigo los módulos asociados al rol

	$data_mod = array_map(function ($item) {
		$item['modsrol'] = (int) $item['modsrol'];
		$item['tipo'] = (int) $item['tipo'];
		$item['orden'] = (int) $item['orden'];
		return $item;
	}, $data_mod);

	$_SESSION["MODS_ROL"] = $data_mod; // Guardo en la session los módulos asociados al rol
	$_SESSION["ABM_ROL"] = $ABMRol; // Guardo en la session los permisos del rol
	$_SESSION["USER_AD"] = $row['user_ad'] ?? '0'; // Guardo en la session los permisos del rol

	$arrModProy = array_pdoQuery("SELECT `mod_roles`.`modulo` AS `modsrol`, `modulos`.`idtipo` AS `tipo`, `modulos`.`nombre` as `modulo`, `modulos`.`orden` as `orden` FROM `mod_roles` INNER JOIN `modulos` ON `mod_roles`.`modulo`=`modulos`.`id` WHERE `mod_roles`.`recid_rol`='$row[recid_rol]' and `modulos` .`idtipo`=6");

	$_SESSION["MODS_ROL_PROY"] = ($arrModProy) ? $arrModProy : 'error';

	function estructura_recid_rol(string $recid_rol,string  $e, string $data)
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
				// $respuesta = array('success' => 'NO', 'error' => '1', 'mensaje' => 'No se especifico parámetro de estructura');
				// $datos = array($respuesta);
				// echo json_encode($datos);
				// exit;
				break;
		}

		$recidRol = (isset($e)) ? "WHERE $tabla.recid_rol = '$recid_rol'" : "";
		$query = "SELECT DISTINCT $tabla.$ColEstr AS id, $tabla.recid_rol AS recid_rol $concat FROM $tabla $recidRol";
		$result = mysqli_query($link, $query);

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
	function estructUsuario(int $uid, int $lista)
	{
		$v = dataListaEstruct($lista, $uid);
		$v = implode(',', $v);
		$v = ($v == '-') ? '' : $v;
		return $v;
	}

	$_SESSION['EstrUser'] = estructUsuario(intval($row['id']), 8);
	
	if ($row["recid_cliente"] == 'kxo7w2q-') { // solo para la cuenta de SKF 'kxo7w2q-'
		$checkEstruct = count_pdoQuery("select 1 from lista_estruct where uid = '$row[id]'");
		if ($checkEstruct > 0) { // Si ya existe una estructura para el usuario en la tabla lista_estruct Cargamos las sesiones de estructura por usuarios
			$_SESSION['EmprRol'] = estructUsuario(intval($row['id']), 1);
			$_SESSION['PlanRol'] = estructUsuario(intval($row['id']), 2);
			$_SESSION['ConvRol'] = estructUsuario(intval($row['id']), 3);
			$_SESSION['SectRol'] = estructUsuario(intval($row['id']), 4);
			$_SESSION['Sec2Rol'] = estructUsuario(intval($row['id']), 5);
			$_SESSION['GrupRol'] = estructUsuario(intval($row['id']), 6);
			$_SESSION['SucuRol'] = estructUsuario(intval($row['id']), 7);
		} else {
			$_SESSION['EmprRol'] = (estructura_recid_rol($row['recid_rol'], 'empresas', 'empresa'));
			$_SESSION['PlanRol'] = (estructura_recid_rol($row['recid_rol'], 'plantas', 'planta'));
			$_SESSION['ConvRol'] = (estructura_recid_rol($row['recid_rol'], 'convenios', 'convenio'));
			$_SESSION['SectRol'] = (estructura_recid_rol($row['recid_rol'], 'sectores', 'sector'));
			$_SESSION['Sec2Rol'] = (estructura_recid_rol($row['recid_rol'], 'secciones', 'seccion'));
			$_SESSION['GrupRol'] = (estructura_recid_rol($row['recid_rol'], 'grupos', 'grupo'));
			$_SESSION['SucuRol'] = (estructura_recid_rol($row['recid_rol'], 'sucursales', 'sucursal'));
		}
	} else {
		$_SESSION['EmprRol'] = estructUsuario(intval($row['id']), 1);
		$_SESSION['PlanRol'] = estructUsuario(intval($row['id']), 2);
		$_SESSION['ConvRol'] = estructUsuario(intval($row['id']), 3);
		$_SESSION['SectRol'] = estructUsuario(intval($row['id']), 4);
		$_SESSION['Sec2Rol'] = estructUsuario(intval($row['id']), 5);
		$_SESSION['GrupRol'] = estructUsuario(intval($row['id']), 6);
		$_SESSION['SucuRol'] = estructUsuario(intval($row['id']), 7);
	}


	$_SESSION["CONEXION_MS"] = ['host' => $row["host"], 'db' => $row["db"], 'user' => $row["user"], 'pass' => $row["pass"], 'auth' => $row['auth']];
	$_SESSION["secure_auth_ch"] = true;
	$_SESSION["user"] = strtolower($row['usuario']);
	$_SESSION["ultimoAcceso"] = date("Y-m-d H:i:s");
	$_SESSION["UID"] = $row["id"];
	$_SESSION["NOMBRE_SESION"] = $row["nombre"];
	$_SESSION["LEGAJO_SESION"] = $row["legajo"];
	$_SESSION["RECID_USER"] = $row["recid_user"];
	$_SESSION["ID_ROL"] = $row["id_rol"];
	$_SESSION["ID_CLIENTE"] = $row["id_cliente"];
	$q = simple_pdoQuery("SELECT ApiMobileHRP FROM clientes WHERE id = '$row[id_cliente]' LIMIT 1");
	$_SESSION["APIMOBILEHRP"] = $q["ApiMobileHRP"];
	$_SESSION["CLIENTE"] = $row["cliente"];
	$_SESSION["ROL"] = $row["rol"];
	$_SESSION["RECID_ROL"] = $row["recid_rol"];
	$_SESSION["RECID_CLIENTE"] = $row["recid_cliente"];
	$_SESSION["TK_MOBILE"] = $row["tkmobile"];
	$_SESSION["WEBSERVICE"] = $row["WebService"];
	$_SESSION["HASH_CLAVE"] = ($row['clave']);
	$_SESSION["LIMIT_SESION"] = 3600;
	$_SESSION['USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
	$_SESSION['IP_CLIENTE'] = get_client_ip();
	$_SESSION['DIA_ACTUAL'] = hoy();
	$_SESSION['VER_DB_CH'] = false;
	$_SESSION['CONECT_MSSQL'] = false;
	$_SESSION['HOST_CHWEB'] = OS() === 'linux' ? 'http://localhost' : gethostCHWeb();

	$modRol = array_pdoQuery("SELECT mod_roles.modulo AS 'id', modulos.nombre as 'modulo' FROM mod_roles INNER JOIN modulos ON mod_roles.modulo = modulos.id WHERE mod_roles.recid_rol ='$row[recid_rol]'");
	$modRol = array_map(function ($item) {
		$item['id'] = (string) $item['id'];
		return $item;
	}, $modRol);

	$_SESSION['MODULOS'] = $modRol;

	// OBTENER ETIQUETAS DE ESTRUCTURA PARA EL CLIENTE
	$url = gethostCHWeb() . "/" . HOMEHOST . "/api/v1/parametros/paragene";
	$authBasic = base64_encode('chweb:' . HOMEHOST);
	$token = sha1($row["recid_cliente"]);
	$paragene = requestApi($url, $token, $authBasic, [], 20, 'GET');
	$paragene = parseApiResponse($paragene);
	$Etiquetas = $paragene['DATA']['Etiquetas'] ?? [];

	if (session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
	}
	$_SESSION['LABELS'] = $Etiquetas;

	login_logs('1');

	if ($_POST['lasturl'] ?? '') {
		header('Location:' . urldecode($_POST['lasturl']));
		// } else if (count_pdoQuery("SELECT mod_roles.modulo AS modsrol FROM mod_roles WHERE mod_roles.recid_rol ='$row[recid_rol]' AND mod_roles.modulo = '8'")) {
		// 	header('Location:/' . HOMEHOST . '/dashboard/');
	} else if (count_pdoQuery("SELECT mod_roles.modulo AS modsrol FROM mod_roles WHERE mod_roles.recid_rol ='$row[recid_rol]' AND mod_roles.modulo = '6'")) {
		header('Location:/' . HOMEHOST . '/mishoras/');
		exit;
	} else if (count_pdoQuery("SELECT mod_roles.modulo AS modsrol FROM mod_roles WHERE mod_roles.recid_rol ='$row[recid_rol]' AND mod_roles.modulo = '5'")) {
		header('Location:/' . HOMEHOST . '/mobile/');
	} else if (count_pdoQuery("SELECT mod_roles.modulo AS modsrol FROM mod_roles WHERE mod_roles.recid_rol ='$row[recid_rol]' AND mod_roles.modulo = '32'")) {
		header('Location:/' . HOMEHOST . '/mobile/hrp/');
	} else if (count_pdoQuery("SELECT mod_roles.modulo AS modsrol FROM mod_roles WHERE mod_roles.recid_rol ='$row[recid_rol]' AND mod_roles.modulo = '43'")) {
		header('Location:/' . HOMEHOST . '/proy/');
	} else {
		header('Location:/' . HOMEHOST . '/inicio/');
	}
	access_log('Login correcto');


} else {
	login_logs('2');
	header('Location:/' . HOMEHOST . '/login/?error');
	access_log('Login incorrecto');
}

// } catch (\Throwable $th) {
// 	error_log($th->getMessage()); // escribir en el log de errores
// }
<?php
/** FUNCIÓN PARA ESCRIBIR UN ARCHIVO */
E_ALL();
if (isset($_GET['_c'])) {
	require __DIR__ . '/conect_mysql.php';
	$querydb = "SELECT clientes.host, clientes.db, clientes.user, clientes.pass, clientes.auth FROM clientes WHERE clientes.recid = '$_GET[_c]'";
	$result = mysqli_query($link, $querydb);
	$row    = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
	$serverName = $row['host'];
	$db = $row['db'];
	$user = $row['user'];
	$pass = $row['pass'];
	$auth = $row['auth'];
	mysqli_close($link);
	$conexionSesion = false;
} else {
	$serverName = $_SESSION["CONEXION_MS"]['host']; //serverName
	$db = $_SESSION["CONEXION_MS"]['db']; //"SIS_DB";
	$user = $_SESSION["CONEXION_MS"]['user'];
	$pass = $_SESSION["CONEXION_MS"]['pass'];
	$auth = $_SESSION["CONEXION_MS"]['auth']; // 0 = SQL Server Authentication, 1 = Windows Authentication
	$conexionSesion = true;
}
if ((empty($db . $user . $pass . $serverName))) { // Si no hay datos de conexion SQL
	$data = array();
	PrintRespuestaJson('Error', 'Error: no hay datos de conexion SQL');
	exit; // Termina el script
}

switch ($auth) { // 0 = SQL Server Authentication, 1 = Windows Authentication
	case '1':
		$connectionInfo = array("Database" => $db, "CharacterSet" => "utf-8");  // Windows Authentication
		break;
	case '0':
		$connectionInfo = array("Database" => $db, "UID" => $user, "PWD" => $pass, "CharacterSet" => "utf-8"); // SQL Server Authentication
		break;
}

// require __DIR__. '../../log.class.php';
/********************************************************************************* */
/** conexion mediante autenticacion de windows */
//$connectionInfo = array("Database"=>$base, "CharacterSet" => "UTF-8");
/********************************************************************************* */
// header("Content-Type: application/json");
// $data = array('status' => 'Error', 'Mensaje' => $connectionInfo);
// echo json_encode($data);
// exit;
$errorLink = false;
$link = sqlsrv_connect($serverName, $connectionInfo);
if ($link === false) {
	$errorLink = true;
	if (($errors = sqlsrv_errors()) != null) {
		foreach ($errors as $key => $error) {
			date_default_timezone_set('America/Argentina/Buenos_Aires');
			$date = date('d-m-Y H:i:s');
			$errorHTML = "<div class=''>" . $date . "<br />SQLSTATE: <b>" . $error['SQLSTATE'] . "</b><br />Code: <b>" . $error['code'] . "</b><br />Mensaje: <b>" . $error['message'] . "</b></div>"; // Texto Error en HTML
			$errorHTML2 = $date . PHP_EOL."SQLSTATE: " . $error['SQLSTATE'] . "\nCode: " . $error['code'] . "\nMensaje: " . $error['message']; // Texto Error en HTML
			$SQLSTATE = $error['SQLSTATE'];  // Codigo de error SQLSTATE
			$code = $error['code'];  // Codigo de error SQL
			$message = $error['message']; // Mensaje de error
			$text = "\nSQLSTATE: \"$SQLSTATE\"\ncode: \"$code\"\nMessage: \"$message\""; // Texto Error
			$text .= ($key === 1) ? "\n----" : ''; // Separador
			if ($key === 1) : break; // Solo mostrar el primer error
			endif; // si es el primer error, termina el script
			$ruta_archivo = __DIR__ . "../../logs/error/" . date('Ymd') . "_Error_Conn.log"; // Ruta del archivo de error
			fileLog($text, $ruta_archivo); // Función para escribir en el archivo de error
		}
		if (!$conexionSesion) { // Si no se esta usando la conexion de sesion
			header("Content-Type: application/json"); 	// Tipo de contenido de la respuesta
			// echo '<pre>';
			PrintRespuestaJson('Error', $errorHTML);
			exit;
		}
	}
} else { // Si la conexion fue exitosa
	$queryDateFirst = "SET DATEFIRST 7"; // Establece el primer dia de la semana como el 1
	$rs = sqlsrv_query($link, $queryDateFirst); // Ejecuta la consulta
	sqlsrv_free_stmt($rs); // Libera la consulta

	if ($conexionSesion) {
		if ((!$_SESSION['VER_DB_CH'])) { // Si no se ha verificado la version de la base de datos
			$_SESSION['VER_DB_CH'] = getVerDBCH($link); // Obtiene la version de la base de datos
		}
		if ((!$_SESSION['CONECT_MSSQL'])) { // Si no se ha conectado a la base de datos
			$_SESSION['CONECT_MSSQL'] = true; // Se conecto a la base de datos
		}
	}
}
// print_r($connectionInfo);
// print_r(json_encode(sqlsrv_client_info($link)));
// if ($client_info = sqlsrv_client_info($link)) {
// 	foreach ($client_info as $key => $value) {
// 		// echo $key.": ".$value."<br />";
// 	}
// } 
// else {
// 	header("Content-Type: application/json");
// 	$data = array('status' => 'Error', 'Mensaje' => 'Error al recuperar información del cliente.');
// 	echo json_encode($data);
// 	// exit;
// }

<?php
// session_start();
// require __DIR__ . '/index.php';
/** FUNCIÓN PARA ESCRIBIR UN ARCHIVO */
if (isset($_GET['_c'])) {
	require __DIR__ . '/conect_mysql.php';
	$querydb = "SELECT clientes.host, clientes.db, clientes.user, clientes.pass, clientes.auth FROM clientes WHERE clientes.recid = '$_GET[_c]'";
	// print_r($query);
	$result = mysqli_query($link, $querydb);
	$row    = mysqli_fetch_assoc($result);
	mysqli_free_result($result);
	$serverName = $row['host'];
	$db = $row['db'];
	$user = $row['user'];
	$pass = $row['pass'];
	$auth = $row['auth'];
	mysqli_close($link);
} else {
	$serverName = $_SESSION["CONEXION_MS"]['host'];
	$db = $_SESSION["CONEXION_MS"]['db'];
	$user = $_SESSION["CONEXION_MS"]['user'];
	$pass = $_SESSION["CONEXION_MS"]['pass'];
	$auth = $_SESSION["CONEXION_MS"]['auth'];
}
switch ($auth) {
	case '1':
		$connectionInfo = array("Database" => $db, "CharacterSet" => "utf-8");
		break;
	case '0':
		$connectionInfo = array("Database" => $db, "UID" => $user, "PWD" => $pass, "CharacterSet" => "utf-8");
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

$link = sqlsrv_connect($serverName, $connectionInfo);
if ($link === false) {
	if (($errors = sqlsrv_errors()) != null) {
		foreach ($errors as $error) {
			date_default_timezone_set('America/Argentina/Buenos_Aires');
			$date = date('d-m-Y H:i:s');
			$TextErr = "------------------------------\n" . $date . "\nSQLSTATE: " . $error['SQLSTATE'] . "\ncode: " . $error['code'] . "\nMensaje: " . $error['message'] . "\n------------------------------";
			$TextErr2 = "<div class='alert alert-warning m-3 w-100'>" . $date . "<br />SQLSTATE: " . $error['SQLSTATE'] . "<br />code: " . $error['code'] . "<br /><span class='fw5'>Mensaje: " . $error['message'] . "</span><br /></div>";
			$TextErr3 = "<div class=''>" . $date . "<br />SQLSTATE: <b>" . $error['SQLSTATE'] . "</b><br />Code: <b>" . $error['code'] . "</b><br />Mensaje: <b>" . $error['message'] . "</b></div>";
			EscribirArchivo("Error_Conn_" . date('YmdHis'), "../logs/error/", $TextErr, false, true, false);
		}
	}
	header("Content-Type: application/json");
	$data = array('status' => 'Error', 'Mensaje' => $TextErr3);
	echo json_encode($data);
	exit;
	// header('Location:/' . HOMEHOST . '/inicio/?err_conexion_mssql');
	// exit;
} else {
	$queryDateFirst = "SET DATEFIRST 7";
	$rs = sqlsrv_query($link, $queryDateFirst);
	sqlsrv_free_stmt($rs);
}
// print_r(json_encode(sqlsrv_client_info($link)));
if ($client_info = sqlsrv_client_info($link)) {
	foreach ($client_info as $key => $value) {
		// echo $key.": ".$value."<br />";
	}
} else {
	header("Content-Type: application/json");
	$data = array('status' => 'Error', 'Mensaje' => 'Error al recuperar información del cliente.');
	echo json_encode($data);
	exit;
}

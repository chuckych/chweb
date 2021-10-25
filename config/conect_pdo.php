<?php
require __DIR__ . '../dataconnmysql.php';
// class Connection
// {
// 	public static function make($host, $db, $user, $pw)
// 	{
// 		$dsn = "mysql:host=$host;dbname=$db;charset=UTF8";
// 		try {
// 			if ($_SERVER['SERVER_NAME'] == 'localhost') { // Si es localhost
// 				$pathLog = __DIR__ . '../../logs/' . date('Ymd') . '_successConexionPDO.log';
// 				fileLog($_SERVER['PHP_SELF'] . ' -> Conexion Exitosa', $pathLog); // escribir en el log de errores
// 			}
// 			$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
// 			return new PDO($dsn, $user, $pw, $options);
// 		} catch (PDOException $e) {
// 			$msj = die($e->getMessage());
// 			$pathLog = __DIR__ . '../../logs/' . date('Ymd') . '_errorConexionPDO.log';
// 			fileLog($msj, $pathLog); // escribir en el log de errores
// 			header("location:/" . HOMEHOST . "/login/error.php?e=noHayConexion"); // Redirecciona a login
// 			exit;
// 		}
// 	}
// }
// return Connection::make($host, $db, $user, $pw);

$dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

try {
	$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
	$connpdo =  new PDO($dsn, $user, $pw, $options);
	if ($connpdo) {
		if ($_SERVER['SERVER_NAME'] == 'localhost') { // Si es localhost
			//$pathLog = __DIR__ . '../../logs/' . date('Ymd') . '_successConexionPDO.log';
			//fileLog($_SERVER['PHP_SELF'] . ' -> Conexion Exitosa', $pathLog); // escribir en el log de errores
		}
	}
} catch (PDOException $e) {
	$msj = die($e->getMessage());
	$pathLog = __DIR__ . '../../logs/' . date('Ymd') . '_errorConexionPDO.log';
	fileLog($msj, $pathLog); // escribir en el log de errores
	header("location:/" . HOMEHOST . "/login/error.php?e=noHayConexion"); // Redirecciona a login
	exit;
}

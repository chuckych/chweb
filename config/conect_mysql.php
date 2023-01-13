<?php
E_ALL();
require __DIR__ . '../dataconnmysql.php';
try {
	if ($link = mysqli_connect($host, $user, $pw, $db)) {
		mysqli_query($link, "SET @@GLOBAL.sql_mode='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION'");
		printf("", mysqli_character_set_name($link));
		if (!mysqli_set_charset($link, "utf8")) {
			$pathLog = __DIR__ . '../../logs/' . date('Ymd') . '_error_mysqli_set_charset.log';
			fileLog("Error cargando el conjunto de caracteres utf8: %s\n </br>", $pathLog); // escribir en el log de errores
			printf("Error cargando el conjunto de caracteres utf8: %s\n </br>", mysqli_error($link));
			exit();
		} else {
			printf("", mysqli_character_set_name($link));
			// if ($_SERVER['SERVER_NAME'] == 'localhost') { // Si es localhost
				//$pathLog = __DIR__ . '../../logs/' . date('Ymd') . '_successConexionDB.log';
				//fileLog($_SERVER['PHP_SELF'] . ' -> Conexion Exitosa', $pathLog); // escribir en el log de errores
			// }
		}
	} else {
		throw new Exception(mysqli_connect_error());
	}
} catch (Exception $e) {
	$pathLog = __DIR__ . '../../logs/' . date('Ymd') . '_errorConexionDB.log';
	fileLog($_SERVER['PHP_SELF'] . ' -> ' . $e->getMessage(), $pathLog); // escribir en el log de errores
	header("location:/" . HOMEHOST . "/login/error.php?e=noHayConexion"); // Redirecciona a login
	exit;
}
<?php 
error_reporting(E_ALL);
ini_set('display_errors', '0');
$host = "localhost";
$user = "root";
$pw   = "";
$db   = "chwebhrp";
$link = mysqli_connect($host, $user, $pw, $db) or die("
<html>
<body style='background:#efefef'>
<h1 style='text-align:center; padding:20px;'>
Error de Conexión.
</h1>
</body>
</html>
");
printf("", mysqli_character_set_name($link));
if (!mysqli_set_charset($link, "utf8")) {
	printf("Error cargando el conjunto de caracteres utf8: %s\n </br>", mysqli_error($link));
	exit();
} else {
	printf("", mysqli_character_set_name($link));
}
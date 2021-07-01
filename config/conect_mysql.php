<?php 
error_reporting(E_ALL);
ini_set('display_errors', '1');
$host = "localhost";
$user = "root";
$pw   = "";
$db   = "chwebhrp";
$ErrConBDHTML="<html><body style='background:#333'><h1 style='text-align:center; padding:20px;color:#fff; font-family: Arial'>No hay conexión.
</h1></body></html>";
$link = mysqli_connect($host, $user, $pw, $db) or die($ErrConBDHTML);
mysqli_query($link, "SET @@GLOBAL.sql_mode='NO_ZERO_IN_DATE,NO_ZERO_DATE,NO_ENGINE_SUBSTITUTION'");
printf("", mysqli_character_set_name($link));
if (!mysqli_set_charset($link, "utf8")) {
	printf("Error cargando el conjunto de caracteres utf8: %s\n </br>", mysqli_error($link));
	exit();
} else {
	printf("", mysqli_character_set_name($link));
}
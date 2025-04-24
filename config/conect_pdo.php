<?php
// require __DIR__ . '/function.php';
require __DIR__ . '/../vendor/autoload.php';

$routeEnv = getConfigPath();

if (!is_dir($routeEnv)) {
	if (file_exists(__DIR__ . '/dataconnmysql.php')) {
		require __DIR__ . '/dataconnmysql.php';
	} else {
		$host = "localhost";
		$user = "root";
		$pw = "";
		$db = "chweb";
	}
	mkdir($routeEnv);
	if (!file_exists($routeEnv . '.env')) {
		$environment = "DB_CHWEB_HOST=$host\nDB_CHWEB_USER=$user\nDB_CHWEB_PASSWORD=$pw\nDB_CHWEB_NAME=$db\n";
		file_put_contents($routeEnv . '.env', $environment);
		$dataconnmysql = __DIR__ . '/dataconnmysql.php';
		if (!file_exists($dataconnmysql)) {
			return false;
		}
		if (!unlink(__DIR__ . '/dataconnmysql.php')) {
			// echo ("Error deleting");
		} else {
			// echo ("Deleted");
		}
	}
}

$dotenv = Dotenv\Dotenv::createImmutable($routeEnv);
$dotenv->safeLoad();

$host = $_ENV['DB_CHWEB_HOST'] ?? '';
$user = $_ENV['DB_CHWEB_USER'] ?? '';
$pw = $_ENV['DB_CHWEB_PASSWORD'] ?? '';
$db = $_ENV['DB_CHWEB_NAME'] ?? '';

$dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

try {
	$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
	$connpdo = new PDO($dsn, $user, $pw, $options);
} catch (PDOException $e) {
	$msj = trim($e->getMessage());
	$pathLog = __DIR__ . '/../logs/' . date('Ymd') . '_errorConexionPDO.log';
	fileLog($msj, $pathLog); // escribir en el log de errores
	header("location:/" . HOMEHOST . "/login/error.php?e=noHayConexion"); // Redirection a login
	exit;
}

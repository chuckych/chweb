<?php
// require __DIR__ . '/function.php';
require __DIR__ . '/../vendor/autoload.php';

// Evitar reconexión si ya existe la conexión
if (!isset($connpdo) || !($connpdo instanceof PDO)) {
	try {
		$routeEnv = in_array(getOS(), ['linux', 'mac'], true) ? '/' : getConfigPath();
		$dotenv = Dotenv\Dotenv::createImmutable($routeEnv);
		$dotenv->safeLoad();

		// Validar que las variables de entorno existan
		$host = $_ENV['DB_CHWEB_HOST'] ?? '';
		$user = $_ENV['DB_CHWEB_USER'] ?? '';
		$pw = $_ENV['DB_CHWEB_PASSWORD'] ?? '';
		$db = $_ENV['DB_CHWEB_NAME'] ?? '';

		// Validar que ningún parámetro esté vacío
		if (empty($host) || empty($user) || empty($db)) {
			$msj = 'Error: Faltan parámetros de conexión en las variables de entorno';
			$pathLog = __DIR__ . '/../logs/' . date('Ymd') . '_errorConexionPDO.log';
			error_log($msj);
			fileLog($msj, $pathLog);
			throw new PDOException($msj);
		}

		$dsn = "mysql:host=$host;dbname=$db;charset=UTF8";

		// Opciones PDO mejoradas
		$options = [
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lanzar excepciones en errores
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch asociativo por defecto
			// PDO::ATTR_EMULATE_PREPARES => false, // Usar prepared statements reales
			PDO::ATTR_PERSISTENT => false, // Desactivar conexiones persistentes por defecto
			PDO::ATTR_TIMEOUT => 5, // Timeout de conexión de 5 segundos
			PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci", // Soporte completo UTF-8
		];

		$connpdo = new PDO($dsn, $user, $pw, $options);
		
	} catch (PDOException $e) {
		$msj = trim($e->getMessage());
		$pathLog = __DIR__ . '/../logs/' . date('Ymd') . '_errorConexionPDO.log';
		
		// Log más detallado (sin exponer contraseña)
		$errorDetail = sprintf(
			"[%s] Error de conexión PDO: %s | Host: %s | DB: %s | User: %s",
			date('Y-m-d H:i:s'),
			$msj,
			$host ?? 'N/A',
			$db ?? 'N/A',
			$user ?? 'N/A'
		);
		
		error_log($errorDetail);
		fileLog($errorDetail, $pathLog);
		
		// Terminar la ejecución
		exit;
	} catch (Exception $e) {
		$msj = 'Error inesperado al establecer conexión: ' . trim($e->getMessage());
		$pathLog = __DIR__ . '/../logs/' . date('Ymd') . '_errorConexionPDO.log';
		error_log($msj);
		fileLog($msj, $pathLog);
		exit;
	}
}

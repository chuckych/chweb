<?php
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../config/index.php';
header("Content-Type: application/json");
E_ALL();
// sleep(2);
/** Consultamos el si el usuario y clave son correctos */
$tarjetaLogin = filter_input(INPUT_POST, 'tarjeta', FILTER_DEFAULT);

if (valida_campo($tarjetaLogin)) {
    PrintRespuestaJson('error', '<div class="d-inline-flex align-items-center text-danger font-weight-bold"><span class="bi bi-credit-card-2-front me-2 font15"></span>Tarjeta obligatoria<div>');
    exit;
}
session_start();

require_once __DIR__ . '../../../config/conect_pdo.php'; //Conexion a la base de datos
try {
    $sql = "SELECT usuarios.usuario AS 'usuario', usuarios.clave AS 'clave', usuarios.nombre AS 'nombre', usuarios.legajo AS 'legajo', usuarios.id AS 'id', usuarios.rol AS 'id_rol', usuarios.cliente AS 'id_cliente', clientes.nombre AS 'cliente', roles.nombre AS 'rol', roles.recid AS 'recid_rol', roles.id AS 'id_rol', clientes.host AS 'host', clientes.db AS 'db', clientes.user AS 'user', clientes.pass AS 'pass', clientes.auth AS 'auth', clientes.recid AS 'recid_cliente', clientes.tkmobile AS 'tkmobile', clientes.WebService AS 'WebService', usuarios.recid AS 'recid_user', uident.expira as 'expira', uident.login as 'login', usuarios.estado as 'estado' FROM usuarios 
    INNER JOIN clientes ON usuarios.cliente=clientes.id 
    INNER JOIN roles ON usuarios.rol=roles.id 
    INNER JOIN uident ON usuarios.id = uident.usuario 
    WHERE uident.ident = :tarjeta LIMIT 1";
    $stmt = $connpdo->prepare($sql); // prepara la consulta
    $stmt->bindParam(':tarjeta', $tarjetaLogin);
    $stmt->execute(); // ejecuta la consulta
    $row  = $stmt->fetch(PDO::FETCH_ASSOC); // obtiene el resultado de la consulta
    $connpdo = null; // cierra la conexion con la base de datos
} catch (\Throwable $th) { // si hay error en la consulta
    $pathLog = __DIR__ . '../../../logs/' . date('Ymd') . '_errorLogSesion.log'; // ruta del archivo de Log de errores
    fileLog($th->getMessage(), $pathLog); // escribir en el log de errores el error
    exit; // termina la ejecucion
}
(!$row) ? PrintRespuestaJson('error', 'Intente de nuevo') . exit:'';
// PrintRespuestaJson('error', $row['tarjeta']);
// exit;
($row['expira'] >= hoy() || $row['expira'] == '0000-00-00') ? '' : PrintRespuestaJson('error', 'Tarjeta Exiprada') . exit;
/** Si la fecha de expiracion es mayor a igual a la actual*/
($row['login'] == '0') ? '' : PrintRespuestaJson('error', 'Login NO VALIDO') . exit;
/** Si el login es igual a 0 */
($row['estado'] == '0') ? '' : PrintRespuestaJson('error', 'Usuario NO VALIDO') . exit;
/** Si el estado es igual a 0 */

if ($row['usuario']) {
    require __DIR__ . './processUser.php';
} else {
    session_destroy();
    $_SESSION["UID"]        = '';
    $_SESSION["ID_ROL"]     = '';
    $_SESSION["ID_CLIENTE"] = '';
    login_logs('2', 'Tarjeta Invalida');
    PrintRespuestaJson('error', 'INGRESO INCORRECTO') . exit;
}

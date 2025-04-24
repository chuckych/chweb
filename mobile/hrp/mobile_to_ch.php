<?php
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../config/index.php';
header("Content-Type: application/json");

error_reporting(E_ALL);
ini_set('display_errors', '0');

function MSQuery2($query)
{
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    require __DIR__ . '/../../config/conect_mssql.php';
    try {
        $stmt = sqlsrv_query($link, $query, $params, $options);
        if ($stmt === false) {
            $error = sqlsrv_errors();
            foreach ($error as $key => $e) {
                throw new Exception(($e['message']));
            }
            return false;
        } else {
            return true;
        }
    } catch (\Throwable $th) { // si hay error en la consulta
        $error[] = array(1);
        $pathLog = __DIR__ . '/logs/error_insert_CH/';
        fileLog($th->getMessage(), $pathLog . '/' . date('Ymd') . '_error_MSQuery.log');
    }
}
function json($v, $code = 200)
{
    return Flight::json($v);
}

$r = Flight::request();

($r->method != 'POST') ? json("Invalid request method :" . $r->method) . exit : '';

$dp = $r->data;

$user = $dp->user ?? '';
$inicio = $dp->inicio ?? '';
$fin = $dp->fin ?? '';
$recid = $dp->recid ?? '';

$_GET['_c'] = $recid; // para la conexion SQL

if (empty($user)) {
    json('El usuario es requerido', 400);
    exit;
}
if (empty($inicio)) {
    json('La fecha de inicio es requerida', 400);
    exit;
}
if (empty($fin)) {
    json('La fecha de fin es requerida', 400);
    exit;
}
if (empty($recid)) {
    json('La recid es requerido', 400);
    exit;
}

$inicio .= " 00:00:00";
$fin .= " 23:59:59";

$user = explode(',', $user);

foreach ($user as $key => $v) {
    $u[] = intval($v);
}

$u = implode(',', array_unique($u));

$query = "SELECT `id_user`, `fechaHora`, `eventZone` FROM `reg_` `r` WHERE `r`.`id_user` IN ($u) AND `r`.`fechahora` BETWEEN '$inicio' AND '$fin'";
$ar = array_pdoQuery($query);
if (empty($ar)) {
    json('No hay resultados en Mobile', 400);
    exit;
}

$i = array();
foreach ($ar as $key => $v) {

    $RegTarj = ($v['id_user']);
    $RegFech = FechaFormatVar($v['fechaHora'], 'Ymd');
    $RegFech2 = FechaFormatVar($v['fechaHora'], 'Y-m-d');
    $RegHora = FechaFormatVar($v['fechaHora'], 'H:i');
    $RegRelo = '9999';
    $RegLect = $v['eventZone'];
    $RegEsta = '0';
    $insertFichadas = "INSERT INTO FICHADAS (RegTarj, RegFech, RegHora, RegRelo, RegLect, RegEsta) VALUES ('$RegTarj', '$RegFech', '$RegHora', '$RegRelo', '$RegLect', '$RegEsta');";
    $i[] = ("$RegTarj $RegFech2 $RegHora $RegRelo $RegLect $RegEsta");
    MSQuery2($insertFichadas);

}
echo json_encode(array(
    "Fichadas encontradas" => count($ar),
    "Fichadas insertadas" => count($i),
    "Fichadas detalle" => $i,
    "Errores" => count($error),
    "_request_data" => $r->data,
    "_request_ip" => $r->ip,
    '_usersClean' => $u
), JSON_PRETTY_PRINT);
exit;

// $valores = implode(', ', array_reduce($data, function ($carry, $item) {
//     $values = implode(', ', array_map(function ($value) {
//         return '\'' . addslashes($value) . '\'';
//     }, array_values($item)));
//     $carry[] = "($values)";
//     return $carry;
// }, []));

// $insertFichadas = "INSERT INTO FICHADAS (RegTarj, RegFech, RegHora, RegRelo, RegLect, RegEsta) VALUES $valores;";

// $_GET['_c'] = $recid;
// try {
//     MSQuery($insertFichadas);
//     $success = array('Se insertaron: ' . count($data) . ' Registros.');
//     print_r($success) . PHP_EOL;
//     // echo '<p>Se insertaron ' . count($data) . ' fichadas</p>';
// } catch (\Throwable $th) { // si hay error en la consulta
//     $error[] = $th->getMessage();
//     print_r($error);
// }
// exit;

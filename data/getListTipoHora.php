<?php
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
require_once __DIR__ . '../../config/index.php';
session_start();
E_ALL();
require_once __DIR__ . '../../config/conect_mssql.php';

$params  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$filtroTipoHora = '';
$ListaTipoHora = $_SESSION['ListaTipoHora'];
if ($ListaTipoHora  != "-") {
    $filtroTipoHora = " AND TipoHora.THoCodi IN ($ListaTipoHora)";
}

$_POST['modHora'] = $_POST['modHora'] ?? '';
$modHora = test_input($_POST['modHora']);

$_POST['THoCodi'] = $_POST['THoCodi'] ?? '';
$THoCodi = test_input($_POST['THoCodi']);

$FiltroTHoCodi = ($modHora == 1) ? "AND THoCodi = '$THoCodi'" : '';

$_POST['q'] = $_POST['q'] ?? '';
$q = test_input($_POST['q']);

$Datos = explode('-', $_POST['Datos']);
$FicLega = $Datos[0];
$FicFech = $Datos[1];

$data = array();

$q = (ValString($_POST['q'])) ? $_POST['q'] : exit;
// $NovCNove = (ValNumerico($_POST['NovCNove'])) ? $_POST['NovCNove'] : exit;
$valores='';
$query="SELECT FICHAS1.FicHora FROM FICHAS1 WHERE FICHAS1.FicLega='$FicLega' AND FICHAS1.FicFech='$FicFech'";
$result  = sqlsrv_query($link, $query, $params, $options);
if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)) :
        $FicHora[]    = $row['FicHora'];
        $valores = implode(",", $FicHora);
        $valores = 'AND THoCodi NOT IN ('.$valores.')';
    endwhile;
}
sqlsrv_free_stmt($result);


$query="SELECT THoCodi AS Codigo, THoDesc AS Descripcion FROM TipoHora WHERE THoCodi > 0 $valores AND THoDesc LIKE '%$q%' $FiltroTHoCodi $filtroTipoHora ORDER BY THoCodi";

$result  = sqlsrv_query($link, $query, $params, $options);
// print_r($query); exit;

if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)) :
        // $cod = str_pad($row['Codigo'], 3, "0", STR_PAD_LEFT);
        $data[] = array(
            'id'       => $row['Codigo'],
            'text'     => $row['Descripcion'],
        );
    endwhile;
    sqlsrv_free_stmt($result);
}

sqlsrv_close($link);
echo json_encode(($data)); 

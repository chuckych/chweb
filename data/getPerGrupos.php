<?php
session_start();
// header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();

FusNuloGET("Emp", '');
FusNuloGET("Plan", '');
FusNuloGET("Sect", '');
FusNuloGET("Sec2", '');
FusNuloGET("Grup", '');
FusNuloGET("Sucur", '');
FusNuloGET("Tipo", '');
FusNuloGET("Per",'');

$Per = !empty(($_GET['Per'])) ? implode(',', $_GET['Per']):''; 
$Per = !empty(($_GET['Per'])) ? "AND PERSONAL.LegNume IN ($Per)" : '';
$Tipo = test_input($_GET['Tipo']);
switch ($Tipo) {
    case '2':
        $Tipo = "AND PERSONAL.LegTipo = '0'";
        break;
    case '1':
        $Tipo = "AND PERSONAL.LegTipo = '1'";
        break;
    default:
        $Tipo = "AND PERSONAL.LegTipo = '0'";
        break;
}

$Tipo = empty(($_GET['Tipo'])) ? "" : $Tipo;

$Empresa      = datosGet($_GET['Emp'], "PERSONAL.LegEmpr");
$Planta       = datosGet($_GET['Plan'], "PERSONAL.LegPlan");
$Sector       = datosGet($_GET['Sect'], "PERSONAL.LegSect");
$Seccion      = datosGet($_GET['Sec2'], "PERSONAL.LegSec2");
$Grupo        = datosGet($_GET['Grup'], "PERSONAL.LegGrup");
$Sucursal     = datosGet($_GET['Sucur'], "PERSONAL.LegSucu");

$TipoPersonal = $Tipo;

$FilterEstruct  = $Empresa;
$FilterEstruct  .= $Planta;
$FilterEstruct  .= $Sector;
$FilterEstruct  .= $Seccion;
$FilterEstruct  .= $Grupo;
$FilterEstruct  .= $Sucursal;
$FilterEstruct  .= $TipoPersonal;
$FilterEstruct  .= $Per;

require __DIR__ . '../../filtros/filtros.php';
require __DIR__ . '../../config/conect_mssql.php';

FusNuloGET('q', '');
$q = $_GET['q'];

$query="SELECT PERSONAL.LegGrup AS 'id', GRUPOS.GruDesc AS 'text' FROM PERSONAL INNER JOIN GRUPOS ON PERSONAL.LegGrup=GRUPOS.GruCodi WHERE PERSONAL.LegNume >'0' AND PERSONAL.LegFeEg='17530101' AND CONCAT(PERSONAL.LegGrup,GRUPOS.GruDesc) LIKE '%$q%' AND PERSONAL.LegGrup >'0' AND PERSONAL.LegFeEg='17530101' $filtros $FilterEstruct GROUP BY PERSONAL.LegGrup, GRUPOS.GruDesc";
// print_r($query); exit;

$params  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$result  = sqlsrv_query($link, $query, $params, $options);
$data    = array();

if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)) :

        $id   = $row['id'];
        $text = $row['text'];

        $data[] = array(
            'id'   => $id,
            'text' => $text,
        );
    endwhile;
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode($data);

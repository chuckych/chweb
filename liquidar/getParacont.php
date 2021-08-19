<?php
session_start();
// header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");
E_ALL();

require __DIR__ . '../../filtros/filtros.php';
require __DIR__ . '../../config/conect_mssql.php';

FusNuloPOST('q', '');
$q = $_POST['q'];

FusNuloPOST('Anio', date('Y'));
$anio = $_POST['Anio'];
FusNuloPOST('Mes', date('m'));
$mes = $_POST['Mes'];

$query="SELECT ARCHIVOS.ArchPath AS 'ArchPath', ARCHIVOS.ArchDesc AS 'ArchDesc', ARCHIVOS.ArchNomb AS 'ArchNomb', PARACONT.ParPeMeD AS 'MensDesde', PARACONT.ParPeMeH AS 'MensHasta', PARACONT.ParPeJ1D AS 'Jor1Desde', PARACONT.ParPeJ1H AS 'Jor1Hasta', PARACONT.ParPeJ2D AS 'Jor2Desde', PARACONT.ParPeJ2H AS 'Jor2Hasta' FROM PARACONT INNER JOIN ARCHIVOS ON PARACONT.ParCodi=ARCHIVOS.ArchModu WHERE PARACONT.ParCodi='0' AND ARCHIVOS.ArchModu='0' AND ARCHIVOS.ArchTipo='0' AND ARCHIVOS.ArchCodi='0'";
// print_r($query); exit;

$params  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$result  = sqlsrv_query($link, $query, $params, $options);
$data    = array();

if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)) :

        $ArchPath = $row['ArchPath'];
        // $ArchPath = str_replace("","",$ArchPath );
        $ArchDesc = $row['ArchDesc'];
        $ArchNomb = $row['ArchNomb'];

        $data = array(
            'MensDesde'=> str_pad($row['MensDesde'], 2, "0", STR_PAD_LEFT),
            'MensHasta'=> str_pad($row['MensHasta'], 2, "0", STR_PAD_LEFT),
            'Jor1Desde'=> str_pad($row['Jor1Desde'], 2, "0", STR_PAD_LEFT),
            'Jor1Hasta'=> str_pad($row['Jor1Hasta'], 2, "0", STR_PAD_LEFT),
            'Jor2Desde'=> str_pad($row['Jor2Desde'], 2, "0", STR_PAD_LEFT),
            'Jor2Hasta'=> str_pad($row['Jor2Hasta'], 2, "0", STR_PAD_LEFT),
            'ArchPath' => "$ArchPath",
            'ArchDesc' => "$ArchDesc",
            'ArchNomb' => "$ArchNomb"
        );
    endwhile;
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode($data);

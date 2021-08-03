<?php
header("Content-type: application/json; charset=utf-8");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '../../config/index.php';
E_ALL();
UnsetGet('q2');
session_start();
require __DIR__ . '../../config/conect_mssql.php';
$q2 = $_GET['q2'];

$query = "SELECT RELOHABI.RelGrup AS GrupoCod,  GRUPCAPT.GHaDesc as Grupo, RELOHABI.RelReMa AS Marca ,RELOJES.RelRelo AS Reloj ,RELOJES.RelSeri AS Serie, RELOJES.RelDeRe AS Descrip FROM RELOHABI,RELOJES,GRUPCAPT WHERE RELOHABI.RelGrup = '$q2' AND RELOHABI.RelGrup = GRUPCAPT.GHaCodi AND RELOHABI.RelReMa = RELOJES.RelReMa AND RELOHABI.RelRelo = RELOJES.RelRelo ORDER BY RELOHABI.RelGrup,RELOHABI.RelReMa,RELOHABI.RelRelo";

//    print_r($query);

$params  = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$result  = sqlsrv_query($link, $query, $params, $options);
$data    = array();

if (sqlsrv_num_rows($result) > 0) {
    while ($fila = sqlsrv_fetch_array($result)) {
        $Grupo   = $fila['Grupo'];
        $Reloj   = $fila['Reloj'];
        $Serie   = $fila['Serie'];
        $Descrip = $fila['Descrip'];
        $Marca = $fila['Marca'];
        switch ($Marca) {
            case '0'  : 
               $Marca = 'ASCII';
                break;
            case '1'  : 
               $Marca = 'Macronet';
                break;
            case '10' : 
               $Marca = 'Hand Reader';
                break;
            case '21' : 
               $Marca = 'SB CAuto';
                break;
            case '30' : 
               $Marca = 'ZKTeco';
                break;
            case '41' : 
               $Marca = 'Suprema';
                break;
            case '50' : 
               $Marca = 'HikVision';
                break;
            default   : 
                $Marca = $fila['Marca'];
                break;
        }
        $data[] = array(
            "Grupo"   => $Grupo,
            "Reloj"   => $Reloj,
            "Serie"   => $Serie,
            "Descrip" => $Descrip,
            "Marca"   => $Marca,
            "null"    => ''
        );
    }
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode(array("data" => $data));

<?php
E_ALL();
require __DIR__ . '../../../../config/conect_mssql.php';
$dataTHoDesc2 = array();
$sql_query="SELECT TIPOHORA.THoDesc2, TIPOHORA.THoDesc FROM TIPOHORA WHERE TIPOHORA.THoColu > 0 ORDER BY TIPOHORA.THoColu";
$param        = array();
$options      = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$queryRecords = sqlsrv_query($link, $sql_query,$param, $options);
while ($row = sqlsrv_fetch_array($queryRecords)) {
    $THoDesc   = $row['THoDesc'];
    $THoDesc2   = $row['THoDesc2'];
    $dataTHoDesc2[] = array(
        'THoDesc'   => $THoDesc,
        'THoDesc2'   => $THoDesc2,
    );
}
sqlsrv_free_stmt($queryRecords);
sqlsrv_close($link);

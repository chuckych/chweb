<?php
header("Content-type: application/json; charset=utf-8");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '../../config/session_start.php';
require __DIR__ . '../../config/index.php';
E_ALL();
UnsetGet('q2');
require __DIR__ . '../../config/conect_mssql.php';
$q2 = $_GET['q2'];
$query = "SELECT [IDCodigo] ,[IDFichada], [FechaHora], [IDVence], [IDTarjeta], [IDCap01], [IDCap03], [IDCap04], [IDCap05], [IDCap06] FROM IDENTIFICA WHERE IDLegajo='$q2' ORDER BY FechaHora DESC";
//    print_r($query);

$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$result = sqlsrv_query($link, $query, $params, $options);
$data = array();
$icon_trash = imgIcon('trash3', 'Eliminar registro ', 'w15 opa5');
$icon_trash = '<i class="bi bi-trash font1"></i>';

if (sqlsrv_num_rows($result) > 0) {
    while ($fila = sqlsrv_fetch_array($result)) {
        $IDCodigo = $fila['IDCodigo'];
        $IDTarjeta = $fila['IDTarjeta'];
        $IDVence = $fila['IDVence']->format('d/m/Y');
        $IDVence = $IDVence == '01/01/1753' ? '-' : $IDVence;
        $IDFichada = $fila['IDFichada'] == '0' ? '-' : '<i class="bi bi-check2 font1"></i>';
        $FechaHora = $fila['FechaHora']->format('d/m/Y');
        $IDCap01 = $fila['IDCap01'] == '0' ? '-' : '<i class="bi bi-check2 font1"></i>'; // Macronet
        $IDCap03 = $fila['IDCap03'] == '0' ? '-' : '<i class="bi bi-check2 font1"></i>'; // Silycon Bayres
        $IDCap04 = $fila['IDCap04'] == '0' ? '-' : '<i class="bi bi-check2 font1"></i>'; // ZKTECO
        $IDCap05 = $fila['IDCap05'] == '0' ? '-' : '<i class="bi bi-check2 font1"></i>'; // SUPREMA
        $IDCap06 = $fila['IDCap06'] == '0' ? '-' : '<i class="bi bi-check2 font1"></i>'; // Hikvsion
        $eliminar = '<div class="item"><a class="btn btn-light btn-sm delete_identifica" data="' . $IDCodigo . '" data2="' . $q2 . '" data3="true">' . $icon_trash . '</a></div>';
        $data[] = array(
            "FechaHora" => $FechaHora,
            "IDCap01" => $IDCap01,
            "IDCap03" => $IDCap03,
            "IDCap04" => $IDCap04,
            "IDCap05" => $IDCap05,
            "IDCap06" => $IDCap06,
            "IDCodigo" => $IDCodigo,
            "IDFichada" => $IDFichada,
            "IDTarjeta" => $IDTarjeta,
            "IDVence" => $IDVence,
            "eliminar" => $eliminar,
            "null" => ''
        );
    }
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode(array("data" => $data));

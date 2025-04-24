<?php
require __DIR__ . '/../../config/session_start.php';
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");

require __DIR__ . '/../../config/conect_mssql.php';

$data = array();

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$_POST['cod'] = $_POST['cod'] ?? '';
$_POST['estruc'] = $_POST['estruc'] ?? '';
$cod = test_input($_POST['cod']);
$estruc = test_input($_POST['estruc']);
switch ($estruc) {
    case 'Empresa':
        $Col = 'P.LegEmpr';
        break;
    case 'Planta':
        $Col = 'P.LegPlan';
        break;
    case 'Sucursal':
        $Col = 'P.LegSucu';
        break;
    case 'Grupo':
        $Col = 'P.LegGrup';
        break;
    case 'Tarea':
        $Col = 'P.LegTareProd';
        break;
    case 'Sector':
        $Col = 'P.LegSect';
        break;
}
$query = "SELECT P.LegNume as 'Lega', P.LegApNo AS 'ApNo', P.LegFeEg AS 'FeEg' FROM PERSONAL P WHERE $Col = '$cod' AND P.LegNume > 0 ORDER BY P.LegFeEg, P.LegApNo";
// print_r($query).PHP_EOL; exit;
$rs = sqlsrv_query($link, $query, $params, $options);
if (sqlsrv_num_rows($rs) > 0) {
    while ($r = sqlsrv_fetch_array($rs)):
        $ApNo = utf8str($r['ApNo']);
        $Lega = $r['Lega'];
        $FeEg = $r['FeEg']->format('Ymd');
        $FeEg = ($FeEg == '17530101') ? '<span class="bg-success text-white p-1 px-2 fontp" data-titlel="Legajo ' . $ApNo . ': Activo">Activo</span>' : '<span class="bg-danger text-white p-1 px-2 fontp" data-titlel="Legajo ' . $ApNo . ': De Baja">De Baja</span>';
        $Check = '<div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input checkReg" id="Check' . $Lega . '" name="checks[]" value="' . $Lega . '@' . $ApNo . '"><label class="custom-control-label" for="Check' . $Lega . '"></label></div>';
        $data[] = array($Lega, $ApNo, $Check, $FeEg);
    endwhile;
}
sqlsrv_free_stmt($rs);
sqlsrv_close($link);


$json_data = array(
    "data" => $data,
);

echo json_encode($json_data);
exit;

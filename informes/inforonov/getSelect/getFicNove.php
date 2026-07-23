<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../../config/index.php';
ultimoacc();
secure_auth_ch();
header("Content-Type: application/json");
E_ALL();

// require __DIR__ . '/../valores.php';

require __DIR__ . '/../../../filtros/filtros.php';
require __DIR__ . '/../../../config/conect_mssql.php';


FusNuloPOST('q', '');
$q = test_input($_POST['q']);
$FiltroQ = (!empty($q)) ? "AND CONCAT(FICHAS2.FicONov, OTRASNOV.ONovDesc) LIKE '%$q%'" : '';

$DateRange = explode(' al ', $_POST['_dr']);
$FechaIni = test_input(dr_fecha($DateRange[0]));
$FechaFin = test_input(dr_fecha($DateRange[1]));

FusNuloPOST("Per", '');
FusNuloPOST("Per2", '');
FusNuloPOST("Emp", '');
FusNuloPOST("Plan", '');
FusNuloPOST("Sect", '');
FusNuloPOST("Sec2", '');
FusNuloPOST("Grup", '');
FusNuloPOST("Sucur", '');
FusNuloPOST("Tipo", '');
FusNuloPOST("FicDiaL", false);
FusNuloPOST("FicFalta", 0);
FusNuloPOST("FicNovT", 0);
FusNuloPOST("FicNovI", 0);
FusNuloPOST("FicNovS", 0);
FusNuloPOST("FicNovA", 0);

$Per = test_input($_POST['Per']);
$Per2 = test_input($_POST['Per2']);
$Per3 = test_input($_POST['Per2']);
$Emp = test_input($_POST['Emp']);
$Plan = test_input($_POST['Plan']);
$Sect = test_input($_POST['Sect']);
$Grup = test_input($_POST['Grup']);
$Sucur = test_input($_POST['Sucur']);
$FicDiaL = test_input($_POST['FicDiaL']);

$Per2 = !empty($Per2) ? "AND PERSONAL.LegNume = '$Per2'" : "";


$Tipo = test_input($_POST['Tipo']);
$Tipo = ($Tipo == '2') ? "AND PERSONAL.LegTipo = '0'" : "AND PERSONAL.LegTipo = '$Tipo'";
$Tipo = empty(($_POST['Tipo'])) ? "" : $Tipo;

if (!empty($_POST['Sec2'])) {
    $Sec2 = implode(',', ($_POST['Sec2']));
    $Seccion = !empty($Sec2) ? "AND CONCAT(FICHAS.FicSect, FICHAS.FicSec2) IN ($Sec2)" : '';
} else {
    $Seccion = '';
}

$Empresa = datosGetIn($_POST['Emp'], "PERSONAL.LegEmpr");
$Planta = datosGetIn($_POST['Plan'], "PERSONAL.LegPlan");
$Sector = datosGetIn($_POST['Sect'], "PERSONAL.LegSect");
$Grupo = datosGetIn($_POST['Grup'], "PERSONAL.LegGrup");
$Sucursal = datosGetIn($_POST['Sucur'], "PERSONAL.LegSucu");
$Legajos = datosGetIn($_POST['Per'], "FICHAS.FicLega");
$Legajos = !empty($Per2) ? "" : "$Legajos";

$FilterEstruct = $Empresa;
$FilterEstruct .= $Planta;
$FilterEstruct .= $Sector;
$FilterEstruct .= $Seccion;
$FilterEstruct .= $Grupo;
$FilterEstruct .= $Sucursal;
$FilterEstruct .= $Tipo;
$FilterEstruct .= $Legajos;
$FilterEstruct .= $Per2;

$FicNovT2 = test_input($_POST['FicNovT']);
$FicNovA2 = test_input($_POST['FicNovA']);
$FicNovI2 = test_input($_POST['FicNovI']);
$FicNovS2 = test_input($_POST['FicNovS']);


$FicNovT2 = ($FicNovT2) ? "0" : '';
$FicNovT2 .= ($FicNovI2 && $FicNovT2 == '0') ? "," : '';
$FicNovT2 .= ($FicNovS2 && $FicNovT2 == '0') ? "," : '';
$FicNovT2 .= ($FicNovA2 && $FicNovT2 == '0') ? "," : '';
$FicNovI2 = ($FicNovI2) ? "1" : '';
$FicNovI2 .= ($FicNovS2 && $FicNovI2 == '1') ? "," : '';
$FicNovI2 .= ($FicNovA2 && $FicNovI2 == '1') ? "," : '';
$FicNovS2 = ($FicNovS2) ? "2" : '';
$FicNovS2 .= ($FicNovA2 && $FicNovS2 == '2') ? "," : '';
$FicNovA2 = ($FicNovA2) ? "3,4,5,6,7,8" : '';

$noveTipos = $FicNovT2;
$noveTipos .= $FicNovI2;
$noveTipos .= $FicNovS2;
$noveTipos .= $FicNovA2;

$query = "SELECT FICHAS2.FicONov AS 'id',
    OTRASNOV.ONovDesc AS 'text'
FROM FICHAS2
    INNER JOIN OTRASNOV ON FICHAS2.FicONov = OTRASNOV.ONovCodi
    INNER JOIN PERSONAL ON FICHAS2.FicLega = PERSONAL.LegNume
    LEFT JOIN FICHAS ON FICHAS2.FicLega = FICHAS.FicLega
    AND FICHAS2.FicFech = FICHAS.FicFech
WHERE FICHAS2.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FiltroQ $FilterEstruct $FiltrosFichas
GROUP BY FICHAS2.FicONov,
    OTRASNOV.ONovDesc
ORDER BY FICHAS2.FicONov";

$params = [];
$options = ["Scrollable" => SQLSRV_CURSOR_KEYSET];

$result = sqlsrv_query($link, $query, $params, $options);
$data = [];

if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)):

        $id = $row['id'];
        $text = $row['text'];

        $data[] = array(
            'id' => $id,
            'text' => $text,
            'title' => $id . ' - ' . $text,
        );
    endwhile;
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode($data);

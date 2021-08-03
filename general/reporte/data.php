<?php
require __DIR__ . '../../../filtros/filtros.php';
require __DIR__ . '../../../config/conect_mssql.php';
E_ALL();
$dataLegajo = array();

// if(empty($DateRange)){
//     $json_data = array(
//         "draw"            => '',
//         "recordsTotal"    => '',
//         "recordsFiltered" => '',
//         "data"            => $data
//     );
    
//     echo json_encode($json_data);
//     exit;
// }

$legajo = test_input(FusNuloPOST('_l', 'vacio'));
FusNuloPOST("Per",'');
FusNuloPOST("FicDiaL", '');
FusNuloPOST("FicFalta", '');
FusNuloPOST("FicNovT", '');
FusNuloPOST("FicNovA", '');
FusNuloPOST("FicNovI", '');
FusNuloPOST("FicNovS", '');
FusNuloPOST("Emp",'');
FusNuloPOST("Plan",'');
FusNuloPOST("Sect",'');
FusNuloPOST("Sec2",'');
FusNuloPOST("Grup",'');
FusNuloPOST("Sucur",'');
FusNuloPOST("Tipo",'');

$Per      = test_input($_POST['Per']);
// $Emp      = ($_POST['Emp']=='null') ? '' : test_input($_POST['Emp']);
$Emp  = test_input($_POST['Emp']);
$FicDiaL  = test_input($_POST['FicDiaL']);
$FicFalta = test_input($_POST['FicFalta']);
$FicNovT  = test_input($_POST['FicNovT']);
$FicNovT2 = test_input($_POST['FicNovT']);
$FicNovA  = test_input($_POST['FicNovA']);
$FicNovA2 = test_input($_POST['FicNovA']);
$FicNovI  = test_input($_POST['FicNovI']);
$FicNovI2 = test_input($_POST['FicNovI']);
$FicNovS  = test_input($_POST['FicNovS']);
$FicNovS2 = test_input($_POST['FicNovS']);
$Tipo     = test_input($_POST['Tipo']);
$Plan     = test_input($_POST['Plan']);
$Sect     = test_input($_POST['Sect']);
$Sec2     = test_input($_POST['Sec2']);
$Grup     = test_input($_POST['Grup']);
$Sucur    = test_input($_POST['Sucur']);

/** Legajos */
$legajo = empty($Per) ? test_input(FusNuloPOST('_l', 'vacio')):$Per;
$legajo = $Per;
$Per    = (!empty($Per) && $Per !='null')? "AND FICHAS.FicLega IN ($Per)":'';

$Emp      = ($Emp && $Emp !='null') ? " AND FICHAS.FicEmpr IN ($Emp)" : "";/** Empresa */
$Plan     = ($Plan && $Plan !='null') ? " AND FICHAS.FicPlan IN ($Plan)" : "";/** Plantas */
$Sect     = ($Sect && $Sect !='null') ? " AND FICHAS.FicSect IN ($Sect)" :'';/** Sectores */
$Sec2     = ($Sec2 && $Sec2 !='null') ? " AND CONCAT(FICHAS.FicSect, FICHAS.FicSec2) IN ($Sec2)" :'';/** Secciones */
$Grup     = ($Grup && $Grup !='null') ? " AND FICHAS.FicGrup IN ($Grup)" :'';/** Grupos */
$Sucur    = ($Sucur && $Sucur !='null') ? " AND FICHAS.FicSucu IN ($Sucur)" :'';/** Sucursal */
$FicDiaL  = ($FicDiaL && $FicDiaL !='null') ? " AND FICHAS.FicDiaL = '$FicDiaL'" : "";/** Día Laboral */
$FicFalta = ($FicFalta && $FicFalta !='null') ? " AND FICHAS.FicFalta = '$FicFalta'" : "";/** Fichadas Inconsistentes */
$FicNovT  = ($FicNovT && $FicNovT !='null') ? " AND FICHAS.FicNovT = '$FicNovT'" : "";/** Novedades tipo Tarde */
$FicNovA  = ($FicNovA && $FicNovA !='null') ? " AND FICHAS.FicNovA = '$FicNovA'" : "";/** Novedades tipo Ausencia */
$FicNovI  = ($FicNovI && $FicNovI !='null') ? " AND FICHAS.FicNovI = '$FicNovI'" : "";/** Novedades tipo Incumplimiento */
$FicNovS  = ($FicNovS && $FicNovS !='null') ? " AND FICHAS.FicNovS = '$FicNovS'" : "";/** Novedades tipo Salida */

$FicNovT2 = ($FicNovT2) ? "0":'';
$FicNovT2 .= ($FicNovI2 && $FicNovT2=='0') ? ",":'';
$FicNovT2 .= ($FicNovS2 && $FicNovT2=='0') ? ",":'';
$FicNovI2 = ($FicNovI2) ? "1":'';
$FicNovI2 .= ($FicNovS2 && $FicNovI2 =='1') ? ",":'';
$FicNovS2 = ($FicNovS2) ? "2":'';
$FicNovA2 = ($FicNovA2) ? "3,4,5,6,7,8":'';

$noveTipos = $FicNovT2;
$noveTipos .= $FicNovI2;
$noveTipos .= $FicNovS2;
$noveTipos .= $FicNovA2;
$FicNoTi  = ($noveTipos>='0') ? "AND FICHAS3.FicNoTi IN ($noveTipos)":'';
// if($noveTipos>='0'){
// $FicNoTi  = "AND FICHAS3.FicNoTi IN ($noveTipos)"; /** Novedades tipo para la query de novedades */
// }
// // echo $FicNoTi; exit;

switch ($Tipo) {
    case '1':
        $Tipo = "AND PERSONAL.LegTipo = '1'";
        break;
    case '2':
        $Tipo = "AND PERSONAL.LegTipo = '0'";
        break;
    case 'null':
        $Tipo = "";
    break;
        default:
        $Tipo = "";
        break;
}

$FilterEstruct  = $Emp;
$FilterEstruct  .= $Plan;
$FilterEstruct  .= $Sect;
$FilterEstruct  .= $Sec2;
$FilterEstruct  .= $Grup;
$FilterEstruct  .= $Sucur;
$FilterEstruct  .= $Per;
$FilterEstruct  .= $FicDiaL;
$FilterEstruct  .= $FicFalta;
$FilterEstruct  .= $FicNovT;
$FilterEstruct  .= $FicNovA;
$FilterEstruct  .= $FicNovI;
$FilterEstruct  .= $FicNovS;
$FilterEstruct  .= $Tipo;

$FilterEstruct2 = $FicNoTi;
// $FilterEstruct2 .= $FicNovS2;


$sql_query="SELECT FICHAS.FicLega AS 'Legajo', PERSONAL.LegApNo AS 'Nombre', PERSONAL.LegCUIT AS 'Cuil' FROM FICHAS INNER JOIN PERSONAL ON FICHAS.FicLega=PERSONAL.LegNume WHERE FICHAS.FicFech BETWEEN '$FechaIni' AND '$FechaFin' $FilterEstruct $FiltrosFichas GROUP BY FICHAS.FicLega, PERSONAL.LegApNo, PERSONAL.LegCUIT ORDER BY FICHAS.FicLega";

// print_r($sql_query); exit;

$param        = array();
$options      = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$queryRecords = sqlsrv_query($link, $sql_query,$param, $options);

while ($row = sqlsrv_fetch_array($queryRecords)) {
    $Cuil   = $row['Cuil'];
    $Legajo   = $row['Legajo'];
    $Nombre   = empty($row['Nombre']) ? 'Sin Nombre' : $row['Nombre'];
    $dataLegajo[] = array(
        'Cuil'   => $Cuil,
        'Legajo' => $Legajo,
        'Nombre' => $Nombre,
    );
}
sqlsrv_free_stmt($queryRecords);
sqlsrv_close($link);
// print_r($dataLegajo);exit;

<?php

$DateRange = explode(' al ', $_POST['_dr']);
$FechaIni  = test_input(dr_fecha($DateRange[0]));
$FechaFin  = test_input(dr_fecha($DateRange[1]));

FusNuloPOST("Per",'');
FusNuloPOST("FicDiaL", '');
FusNuloPOST("FicFalta", '');
FusNuloPOST("FicNove", '');
FusNuloPOST("FicNovT", '');
FusNuloPOST("FicNovA", '');
FusNuloPOST("FicNovI", '');
FusNuloPOST("FicNovS", '');
FusNuloPOST("_agrupar", '');
FusNuloPOST("_resaltar", '');
FusNuloPOST("Emp",'');
FusNuloPOST("Plan",'');
FusNuloPOST("Sect",'');
FusNuloPOST("Sec2",'');
FusNuloPOST("Grup",'');
FusNuloPOST("Sucur",'');
FusNuloPOST("Tipo",'');

$Per       = test_input($_POST['Per']);
$Emp       = test_input($_POST['Emp']);
$FicDiaL   = test_input($_POST['FicDiaL']);
$FicFalta  = test_input($_POST['FicFalta']);
$FicNovT   = test_input($_POST['FicNovT']);
$FicNovT2  = test_input($_POST['FicNovT']);
$FicNovA   = test_input($_POST['FicNovA']);
$FicNovA2  = test_input($_POST['FicNovA']);
$FicNovI   = test_input($_POST['FicNovI']);
$FicNovI2  = test_input($_POST['FicNovI']);
$FicNovS   = test_input($_POST['FicNovS']);
$FicNovS2  = test_input($_POST['FicNovS']);
$_resaltar = test_input($_POST['_resaltar']);
$Tipo      = test_input($_POST['Tipo']);
$Plan      = test_input($_POST['Plan']);
$Sect      = test_input($_POST['Sect']);
$Sec2      = test_input($_POST['Sec2']);
$Grup      = test_input($_POST['Grup']);
$Sucur     = test_input($_POST['Sucur']);
$FicNove   = test_input($_POST['FicNove']);
$FicNove   = $FicNove=='null' ? '':$FicNove;



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
$FicNove  = ($FicNove) ? "AND FICHAS3.FicNove IN ($FicNove)": "";
// $FicDiaL  = ($FicDiaL && $FicDiaL !='null') ? " AND FICHAS.FicDiaL = '$FicDiaL'" : "";/** Día Laboral */
// $FicFalta = ($FicFalta && $FicFalta !='null') ? " AND FICHAS.FicFalta = '$FicFalta'" : "";/** Fichadas Inconsistentes */
$FicNovT  = ($FicNovT=='0') ? " AND FICHAS.FicNovT != '1'" : ""; /** Novedades tipo Tarde */
$FicNovA  = ($FicNovA=='0') ? " AND FICHAS.FicNovA != '1'" : ""; /** Novedades tipo Ausencia */
$FicNovI  = ($FicNovI=='0') ? " AND FICHAS.FicNovI != '1'" : ""; /** Novedades tipo Incumplimiento */
$FicNovS  = ($FicNovS=='0') ? " AND FICHAS.FicNovS != '1'" : ""; /** Novedades tipo Salida */

$FicNovT2 = ($FicNovT2) ? "0":'';
$FicNovT2 .= ($FicNovI2 && $FicNovT2=='0') ? ",":'';
$FicNovT2 .= ($FicNovS2 && $FicNovT2=='0') ? ",":'';
$FicNovT2 .= ($FicNovA2 && $FicNovT2=='0') ? ",":'';
$FicNovI2 = ($FicNovI2) ? "1":'';
$FicNovI2 .= ($FicNovS2 && $FicNovI2 =='1') ? ",":'';
$FicNovI2 .= ($FicNovA2 && $FicNovI2 =='1') ? ",":'';
$FicNovS2 = ($FicNovS2) ? "2":'';
$FicNovS2 .= ($FicNovA2 && $FicNovS2 =='2') ? ",":'';
$FicNovA2 = ($FicNovA2) ? "3,4,5,6,7,8":'';

$noveTipos = $FicNovT2;
$noveTipos .= $FicNovI2;
$noveTipos .= $FicNovS2;
$noveTipos .= $FicNovA2;

if($noveTipos>='0'){
$FicNoTi  = "AND FICHAS3.FicNoTi IN ($noveTipos)"; /** Novedades tipo para la query de novedades */
}

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

$FilterEstruct    = $Emp;
$FilterEstruct  .= $Plan;
$FilterEstruct  .= $Sect;
$FilterEstruct  .= $Sec2;
$FilterEstruct  .= $Grup;
$FilterEstruct  .= $Sucur;
$FilterEstruct  .= $Per;
$FilterEstruct  .= $Tipo;
$FilterEstruct .= $FicNoTi ?? '';
$FilterEstruct  .= $FicNove;
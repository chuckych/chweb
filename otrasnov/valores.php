<?php
FusNuloPOST('q', '');
$q = test_input($_POST['q']);


if (isset($_POST['_dr']) && !empty($_POST['_dr'])) {
    $DateRange = explode(' al ', $_POST['_dr']);
    $FechaIni  = test_input(dr_fecha($DateRange[0]));
    $FechaFin  = test_input(dr_fecha($DateRange[1]));
}else{
    $FechaIni  = date('Ymd');
    $FechaFin  = date('Ymd');
}

FusNuloPOST("Per",'');
FusNuloPOST("Per2",'');
FusNuloPOST("Emp",'');
FusNuloPOST("Plan",'');
FusNuloPOST("Sect",'');
FusNuloPOST("Sec2",'');
FusNuloPOST("Grup",'');
FusNuloPOST("Sucur",'');
FusNuloPOST("Tipo",'');
// FusNuloPOST("FicNoTi",'');

$Per     = ($_POST['Per']);
$Per2    = test_input($_POST['Per2']);
$Per3    = test_input($_POST['Per2']);
$Emp     = ($_POST['Emp']);
$Plan    = ($_POST['Plan']);
$Sect    = ($_POST['Sect']);
$Grup    = ($_POST['Grup']);
$Sucur   = ($_POST['Sucur']);

$Per2 = !empty($Per2) ? "AND PERSONAL.LegNume = '$Per2'": "";

$Tipo = test_input($_POST['Tipo']);
$Tipo = ($Tipo=='2') ? "AND PERSONAL.LegTipo = '0'": "AND PERSONAL.LegTipo = '$Tipo'";
$Tipo = empty(($_POST['Tipo'])) ? "": $Tipo;

if(!empty($_POST['Sec2'])){
    $Sec2 = implode(',',($_POST['Sec2']));
    $Seccion = !empty($Sec2) ? "AND CONCAT(FICHAS.FicSect, FICHAS.FicSec2) IN ($Sec2)" :'';
}else{
    $Seccion ='';
}
if(!empty($_POST['FicONove'])){
$FicONove = implode(',',($_POST['FicONove']));
$FicONove = ($FicONove!='') ? "AND FICHAS2.FicONov IN ($FicONove)" :'';
}else{
    $FicONove ='';
}

$Empresa  = datosGetIn($_POST['Emp'], "FICHAS.FicEmpr");
$Planta   = datosGetIn($_POST['Plan'], "FICHAS.FicPlan");
$Sector   = datosGetIn($_POST['Sect'], "FICHAS.FicSect");
$Grupo    = datosGetIn($_POST['Grup'], "FICHAS.FicGrup");
$Sucursal = datosGetIn($_POST['Sucur'], "FICHAS.FicSucu");
$Legajos  = datosGetIn($_POST['Per'], "FICHAS.FicLega");
$Legajos = !empty($Per2) ? "": "$Legajos";

$FilterEstruct  = $Empresa;
$FilterEstruct  .= $Planta;
$FilterEstruct  .= $Sector;
$FilterEstruct  .= $Seccion;
$FilterEstruct  .= $Grupo;
$FilterEstruct  .= $Sucursal;
$FilterEstruct  .= $Tipo;
$FilterEstruct  .= $Legajos;
$FilterEstruct  .= $Per2;
// $FilterEstruct  .= $FicNoTi;

$FilterEstruct2  = $FicONove;
// $FilterEstruct  .= $Thora;
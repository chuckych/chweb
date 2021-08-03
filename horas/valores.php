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
FusNuloPOST("Emp",'');
FusNuloPOST("Plan",'');
FusNuloPOST("Sect",'');
FusNuloPOST("Sec2",'');
FusNuloPOST("Grup",'');
FusNuloPOST("Sucur",'');
FusNuloPOST("Tipo",'');
FusNuloPOST("Modulo",'');
FusNuloPOST("Thora",'');
FusNuloPOST("SHoras",'1');
FusNuloPOST("Calculos",'2');


$Per      = ($_POST['Per']);
$Emp      = ($_POST['Emp']);
$Plan     = ($_POST['Plan']);
$Sect     = ($_POST['Sect']);
$Grup     = ($_POST['Grup']);
$Sucur    = ($_POST['Sucur']);
$Thora    = test_input($_POST['Thora']);
$Calculos = test_input($_POST['Calculos']);

$Tipo = test_input($_POST['Tipo']);
$Tipo = ($Tipo=='2') ? "AND PERSONAL.LegTipo = '0'": "AND PERSONAL.LegTipo = '$Tipo'";
$Tipo = empty(($_POST['Tipo'])) ? "": $Tipo;

$HoraMin = test_input($_POST['HoraMin']);
$HoraMax = test_input($_POST['HoraMax']);
$FiltroHoraMin = "AND dbo.fn_STRMinutos(FICHAS1.FicHsAu)  BETWEEN dbo.fn_STRMinutos('$HoraMin') AND dbo.fn_STRMinutos('$HoraMax')";
$FiltroHoraMax = "AND dbo.fn_STRMinutos(FICHAS1.FicHsAu2) BETWEEN dbo.fn_STRMinutos('$HoraMin') AND dbo.fn_STRMinutos('$HoraMax')";
$HoraMinMax = (test_input($_POST['SHoras']==1)) ? $FiltroHoraMin : $FiltroHoraMax;

if(!empty($_POST['Sec2'])){
    $Sec2 = implode(',',($_POST['Sec2']));
    $Seccion = !empty($Sec2) ? "AND CONCAT(FICHAS.FicSect, FICHAS.FicSec2) IN ($Sec2)" :'';
}else{
    $Seccion ='';
}
$Empresa  = datosGetIn($_POST['Emp'], "PERSONAL.LegEmpr");
$Planta   = datosGetIn($_POST['Plan'], "PERSONAL.LegPlan");
$Sector   = datosGetIn($_POST['Sect'], "PERSONAL.LegSect");
$Grupo    = datosGetIn($_POST['Grup'], "PERSONAL.LegGrup");
$Sucursal = datosGetIn($_POST['Sucur'], "PERSONAL.LegSucu");
$Legajos  = datosGetIn($_POST['Per'], "FICHAS1.FicLega");
$Thora    = datosGetIn($_POST['Thora'], "FICHAS1.FicHora");

$FilterEstruct  = $Empresa;
$FilterEstruct  .= $Planta;
$FilterEstruct  .= $Sector;
$FilterEstruct  .= $Seccion;
$FilterEstruct  .= $Grupo;
$FilterEstruct  .= $Sucursal;
$FilterEstruct  .= $Tipo;
$FilterEstruct  .= $Legajos;
$FilterEstruct  .= $Thora;
$FilterEstruct  .= $HoraMinMax;
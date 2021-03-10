<?php
FusNuloPOST('q', '');
$q = test_input($_POST['q']);

$FechaIni = '20200201';
$FechaFin = '20200730';

$DateRange = explode(' al ', $_POST['fecha']);
$FechaIni  = test_input(dr_fecha($DateRange[0]));
$FechaFin  = test_input(dr_fecha($DateRange[1]));

$_POST['ordenar'] = $_POST['ordenar'] ?? 0;
$orderBy = (test_input($_POST['ordenar'])) ? 'ORDER BY PERSONAL.LegApNo':'ORDER BY F.FicLega';

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

$Emp     = ($_POST['Emp']);
$Plan    = ($_POST['Plan']);
$Per     = ($_POST['Per']);
$Per2    = ($_POST['Per2']);
$Per3    = ($_POST['Per2']);
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

// $FicNoTi = implode(',',($_POST['FicNoTi']));
// $FicNoTi = ($FicNoTi!='') ? "AND FICHAS3.FicNoTi IN ($FicNoTi)" :'';

// $FicNove = implode(',',($_POST['FicNove']));
// $FicNove = ($FicNove!='') ? "AND FICHAS3.FicNove IN ($FicNove)" :'';

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

// $FilterEstruct2  = $FicNoTi;
// $FilterEstruct2  .= $FicNove;
// $FilterEstruct  .= $Thora;
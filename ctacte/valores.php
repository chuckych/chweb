<?php
FusNuloPOST('q', '');
$q = test_input($_POST['q']);

$DateRange = explode(' al ', $_POST['_dr']);
$FechaIni  = test_input(dr_fecha($DateRange[0]));
$FechaFin  = test_input(dr_fecha($DateRange[1]));

$nove       = nov_cta_cte();
$noves      = (super_unique($nove, 'cod'));
$novedad    = (isset($_POST['nove'])) ? $_POST['nove'] : $noves[0]['cod'];

FusNuloPOST("CTA2Peri",'');
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

$periodo    = peri_min_max();
$periodo    = (!empty($_POST['CTA2Peri'])) ? $_POST['CTA2Peri'] : $periodo['max'];

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
    $Seccion = !empty($Sec2) ? "AND dbo.fn_Concatenar(PERSONAL.LegSect, PERSONAL.LegSec2) IN ($Sec2)" :'';
}else{
    $Seccion ='';
}

// $FicNoTi = implode(',',($_POST['FicNoTi']));
// $FicNoTi = ($FicNoTi!='') ? "AND FICHAS3.FicNoTi IN ($FicNoTi)" :'';

// $FicNove = implode(',',($novedad));
// $FicNove = ($FicNove!='') ? "AND FICHAS3.FicNove IN ($FicNove)" :'';

$Empresa  = datosGetIn($_POST['Emp'], "PERSONAL.LegEmpr");
$Planta   = datosGetIn($_POST['Plan'], "PERSONAL.LegPlan");
$Sector   = datosGetIn($_POST['Sect'], "PERSONAL.LegSect");
$Grupo    = datosGetIn($_POST['Grup'], "PERSONAL.LegGrup");
$Sucursal = datosGetIn($_POST['Sucur'], "PERSONAL.LegSucu");
$Legajos  = datosGetIn($_POST['Per'], "PERSONAL.LegNume");
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
// $FilterEstruct  .= $FicNove;
$FilterEstruct2 ='';
// $FilterEstruct2  = $FicNoTi;
// $FilterEstruct2  .= "AND FICHAS3.FicNove IN ($novedad)";
// $FilterEstruct  .= $Thora;
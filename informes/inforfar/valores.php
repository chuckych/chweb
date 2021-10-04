<?php
FusNuloPOST('q', '');
$q = test_input($_POST['q']);

$DateRange = explode(' al ', $_POST['_dr']);
$FechaIni  = test_input(dr_fecha($DateRange[0]));
$FechaFin  = test_input(dr_fecha($DateRange[1]));

FusNuloPOST("Per",'');
FusNuloPOST("Per2",'');
FusNuloPOST("Emp",'');
FusNuloPOST("Plan",'');
FusNuloPOST("Sect",'');
FusNuloPOST("Sec2",'');
FusNuloPOST("Grup",'');
FusNuloPOST("Sucur",''); 
FusNuloPOST("Tipo",'');
FusNuloPOST("FicDiaL", false);
FusNuloPOST("FicFalta", 0);
FusNuloPOST("FicNovT", 0);
FusNuloPOST("FicNovI", 0);
FusNuloPOST("FicNovS", 0);
FusNuloPOST("FicNovA", 0);
FusNuloPOST("FicNove", '');

$Per      = ($_POST['Per']);
$Per2     = ($_POST['Per2']);
$Per3     = ($_POST['Per2']);
$Emp      = ($_POST['Emp']);
$Plan     = ($_POST['Plan']);
$Sect     = ($_POST['Sect']);
$Grup     = ($_POST['Grup']);
$Sucur    = ($_POST['Sucur']);
$FicDiaL  = ($_POST['FicDiaL']);
$FicFalta = ($_POST['FicFalta']);
$FicNovT  = ($_POST['FicNovT']);
$FicNovI  = ($_POST['FicNovI']);
$FicNovS  = ($_POST['FicNovS']);
$FicNovA  = ($_POST['FicNovA']);
$FicNove  = ($_POST['FicNove']);

$FicDiaL  = ($FicDiaL) ? "AND FICHAS.FicDiaL = '$FicDiaL'": "";
$FicFalta = ($FicFalta) ? "AND FICHAS.FicFalta = '$FicFalta'": "";
$FicNovT  = ($FicNovT) ? "AND FICHAS.FicNovT = '$FicNovT'": "";
$FicNovI  = ($FicNovI) ? "AND FICHAS.FicNovI = '$FicNovI'": "";
$FicNovS  = ($FicNovS) ? "AND FICHAS.FicNovS = '$FicNovS'": "";
$FicNovA  = ($FicNovA) ? "AND FICHAS.FicNovA = '$FicNovA'": "";
$FicNove  = ($FicNove) ? "AND FICHAS3.FicNove IN '$FicNove'": "";

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

$Empresa  = datosGetIn($_POST['Emp'], "PERSONAL.LegEmpr");
$Planta   = datosGetIn($_POST['Plan'], "PERSONAL.LegPlan");
$Sector   = datosGetIn($_POST['Sect'], "PERSONAL.LegSect");
$Grupo    = datosGetIn($_POST['Grup'], "PERSONAL.LegGrup");
$Sucursal = datosGetIn($_POST['Sucur'], "PERSONAL.LegSucu");
$Legajos  = datosGetIn($_POST['Per'], "FICHAS.FicLega");
$Legajos = !empty($Per2) ? "": "$Legajos";

$FilterEstruct  = $Empresa;
$FilterEstruct  .= $FicDiaL;
$FilterEstruct  .= $FicFalta;
$FilterEstruct  .= $FicNovT;
$FilterEstruct  .= $FicNovI;
$FilterEstruct  .= $FicNovS;
$FilterEstruct  .= $FicNovA;
$FilterEstruct  .= $FicNove;
$FilterEstruct  .= $Planta;
$FilterEstruct  .= $Sector;
$FilterEstruct  .= $Seccion;
$FilterEstruct  .= $Grupo;
$FilterEstruct  .= $Sucursal;
$FilterEstruct  .= $Tipo;
$FilterEstruct  .= $Legajos;
$FilterEstruct  .= $Per2;
// $FilterEstruct  .= $Thora;
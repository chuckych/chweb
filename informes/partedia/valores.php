<?php
FusNuloPOST('q', '');
$q = test_input($_POST['q']);

// $DateRange = explode(' al ', $_POST['_dr']);
$FechaIni  = test_input(dr_fecha($_POST['_dr']));
$FechaFin  = test_input(dr_fecha($_POST['_dr']));
// $FechaIni  = test_input(dr_fecha($DateRange[1]));

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

$Per      = test_input($_POST['Per']);
$Per2     = test_input($_POST['Per2']);
$Per3     = test_input($_POST['Per2']);
$Emp      = test_input($_POST['Emp']);
$Plan     = test_input($_POST['Plan']);
$Sect     = test_input($_POST['Sect']);
$Grup     = test_input($_POST['Grup']);
$Sucur    = test_input($_POST['Sucur']);
$FicDiaL  = test_input($_POST['FicDiaL']);
$FicFalta = test_input($_POST['FicFalta']);
$FicNovT  = test_input($_POST['FicNovT']);
$FicNovI  = test_input($_POST['FicNovI']);
$FicNovS  = test_input($_POST['FicNovS']);
$FicNovA  = test_input($_POST['FicNovA']);
$FicDiaL  = ($FicDiaL) ? "AND FICHAS.FicDiaL = '$FicDiaL'": "";
$FicFalta = ($FicFalta) ? "AND FICHAS.FicFalta = '$FicFalta'": "";
$FicNovT  = ($FicNovT) ? "AND FICHAS.FicNovT = '$FicNovT'": "";
$FicNovI  = ($FicNovI) ? "AND FICHAS.FicNovI = '$FicNovI'": "";
$FicNovS  = ($FicNovS) ? "AND FICHAS.FicNovS = '$FicNovS'": "";
$FicNovA  = ($FicNovA) ? "AND FICHAS.FicNovA = '$FicNovA'": "";

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
$FilterEstruct  .= $Planta;
$FilterEstruct  .= $Sector;
$FilterEstruct  .= $Seccion;
$FilterEstruct  .= $Grupo;
$FilterEstruct  .= $Sucursal;
$FilterEstruct  .= $Tipo;
$FilterEstruct  .= $Legajos;
$FilterEstruct  .= $Per2;
// $FilterEstruct  .= $Thora;
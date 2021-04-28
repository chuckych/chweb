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
FusNuloPOST("ToInfornov",'');
FusNuloPOST("FicNoTi",'');
FusNuloPOST("FicNove",'');
FusNuloPOST("FicCausa",'');
FusNuloPOST("FicNovA",'');
FusNuloPOST("estruct",'');
$estruct    = test_input($_POST['estruct']);
$Per        = test_input($_POST['Per']);
$Per2       = test_input($_POST['Per2']);
$Per3       = test_input($_POST['Per2']);
$Emp        = test_input($_POST['Emp']);
$Plan       = test_input($_POST['Plan']);
$Sect       = ($_POST['Sect']);
$Grup       = test_input($_POST['Grup']);
$Sucur      = test_input($_POST['Sucur']);
$ToInfornov = test_input($_POST['ToInfornov']);
$FicNovA = test_input($_POST['FicNovA']);

switch ($estruct) {
    case 'Empr':
        $_POST['Emp'] = '';
        break;
    case 'Plan':
        $_POST['Plan'] = '';
        break;
    case 'Sect':
        $_POST['Sect'] = '';
        break;
    case 'Sec2':
        $_POST['Sec2'] = '';
        break;
    case 'Grup':
        $_POST['Grup'] = '';
        break;
    case 'Sucu':
        $_POST['Sucur'] = '';
        break;
    case 'Lega':
        $_POST['Per'] = '';
        break;
    case 'Tipo':
        $_POST['Tipo'] = '';
        break;
    case 'FicNoTi':
        $_POST['FicNoTi'] = '';
        break;
    case 'FicNove':
        $_POST['FicNove'] = '';
        break;
    case 'FicCausa':
        $_POST['FicCausa'] = '';
        break;
}

$Per2 = !empty($Per2) ? "AND PERSONAL.LegNume = '$Per2'": "";

$Tipo = test_input($_POST['Tipo']);
$Tipo = ($Tipo=='null')?'':$Tipo;
$Tipo = ($Tipo=='2') ? "AND PERSONAL.LegTipo = '0'": "AND PERSONAL.LegTipo = '$Tipo'";
// $FicNovA = ($FicNovA=='1') ? "AND FICHAS.FicNovA = 1": "";
$Tipo = empty(($_POST['Tipo'])) ? "": $Tipo;

if(!empty($_POST['Sec2'])){
    $Sec2 = implode(',',($_POST['Sec2']));
    $Seccion = !empty($Sec2) ? "AND dbo.fn_Concatenar(FICHAS.FicSect, FICHAS.FicSec2) IN ($Sec2)" :'';
}else{
    $Seccion ='';
}
$FicNoTi=$FicNove=$FicCausa='';
if (!empty($_POST['FicNoTi'])) {
    $FicNoTi = implode(',',($_POST['FicNoTi']));
    $FicNoTi = ($FicNoTi!='') ? "AND FICHAS3.FicNoTi IN ($FicNoTi)" :'';
}
if (!empty($_POST['FicNove'])) {
$FicNove = implode(',',($_POST['FicNove']));
$FicNove = ($FicNove!='') ? "AND FICHAS3.FicNove IN ($FicNove)" :'';
}
if (!empty($_POST['FicCausa'])) {
$FicCausa = implode(',',($_POST['FicCausa']));
$FicCausa = ($FicCausa!='') ? "AND dbo.fn_Concatenar(FICHAS3.FicNove, FICHAS3.FicCaus) IN ($FicCausa)" :'';
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
$FilterEstruct  .= $FicNoTi;
$FilterEstruct  .= $FicNove;
$FilterEstruct  .= $FicCausa;
// $FilterEstruct  .= $FicNovA;
$FilterEstruct2  = '';
// $FilterEstruct2  = $FicNoTi;
// $FilterEstruct2  .= $FicNove;
// $FilterEstruct2  .= $FicCausa;
// $FilterEstruct  .= $Thora;
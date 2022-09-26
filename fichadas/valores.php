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
FusNuloPOST("estruct",'');

FusNuloPOST("FicFalta", 0);
$FicFalta = test_input($_POST['FicFalta']);


$Per      = ($_POST['Per']);
$Per2     = ($_POST['Per2']);
$Per3     = ($_POST['Per2']);
$Emp      = ($_POST['Emp']);
$Plan     = ($_POST['Plan']);
$Sect     = ($_POST['Sect']);
$Grup     = ($_POST['Grup']);
$Sucur    = ($_POST['Sucur']);
$estruct    = test_input($_POST['estruct']);

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
}

$Per2 = !empty($Per2) ? "AND PERSONAL.LegNume = '$Per2'": "";

$Tipo = test_input($_POST['Tipo']);
$Tipo = ($Tipo=='2') ? "AND PERSONAL.LegTipo = '0'": "AND PERSONAL.LegTipo = '$Tipo'";
$Tipo = empty(($_POST['Tipo'])) ? "": $Tipo;

if(!empty($_POST['Sec2'])){
    $Sec2 = implode(',',($_POST['Sec2']));
    $Seccion = !empty($Sec2) ? "AND CONCAT(FICHAS.FicSect ,FICHAS.FicSec2) IN ($Sec2)" :'';
}else{
    $Seccion = '';
}
$FicFalta = ($FicFalta) ? "AND FICHAS.FicFalta = '$FicFalta'": "";
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
$FilterEstruct  .= $FicFalta;
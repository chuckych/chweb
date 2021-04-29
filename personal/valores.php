<?php
FusNuloPOST('q', '');
$q = test_input($_POST['q']);

FusNuloPOST("Per",'');
FusNuloPOST("Emp",'');
FusNuloPOST("Plan",'');
FusNuloPOST("Sect",'');
FusNuloPOST("Sec2",'');
FusNuloPOST("Grup",'');
FusNuloPOST("Sucur",'');
FusNuloPOST("Tipo",'');
FusNuloPOST("Tare",'');
FusNuloPOST("Regla",'');
FusNuloPOST("Conv",'');
FusNuloPOST("estruct",'');
FusNuloPOST("_eg",'');
FusNuloPOST("_porApNo",'');
FusNuloPOST("toexcel",'');

$estruct  = test_input($_POST['estruct']);
$Per      = ($_POST['Per']);
$Emp      = ($_POST['Emp']);
$Plan     = ($_POST['Plan']);
$Sect     = ($_POST['Sect']);
$Grup     = ($_POST['Grup']);
$Tare    = ($_POST['Tare']);
$Conv    = ($_POST['Conv']);
$Regla    = ($_POST['Regla']);
$Sucur    = ($_POST['Sucur']);
$toexcel    = test_input($_POST['toexcel']);
$eg       = test_input($_POST['_eg']);
$_porApNo = test_input($_POST['_porApNo']);

if (!$toexcel) {
    $estado = (($eg === 'on')) ? "AND PERSONAL.LegFeEg != '17530101'" : "AND PERSONAL.LegFeEg = '17530101'";
}
$OrderBy = (($_porApNo === 'on')) ? "ORDER BY PERSONAL.LegApNo":"ORDER BY PERSONAL.LegNume";

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
    case 'Tare':
        $_POST['Tare'] = '';
        break;
    case 'Regla':
        $_POST['Regla'] = '';
        break;
    case 'Conv':
        $_POST['Conv'] = '';
        break;
}


$Tipo = test_input($_POST['Tipo']);
$Tipo = ($Tipo=='null')?'':$Tipo;
$Tipo = ($Tipo=='2') ? "AND PERSONAL.LegTipo = '0'": "AND PERSONAL.LegTipo = '$Tipo'";
$Tipo = empty(($_POST['Tipo'])) ? "": $Tipo;

if(!empty($_POST['Sec2'])){
    $Sec2 = implode(',',($_POST['Sec2']));
    $Seccion = !empty($Sec2) ? "AND dbo.fn_Concatenar(PERSONAL.LegSect, PERSONAL.LegSec2) IN ($Sec2)" :'';
}else{
    $Seccion ='';
}

$Empresa  = datosGetIn($_POST['Emp'], "PERSONAL.LegEmpr");
$Planta   = datosGetIn($_POST['Plan'], "PERSONAL.LegPlan");
$Sector   = datosGetIn($_POST['Sect'], "PERSONAL.LegSect");
$Grupo    = datosGetIn($_POST['Grup'], "PERSONAL.LegGrup");
$Sucursal = datosGetIn($_POST['Sucur'], "PERSONAL.LegSucu");
$Legajos  = datosGetIn($_POST['Per'], "PERSONAL.LegNume");
$Tareas   = datosGetIn($_POST['Tare'], "PERSONAL.LegTareProd");
$Convenio = datosGetIn($_POST['Conv'], "PERSONAL.LegConv");
$Regla    = datosGetIn($_POST['Regla'], "PERSONAL.LegRegCH");
// $Tipo     = datosGetIn($_POST['Tipo'], "PERSONAL.LegTipo");
// $Legajos = !empty($Per2) ? "": "$Legajos";

$FilterEstruct  = $estado;
$FilterEstruct  .= $Empresa;
$FilterEstruct  .= $Planta;
$FilterEstruct  .= $Sector;
$FilterEstruct  .= $Seccion;
$FilterEstruct  .= $Grupo;
$FilterEstruct  .= $Sucursal;
$FilterEstruct  .= $Tipo;
$FilterEstruct  .= $Legajos;
$FilterEstruct  .= $Tareas;
$FilterEstruct  .= $Convenio;
$FilterEstruct  .= $Regla;

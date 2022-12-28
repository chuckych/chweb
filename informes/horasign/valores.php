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
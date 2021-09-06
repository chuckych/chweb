<?php
FusNuloPOST('q', '');
$q = test_input($_POST['q']);
$data = array();
if (isset($_POST['_dr']) && !empty($_POST['_dr'])) {
    $DateRange = explode(' al ', $_POST['_dr']);
    $FechaIni  = test_input(dr_fecha($DateRange[0]));
    $FechaFin  = test_input(dr_fecha($DateRange[1]));
} else {
    FusNuloPOST("FechaFin", '');
    $FechaIni = FechaString(test_input($_POST['FechaFin']));
    $FechaFin = FechaString(test_input($_POST['FechaFin']));
}

FusNuloPOST("Per", '');
FusNuloPOST("Per2", '');
FusNuloPOST("Emp", '');
FusNuloPOST("Plan", '');
FusNuloPOST("Sect", '');
FusNuloPOST("Sec2", '');
FusNuloPOST("Grup", '');
FusNuloPOST("Sucur", '');
FusNuloPOST("Tipo", '');
FusNuloPOST("FicDiaL", false);
FusNuloPOST("FicFalta", 0);
FusNuloPOST("FicNovT", 0);
FusNuloPOST("FicNovI", 0);
FusNuloPOST("FicNovS", 0);
FusNuloPOST("FicNovA", 0);
FusNuloPOST("Fic3Nov", '');


$Per      = ($_POST['Per']);
$Per2     = test_input($_POST['Per2']);
$Per3     = test_input($_POST['Per2']);
$Emp      = ($_POST['Emp']);
$Plan     = ($_POST['Plan']);
$Sect     = ($_POST['Sect']);
$Grup     = ($_POST['Grup']);
$Sucur    = ($_POST['Sucur']);
$FicDiaL  = test_input($_POST['FicDiaL']);
$FicFalta = test_input($_POST['FicFalta']);
$FicNovT  = test_input($_POST['FicNovT']);
$FicNovI  = test_input($_POST['FicNovI']);
$FicNovS  = test_input($_POST['FicNovS']);
$FicNovA  = test_input($_POST['FicNovA']);
$Fic3Nov  = test_input($_POST['Fic3Nov']);
$FicDiaL  = ($FicDiaL) ? "AND FICHAS.FicDiaL = '$FicDiaL'" : "";
$FicFalta = ($FicFalta) ? "AND FICHAS.FicFalta = '$FicFalta'" : "";
$FicNovT  = ($FicNovT) ? "AND FICHAS.FicNovT = '$FicNovT'" : "";
$FicNovI  = ($FicNovI) ? "AND FICHAS.FicNovI = '$FicNovI'" : "";
$FicNovS  = ($FicNovS) ? "AND FICHAS.FicNovS = '$FicNovS'" : "";
$FicNovA  = ($FicNovA) ? "AND FICHAS.FicNovA = '$FicNovA'" : "";
$Fic3Nov  = ($Fic3Nov) ? "AND FICHAS3.FicNove = '$Fic3Nov'" : "";

FusNuloPOST("Filtros", '');
$arrFiltros = json_decode($_POST['Filtros']);
$LegDe = (intval($arrFiltros->LegDe)) ? intval($arrFiltros->LegDe) : 1;
$LegHa = (intval($arrFiltros->LegHa)) ? intval($arrFiltros->LegHa) : 999999999999;

if (($LegDe + $LegHa) > 0) {
    $LegDe = ($LegHa < $LegDe) ? $LegHa : $LegDe;
    $Filtros = "AND FICHAS.FicLega BETWEEN $LegDe AND $LegHa";
}else {
    $Filtros = "";
}
// print_r($Filtros);exit;

$Per2 = !empty($Per2) ? "AND PERSONAL.LegNume = '$Per2'" : "";

$Tipo = test_input($_POST['Tipo']);
$Tipo = ($Tipo == '2') ? "AND PERSONAL.LegTipo = '0'" : "AND PERSONAL.LegTipo = '$Tipo'";
$Tipo = empty(($_POST['Tipo'])) ? "" : $Tipo;

if (!empty($_POST['Sec2'])) {
    $Sec2 = implode(',', ($_POST['Sec2']));
    $Seccion = !empty($Sec2) ? "AND CONCAT(FICHAS.FicSect, FICHAS.FicSec2) IN ($Sec2)" : '';
} else {
    $Seccion = '';
}


/** Filtro de novedad */
$FicNovTNov  = test_input($_POST['FicNovT']);
$FicNovINov  = test_input($_POST['FicNovI']);
$FicNovSNov  = test_input($_POST['FicNovS']);
$FicNovANov  = test_input($_POST['FicNovA']);

$tipoNovT = ($FicNovTNov) ? "99" : '';
$tipoNovI = ($FicNovINov) ? '1' : '';
$tipoNovS = ($FicNovSNov) ? '2' : '';
$tipoNovA = ($FicNovANov) ? '3,4,5,6,7,8' : '';

$arraTipo = array();
($tipoNovT) ? array_push($arraTipo, $tipoNovT) : '';
($tipoNovI) ? array_push($arraTipo, $tipoNovI) : '';
($tipoNovS) ? array_push($arraTipo, $tipoNovS) : '';
($tipoNovA) ? array_push($arraTipo, $tipoNovA) : '';


$arraTipo = implode(',', $arraTipo);

if ($arraTipo) {
    $arraTipo   = str_replace('99', '0', $arraTipo);
    $filtroTipo = "AND FICHAS3.FicNoTi IN ($arraTipo)";
} else {
    $filtroTipo = '';
}
if (test_input($_POST['Fic3Nov'])) {
    $joinFichas3 = "INNER JOIN FICHAS3 ON FICHAS.FicLega=FICHAS3.FicLega AND FICHAS.FicFech=FICHAS3.FicFech AND FICHAS.FicTurn=FICHAS3.FicTurn";
}else {
    $joinFichas3 = '';
}

/** Fin Filtro de novedad */

$Empresa  = datosGetIn($_POST['Emp'], "PERSONAL.LegEmpr");
$Planta   = datosGetIn($_POST['Plan'], "PERSONAL.LegPlan");
$Sector   = datosGetIn($_POST['Sect'], "PERSONAL.LegSect");
$Grupo    = datosGetIn($_POST['Grup'], "PERSONAL.LegGrup");
$Sucursal = datosGetIn($_POST['Sucur'], "PERSONAL.LegSucu");
$Legajos  = datosGetIn($_POST['Per'], "FICHAS.FicLega");
$Legajos = !empty($Per2) ? "" : "$Legajos";

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
$FilterEstruct  .= $Fic3Nov;
$FilterEstruct  .= $Filtros;
// $FilterEstruct  .= $Thora;
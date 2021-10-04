<?php
function implodeData($Get, $Col)
{
    if ($Get) {
        $Get = implode(',', $Get);
        $t = "AND FICHAS." . $Col . " IN (" . (test_input($Get)) . ") ";
        return ($t);
    }
    return false;
};

$dataEmpresas     = implodeData($_POST['datos']['empresa'], 'FicEmpr'); // Empresas a consultar
$dataPlantas      = implodeData($_POST['datos']['planta'], 'FicPlan'); // Planta a consultar
$dataSectores     = implodeData($_POST['datos']['sector'], 'FicSect'); // Sector a consultar
$dataSecciones    = implodeData($_POST['datos']['seccion'],'FicSec2'); // Seccion a consultar
$dataGrupos       = implodeData($_POST['datos']['grupo'], 'FicGrup'); // Grupo a consultar
$dataSucursales   = implodeData($_POST['datos']['sucursal'], 'FicSucu');// Sucursal a consultar
$dataPersonal     = implodeData($_POST['datos']['personal'], 'FicLega'); // Personal a consultar
// $dataTipoPersonal = $_POST['datos']['tipoper']; // Tipo de Personal a consultar

$DateRange = explode(' al ', $_POST['datos']['_dr']);
$FechaIni  = test_input(dr_fecha($DateRange[0]));
$FechaFin  = test_input(dr_fecha($DateRange[1]));

$FilterEstruct = '';
$FilterEstruct .= $dataEmpresas;    
$FilterEstruct .= $dataPlantas;
$FilterEstruct .= $dataSectores;
$FilterEstruct .= $dataSecciones;
$FilterEstruct .= $dataGrupos;
$FilterEstruct .= $dataSucursales;
$FilterEstruct .= $dataPersonal;
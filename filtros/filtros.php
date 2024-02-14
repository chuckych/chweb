<?php
E_ALL();
// session_start();
$Get_Emp = (!isset($_GET['_emp'])) ? '' : $_GET['_emp'];
$Get_Pla = (!isset($_GET['_pla'])) ? '' : $_GET['_pla'];
$Get_Con = (!isset($_GET['_con'])) ? '' : $_GET['_con'];
$Get_Sec = (!isset($_GET['_sec'])) ? '' : $_GET['_sec'];
$Get_Gru = (!isset($_GET['_gru'])) ? '' : $_GET['_gru'];
$Get_suc = (!isset($_GET['_suc'])) ? '' : $_GET['_suc'];
$Get_per = (!isset($_GET['_per'])) ? '' : $_GET['_per'];
$Get_per2 = (!isset($_POST['_lega'])) ? '' : 'AND PERSONAL.LegNume = ' . $_POST['_lega'];

function filtrar_estruc($get, $col)
{
    $cond = ((!empty($get)));
    $Nombre_Filtro = ($cond ? $valores = implode(",", $get) : $cond) ? "AND PERSONAL.$col IN ($valores)" : '';
    return $Nombre_Filtro;
}
;

function Filtro_Personal($get_per)
{
    $get_per = (!isset($_GET['_per'])) ? '' : $_GET['_per'];
    $cond = ((!empty($get_per)));
    $Filtro_Per = $cond ? "WHERE PERSONAL.LegNume IN (" . implode(',', $get_per) . ")" : '';
    require __DIR__ . '../../config/conect_mssql.php';
    $query = "SELECT PERSONAL.LegNume AS pers_legajo FROM PERSONAL $Filtro_Per";
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $result = sqlsrv_query($link, $query, $params, $options);
    if (sqlsrv_num_rows($result) > 0) {
        while ($row = sqlsrv_fetch_array($result)):
            $id[] = $row['pers_legajo'];
            $valores = implode(",", $id);
        endwhile;
    }
    sqlsrv_free_stmt($result);
    sqlsrv_close($link);
    return 'AND PERSONAL.LegNume IN(' . ($valores) . ')';
}
// FusNuloGET("_r",'');
// $data_emp      = (estructura_rol('GetEstructRol', $_GET['_r'], 'empresas', 'empresa'));
$_SESSION['EmprRol'] = $_SESSION['EmprRol'] ?? '';
$_SESSION['PlanRol'] = $_SESSION['PlanRol'] ?? '';
$_SESSION['ConvRol'] = $_SESSION['ConvRol'] ?? '';
$_SESSION['SectRol'] = $_SESSION['SectRol'] ?? '';
$_SESSION['Sec2Rol'] = $_SESSION['Sec2Rol'] ?? '';
$_SESSION['GrupRol'] = $_SESSION['GrupRol'] ?? '';
$_SESSION['SucuRol'] = $_SESSION['SucuRol'] ?? '';
$_SESSION['EstrUser'] = $_SESSION['EstrUser'] ?? '';

$data_emp = $_SESSION['EmprRol'];
$data_emp1 = str_replace(32768, 0, $data_emp);
$Empresas = (!empty($data_emp)) ? "AND PERSONAL.LegEmpr IN ($data_emp1) " : '';
$EmpresasFichas = (!empty($data_emp)) ? "AND FICHAS.FicEmpr IN ($data_emp1) " : '';
// $data_pla      = (estructura_rol('GetEstructRol', $_GET['_r'], 'plantas', 'planta'));
$data_pla = $_SESSION['PlanRol'];
$data_pla1 = str_replace(32768, 0, $data_pla);
$Plantas = (!empty($data_pla)) ? "AND PERSONAL.LegPlan IN ($data_pla1) " : '';
$PlantasFichas = (!empty($data_pla)) ? "AND FICHAS.FicPlan IN ($data_pla1) " : '';
// $data_con      = (estructura_rol('GetEstructRol', $_GET['_r'], 'convenios', 'convenio'));
$data_con = $_SESSION['ConvRol'];
$data_con1 = str_replace(32768, 0, $data_con);
$Convenios = (!empty($data_con)) ? "AND PERSONAL.LegConv IN ($data_con1) " : '';
$ConveniosFichas = (!empty($data_con)) ? "AND FICHAS.FicConv IN ($data_con1) " : '';
// $data_sec      = (estructura_rol('GetEstructRol', $_GET['_r'], 'sectores', 'sector'));
$data_sec = $_SESSION['SectRol'];
$data_sec1 = str_replace(32768, 0, $data_sec);
$Sectores = (!empty($data_sec)) ? "AND PERSONAL.LegSect IN ($data_sec1) " : '';
$SectoresFichas = (!empty($data_sec)) ? "AND FICHAS.FicSect IN ($data_sec1) " : '';
// $data_se2      = (estructura_rol('GetEstructRol', $_GET['_r'], 'secciones', 'seccion'));
$data_se2 = $_SESSION['Sec2Rol'];
$Secciones = (!empty($data_se2)) ? "AND CONCAT(PERSONAL.LegSect,PERSONAL.LegSec2) IN ($data_se2) " : '';
$SeccionesFichas = (!empty($data_se2)) ? "AND CONCAT(FICHAS.FicSect,FICHAS.FicSec2) IN ($data_se2) " : '';
// $data_gru      = (estructura_rol('GetEstructRol', $_GET['_r'], 'grupos', 'grupo'));
$data_gru = $_SESSION['GrupRol'];
$Grupos = (!empty($data_gru)) ? "AND PERSONAL.LegGrup IN ($data_gru) " : '';
$GruposFichas = (!empty($data_gru)) ? "AND FICHAS.FicGrup IN ($data_gru) " : '';
// $data_suc      = (estructura_rol('GetEstructRol', $_GET['_r'], 'sucursales', 'sucursal'));
$data_suc = $_SESSION['SucuRol'];
$Sucursales = (!empty($data_suc)) ? "AND PERSONAL.LegSucu IN ($data_suc) " : '';
$SucursalesFichas = (!empty($data_suc)) ? "AND FICHAS.FicSucu IN ($data_suc) " : '';
$data_per = $_SESSION['EstrUser'];
$Legajos = (!empty($data_per)) ? "AND PERSONAL.LegNume IN ($data_per) " : '';
$LegajosFichas = (!empty($data_per)) ? "AND FICHAS.FicLega IN ($data_per) " : '';

$Filtro2 = filtrar_estruc($Get_Emp, 'LegEmpr');
$Filtro2 .= filtrar_estruc($Get_Pla, 'LegPlan');
$Filtro2 .= filtrar_estruc($Get_Con, 'LegConv');
$Filtro2 .= filtrar_estruc($Get_Sec, 'LegSect');
$Filtro2 .= filtrar_estruc($Get_Gru, 'LegGrup');
$Filtro2 .= filtrar_estruc($Get_suc, 'LegSucu');

$filtros = $Empresas;
$filtros .= $Plantas;
$filtros .= $Convenios;
$filtros .= $Sectores;
$filtros .= $Grupos;
$filtros .= $Sucursales;
$filtros .= $Secciones;
$filtros .= $Filtro2;
$filtros .= ($Get_per) ? Filtro_Personal($Get_per) : '';
$filtros .= ($Get_per2);
$filtros .= ($Legajos);

$FiltrosFichas = $EmpresasFichas;
$FiltrosFichas .= $PlantasFichas;
$FiltrosFichas .= $ConveniosFichas;
$FiltrosFichas .= $SectoresFichas;
$FiltrosFichas .= $SeccionesFichas;
$FiltrosFichas .= $GruposFichas;
$FiltrosFichas .= $SucursalesFichas;
$FiltrosFichas .= $LegajosFichas;
// print_r($filtros);

<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../config/index.php';
ultimoacc();
secure_auth_ch_json();
header("Content-Type: application/json");

require __DIR__ . '/../../config/conect_mssql.php';
// sleep(2);
$data = array();
$dataLista = array();

$_GET['_c'] = $_GET['_c'] ?? '';
$_GET['uid'] = $_GET['uid'] ?? '';
$_GET['rel'] = $_GET['rel'] ?? '';

$uid = test_input($_GET['uid']);
$lista = test_input($_GET['lista']);
$cliente = test_input($_GET['_c']);
$rel = test_input($_GET['rel']);

if (!$uid) {
    $data = array();
    exit;
}
if (!$lista) {
    $data = array();
    exit;
}

function filtroListas($uid, $lista)
{
    $r = dataListaEstruct($lista, $uid);
    $r = implode(',', $r);
    $r = str_replace('-', '', $r);
    $r = str_replace(32768, 0, $r);
    return $r;
}
$filtroListas = '';
if ($rel) {
    $Empr = test_input(filtroListas($uid, 1));
    $Plan = test_input(filtroListas($uid, 2));
    $Conv = test_input(filtroListas($uid, 3));
    $Sect = test_input(filtroListas($uid, 4));
    $Sec2 = test_input(filtroListas($uid, 5));
    $Grup = test_input(filtroListas($uid, 6));
    $Sucu = test_input(filtroListas($uid, 7));
    $Lega = test_input(filtroListas($uid, 8));

    $filtroListas = ($Empr != '') ? "AND PERSONAL.LegEmpr IN($Empr)" : '';
    $filtroListas .= ($Plan != '') ? "AND PERSONAL.LegPlan IN($Plan)" : '';
    $filtroListas .= ($Conv != '') ? "AND PERSONAL.LegConv IN($Conv)" : '';
    $filtroListas .= ($Sect != '') ? "AND PERSONAL.LegSect IN($Sect)" : '';
    $filtroListas .= ($Sec2 != '') ? "AND CONCAT(PERSONAL.LegSect,PERSONAL.LegSec2) IN($Sec2)" : '';
    $filtroListas .= ($Grup != '') ? "AND PERSONAL.LegGrup IN($Grup)" : '';
    $filtroListas .= ($Sucu != '') ? "AND PERSONAL.LegSucu IN($Sucu)" : '';
    $filtroListas .= ($Lega != '') ? "AND PERSONAL.LegNume IN($Lega)" : '';
}
// print_r($Empr);exit;

$dataLista = dataListaEstruct($lista, $uid);
$dataLista = explode(',', $dataLista[0]);

$param = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

function totLeg($col, $codi, $filtroListas)
{
    return "SELECT COUNT(1) FROM PERSONAL WHERE $col = $codi AND PERSONAL.LegNume > 0 AND PERSONAL.LegFeEg = '17530101' $filtroListas";
}
;
$set = '';
switch ($lista) {
    case 1:
        // sleep(3);
        $totLeg = totLeg('LegEmpr', 'EmpCodi', $filtroListas);
        $query = "SELECT DISTINCT(PERSONAL.LegEmpr) AS 'EmpCodi', EMPRESAS.EmpRazon AS 'EmpRazon', ($totLeg) AS 'totLeg' FROM PERSONAL INNER JOIN EMPRESAS ON PERSONAL.LegEmpr = EMPRESAS.EmpCodi WHERE ($totLeg) > 0 $filtroListas";
        // print_r($query);exit;
        $rs = sqlsrv_query($link, $query, $params, $options);
        if (sqlsrv_num_rows($rs) > 0) {
            while ($r = sqlsrv_fetch_array($rs)):
                foreach ($dataLista as $key => $value) {
                    if ($value != '-') {
                        $value = ($value == '32768') ? 0 : $value;
                        $set = (($value) == $r['EmpCodi']) ? 1 : 0;
                        if ($set === 1) {
                            break;
                        } else {
                            $set = 0;
                        }
                    }
                }
                $data[] = array(
                    'codigo' => $r['EmpCodi'],
                    'descripcion' => empty($r['EmpRazon']) ? 'Sin Empresa' : $r['EmpRazon'],
                    'totLeg' => $r['totLeg'],
                    'set' => ($set),
                );
            endwhile;
        }
        sqlsrv_free_stmt($rs);
        sqlsrv_close($link);
        break;
    case 2:
        $totLeg = totLeg('LegPlan', 'PlaCodi', $filtroListas);
        // $query = "SELECT PlaCodi, PlaDesc, ($totLeg) as 'totLeg' FROM PLANTAS WHERE PlaCodi > 0";
        $query = "SELECT DISTINCT(PERSONAL.LegPlan) as 'PlaCodi', PLANTAS.PlaDesc as 'PlaDesc', ($totLeg) as 'totLeg' FROM PERSONAL INNER JOIN PLANTAS ON PERSONAL.LegPlan=PLANTAS.PlaCodi WHERE ($totLeg) > 0 $filtroListas";
        $rs = sqlsrv_query($link, $query, $params, $options);
        if (sqlsrv_num_rows($rs) > 0) {
            while ($r = sqlsrv_fetch_array($rs)):
                foreach ($dataLista as $key => $value) {
                    if ($value != '-') {
                        $value = ($value == '32768') ? 0 : $value;
                        $set = (($value) == $r['PlaCodi']) ? 1 : 0;
                        if ($set === 1) {
                            break;
                        } else {
                            $set = 0;
                        }
                    }
                }
                $data[] = array(
                    'codigo' => $r['PlaCodi'],
                    'descripcion' => empty($r['PlaDesc']) ? 'Sin Planta' : $r['PlaDesc'],
                    'totLeg' => $r['totLeg'],
                    'set' => ($set),
                );
            endwhile;
        }
        sqlsrv_free_stmt($rs);
        sqlsrv_close($link);
        break;
    case 3:
        $totLeg = totLeg('LegConv', 'ConCodi', $filtroListas);
        // $query = "SELECT ConCodi, ConDesc, ($totLeg) as 'totLeg' FROM CONVENIO";
        $query = "SELECT DISTINCT(PERSONAL.LegConv) as 'ConCodi', CONVENIO.ConDesc as 'ConDesc', ($totLeg) as 'totLeg' FROM PERSONAL INNER JOIN CONVENIO ON PERSONAL.LegConv=CONVENIO.ConCodi WHERE ($totLeg) > 0 $filtroListas";
        $rs = sqlsrv_query($link, $query, $params, $options);
        if (sqlsrv_num_rows($rs) > 0) {
            while ($r = sqlsrv_fetch_array($rs)):
                foreach ($dataLista as $key => $value) {
                    if ($value != '-') {
                        $value = ($value == '32768') ? 0 : $value;
                        $set = (($value) == $r['ConCodi']) ? 1 : 0;
                        if ($set === 1) {
                            break;
                        } else {
                            $set = 0;
                        }
                    }
                }
                $data[] = array(
                    'codigo' => ($r['ConCodi']),
                    'descripcion' => $r['ConDesc'],
                    'totLeg' => $r['totLeg'],
                    'set' => ($set),
                );
            endwhile;
        }
        sqlsrv_free_stmt($rs);
        sqlsrv_close($link);
        break;
    case 4:
        $totLeg = totLeg('LegSect', 'SecCodi', $filtroListas);
        // $query = "SELECT SecCodi, SecDesc, ($totLeg) as 'totLeg' FROM SECTORES WHERE SecCodi > 0";
        $query = "SELECT DISTINCT(PERSONAL.LegSect) as 'LegSect', SECTORES.SecDesc as 'SecDesc', ($totLeg) as 'totLeg' FROM PERSONAL INNER JOIN SECTORES ON PERSONAL.LegSect=SECTORES.SecCodi WHERE ($totLeg) > 0 $filtroListas";
        $rs = sqlsrv_query($link, $query, $params, $options);
        if (sqlsrv_num_rows($rs) > 0) {
            while ($r = sqlsrv_fetch_array($rs)):
                foreach ($dataLista as $key => $value) {
                    if ($value != '-') {
                        $value = ($value == '32768') ? 0 : $value;
                        $set = (($value) == $r['LegSect']) ? 1 : 0;
                        if ($set === 1) {
                            break;
                        } else {
                            $set = 0;
                        }
                    }
                }
                $data[] = array(
                    'codigo' => $r['LegSect'],
                    'descripcion' => empty($r['SecDesc']) ? 'Sin Sector' : $r['SecDesc'],
                    'totLeg' => $r['totLeg'],
                    'set' => ($set),
                );
            endwhile;
        }
        sqlsrv_free_stmt($rs);
        sqlsrv_close($link);
        break;
    case 5:
        $dataListaSecc = dataListaEstruct(4, $uid);
        $dataListaSecc = implode(',', $dataListaSecc);
        $dataListaSecc = ($dataListaSecc == '-') ? '0' : $dataListaSecc;

        $totLeg = "SELECT COUNT(1) FROM PERSONAL WHERE PERSONAL.LegSect = SECCION.SecCodi AND PERSONAL.LegSec2 = SECCION.Se2Codi AND PERSONAL.LegFeEg = '17530101' $filtroListas";

        // $query = "SELECT SECCION.SecCodi AS 'SecCodi', SECTORES.SecDesc AS 'SecDesc', SECCION.Se2Codi AS 'Se2Codi', SECCION.Se2Desc AS 'Se2Desc', ($totLeg) as 'totLeg'
        // FROM SECCION
        // INNER JOIN SECTORES ON SECCION.SecCodi = SECTORES.SecCodi WHERE SECCION.SecCodi > 0 AND SECCION.Se2Codi > 0 AND SECCION.SecCodi IN ($dataListaSecc) ORDER BY SECCION.SecCodi, SECCION.Se2Codi";
        $query = "SELECT DISTINCT(PERSONAL.LegSec2) as 'Se2Codi', SECCION.Se2Desc, PERSONAL.LegSect AS 'SecCodi', SECTORES.SecDesc AS 'SecDesc', ($totLeg) as 'totLeg' FROM PERSONAL INNER JOIN SECCION ON PERSONAL.LegSect=SECCION.SecCodi AND PERSONAL.LegSec2=SECCION.Se2Codi INNER JOIN SECTORES ON PERSONAL.LegSect=SECTORES.SecCodi WHERE PERSONAL.LegSect >0 AND PERSONAL.LegSect IN ($dataListaSecc) and ($totLeg) > 0 $filtroListas ORDER BY PERSONAL.LegSect, PERSONAL.LegSec2";
        // print_r($query); exit;
        $rs = sqlsrv_query($link, $query, $params, $options);
        if (sqlsrv_num_rows($rs) > 0) {
            while ($r = sqlsrv_fetch_array($rs)):
                foreach ($dataLista as $key => $value) {
                    $set = ($value == $r['SecCodi'] . $r['Se2Codi']) ? 1 : 0;
                    if ($set === 1) {
                        break;
                    }
                }
                $data[] = array(
                    'Se2Codi' => $r['Se2Codi'],
                    // 'Se2Desc' => $r['Se2Desc'],
                    'Se2Desc' => empty($r['Se2Desc']) ? 'Sin Sección' : $r['Se2Desc'],
                    'SecCodi' => $r['SecCodi'],
                    'SecDesc' => $r['SecDesc'],
                    'codigo' => $r['SecCodi'] . $r['Se2Codi'],
                    'totLeg' => $r['totLeg'],
                    'set' => ($set),
                );
            endwhile;
        }
        sqlsrv_free_stmt($rs);
        sqlsrv_close($link);
        break;
    case 6:
        $totLeg = totLeg('LegGrup', 'GruCodi', $filtroListas);
        // $query = "SELECT GruCodi, GruDesc, ($totLeg) as totLeg FROM GRUPOS WHERE GruCodi > 0";
        $query = "SELECT DISTINCT(PERSONAL.LegGrup) as 'GruCodi', GRUPOS.GruDesc as 'GruDesc', ($totLeg) as totLeg FROM PERSONAL INNER JOIN GRUPOS ON PERSONAL.LegGrup=GRUPOS.GruCodi WHERE ($totLeg) > 0 $filtroListas";
        $rs = sqlsrv_query($link, $query, $params, $options);
        if (sqlsrv_num_rows($rs) > 0) {
            while ($r = sqlsrv_fetch_array($rs)):
                foreach ($dataLista as $key => $value) {
                    if ($value != '-') {
                        $value = ($value == '32768') ? 0 : $value;
                        $set = (($value) == $r['GruCodi']) ? 1 : 0;
                        if ($set === 1) {
                            break;
                        } else {
                            $set = 0;
                        }
                    }
                }
                $data[] = array(
                    'codigo' => $r['GruCodi'],
                    'descripcion' => empty($r['GruDesc']) ? 'Sin Grupo' : $r['GruDesc'],
                    'totLeg' => $r['totLeg'],
                    'set' => ($set),
                );
            endwhile;
        }
        sqlsrv_free_stmt($rs);
        sqlsrv_close($link);
        break;
    case 7:
        $totLeg = totLeg('LegSucu', 'SucCodi', $filtroListas);
        // $query = "SELECT SucCodi, SucDesc, ($totLeg) as 'totLeg' FROM SUCURSALES WHERE SucCodi > 0";
        $query = "SELECT DISTINCT(PERSONAL.LegSucu) as 'SucCodi', SUCURSALES.SucDesc as 'SucDesc', ($totLeg) as 'totLeg' FROM PERSONAL INNER JOIN SUCURSALES ON PERSONAL.LegSucu=SUCURSALES.SucCodi WHERE ($totLeg) > 0 $filtroListas";
        $rs = sqlsrv_query($link, $query, $params, $options);
        if (sqlsrv_num_rows($rs) > 0) {
            while ($r = sqlsrv_fetch_array($rs)):
                foreach ($dataLista as $key => $value) {
                    if ($value != '-') {
                        $value = ($value == '32768') ? 0 : $value;
                        $set = (($value) == $r['SucCodi']) ? 1 : 0;
                        if ($set === 1) {
                            break;
                        } else {
                            $set = 0;
                        }
                    }
                }
                $data[] = array(
                    'codigo' => $r['SucCodi'],
                    'descripcion' => empty($r['SucDesc']) ? 'Sin Sucursal' : $r['SucDesc'],
                    'totLeg' => $r['totLeg'],
                    'set' => ($set),
                );
            endwhile;
        }
        sqlsrv_free_stmt($rs);
        sqlsrv_close($link);
        break;
    case 8:
        $query = "SELECT LegNume, LegApNo, LegFeEg FROM PERSONAL WHERE LegNume > 0 AND PERSONAL.LegFeEg = '17530101' $filtroListas ORDER BY LegFeEg, LegNume";
        $rs = sqlsrv_query($link, $query, $params, $options);
        if (sqlsrv_num_rows($rs) > 0) {
            while ($r = sqlsrv_fetch_array($rs)):
                foreach ($dataLista as $key => $value) {
                    $set = ($value == $r['LegNume']) ? 1 : 0;
                    if ($set === 1) {
                        break;
                    }
                }
                $data[] = array(
                    'codigo' => $r['LegNume'],
                    'descripcion' => $r['LegApNo'],
                    'estado' => ($r['LegFeEg']->format('Ymd') == '17530101') ? 1 : 0,
                    'set' => ($set),
                );
            endwhile;
        }
        sqlsrv_free_stmt($rs);
        sqlsrv_close($link);
        break;
    case 10:
        require __DIR__ . '/../../config/conect_mysql.php';
        $stmt = mysqli_query($link, "SELECT u.id, u.nombre, u.legajo, c.nombre as cuenta FROM USUARIOS u INNER JOIN clientes c ON u.cliente = c.id WHERE c.recid = '$cliente' AND u.id > 1 AND u.id !='$uid'");
        // print_r($query); exit;
        if (($stmt)) {
            if (mysqli_num_rows($stmt) > 0) {
                while ($row = mysqli_fetch_assoc($stmt)) {
                    $data[] = array(
                        'codigo' => $row['id'],
                        'descripcion' => $row['nombre'],
                        'legajo' => $row['legajo'],
                        'cuenta' => $row['cuenta'],
                        'set' => 0,
                    );
                }
            }
            mysqli_free_result($stmt);
            mysqli_close($link);
        } else {
            statusData('error', mysqli_error($link));
            mysqli_close($link);
            exit;
        }
        break;
}

$json_data = array(
    "data" => $data
);

echo json_encode($json_data);
exit;

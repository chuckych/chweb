<?php
session_start();
header('Content-type: text/html; charset=utf-8');
header("Content-Type: application/json");
require __DIR__ . '/../../config/index.php';
ultimoacc();
secure_auth_ch();
E_ALL();


$estruct = FusNuloPOST('estruct', '');
$q = FusNuloPOST('q', '');

$request = Flight::request();
$method = $request->method;
$params = $request->data;
$authBasic = base64_encode('chweb:' . HOMEHOST);
$token = sha1($_SESSION['RECID_CLIENTE']);


require __DIR__ . '/../valores.php';
require __DIR__ . '/../../filtros/filtros.php';
require __DIR__ . '/../../config/conect_mssql.php';


$params['Per'] = $params['Per'] ?? '';
$params['Emp'] = $params['Emp'] ?? '';
$params['Plan'] = $params['Plan'] ?? '';
$params['Sect'] = $params['Sect'] ?? '';
$params['Sec2'] = $params['Sec2'] ?? '';
$params['Grup'] = $params['Grup'] ?? '';
$params['Sucur'] = $params['Sucur'] ?? '';
$params['Tipo'] = ($params['Tipo']) ?? '';
$params['Regla'] = ($params['Regla']) ?? '';

$Empr = $params['Emp'] ? ($params['Emp']) : explode(',', $_SESSION['EmprRol']);
$Per = $params['Per'] ? ($params['Per']) : array();
$Per2 = $params['Per2'] ? array($params['Per2']) : explode(',', $_SESSION['EstrUser']);
$Plan = $params['Plan'] ? $params['Plan'] : explode(',', $_SESSION['PlanRol']);
$Sect = $params['Sect'] ? $params['Sect'] : explode(',', $_SESSION['SectRol']);
$Grup = $params['Grup'] ? $params['Grup'] : explode(',', $_SESSION['GrupRol']);
$Sucu = $params['Sucur'] ? $params['Sucur'] : explode(',', $_SESSION['SucuRol']);
$Sec2 = $params['Sec2'] ? $params['Sec2'] : explode(',', $_SESSION['Sec2Rol']);
$Conv = $params['Conv'] ? $params['Conv'] : explode(',', $_SESSION['ConvRol']);
$Tare = $params['Tare'] ? $params['Tare'] : '';
switch ($params['Tipo']) {
    case '2':
        $LegTipo = array('0');
        break;
    case '1':
        $LegTipo = array('1');
        break;
    default:
        $LegTipo = array();
        break;
}
$RegCH = $params['Regla'] ? $params['Regla'] : '';
$Legajos = ($Per) ? ($Per) : array();

$dataApiPerson['DATA'] = $dataApiPerson['DATA'] ?? '';
$dataApiPerson['MESSAGE'] = $dataApiPerson['MESSAGE'] ?? '';

$dataParamPerson = array(
    "Nume" => ($Legajos),
    "ApNoNume" => ($q),
    "getDatos" => 0,
    "getEstruct" => 0,
    "Baja" => array("0"),
    "Empr" => ($Empr),
    "Plan" => ($Plan),
    "Sect" => ($Sect),
    "Sec2" => ($Sec2),
    "Grup" => ($Grup),
    "Sucu" => ($Sucu),
    "Conv" => ($Conv),
    "TareProd" => ($Tare),
    "RegCH" => ($RegCH),
    "Tipo" => ($LegTipo),
    "start" => 0,
    "length" => 500,
);
// Flight::json($dataParamPerson) . exit;
$url = gethostCHWeb() . "/" . HOMEHOST . "/api/personal/";

switch ($estruct) {
    case 'Empr':
        $FicEstruct = 'PERSONAL.LegEmpr';
        $ColEstruc = 'EMPRESAS';
        $ColEstrucDesc = 'EMPRESAS.EmpRazon';
        $ColEstrucCod = 'EMPRESAS.EmpCodi';
        break;
    case 'Plan':
        $FicEstruct = 'PERSONAL.LegPlan';
        $ColEstruc = 'PLANTAS';
        $ColEstrucDesc = 'PLANTAS.PlaDesc';
        $ColEstrucCod = 'PLANTAS.PlaCodi';
        break;
    case 'Grup':
        $FicEstruct = 'PERSONAL.LegGrup';
        $ColEstruc = 'GRUPOS';
        $ColEstrucDesc = 'GRUPOS.GruDesc';
        $ColEstrucCod = 'GRUPOS.GruCodi';
        break;
    case 'Sect':
        $FicEstruct = 'PERSONAL.LegSect';
        $ColEstruc = 'SECTORES';
        $ColEstrucDesc = 'SECTORES.SecDesc';
        $ColEstrucCod = 'SECTORES.SecCodi';
        break;
    case 'Sucu':
        $FicEstruct = 'PERSONAL.LegSucu';
        $ColEstruc = 'SUCURSALES';
        $ColEstrucDesc = 'SUCURSALES.SucDesc';
        $ColEstrucCod = 'SUCURSALES.SucCodi';
        break;
    case 'Tare':
        $FicEstruct = 'PERSONAL.LegTareProd';
        $ColEstruc = 'TAREAS';
        $ColEstrucDesc = 'TAREAS.TareDesc';
        $ColEstrucCod = 'TAREAS.TareCodi';
        break;
    case 'Conv':
        $FicEstruct = 'PERSONAL.LegConv';
        $ColEstruc = 'CONVENIO';
        $ColEstrucDesc = 'CONVENIO.ConDesc';
        $ColEstrucCod = 'CONVENIO.ConCodi';
        break;
    case 'Regla':
        $FicEstruct = 'PERSONAL.LegRegCH';
        $ColEstruc = 'REGLASCH';
        $ColEstrucDesc = 'REGLASCH.RCDesc';
        $ColEstrucCod = 'REGLASCH.RCCodi';
        break;
    case 'Lega':
        $FicEstruct = 'PERSONAL.LegNume';
        $ColEstruc = 'PERSONAL';
        $ColEstrucDesc = 'PERSONAL.LegApNo';
        $ColEstrucCod = 'PERSONAL.LegNume';
        break;
    case 'Sec2':
        $FicEstruct = 'PERSONAL.LegNume';
        $ColEstruc = 'PERSONAL';
        $ColEstrucDesc = 'PERSONAL.LegApNo';
        $ColEstrucCod = 'PERSONAL.LegNume';
        break;
    case 'Tipo':
        $ColEstruc = 'PERSONAL';
        $ColEstrucCod = 'PERSONAL.LegTipo';
        break;

    default:
        # code...
        break;
}



$FiltroQ = (!empty($q)) ? "AND CONCAT($ColEstrucCod, $ColEstrucDesc) collate SQL_Latin1_General_CP1_CI_AS LIKE '%$q%'" : '';

switch ($estruct) {
    case 'Tipo':
        $query = "SELECT $ColEstrucCod AS 'id' FROM $ColEstruc WHERE PERSONAL.LegNume > 0 $FilterEstruct $filtros GROUP BY $ColEstrucCod ORDER BY $ColEstrucCod";
        break;
    case 'Lega':
        $query = "SELECT $FicEstruct AS 'id', $ColEstrucDesc AS 'Desc' FROM PERSONAL WHERE $FicEstruct > 0 $FiltroQ $FilterEstruct $filtros GROUP BY $FicEstruct, $ColEstrucDesc ORDER BY $FicEstruct";

        // $dataApiPerson = json_decode(requestApi($url, $token, $authBasic, $dataParamPerson, 10), true);

        // foreach ($dataApiPerson['DATA'] as $key => $v) {
        //     $id   = $v['Lega'];
        //     $text = $v['ApNo'];
        //     $data[] = array(
        //         'id'    => $id,
        //         'text'  => $id . ' - ' . $text,
        //         'title' => $id . ' - ' . $text,
        //     );
        // }
        // Flight::json($data) . exit;
        break;
    case 'Sec2':
        $FiltroQ = (!empty($q)) ? "AND CONCAT(SECCION.SecCodi, SECCION.Se2Desc) collate SQL_Latin1_General_CP1_CI_AS LIKE '%$q%'" : '';
        $query = "SELECT PERSONAL.LegSec2 AS 'id', CONCAT(PERSONAL.LegSect, PERSONAL.LegSec2) AS 'id2', SECCION.Se2Desc AS 'Desc' 
        FROM PERSONAL 
        INNER JOIN SECCION ON PERSONAL.LegSec2=SECCION.Se2Codi AND PERSONAL.LegSect=SECCION.SecCodi 
        WHERE PERSONAL.LegSec2 > 0 $FiltroQ $FilterEstruct $filtros 
        GROUP BY PERSONAL.LegSec2, CONCAT(PERSONAL.LegSect, PERSONAL.LegSec2), SECCION.Se2Desc 
        ORDER BY PERSONAL.LegSec2";
        break;
    default:
        $query = "SELECT $FicEstruct AS 'id', $ColEstrucDesc AS 'Desc' FROM PERSONAL INNER JOIN $ColEstruc ON $FicEstruct=$ColEstrucCod 
        WHERE PERSONAL.LegNume > 0 $FiltroQ $FilterEstruct $filtros GROUP BY $FicEstruct, $ColEstrucDesc ORDER BY $FicEstruct";
        break;
}
// Flight::json($query) . exit;
// print_r($query); exit;

$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);

$result = sqlsrv_query($link, $query, $params, $options);
$data = array();

if (sqlsrv_num_rows($result) > 0) {
    while ($row = sqlsrv_fetch_array($result)):

        switch ($estruct) {
            case 'Tipo':
                $id = $row['id'];
                $id2 = ($row['id'] == 0) ? 2 : 1;
                $text = ($id == '0') ? 'Mensuales' : 'Jornales';
                $data[] = array(
                    'id' => $id2,
                    'text' => $text,
                    'title' => $text,
                );
                break;
            case 'Sec2':
                $id = $row['id'];
                $id2 = $row['id2'];
                $text = ($row['Desc'] != '') ? $row['Desc'] : 'Sin Asignar';

                $data[] = array(
                    'id' => $id2,
                    'text' => $id . ' - ' . $text,
                    'title' => $id . ' - ' . $text,
                );
                break;
            default:
                $id = $row['id'];
                $text = ($row['Desc'] != '') ? $row['Desc'] : 'Sin Asignar';

                $data[] = array(
                    'id' => $id,
                    'text' => $id . ' - ' . $text,
                    'title' => $id . ' - ' . $text,
                    'data-title' => $id . ' - ' . $text,
                );
                break;
        }

    endwhile;
}
sqlsrv_free_stmt($result);
sqlsrv_close($link);
echo json_encode($data);

<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '../../../../config/index.php';
header("Content-Type: application/json");
E_ALL();

$params = $_REQUEST;
$params['q'] = $params['q'] ?? '';
$q = $params['q'];
$data = array();
$data2[] = array('id' => '', 'text' => '');
$w_c = '';
$params['TareEstado'] = $params['TareEstado'] ?? '';
$params['TareEstado'] = test_input($params['TareEstado']) ?? 'todos';

switch ($params['TareEstado']) {
    case 'todos':
        $w_c .= "";
        break;
    case 'pendientes':
        $w_c .= " AND TareFin = '0000-00-00 00:00:00'";
        break;
    case 'completadas':
        $w_c .= " AND TareFin != '0000-00-00 00:00:00'";
        break;
    default:
        $w_c .= "";
        break;
}

$w_c .= " AND TareEsta = '0'";
$w_c .= " AND proy_tareas.Cliente = '$_SESSION[ID_CLIENTE]'";
// $w_c .= ($params['TareEstado'] != 'todos') ? " AND EstTipo = '$params[TareEstado]'" : '';
$w_c .= (test_input($params['tarProyNomFiltro'] ?? '')) ? " AND TareProy = '$params[tarProyNomFiltro]'" : '';
$w_c .= (test_input($params['tarEmprFiltro'] ?? '')) ? " AND TareEmp = '$params[tarEmprFiltro]'" : '';
$w_c .= (test_input($params['tarProcNomFiltro'] ?? '')) ? " AND TareProc = '$params[tarProcNomFiltro]'" : '';
$w_c .= (test_input($params['tarPlanoFiltro'] ?? '')) ? " AND TarePlano = '$params[tarPlanoFiltro]'" : '';
$w_c .= (test_input($params['tarRespFiltro'] ?? '')) ? " AND TareResp = '$params[tarRespFiltro]'" : '';
$w_c .= (test_input($params['tableTareas_filter'] ?? '')) ? " AND TareID = '$params[tableTareas_filter]'" : '';
$w_c .= (test_input($params['tarProyEsta'])) ? " AND proy_estados.EstTipo = '$params[tarProyEsta]'" : '';

if (($params['FiltroTarFechas'] ?? '')) {
    $DateRange = explode(' al ', $params['FiltroTarFechas']);
    $TareIni   = test_input(dr_($DateRange[0]) . ' 00:00:00');
    $TareIni2  = test_input(dr_($DateRange[1]) . ' 23:59:59');
    $w_c .= " AND TareIni BETWEEN '$TareIni' AND '$TareIni2'";
}

switch ($params['NomFiltro']) {
    case 'tarProyNomFiltro':
        $FiltroQ = (!empty($q)) ? " AND CONCAT_WS(' - ', TareProy, EmpDesc, ProyNom) LIKE '%$q%'" : '';
        $q = "SELECT TareProy, ProyNom, EmpDesc FROM proy_tareas INNER JOIN proy_proyectos ON proy_tareas.TareProy=proy_proyectos.ProyID INNER JOIN `proy_estados` ON `proy_proyectos`.`ProyEsta`=`proy_estados`.`EstID`
        INNER JOIN proy_empresas ON proy_tareas.TareEmp=proy_empresas.EmpID WHERE TareProy >0";

        $q .= $FiltroQ;
        $q .= $w_c;
        $q .= ' GROUP BY TareProy, ProyNom';
        $q .= ' ORDER BY TareProy DESC';
        $r = array_pdoQuery($q);

        empty($r) ? print_r(json_encode($data2)) . exit : '';

        foreach ($r as $key => $row) {

            $text = '(' . $row['TareProy'] . ') ' . $row['ProyNom'];

            $data[] = array(
                'id'      => $row['TareProy'],
                'empresa' => utf8str($row['EmpDesc']),
                'text'    => utf8str($text),
            );
        }

        function groupAssoc($input, $sortkey)
        {
            foreach ($input as $val) $output[$val[$sortkey]][] = $val;
            return $output;
        }

        $myArray = groupAssoc($data, 'empresa');

        foreach ($myArray as $key => $value) {
            $data_group[] = array(
                'text' => strtoupper(utf8str($key)),
                'children' => $value
            );
        }
        echo json_encode($data_group);
        break;
    case 'tarEmprFiltro':
        $FiltroQ = (!empty($q)) ? " AND CONCAT_WS(' - ', TareEmp, EmpDesc) LIKE '%$q%'" : '';
        $q = "SELECT TareEmp, EmpDesc 
        FROM proy_tareas 
        INNER JOIN proy_empresas ON proy_tareas.TareEmp=proy_empresas.EmpID
        INNER JOIN `proy_proyectos` ON `proy_tareas`.`TareProy`=`proy_proyectos`.`ProyID`
        INNER JOIN `proy_estados` ON `proy_proyectos`.`ProyEsta`=`proy_estados`.`EstID`
        WHERE TareEmp >0";

        $q .= $FiltroQ;
        $q .= $w_c;
        $q .= ' GROUP BY TareEmp, EmpDesc';
        $q .= ' ORDER BY EmpDesc DESC';
        $r = array_pdoQuery($q);

        // print_r(($q)).exit;

        empty($r) ? print_r(json_encode($data2)) . exit : '';

        foreach ($r as $key => $row) {

            $data[] = array(
                'id'   => $row['TareEmp'],
                'text' => utf8str($row['EmpDesc']),
            );
        }
        echo json_encode($data);
        break;
    case 'tarProcNomFiltro':
        $FiltroQ = (!empty($q)) ? " AND CONCAT_WS(' - ', TareProc, ProcDesc) LIKE '%$q%'" : '';
        $q = "SELECT TareProc, ProcDesc 
        FROM proy_tareas 
        INNER JOIN proy_proceso ON proy_tareas.TareProc=proy_proceso.ProcID 
        INNER JOIN `proy_proyectos` ON `proy_tareas`.`TareProy`=`proy_proyectos`.`ProyID`
        INNER JOIN `proy_estados` ON `proy_proyectos`.`ProyEsta`=`proy_estados`.`EstID`
        WHERE TareProc > 0";

        $q .= $FiltroQ;
        $q .= $w_c;
        $q .= ' GROUP BY TareProc, ProcDesc';
        $q .= ' ORDER BY ProcDesc DESC';
        $r = array_pdoQuery($q);

        // print_r(($q)).exit;

        empty($r) ? print_r(json_encode($data2)) . exit : '';

        foreach ($r as $key => $row) {

            $data[] = array(
                'id'   => $row['TareProc'],
                'text' => utf8str($row['ProcDesc']),
            );
        }
        echo json_encode($data);
        break;
    case 'tarPlanoFiltro':
        $FiltroQ = (!empty($q)) ? " AND CONCAT_WS(' - ', TarePlano, PlanoDesc) LIKE '%$q%'" : '';
        $q = "SELECT TarePlano, PlanoDesc 
        FROM proy_tareas 
        INNER JOIN proy_planos ON proy_tareas.TarePlano=proy_planos.PlanoID 
        INNER JOIN `proy_proyectos` ON `proy_tareas`.`TareProy`=`proy_proyectos`.`ProyID`
        INNER JOIN `proy_estados` ON `proy_proyectos`.`ProyEsta`=`proy_estados`.`EstID`
        WHERE TarePlano > 0";

        $q .= $FiltroQ;
        $q .= $w_c;
        $q .= ' GROUP BY TarePlano, PlanoDesc';
        $q .= ' ORDER BY PlanoDesc DESC';
        $r = array_pdoQuery($q);

        // print_r(($q)).exit;

        empty($r) ? print_r(json_encode($data2)) . exit : '';

        foreach ($r as $key => $row) {

            $data[] = array(
                'id'   => $row['TarePlano'],
                'text' => utf8str($row['PlanoDesc']),
            );
        }
        echo json_encode($data);
        break;
    case 'tarRespFiltro':
        $FiltroQ = (!empty($q)) ? " AND CONCAT_WS(' - ', TareResp, usuarios.nombre) LIKE '%$q%'" : '';
        $q = "SELECT TareResp, usuarios.nombre FROM proy_tareas
        INNER JOIN usuarios on proy_tareas.TareResp = usuarios.id
        INNER JOIN `proy_proyectos` ON `proy_tareas`.`TareProy`=`proy_proyectos`.`ProyID`
        INNER JOIN `proy_estados` ON `proy_proyectos`.`ProyEsta`=`proy_estados`.`EstID`
        WHERE TareResp > 0";

        $q .= $FiltroQ;
        $q .= $w_c;
        $q .= ' GROUP BY TareResp, usuarios.nombre';
        $q .= ' ORDER BY usuarios.nombre DESC';
        $r = array_pdoQuery($q);

        // print_r(($q)).exit;

        empty($r) ? print_r(json_encode($data2)) . exit : '';

        foreach ($r as $key => $row) {
            $data[] = array(
                'id'   => $row['TareResp'],
                'text' => utf8str($row['nombre']),
            );
        }
        echo json_encode($data);
        break;
    default:
        echo json_encode($data2);
        break;
}

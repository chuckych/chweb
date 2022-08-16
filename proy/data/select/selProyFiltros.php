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
$where_condition = '';
$params['FiltroEstTipo'] = $params['FiltroEstTipo'] ?? '';
$params['FiltroEstTipo'] = test_input($params['FiltroEstTipo']) ?? 'Abierto';
$where_condition .= " AND proy_proyectos.Cliente = '$_SESSION[ID_CLIENTE]'";
$where_condition .= ($params['FiltroEstTipo'] != 'Todos') ? " AND EstTipo = '$params[FiltroEstTipo]'" : '';
$where_condition .= (test_input($params['ProyEmprFiltro'] ?? '')) ? " AND ProyEmpr = '$params[ProyEmprFiltro]'" : '';
$where_condition .= (test_input($params['ProyRespFiltro'] ?? '')) ? " AND ProyResp = '$params[ProyRespFiltro]'" : '';
$where_condition .= (test_input($params['ProyPlantFiltro'] ?? '')) ? " AND ProyPlant = '$params[ProyPlantFiltro]'" : '';
$where_condition .= (test_input($params['ProyEstaFiltro'] ?? '')) ? " AND ProyEsta = '$params[ProyEstaFiltro]'" : '';
$where_condition .= (test_input($params['ProyNomFiltro'] ?? '')) ? " AND ProyID = '$params[ProyNomFiltro]'" : '';

if (($params['ProyFiltroFechas'] ?? '')) {
    $DateRange = explode(' al ', $params['ProyFiltroFechas']);
    $ProyIni  = test_input(dr_fecha($DateRange[0]));
    $ProyFin  = test_input(dr_fecha($DateRange[1]));
    if ($ProyIni == $ProyFin) {
        $where_condition .= " AND ProyIni = '" . $ProyIni. "'";
    }else{
        $where_condition .= " AND ProyIni >= '" . $ProyIni . "' AND ProyFin <= '" . $ProyFin . "'";
    }
}

switch ($params['NomFiltro']) {
    case 'ProyNomFiltro':
        $FiltroQ = (!empty($q)) ? " AND CONCAT_WS(' - ', ProyID, EmpDesc, ProyNom) LIKE '%$q%'" : '';
        $query = "SELECT ProyID, ProyNom, EmpDesc FROM proy_proyectos
        INNER JOIN proy_empresas on proy_proyectos.ProyEmpr = proy_empresas.EmpID
        INNER JOIN proy_estados on proy_proyectos.ProyEsta = proy_estados.EstID
        WHERE ProyID > 0";

        $query .= $FiltroQ;
        $query .= $where_condition;
        $query .= ' GROUP BY ProyID, ProyNom';
        $query .= ' ORDER BY ProyID DESC';
        $r = array_pdoQuery($query);

        // print_r($query);
        // exit;

        foreach ($r as $key => $row) {

            $text = '(' . $row['ProyID'] . ') ' . $row['ProyNom'];

            $data[] = array(
                'id'      => $row['ProyID'],
                'empresa' => utf8str($row['EmpDesc']),
                'text'    => utf8str($text),
            );
        }
        function groupAssoc($input, $sortkey)
        {
            foreach ($input as $key => $val) $output[$val[$sortkey]][] = $val;
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
    case 'ProyEmpFiltro':
        $FiltroQ = (!empty($q)) ? " AND CONCAT_WS(' - ', ProyEmpr, EmpDesc) LIKE '%$q%'" : '';
        $query = "SELECT ProyEmpr, EmpDesc FROM proy_proyectos
        INNER JOIN proy_empresas on proy_proyectos.ProyEmpr = proy_empresas.EmpID
        INNER JOIN proy_estados on proy_proyectos.ProyEsta = proy_estados.EstID
        WHERE ProyID > 0";

        $query .= $FiltroQ;
        $query .= $where_condition;
        $query .= ' GROUP BY ProyEmpr, EmpDesc';
        $query .= ' ORDER BY ProyID DESC';
        $r = array_pdoQuery($query);

        foreach ($r as $key => $row) {

            $data[] = array(
                'id'   => $row['ProyEmpr'],
                'text' => utf8str($row['EmpDesc']),
            );
        }
        echo json_encode($data);
        break;
    case 'ProyRespFiltro':
        $FiltroQ = (!empty($q)) ? " AND CONCAT_WS(' - ', ProyResp, usuarios.nombre) LIKE '%$q%'" : '';
        $query = "SELECT ProyResp, usuarios.nombre FROM proy_proyectos
        INNER JOIN usuarios on proy_proyectos.ProyResp = usuarios.id
        INNER JOIN proy_estados on proy_proyectos.ProyEsta = proy_estados.EstID
        WHERE ProyID > 0";

        $query .= $FiltroQ;
        $query .= $where_condition;
        $query .= ' GROUP BY ProyResp, nombre';
        $query .= ' ORDER BY ProyID DESC';
        $r = array_pdoQuery($query);

        foreach ($r as $key => $row) {

            $data[] = array(
                'id'   => $row['ProyResp'],
                'text' => utf8str($row['nombre']),
            );
        }
        // echo $query;
        echo json_encode($data);
        break;
    case 'ProyPlantFiltro':
        $FiltroQ = (!empty($q)) ? " AND CONCAT_WS(' - ', ProyPlant, PlantDesc) LIKE '%$q%'" : '';
        $query = "SELECT ProyPlant, PlantDesc FROM proy_proyectos
        INNER JOIN proy_plantillas on proy_proyectos.ProyPlant = proy_plantillas.PlantID
        INNER JOIN proy_estados on proy_proyectos.ProyEsta = proy_estados.EstID
        WHERE ProyID > 0";

        $query .= $FiltroQ;
        $query .= $where_condition;
        $query .= ' GROUP BY ProyPlant, PlantDesc';
        $query .= ' ORDER BY ProyID DESC';
        $r = array_pdoQuery($query);

        foreach ($r as $key => $row) {

            $data[] = array(
                'id'   => $row['ProyPlant'],
                'text' => utf8str($row['PlantDesc']),
            );
        }
        // echo $query;
        echo json_encode($data);
        break;
    case 'ProyEstaFiltro':
        $FiltroQ = (!empty($q)) ? " AND CONCAT_WS(' - ', ProyEsta, EstDesc) LIKE '%$q%'" : '';
        $query = "SELECT ProyEsta, EstDesc, EstTipo, EstColor FROM proy_proyectos
        INNER JOIN proy_estados on proy_proyectos.ProyEsta = proy_estados.EstID
        WHERE ProyID > 0";
        function html($text, $color, $icon, $textIcon)
        {
            $a = "<div class='w-100 bg-transparent p-2 d-flex align-items-center' style='border:0px; border-bottom:2px solid $color; border-radius:0px; padding-bottom:5px'><div>$text</div><div class='text-mutted font08 ms-2'>$icon</div></div>";
            return $a;
        }
        $query .= $FiltroQ;
        $query .= $where_condition;
        $query .= ' GROUP BY ProyEsta, EstDesc';
        $query .= ' ORDER BY ProyID DESC';
        $r = array_pdoQuery($query);

        foreach ($r as $key => $row) {

            switch ($row['EstTipo']) {
                case 'Abierto':
                    $icon = "<i class='bi bi-play-fill font1' style='color: $row[EstColor];'></i>";
                    $textIcon = 'Abierto';
                    break;
                case 'Cerrado':
                    $icon = "<i class='bi bi-stop-fill font1' style='color: $row[EstColor];'></i>";
                    $textIcon = 'Cerrado';
                    break;
                case 'Pausado':
                    $icon = "<i class='bi bi-pause-fill font1' style='color: $row[EstColor];'></i>";
                    $textIcon = 'Pausado';
                    break;

                default:
                    $icon = "<i class='bi bi-play-fill font1' style='color: $row[EstColor];'></i>";
                    $textIcon = 'Abierto';
                    break;
            }

            $data[] = array(
                'id'   => $row['ProyEsta'],
                'text' => utf8str($row['EstDesc']),
                'html'  => html($row['EstDesc'], $row['EstColor'], $icon, $textIcon),
            );
        }
        echo json_encode($data);
        break;
    default:
        $data[] = array(
            'id'   => '',
            'text' => 'Error..',
        );
        echo json_encode($data);
        break;
}

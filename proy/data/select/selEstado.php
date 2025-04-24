<?php
session_start();
header('Content-type: text/html; charset=utf-8');
require __DIR__ . '/../../../config/index.php';
header("Content-Type: application/json");
E_ALL();

$params = $_REQUEST;
$params['q'] = $params['q'] ?? '';
$q = $params['q'];
$data = array();
$where_condition = '';

$where_condition .= (!empty($params['_c'])) ? " AND clientes.recid = '$params[_c]'" : "";
$where_condition .= " AND proy_estados.Cliente = '$_SESSION[ID_CLIENTE]'";

$FiltroQ = (!empty($q)) ? " AND proy_estados.EstDesc LIKE '%$q%'" : '';
$query = "SELECT EstID, EstDesc, EstColor, EstTipo FROM proy_estados WHERE proy_estados.EstID > 0 AND proy_estados.Cliente = '$_SESSION[ID_CLIENTE]'";
$query .= $FiltroQ;
$query .= $where_condition;
$query .= ' ORDER BY proy_estados.EstDesc';
$r = array_pdoQuery($query);

function html($text, $color, $icon, $textIcon)
{
    $a = "
    <div class='flex-center-between'>
        <div>$text</div> 
        <div class='text-mutted font08 ms-2'>$icon</div>
    </div>
        ";
    return $a;
    // <div style='border:0px;border-bottom:3px solid $color; border-radius:0px; padding-bottom:5px'></div>
}

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
        'id' => $row['EstID'],
        'text' => utf8str($row['EstDesc']),
        'html' => html($row['EstDesc'], $row['EstColor'], $icon, $textIcon),
    );
}

echo json_encode($data);
exit;

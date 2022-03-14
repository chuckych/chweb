<?php
require __DIR__ . '../../../config/index.php';
ini_set('max_execution_time', 1800); //1800 seconds = 30 minutes
session_start();
header("Content-Type: application/json");
header('Access-Control-Allow-Origin: *');
ultimoacc();
secure_auth_ch_json();
E_ALL();

borrarLogs(__DIR__ . '', 30, '.log');

function queryCalcZone($lat, $lng, $idCompany)
{
    $query = "
            SELECT
            `rg`.*,
        (
                (
                    (
                        acos(
                            sin(($lat * pi() / 180)) * sin((`rg`.`lat` * pi() / 180)) + cos(($lat * pi() / 180)) * cos((`rg`.`lat` * pi() / 180)) * cos((($lng - `rg`.`lng`) * pi() / 180))
                        )
                    ) * 180 / pi()
                ) * 60 * 1.1515 * 1.609344
            ) as distancia
        FROM
            reg_zones rg WHERE `rg`.`id_company` = $idCompany
        -- HAVING (distancia <= 0.1)
        ORDER BY distancia ASC, rg.id DESC LIMIT 1
    ";
    return $query;
}
$start = microtime(true);
$_GET['company'] = $_GET['company'] ?? '';

if (empty($_GET['company'])) {
    $data = array(
        'Mensaje' => 'Falta de parametros',
        'date'    => date('Y-m-d H:i:s'),
        'status'  => 'no',
        'time'    => '',
        'total'   => '',
    );
    echo json_encode(array('Response' => $data));
    exit;
}

$idCompany = intval($_GET['company']);

$queryReg = "SELECT `reg_`.`rid`, `reg_`.`lat`, `reg_`.`lng`, `reg_`.`id_company` FROM `reg_` WHERE `reg_`.`id_company`=" . $idCompany . " AND `reg_`.`idZone`=0";

$dataReg = array_pdoQuery($queryReg);
// print_r($data);
// exit;
if (empty($dataReg)) {
    $data = array(
        'Mensaje' => 'No hay Registros Fuera de Zona',
        'date'    => date('Y-m-d H:i:s'),
        'status'  => 'no',
        'time'    => '',
        'total'   => '',
    );
    echo json_encode(array('Response' => $data));
    exit;
}

$counter = 0;
foreach ($dataReg as $key => $value) {
    /** Calculamos la Zona */
    $query = queryCalcZone($value['lat'], $value['lng'], $value['id_company']);
    $zona = simple_pdoQuery($query);

    if ($zona) {
        $radio = (intval($zona['radio']) / 1000);
        $distancia = ($zona['distancia']) ? ($zona['distancia']) : 0;
        $idZone = ($distancia <= $radio) ? $zona['id'] : 0;
        if ($idZone>0) {
            $update = "UPDATE `reg_` SET `idZone` = $idZone, `distance` = $distancia WHERE `rid` = " . $value['rid'];
            pdoQuery($update);
            $counter = $counter + 1;
            // echo $update .';'. PHP_EOL;
            // $data = array(
            //     'update'   => $update,
            //     'total'    => $counter,
            //     'distance' => $distancia,
            // );
            // echo json_encode(array($data), JSON_PRETTY_PRINT);
        }else{
            // $counter = $counter + -1;
        }
    }
    /** Fin calculo Zona */
}

$end = microtime(true);
$time = round($end - $start, 2);
// header("Content-Type: application/json");
if ($counter > 0) {
    $data = array(
        'Mensaje' => 'Se actualizaron ' . $counter . ' registros',
        'date'    => date('Y-m-d H:i:s'),
        'status'  => 'ok',
        'time'    => ($time),
        'total'   => $counter,
    );
} else {
    $data = array(
        'Mensaje' => 'No se encontraron registros para actualizar',
        'date'    => date('Y-m-d H:i:s'),
        'status'  => 'no',
        'time'    => ($time),
        'total'   => $counter,
    );
}
echo json_encode(array('Response' => $data));
exit;

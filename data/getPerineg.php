<?php
header("Content-type: application/json; charset=utf-8");
header('Access-Control-Allow-Origin: *');
require __DIR__ . '/../config/session_start.php';
require __DIR__ . '/../config/index.php';
E_ALL();

if (session_status() === PHP_SESSION_ACTIVE) {
    session_write_close();
}

$data = [];
$token = sha1($_SESSION['RECID_CLIENTE']);
$pathApiCH = gethostCHWeb() . "/" . HOMEHOST . "/api";

try {

    $legajo = $_GET['q2'] ?? '';

    if($legajo == '' || $legajo === null || !is_numeric($legajo)) {
        throw new Exception('Legajo es obligatorio', 1);
    }

    $sendApi = curlAPI("$pathApiCH/perineg/?Lega[]=$legajo", [], 'GET', $token);
    $sendApi = json_decode($sendApi, true) ?? [
        'DATA' => [],
        'MESSAGE' => 'Error de conexión con API',
    ];

    $icon_trash = '<span class="bi bi-trash font08"></span>';
    $icon_edit = '<span class="bi bi-pen font08"></span>';

    if (empty($sendApi['DATA'])) {
        throw new Exception($sendApi['MESSAGE'], 1);
    }

    foreach ($sendApi['DATA'] as $key => $fila) {
        $InEgLega = $fila['Lega'];
        $Diff = $fila['Diff'];
        $InEgFeIn = date('d/m/Y', strtotime($fila['FeIn']));
        $InEgFeIn2 = date('Ymd', strtotime($fila['FeIn']));
        $InEgFeEg = date('d/m/Y', strtotime($fila['FeEg']));
        $InEgFeEg = ($fila['FeEg'] == '') ? '' : $InEgFeEg;
        $InEgCaus = $fila['Caus'];
        $FechaHora = date('d/m/Y', strtotime($fila['FechaHora']));
        $eliminar = '<div data-titlet="Eliminar registro"  id="item-' . $InEgFeIn2 . '"><div class="item">
            <a class="btn btn-light btn-sm delete_perineg" data="' . $InEgFeIn2 . '" data2="' . $InEgLega . '" data3="true">' . $icon_trash . '</a></div></div>';
        $editar = '<div data-titlet="Editar registro" id="item-' . $InEgFeIn2 . '"><div class="item">
            <a class="btn btn-light btn-sm edita_perineg" data="' . $InEgFeIn . '" data2="' . $InEgLega . '" data3="true" data4="' . $InEgFeEg . '" data5="' . $InEgCaus . '">' . $icon_edit . '</a></div></div>';

        $data[] = [
            "InEgLega" => $InEgLega,
            "InEgFeIn" => $InEgFeIn,
            "InEgFeEg" => $InEgFeEg,
            "InEgCaus" => $InEgCaus,
            "FechaHora" => $FechaHora,
            "eliminar" => $eliminar,
            "editar" => $editar,
            "Diff" => $Diff
        ];
    }
    Flight::json(["data" => $data]);

} catch (Exception $e) {
    $data['error'] = $e->getMessage();
    Flight::json(["data" => $data]);
}

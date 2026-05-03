<?php
require __DIR__ . '/../config/index.php';
session_start();
E_ALL();
$_SESSION["RECID_ROL"] ??= '';
$_SESSION["MODS_ROL"] ??= '';
$_SESSION["MODS_ROL_PROY"] ??= '';
$_GET['page'] ??= '';
($_SESSION["RECID_ROL"]) ? '' : require __DIR__ . '/salir.php';

function progressBar($step)
{
    $active_1 = $active_2 = $active_3 = $active_4 = $active_5 = '';

    switch ($step) {
        case 1:
            $active_1 = 'active';
            break;
        case 2:
            $active_2 = 'active';
            break;
        case 3:
            $active_3 = 'active';
            break;
        case 4:
            $active_4 = 'active';
            break;
        case 5:
            $active_5 = 'active';
            break;
    }
    $a = "
    <div class='steps steps-counter steps-green fixed-bottom animate__animated animate__fadeIn'>
        <span href='#' class='step-item font08 $active_1'>Proyecto</span>
        <span href='#' class='step-item font08 $active_2'>Proceso</span>
        <span href='#' class='step-item font08 $active_3'>Plano</span>
        <span href='#' class='step-item font08 $active_4'>Finalizar</span>
    </div>
    ";
    return $a;
}
function checkModulo($modulo)
{
    $a = array_filter($_SESSION["MODS_ROL"], function ($item) use ($modulo) {
        return $item['modsrol'] == $modulo;
    });
    if ($a) {
        return true;
    } else {
        return false;
    }
}
// echo '<pre>';
// print_r($_SESSION["MODS_ROL_PROY"]);
// echo '</pre>';
// exit;
($_SESSION["MODS_ROL_PROY"] == 'error') ? require __DIR__ . '/errMod.php' : '';
($_SESSION["MODS_ROL_PROY"] == 'error') ? exit : '';

$rutas = [
    'log_rfid' => [
        "url" => 'login/login_rfid.php',
        "mod" => 0,
    ],
    'log_user' => [
        "url" => 'login/login_user.php',
        "mod" => 0,
    ],
    'inicio' => [
        "url" => 'inicio/index.php',
        "mod" => 43,
    ],
    'salir' => [
        "url" => 'salir.php',
        "mod" => 0,
    ],
    'empresas' => [
        "url" => 'op/empresas.php',
        "mod" => 42,
    ],
    'estados' => [
        "url" => 'op/estados.php',
        "mod" => 38,
    ],
    'procesos' => [
        "url" => 'op/procesos.php',
        "mod" => 39,
    ],
    'planos' => [
        "url" => 'op/planos.php',
        "mod" => 41,
    ],
    'plantillas procesos' => [
        "url" => 'op/plantillas.php',
        "mod" => 40,
    ],
    'plantillas planos' => [
        "url" => 'op/plantillasPlanos.php',
        "mod" => 44,
    ],
    'proyectos' => [
        "url" => 'op/proyectos.php',
        "mod" => 35,
    ],
    'tareas' => [
        "url" => 'op/tareas.php',
        "mod" => 37,
    ],
    'mistareas' => [
        "url" => 'op/misTareas.php',
        "mod" => 36,
    ],
    'selProc' => [
        "url" => 'selProc/index.php',
        "mod" => 0,
    ],
    'selPlano' => [
        "url" => 'selPlano/index.php',
        "mod" => 0,
    ],
    'finalizar' => [
        "url" => 'finalizar/index.php',
        "mod" => 0,
    ],
    'finTar' => [
        "url" => 'finalizar/fin.php',
        "mod" => 0,
    ],
];

$request = $_GET['page'];
foreach ($rutas as $key => $pagina) {
    if ($key == $request) {
        ($pagina['mod'] == 0) ? require __DIR__ . '/' . $pagina['url'] : ''; //Si el modulo es 0, no se verifica el permiso
        // ($pagina['mod'] == 0) ? '' : checkModulo($pagina['mod']) ? require __DIR__ . '/' . $pagina['url'] : '';
        ($pagina['mod'] == 0) ? '' : (checkModulo($pagina['mod']) ? require __DIR__ . '/' . $pagina['url'] : '');

        access_log_proy($key);
        exit;
    }
}
foreach ($rutas as $key => $pagina) {
    if ($key != $request) {
        require __DIR__ . '/404.php';
        exit;
    }
}

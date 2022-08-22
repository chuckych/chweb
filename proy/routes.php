<?php
require __DIR__ . '../../config/index.php';
session_start();
E_ALL();
$_SESSION["RECID_ROL"] = $_SESSION["RECID_ROL"] ?? '';
$_SESSION["MODS_ROL"] = $_SESSION["MODS_ROL"] ?? '';
$_GET['page'] = $_GET['page'] ?? '';
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

$rutas = array(
    'log_rfid'   => array(
        "url" => 'login/login_rfid.php',
        "mod" => 0,
    ),
    'log_user'   => array(
        "url" => 'login/login_user.php',
        "mod" => 0,
    ),
    'inicio'     => array(
        "url" => 'inicio/index.php',
        "mod" => 43,
    ),
    'salir'      => array(
        "url" => 'salir.php',
        "mod" => 0,
    ),
    'empresas'   => array(
        "url" => 'op/empresas.php',
        "mod" => 42,
    ),
    'estados'    => array(
        "url" => 'op/estados.php',
        "mod" => 38,
    ),
    'procesos'   => array(
        "url" => 'op/procesos.php',
        "mod" => 39,
    ),
    'planos'     => array(
        "url" => 'op/planos.php',
        "mod" => 41,
    ),
    'plantillas' => array(
        "url" => 'op/plantillas.php',
        "mod" => 40,
    ),
    'proyectos'  => array(
        "url" => 'op/proyectos.php',
        "mod" => 35,
    ),
    'tareas'     => array(
        "url" => 'op/tareas.php',
        "mod" => 37,
    ),
    'mistareas'  => array(
        "url" => 'op/misTareas.php',
        "mod" => 36,
    ),
    'selProc'    => array(
        "url" => 'selProc/index.php',
        "mod" => 0,
    ),
    'selPlano'   => array(
        "url" => 'selPlano/index.php',
        "mod" => 0,
    ),
    'finalizar'  => array(
        "url" => 'finalizar/index.php',
        "mod" => 0,
    ),
    'finTar'     => array(
        "url" => 'finalizar/fin.php',
        "mod" => 0,
    ),
);

$request = $_GET['page'];
foreach ($rutas as $key => $pagina) {
    if ($key == $request) {
        ($pagina['mod'] == 0) ? require __DIR__ . '/' . $pagina['url'] : ''; //Si el modulo es 0, no se verifica el permiso
        ($pagina['mod'] == 0) ? '' : checkModulo($pagina['mod']) ? require __DIR__ . '/' . $pagina['url'] : '';
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

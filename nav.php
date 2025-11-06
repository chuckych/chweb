<input type="hidden" hidden id="_c" value="<?= $_SESSION["RECID_CLIENTE"] ?? '' ?>">
<input type="hidden" hidden id="_r" value="<?= $_SESSION["RECID_ROL"] ?? '' ?>">
<input type="hidden" hidden id="_lega" value="<?= $_SESSION["LEGAJO_SESION"] ?? '' ?>">
<input type="hidden" hidden id="_homehost" value="<?= HOMEHOST ?? '' ?>">
<input type="hidden" hidden id="_host" value="<?= host() ?>">
<input type="hidden" hidden id="_vjs" value="<?= vjs() ?>">
<input type="hidden" id="_sesion" value="0">
<?php
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '';
// ExisteModRol(0)
if ($_SERVER['SERVER_NAME'] != 'localhost') { // Si no es localhost
    echo '<div class="loader"></div>';
}
$dropdownMenu = fn($href, $modulo) => "<a class='dropdown-item font08 fontm px-3 sub_menu' href='" . $href . "'>" . $modulo . "</a>";

$mapOperaciones = [
    'General' => [
        'url' => '/' . HOMEHOST . '/general/',
        'title' => 'Control General',
    ],
    'Fichadas' => [
        'url' => '/' . HOMEHOST . '/fichadas/',
        'title' => 'Fichadas',
    ],
    'Mis Horas' => [
        'url' => '/' . HOMEHOST . '/mishoras/',
        'title' => 'Mis Horas',
    ],
    'Horas' => [
        'url' => '/' . HOMEHOST . '/horas/',
        'title' => 'Horas',
    ],
    'Fichar' => [
        'url' => '/' . HOMEHOST . '/fichar/',
        'title' => 'Ingreso de Fichadas',
    ],
    'Novedades' => [
        'url' => '/' . HOMEHOST . '/novedades/',
        'title' => 'Novedades',
    ],
    'Personal' => [
        'url' => '/' . HOMEHOST . '/personal/',
        'title' => 'Adm de Personal',
    ],
    'Procesar' => [
        'url' => '/' . HOMEHOST . '/procesar/',
        'title' => 'Procesar Datos',
    ],
    'Cierres' => [
        'url' => '/' . HOMEHOST . '/cierres/',
        'title' => 'Generar Cierres',
    ],
    'Liquidar' => [
        'url' => '/' . HOMEHOST . '/liquidar/',
        'title' => 'Generar Liquidación',
    ],
    'Dashboard' => [
        'url' => '/' . HOMEHOST . '/dashboard/',
        'title' => 'Dashboard',
    ],
    'Cta Cte' => [
        'url' => '/' . HOMEHOST . '/ctacte/',
        'title' => 'Cta Cte Novedades',
    ],
    'Cta Cte Horas' => [
        'url' => '/' . HOMEHOST . '/ctacte/horas/',
        'title' => 'Cta Cte Horas',
    ],
    'Otras Novedades' => [
        'url' => '/' . HOMEHOST . '/otrasnov/',
        'title' => 'Otras Novedades',
    ],
    'Horas Costeadas' => [
        'url' => '/' . HOMEHOST . '/horascost/',
        'title' => 'Horas Costeadas',
    ],
    'Horarios' => [
        'url' => '/' . HOMEHOST . '/op/horarios/',
        'title' => 'Adm de Horarios',
    ],
    'Auditoría' => [
        'url' => '/' . HOMEHOST . '/control/aud/',
        'title' => 'Auditoría',
    ],
    'Proyectar Horas' => [
        'url' => '/' . HOMEHOST . '/procesar/proyectar/',
        'title' => 'Proyectar Horas',
    ],
];

$mapInformes = [
    'Horarios Asignados' => '/' . HOMEHOST . '/informes/horasign/',
    'Planilla Horaria' => '/' . HOMEHOST . '/informes/horplan/',
    'Parte Diario' => '/' . HOMEHOST . '/informes/partedia/',
    'Informe de Novedades' => '/' . HOMEHOST . '/informes/infornov/',
    'Informe de Fichadas' => '/' . HOMEHOST . '/informes/inforfic/',
    'Informe de Horas' => '/' . HOMEHOST . '/informes/inforhora/',
    'Informe Presentismo' => '/' . HOMEHOST . '/informes/infornovc/',
    'Informe FAR' => '/' . HOMEHOST . '/informes/inforfar/',
    'Reporte de Totales' => '/' . HOMEHOST . '/informes/reporte/',
    'Reporte Prysmian' => '/' . HOMEHOST . '/informes/custom/prysmian',
];

$mapConfiguracion = [
    'Datos' => '/' . HOMEHOST . '/configuracion/datos/',
    'Estructura' => '/' . HOMEHOST . '/configuracion/estruct/',
];

?>
<input type="hidden" hidden id="_referer" value="<?= urlencode($_SERVER['REQUEST_URI'] ?? '') ?>">
<input type="hidden" hidden id="ID_MODULO" value="<?= ID_MODULO ?? '' ?>">
<!-- navBar -->
<div id="navBarPrimary" class="sticky-top d-print-none" style="z-index:1040;">
    <nav class="navbar navbar-expand-lg navbar-light bg-white row d-flex align-items-center">
        <!-- brandLogo -->
        <?php if (HOMEHOST == 'chweb') { ?>
            <a class="navbar-brand m-0 p-0 d-flex align-items-center" href="/<?= HOMEHOST ?>/inicio/"
                onclick="ShowLoading()">
                <img src="/<?= HOMEHOST ?>/img/logos/logoHRP.svg?v=<?= vjs() ?>" class="m-0 p-0 w110 img-fluid"
                    alt="<?= CUSTOMER ?>">
            </a>
        <?php } elseif (HOMEHOST == 'seguimiento') { ?>
            <a class="navbar-brand" href="/<?= HOMEHOST ?>/inicio/" onclick="ShowLoading()">
                <img src="/<?= HOMEHOST ?>/img/logo.png" alt="<?= CUSTOMER ?>" class="w250">
            </a>
        <?php } ?>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto d-flex">
                <?php
                $array = [
                    mod_roles($_SESSION['RECID_ROL'] ?? '')
                ];
                if (($array[0])):
                    if (!$array[0]['error']) {
                        $rowcount = (count($array[0]['mod_roles']));
                        $dataROL = $array[0]['mod_roles'];

                        $arrIdTipo = super_unique($dataROL, 'idtipo');
                        // exit;
                        function checkTipoMod($array, $idtipo)
                        {
                            foreach ($array as $value) {
                                if ($value['idtipo'] == $idtipo) {
                                    $var = $value['idtipo'];
                                    return $var;
                                }
                            }
                        }
                        /** Tipos de Modulo
                         *  1 Operaciones
                         *  2 Informes
                         *  3 Configuración
                         *  4 Mobile
                         *  5 Cuentas
                         *  6 Proyectos
                         */
                        ?>
                        <?php if (checkTipoMod($arrIdTipo, '1')): # 1 Operaciones
                                        ?>
                            <!--Operaciones-->
                            <li class="nav-item mx-1 dropdown" id="lidrop">
                                <a class="nav-link font08 fontm menu dropdown-toggle" href="#"
                                    id="navbarDropdownOperaciones" role="button" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">Operaciones</a>
                                <div class="dropdown-menu radius" aria-labelledby="navbarDropdownOperaciones">
                                    <?php
                                    foreach ($dataROL as $v) {
                                        $modulo = $mapOperaciones[$v['modulo']] ?? null;
                                        if (!$modulo || $v['modulo'] === '1')
                                            continue;
                                        echo $dropdownMenu($modulo['url'] ?? '#', $modulo['title'] . ' ' . ($v['orden'] ?? ''));
                                    }
                                    ?>
                                </div>
                            </li>
                        <?php endif  # Fin 1 Operaciones
                                    ?>

                        <?php if (checkTipoMod($arrIdTipo, '2')): # 2 Informes
                                        ?>
                            <!--Informes-->
                            <li class="nav-item mx-1 dropdown">
                                <a class="nav-link font08 fontm menu dropdown-toggle" href="#" id="navbarDropdownInformes"
                                    role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Informes</a>
                                <div class="dropdown-menu radius" aria-labelledby="navbarDropdownInformes">
                                    <?php
                                    foreach ($dataROL as $values) {
                                        $modulo = $mapInformes[$values['modulo']] ?? null;
                                        if (!$modulo || $v['modulo'] === '1')
                                            continue;
                                        echo $dropdownMenu($modulo, $values['modulo']);
                                    }
                                    ?>
                                </div>
                            </li>
                        <?php endif# Fin 2 Informes
                                    ?>
                        <?php if (checkTipoMod($arrIdTipo, '3')): # 3 Configuración
                                        ?>
                            <!--Configuración-->
                            <li class="nav-item mx-1 dropdown">
                                <a class="nav-link font08 fontm menu dropdown-toggle" href="#" id="navbarDropdownConf"
                                    role="button" data-toggle="dropdown" aria-haspopup="true"
                                    aria-expanded="false">Configuración</a>
                                <div class="dropdown-menu radius" aria-labelledby="navbarDropdownConf">
                                    <?php
                                    foreach ($dataROL as $v) {
                                        $modulo = $mapConfiguracion[$v['modulo']] ?? null;
                                        if (!$modulo)
                                            continue;
                                        echo $dropdownMenu($modulo, $v['modulo']);
                                    }
                                    ?>
                                </div>
                            </li>
                        <?php endif  # Fin 3 Configuración
                                    ?>


                        <?php if (checkTipoMod($arrIdTipo, '4')): # 4 Mobile
                                        $linkMobile = "/" . HOMEHOST . "/mobile/hrp/";
                                        ?>
                            <!--Mobile-->
                            <li class="nav-item mx-1 dropdown">
                                <a class="nav-link font08 fontm menu" href="<?= $linkMobile ?>" id="navbarDropdownMobile"
                                    role="button">Mobile</a>
                            </li>
                        <?php endif# Fin 4 Mobile
                                    ?>
                        <?php if (checkTipoMod($arrIdTipo, '6')): # 3 proyectos
                                        ?>
                            <!--Proyectos-->
                            <?php
                            foreach ($dataROL as $values) {
                                $Modulo2 = $values['modulo'];
                                switch ($Modulo2) {
                                    case 'Inicio':
                                        echo "<li class='nav-item mx-1'><a class='nav-link font08 fontm menu'  href='/" . HOMEHOST . "/proy/'>Proyectos</a></li>";
                                        break;
                                }
                            }
                            ?>
                        <?php endif  # Fin 3 proyectos
                                    ?>
                        <?php  # 4 Cuentas
                                foreach ($dataROL as $value) {
                                    $Modulo = $value['modulo'];
                                    $Modulo2 = $value['modulo'];

                                    if ($Modulo2 == 'Mi Cuenta') {
                                        if (checkTipoMod($arrIdTipo, '5')) {
                                            ?>
                                    <!--Mi Cuenta-->
                                    <li class="nav-item mx-1 dropdown"><a class="nav-link font08 fontm menu dropdown-toggle"
                                            href="#" id="navbarDropdownMiCuenta" role="button" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">Mi Cuenta </a>
                                        <div class="dropdown-menu radius" aria-labelledby="navbarDropdownMiCuenta">
                                            <a class="dropdown-item font08 fontm px-3 sub_menu"
                                                href="/<?= HOMEHOST ?>/usuarios/roles/?_c=<?= $_SESSION['RECID_CLIENTE'] ?>">Roles</a>
                                            <a class="dropdown-item font08 fontm px-3 sub_menu"
                                                href="/<?= HOMEHOST ?>/usuarios/?_c=<?= $_SESSION['RECID_CLIENTE'] ?>">Usuarios</a>
                                            <?php if (modulo_cuentas() == '1') { ?>
                                                <a class="dropdown-item font08 fontm px-3 sub_menu"
                                                    href="/<?= HOMEHOST ?>/usuarios/clientes/">Cuentas</a>
                                            <?php } ?>
                                        </div>
                                    </li>
                                    <?php
                                        } # Fin Cuentas
                                    } elseif (
                                        ($Modulo2 != 'General')
                                        && ($Modulo2 != 'Cierres')
                                        && ($Modulo2 != 'Cuentas')
                                        && ($Modulo2 != 'Auditoría')
                                        && ($Modulo2 != 'Otras Novedades')
                                        && ($Modulo2 != 'Horas')
                                        && ($Modulo2 != 'Liquidar')
                                        && ($Modulo2 != 'Cta Cte Horas')
                                        && ($Modulo2 != 'Cta Cte')
                                        && ($Modulo2 != 'Novedades')
                                        && ($Modulo2 != 'Procesar')
                                        && ($Modulo2 != 'Fichadas')
                                        && ($Modulo2 != 'Personal')
                                        && ($Modulo2 != 'Fichar')
                                        && ($Modulo2 != 'Horarios Asignados')
                                        && ($Modulo2 != 'Planilla Horaria')
                                        && ($Modulo2 != 'Parte Diario')
                                        && ($Modulo2 != 'Informe de Novedades')
                                        && ($Modulo2 != 'Informe de Fichadas')
                                        && ($Modulo2 != 'Informe de Horas')
                                        && ($Modulo2 != 'Informe FAR')
                                        && ($Modulo2 != 'Reporte de Totales')
                                        && ($Modulo2 != 'Mobile')
                                        && ($Modulo2 != 'Zonas Mobile')
                                        && ($Modulo2 != 'Usuarios Mobile')
                                        && ($Modulo2 != 'Mensajes Mobile')
                                        && ($Modulo2 != 'Informe Presentismo')
                                        && ($Modulo2 != 'Datos')
                                        && ($Modulo2 != 'Estructura')
                                        && ($Modulo2 != 'Mobile HRP')
                                        && ($Modulo2 != 'Dashboard')
                                        && ($Modulo2 != 'Horarios')
                                        && ($Modulo2 != 'Proyectos')
                                        && ($Modulo2 != 'Mis Tareas')
                                        && ($Modulo2 != 'Tareas')
                                        && ($Modulo2 != 'Estados')
                                        && ($Modulo2 != 'Procesos')
                                        && ($Modulo2 != 'Plantilla Procesos')
                                        && ($Modulo2 != 'Plantilla Planos')
                                        && ($Modulo2 != 'Planos')
                                        && ($Modulo2 != 'Inicio')
                                        && ($Modulo2 != 'Empresas')
                                        // &&($Modulo2 != 'Mis Horas')
                                        && ($Modulo2 != 'Horas Costeadas')
                                        && ($Modulo2 != 'Reporte Prysmian')
                                        && ($Modulo2 != 'Proyectar Horas')
                                    ) { ?>
                                <li class="nav-item mx-1"><a class="nav-link font08 fontm menu"
                                        href="/<?= HOMEHOST ?>/<?= strtolower(str_replace(' ', '', $Modulo)) ?>/">
                                        <?= $Modulo2 ?>
                                    </a></li>
                            <?php }
                                }
                                ;
                    } else {
                        echo '<a class="nav-link font08 fontm hob" href="#">No hay Módulos</a>';
                    }
                endif; ?>
            </ul>
            <div id="contenedoruser" class="d-inline-flex" style="gap: 4px;">
                <div class="hint hint--bottom hint--rounded hint--no-arrow hint--info hint--no-shadow" aria-label="<?= $_SESSION['NOMBRE_SESION'] ?? '' ?>">
                    <?php if (($_SESSION['USER_AD'] ?? '') === '0'): ?>
                        <a href="/<?= HOMEHOST ?>/usuarios/perfil/index.php"
                            class="btn btn-sm px-2 py-1 border radius btn-outline-custom">
                            <i class="bi bi-person-fill font1"></i>
                        </a>
                    <?php endif; ?>
                    <div class="bg-white mt-2 shadow p-0 fadeIn radius" id="showuser" style="z-index:9999">
                        <div class="p-3">
                            <p class="m-0 h6">
                                <?= $_SESSION['NOMBRE_SESION'] ?? '' ?>
                            </p>
                            <p class="m-0 pt-1 font08 fontm">Usuario:<span class="ml-1 fw4">
                                    <?= $_SESSION['user'] ?? '' ?>
                                </span></p>
                            <p class="m-0 pt-1 font08 fontm">Empresa:<span class="ml-1 fw4">
                                    <?= $_SESSION['CLIENTE'] ?? '' ?>
                                </span></p>
                        </div>
                    </div>
                </div>
                <div class="hint hint--right hint--rounded hint--no-arrow hint--info hint--no-shadow" aria-label="Salir">
                    <a href="/<?= HOMEHOST ?>/logout.php" class="btn btn-sm px-2 py-1 border radius btn-outline-custom"
                        data-toggle="modal" data-target="#salir">
                        <i class="bi bi-arrow-right font1"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>
</div>
<!-- Salir -->
<div id="salir" class="modal" tabindex="-1" role="dialog" aria-labelledby="salir-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered fadeIn" role="document">
        <div class="modal-content" style="z-index:9999;">
            <div class="modal-body text-center my-3 mx-auto">
                <p class="lead">¿Desea Salir?</p>
                <div class="form-inline">
                    <form action="/<?= HOMEHOST ?>/logout.php" class="mx-auto"><button
                            class="border btn px-5 btn-round font08 fontm" data-dismiss="modal"
                            type="button">NO</button><button type="submit"
                            class="ml-2 btn btn-info px-5 btn-round font08 fontm btn-custom">SI</button></form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="loader"></div>
<input type="hidden" hidden id="_c" value="<?= $_SESSION["RECID_CLIENTE"] ?>">
<input type="hidden" hidden id="_r" value="<?= $_SESSION["RECID_ROL"] ?>">
<input type="hidden" hidden id="_lega" value="<?= $_SESSION["LEGAJO_SESION"] ?>">
<input type="hidden" hidden id="_homehost" value="<?= HOMEHOST ?>">
<input type="hidden" hidden id="_host" value="<?= host() ?>">
<input type="hidden" hidden id="_vjs" value="<?= vjs() ?>">
<input type="hidden" hidden id="_sesion" value="0">
<!-- <input type="" id="" value="<?=$_SESSION['ConvRol']?>"> -->
<?php
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '';
?>
<input type="hidden" hidden id="_referer" value="<?=urlencode($_SERVER['REQUEST_URI'])?>">
<input type="hidden" hidden id="ID_MODULO" value="<?=ID_MODULO?>">

<nav class="navbar sticky-top navbar-expand-lg navbar-light bg-white row d-flex align-items-center" style="z-index:1040;">
    <?php if (HOMEHOST == 'chweb') { ?>
        <a class="navbar-brand" href="/<?= HOMEHOST ?>/inicio/" onclick="ShowLoading()">
            <img src="/<?= HOMEHOST ?>/img/Logo-HRP.jpg?v=<?= vjs() ?>" alt="<?= CUSTOMER ?>">
        </a>
    <?php } elseif (HOMEHOST == 'seguimiento') { ?>
        <a class="navbar-brand" href="/<?= HOMEHOST ?>/inicio/" onclick="ShowLoading()">
            <img src="/<?= HOMEHOST ?>/img/logo.png" alt="<?= CUSTOMER ?>" class="w250">
        </a>
    <?php } ?>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto d-flex">
            <?php
            //$url   = host() . "/" . HOMEHOST . "/data/GetModRol.php?tk=" . token() . "&recidRol=" . $_SESSION["RECID_ROL"];
            // $json  = file_get_contents(($url));
            // $array = json_decode($json, TRUE);

            $array = (array(mod_roles($_SESSION['RECID_ROL'])));

            if (is_array($array)) :
                if (!$array[0]['error']) {
                    $rowcount = (count($array[0]['mod_roles']));
                    $dataROL  = $array[0]['mod_roles'];

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
                     *  3 Configuracion
                     *  4 Mobile
                     *  5 Cuentas
                     */
            ?>
                    <?php if (checkTipoMod($arrIdTipo, '1')) : # 1 Operaciones 
                    ?>
                        <!--Operaciones-->
                        <li class="nav-item mx-1 dropdown" id="lidrop">
                            <a class="nav-link fontq fw4 dropdown-toggle text-dark" href="#" id="navbarDropdownOperaciones" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Operaciones</a>
                            <div class="dropdown-menu radius" aria-labelledby="navbarDropdownOperaciones">
                                <?php
                                foreach ($dataROL as $values) {
                                    $Modulo  = $values['modulo'];
                                    $Modulo2 = $values['modulo'];
                                    $Modulo2 = ($Modulo2 == 'Fichar') ? 'Ingreso de Fichadas' : $Modulo2;
                                    $Modulo2 = ($Modulo2 == 'Cierres') ? 'Generar Cierres' : $Modulo2;
                                    $Modulo2 = ($Modulo2 == 'Liquidar') ? 'Generar Liquidación' : $Modulo2;
                                    $Modulo2 = ($Modulo2 == 'Horarios') ? 'Adm de Horarios' : $Modulo2;
                                    $Modulo2 = ($Modulo2 == 'General') ? 'Control General' : $Modulo2;
                                    $Modulo2 = ($Modulo2 == 'Cta Cte') ? 'Cta Cte Novedades' : $Modulo2;
                                    $Modulo2 = ($Modulo2 == 'Personal') ? 'Adm de Personal' : $Modulo2;
                                    $Modulo2 = ($Modulo2 == 'Procesar') ? 'Procesar Datos' : $Modulo2;
                                    switch ($Modulo2) {
                                        case 'Control General':
                                        case 'Mis Horas':
                                        case 'Fichadas':
                                        case 'Horas':
                                        case 'Ingreso de Fichadas':
                                        case 'Novedades':
                                        case 'Adm de Personal':
                                        case 'Procesar Datos':
                                        case 'Generar Cierres':
                                        case 'Generar Liquidación':
                                        case 'Dashboard':
                                        case 'Cta Cte Novedades':
                                            echo "<a class='dropdown-item fontq px-3 sub_menu' href=/" . HOMEHOST . "/" . strtolower(str_replace(" ", "", $Modulo)) . "/>" . $Modulo2 . "</a>";
                                            break;
                                        case 'Horas Costeadas':
                                            echo "<a class='dropdown-item fontq px-3 sub_menu' href=/" . HOMEHOST . "/horascost/>" . $Modulo2 . "</a>";
                                            break;
                                        case 'Cta Cte Horas':
                                            echo "<a class='dropdown-item fontq px-3 sub_menu' href=/" . HOMEHOST . "/ctacte/horas/>" . $Modulo2 . "</a>";
                                            break;
                                        case 'Otras Novedades':
                                            echo "<a class='dropdown-item fontq px-3 sub_menu' href=/" . HOMEHOST . "/otrasnov/>" . $Modulo2 . "</a>";
                                            break;
                                        case 'Adm de Horarios':
                                            echo "<a class='dropdown-item fontq px-3 sub_menu' href=/" . HOMEHOST . "/op/horarios/>" . $Modulo2 . "</a>";
                                            break;
                                        case 'Auditoría':
                                            echo "<a class='dropdown-item fontq px-3 sub_menu' href=/" . HOMEHOST . "/auditor/>" . $Modulo2 . "</a>";
                                            break;
                                    }
                                }
                                ?>
                            </div>
                        </li>
                    <?php endif  # Fin 1 Operaciones 
                    ?>

                    <?php if (checkTipoMod($arrIdTipo, '2')) : # 2 Informes 
                    ?>
                        <!--Informes-->
                        <li class="nav-item mx-1 dropdown">
                            <a class="nav-link fontq fw4 dropdown-toggle text-dark" href="#" id="navbarDropdownInformes" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Informes</a>
                            <div class="dropdown-menu radius" aria-labelledby="navbarDropdownInformes">
                                <?php
                                foreach ($dataROL as $values) {
                                    $Modulo = $values['modulo'];
                                    $Modulo2 = $values['modulo'];
                                    $Modulo2 = ($Modulo2 == 'Fichar') ? 'Ingreso de Fichadas' : $Modulo2;
                                    $Modulo2 = ($Modulo2 == 'Cierres') ? 'Generar Cierres' : $Modulo2;
                                    $Modulo2 = ($Modulo2 == 'Liquidar') ? 'Generar Liquidación' : $Modulo2;
                                    switch ($Modulo2) {
                                        case 'Horarios Asignados':
                                            echo "<a class='dropdown-item fontq px-3 sub_menu d-none' href=/" . HOMEHOST . "/informes/horasign/>" . $Modulo2 . "</a>";
                                            break;
                                        case 'Planilla Horaria':
                                            echo "<a class='dropdown-item fontq px-3 sub_menu d-none' href=/" . HOMEHOST . "/informes/horplan/>" . $Modulo2 . "</a>";
                                            break;
                                        case 'Parte Diario':
                                            echo "<a class='dropdown-item fontq px-3 sub_menu' href=/" . HOMEHOST . "/informes/partedia/>" . $Modulo2 . "</a>";
                                            break;
                                        case 'Informe de Novedades':
                                            echo "<a class='dropdown-item fontq px-3 sub_menu' href=/" . HOMEHOST . "/informes/infornov/>" . $Modulo2 . "</a>";
                                            break;
                                        case 'Informe de Fichadas':
                                            echo "<a class='dropdown-item fontq px-3 sub_menu' href=/" . HOMEHOST . "/informes/inforfic/>" . $Modulo2 . "</a>";
                                            break;
                                        case 'Informe de Horas':
                                            echo "<a class='dropdown-item fontq px-3 sub_menu' href=/" . HOMEHOST . "/informes/inforhora/>" . $Modulo2 . "</a>";
                                            break;
                                        case 'Informe Presentismo':
                                            echo "<a class='dropdown-item fontq px-3 sub_menu' href=/" . HOMEHOST . "/informes/infornovc/>" . $Modulo2 . "</a>";
                                            break;
                                        case 'Informe FAR':
                                            echo "<a class='dropdown-item fontq px-3 sub_menu' href=/" . HOMEHOST . "/informes/inforfar/>" . $Modulo2 . "</a>";
                                            break;
                                    }
                                }
                                ?>
                            </div>
                        </li>
                    <?php endif # Fin 2 Informes 
                    ?>
                    <?php if (checkTipoMod($arrIdTipo, '3')) : # 3 Configuración
                    ?>
                        <!--Operaciones-->
                        <li class="nav-item mx-1 dropdown">
                            <a class="nav-link fontq fw4 dropdown-toggle text-dark" href="#" id="navbarDropdownconfiguracion" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Configuración</a>
                            <div class="dropdown-menu radius" aria-labelledby="navbarDropdownconfiguracion">
                                <?php
                                foreach ($dataROL as $values) {
                                    $Modulo  = $values['modulo'];
                                    $Modulo2 = $values['modulo'];
                                    switch ($Modulo2) {
                                        case 'Datos':
                                            echo "<a class='dropdown-item fontq px-3 sub_menu' href='/" . HOMEHOST . "/configuracion/datos/'>" . $Modulo2 . "</a>";
                                            break;
                                        case 'Estructura':
                                            echo "<a class='dropdown-item fontq px-3 sub_menu' href='/" . HOMEHOST . "/configuracion/estruct/'>" . $Modulo2 . "</a>";
                                            break;
                                    }
                                }
                                ?>
                            </div>
                        </li>
                    <?php endif  # Fin 1 Operaciones 
                    ?>

                    <?php if (checkTipoMod($arrIdTipo, '4')) : # 4 Mobile 
                    ?>
                        <!--Mobile-->
                        <li class="nav-item mx-1 dropdown">
                            <a class="nav-link fontq fw4 dropdown-toggle text-dark" href="#" id="navbarDropdownMobile" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Mobile</a>
                            <div class="dropdown-menu radius" aria-labelledby="navbarDropdownMobile">
                                <?php
                                foreach ($dataROL as $values) {
                                    $Modulo = $values['modulo'];
                                    $Modulo = ($Modulo == 'Mobile') ? 'Fichadas Mobile' : $Modulo;
                                    // $Modulo = ($Modulo=='Zonas Mobile') ? 'Zonas': $Modulo;
                                    switch ($Modulo) {
                                        case 'Fichadas Mobile':
                                            echo "<a class='dropdown-item fontq px-3 sub_menu' href=/" . HOMEHOST . "/mobile/>" . $Modulo . "</a>";
                                            break;
                                        case 'Zonas Mobile':
                                            echo "<a class='dropdown-item fontq px-3 sub_menu' href=/" . HOMEHOST . "/mobile/zonas/>" . $Modulo . "</a>";
                                            break;
                                        case 'Usuarios Mobile':
                                            echo "<a class='dropdown-item fontq px-3 sub_menu' href=/" . HOMEHOST . "/mobile/usuarios/>" . $Modulo . "</a>";
                                            break;
                                        case 'Mensajes Mobile':
                                            echo "<a class='dropdown-item fontq px-3 sub_menu' href=/" . HOMEHOST . "/mobile/sms/>" . $Modulo . "</a>";
                                            break;
                                        case 'Mobile HRP':
                                            echo "<a class='dropdown-item fontq px-3 sub_menu' href=/" . HOMEHOST . "/mobile/hrp/>" . $Modulo . "</a>";
                                            break;
                                    }
                                }
                                ?>
                            </div>
                        </li>
                    <?php endif # Fin 4 Mobile 
                    ?>
                    <?php  # 4 Cuentas 
                    foreach ($dataROL as $value) :
                        $Modulo = $value['modulo'];
                        $Modulo2 = $value['modulo'];

                        if ($Modulo2 == 'Mi Cuenta') {
                            if (checkTipoMod($arrIdTipo, '5')) {
                    ?>
                                <!--Mi Cuenta-->
                                <li class="nav-item mx-1 dropdown"><a class="nav-link fontq fw4 dropdown-toggle text-dark" href="#" id="navbarDropdownMiCuenta" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Mi Cuenta </a>
                                    <div class="dropdown-menu radius" aria-labelledby="navbarDropdownMiCuenta">
                                        <a class="dropdown-item fontq px-3 sub_menu" href="/<?= HOMEHOST ?>/usuarios/roles/?_c=<?= $_SESSION['RECID_CLIENTE'] ?>">Roles</a>
                                        <a class="dropdown-item fontq px-3 sub_menu" href="/<?= HOMEHOST ?>/usuarios/?_c=<?= $_SESSION['RECID_CLIENTE'] ?>">Usuarios</a>
                                        <?php if (modulo_cuentas() == '1') { ?>
                                            <a class="dropdown-item fontq px-3 sub_menu" href="/<?= HOMEHOST ?>/usuarios/clientes/">Cuentas</a>
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
                            // &&($Modulo2 != 'Mis Horas')
                            && ($Modulo2 != 'Horas Costeadas')
                        ) { ?>
                            <li class="nav-item mx-1"><a class="nav-link fontq fw4 text-dark" href="/<?= HOMEHOST ?>/<?= strtolower(str_replace(' ', '', $Modulo)) ?>/"><?= $Modulo2 ?></a></li>
            <?php }
                    endforeach;
                } else {
                    echo '<a class="nav-link fontq hob" href="#">No hay Módulos</a>';
                }
            endif; ?>
        </ul>
        <div id="contenedoruser">
            <a href="/<?= HOMEHOST ?>/usuarios/perfil/index.php" class="btn btn-sm border-0 btn-light px-3 py-2">
                <p class="m-0"><?= imgIcon('perfil', 'Mi Perfil', '') ?></p>
            </a>
            <div class="bg-white mt-2 shadow p-0 animate__animated animate__fadeIn radius" id="showuser" style="z-index:9999">
                <div class="p-3">
                    <p class="m-0 h6"><?= $_SESSION['NOMBRE_SESION'] ?></p>
                    <p class="m-0 pt-1 fontq">Usuario:<span class="ml-1 fw4"><?= $_SESSION['user'] ?></span></p>
                    <p class="m-0 pt-1 fontq">Empresa:<span class="ml-1 fw4"><?= $_SESSION['CLIENTE'] ?></span></p>
                </div>
            </div>
        </div>
        <a href="/<?= HOMEHOST ?>/logout.php" title="Salir" class="btn btn-sm border-0 btn-light px-3 py-2" data-toggle="modal" data-target="#salir"><?= imgIcon('exit', 'Salir', '') ?></a>
    </div>
</nav>
<div id="salir" class="modal" tabindex="-1" role="dialog" aria-labelledby="salir-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered animate__animated animate__fadeIn" role="document">
        <div class="modal-content" style="z-index:9999;">
            <div class="modal-body text-center my-3 mx-auto">
                <p class="lead">¿Desea Salir?</p>
                <div class="form-inline">
                    <form action="/<?= HOMEHOST ?>/logout.php" class="mx-auto"><button class="border btn px-5 btn-round fontq" data-dismiss="modal" type="button">NO</button><button type="submit" class="ml-2 btn btn-info px-5 btn-round fontq btn-custom">SI</button></form>
                </div>
            </div>
        </div>
    </div>
</div>
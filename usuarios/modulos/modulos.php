<?php
$modulo = '2';
// ExisteRol($_GET['_r']);
// UnsetGet('id');
// UnsetGet('_r');
// list($id_Rol, $nombreRol, $clienteRol, $UsuariosRol, $recid_clienteRol) = Rol_Recid($_GET['_r']);
$Cliente = ExisteCliente($_GET['_c']);
$Rol     = ExisteRol2($_GET['_r'])
?>
<!doctype html>
<html lang="es">

<head>
    <?php require __DIR__ . "../../../llamadas.php"; ?>
    <title><?= MODULOS['cuentas'] ?> » Rol » M&oacute;dulos</title>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow">
        <?php require __DIR__ . '../../../nav.php'; ?>
        <?= encabezado_mod('bg-custom', 'white', 'estructura.png', 'M&oacutedulos', '') ?>
        <div class="row pl-1 py-3">
            <div class="col-12 col-sm-6">
                <div class="form-inline">
                    <label class="w70 fontq">Rol </label><span class="fw4 fontq"><?= $Rol ?></span>
                </div>
                <div class="form-inline">
                    <label class="w70 fontq">Cuenta </label><span class="fw4 fontq"><?= $Cliente ?></span>
                </div>
                <!-- <div class="form-inline">
                    <label class="w70 fontq">Usuarios </label><span class="fw4 fontq"><?= ceronull($UsuariosRol) ?></span>
                </div> -->
            </div>
            <div class="col-12 col-sm-6">
                <a href="/<?= HOMEHOST ?>/usuarios/roles/?_c=<?= $_GET['_c'] ?>" class="btn fontq mt-1 float-right m-0 opa7 btn-custom">Volver a Roles</a>
            </div>
        </div>
        <?php if (!principal($_GET['_r'])) {
            /** Check de usuario principal del sistema*/ ?>
            <div class="row m-1">
            <div class="col-12 fonth mt-2 fw4 bg-light py-2 mb-2">M&oacute;dulos</div>
                <div class="col-sm-4 col-xl-2 col-12">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    </div>
                </div>
                <div class="col-sm-8 col-xl-10 col-12 mt-3 mt-sm-0">
                    <div class="tab-content p-3 d-none" id="v-pills-tabContent" style="min-height: 220px;">
                    </div>
                </div>
            </div>
            <form action="../roles/insert.php" method="POST" class="form_abm_rol w-100">
                <div class="row m-1 d-none" id="RowModulos">
                    <div class="col-12 fonth mt-2 fw4 bg-light py-2 mb-2">Permisos</div>
                    <input type="hidden" id="RecidRol" name="RecidRol" value="<?=$_GET['_r']?>">
                    <input type="hidden" id="act_abm" name="act_abm" value="true">
                    <div class="col-12">
                        <span class="fontq btn btn-link p-0 text-gris" id="marcar">Marcar Todo</span>
                        <span class="ml-2 fontq btn btn-link p-0 text-gris" id="desmarcar">Desmarcar</span>
                    </div>
                    <div class="col-6 col-sm-6 col-md-4 col-xl-3 p-3">
                        <p class="fonth text-secondary m-0 fw4">Fichadas</p>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input abmcheck" id="aFic" name="aFic">
                            <label class="custom-control-label" style="padding-top: 3px;" for="aFic">Alta</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input abmcheck" id="bFic" name="bFic">
                            <label class="custom-control-label" style="padding-top: 3px;" for="bFic">Baja</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input abmcheck" id="mFic" name="mFic">
                            <label class="custom-control-label" style="padding-top: 3px;" for="mFic">Modificación</label>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6 col-md-4 col-xl-3 p-3">
                        <p class="fonth text-secondary m-0 fw4">Novedades</p>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input abmcheck" id="aNov" name="aNov">
                            <label class="custom-control-label" style="padding-top: 3px;" for="aNov">Alta</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input abmcheck" id="bNov" name="bNov">
                            <label class="custom-control-label" style="padding-top: 3px;" for="bNov">Baja</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input abmcheck" id="mNov" name="mNov">
                            <label class="custom-control-label" style="padding-top: 3px;" for="mNov">Modificación</label>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6 col-md-4 col-xl-3 p-3">
                        <p class="fonth text-secondary m-0 fw4">Horas</p>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input abmcheck" id="aHor" name="aHor">
                            <label class="custom-control-label" style="padding-top: 3px;" for="aHor">Alta</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input abmcheck" id="bHor" name="bHor">
                            <label class="custom-control-label" style="padding-top: 3px;" for="bHor">Baja</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input abmcheck" id="mHor" name="mHor">
                            <label class="custom-control-label" style="padding-top: 3px;" for="mHor">Modificación</label>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6 col-md-4 col-xl-3 p-3">
                        <p class="fonth text-secondary m-0 fw4">Otras Novedades</p>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input abmcheck" id="aONov" name="aONov">
                            <label class="custom-control-label" style="padding-top: 3px;" for="aONov">Alta</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input abmcheck" id="bONov" name="bONov">
                            <label class="custom-control-label" style="padding-top: 3px;" for="bONov">Baja</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input abmcheck" id="mONov" name="mONov">
                            <label class="custom-control-label" style="padding-top: 3px;" for="mONov">Modificación</label>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6 col-md-4 col-xl-3 p-3">
                        <p class="fonth text-secondary m-0 fw4">Procesar</p>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input abmcheck" id="Proc" name="Proc">
                            <label class="custom-control-label" style="padding-top: 3px;" for="Proc">Sí</label>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6 col-md-4 col-xl-3 p-3">
                        <p class="fonth text-secondary m-0 fw4">Citaciones</p>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input abmcheck" id="aCit" name="aCit">
                            <label class="custom-control-label" style="padding-top: 3px;" for="aCit">Alta</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input abmcheck" id="mCit" name="mCit">
                            <label class="custom-control-label" style="padding-top: 3px;" for="mCit">Baja</label>
                        </div>
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input abmcheck" id="bCit" name="bCit">
                            <label class="custom-control-label" style="padding-top: 3px;" for="bCit">Modificación</label>
                        </div>
                    </div>
                    <div class="col-6 col-sm-6 col-md-4 col-xl-3 p-3 d-flex align-items-end">
                        <button type="submit" class="fontq fw4 btn btn-custom border px-3 opa7" id="submitABM">Guardar</button>
                        <span class="ml-2 fw5 fontq align-middle respuestaabm p-2"></span>
                    </div>
                </div>
            </form>
        <?php } else {
            echo '<div class="alert alert-light mt-3">Rol principal del sistema. No se puede modificar.</div>';
        }
        /** Fin de check de usuario principal del sistema*/ ?>
    </div>
    <!-- fin container -->
    <?php
    require __DIR__ . "../../../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS JQUERY */
    ?>
    <script src="js/data-min.js?v=<?=vjs()?>"></script>
    <script src="js/abm-min.js?v=<?=vjs()?>"></script>
</body>

</html>
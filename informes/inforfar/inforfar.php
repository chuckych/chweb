<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "/../../llamadas.php"; ?>
    <title><?= MODULOS['inforhora'] ?></title>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '/../../nav.php'; ?>
        <!-- Encabezado -->
        <?php encabezado_mod('bg-custom', 'white', 'informes.png', MODULOS['inforfar'], '') ?>
        <!-- Fin Encabezado -->
        <?php
        $FechaMinMax = (fecha_min_max2('FICHAS1', 'FICHAS1.FicFech'));
        $FirstDate = $FechaMinMax['min'];
        /** FirstDate */
        $FirstYear = Fech_Format_Var($FechaMinMax['min'], 'Y');
        /** FirstYear */
        $maxDate = $FechaMinMax['max'];
        /** maxDate */
        $maxYear = date('Y');
        /** maxYear */
        $FechaIni = $FechaMinMax['max'];
        $FechaFin = $FechaMinMax['max'];
        // $FechaIni = date('Y-m-d');
        // $FechaFin = date('Y-m-d');
        if ($_SESSION["RECID_CLIENTE"] != 'kxo7w2q-') {
            echo '<div class="row">';
            echo '<div class="col-md-12 mt-2 fontq">';
            echo '<div class="alert alert-danger" role="alert">';
            echo '<h4 class="alert-heading">¡Atención!</h4>';
            echo '<p class="m-0">No tiene permisos para acceder a este módulo.</p>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            echo '<input type="hidden" value="1" id="cuentaSKF">';
        }
        ?>
        <div id="contenido">
            <div class="row bg-white py-2">
                <!-- <div class="col-12 col-sm-6">
                    <label for="Tipo"><span class="d-none d-sm-none d-md-none d-lg-block mb-1 fontq">Tipo de Personal: </span></label>
                    <select class="selectjs_tipoper w150" id="Tipo" name="Tipo">
                    </select>
                </div> -->
                <div class="col-12">
                    <div class="d-flex justify-content-sm-end justify-content-center mt-3 mt-sm-0">
                        <span id="trash_allIn" title="Limpiar Filtros" class="trash align-middle mt-2 fw5 ml-1"></span>
                        <input type="text" readonly class="mx-2 form-control text-center w250 ls2" name="_dr" id="_dr">
                    </div>
                </div>
            </div>
            <div class="row bg-white" id="rowFiltros">
                <div class="col-12 col-sm-4">
                    <!-- Empresa -->
                    <label for="Emp" class="mb-1 fontq">Empresas</label>
                    <select class="form-control selectjs_empresa" id="Emp" name="Emp">
                    </select>
                </div>
                <div class="col-12 col-sm-4">
                    <!-- Planta -->
                    <label for="Plan" class="mb-1 w100 fontq">Plantas </label>
                    <select class="form-control selectjs_plantas" id="Plan" name="Plan">
                    </select>
                </div>
                <div class="col-12 col-sm-4">
                    <!-- Sector -->
                    <label for="Sect" class="mb-1 w100 fontq">Sectores</label>
                    <select class="form-control selectjs_sectores" id="Sect" name="Sect">
                    </select>
                </div>
                <div class="col-12 col-sm-4">
                    <!-- Seccion -->
                    <label for="Sec2" class="mb-1 w100 fontq">Secciónes</label>
                    <select disabled class="form-control select_seccion" id="Sec2" name="Sec2">
                    </select>
                </div>
                <div class="col-12 col-sm-4">
                    <!-- Grupos -->
                    <label for="Grup" class="mb-1 w100 fontq">Grupos</label>
                    <select class="form-control selectjs_grupos" id="Grup" name="Grup">
                    </select>
                </div>
                <div class="col-12 col-sm-4">
                    <!-- Sucursal -->
                    <label for="Sucur" class="mb-1 w100 fontq">Sucursales</label>
                    <select class="form-control selectjs_sucursal" id="Sucur" name="Sucur">
                    </select>
                </div>
                <div class="col-12 pb-2 d-none">
                    <!-- Legajo -->
                    <label for="Per" class="mb-1 w100 fontq">Legajos</label>
                    <div class="d-flex align-items-center">
                        <select class="form-control selectjs_personal" id="Per" name="Per">
                        </select>
                    </div>
                </div>
            </div>
            <div class="row py-3">
                <div class="col-12 d-flex justify-content-end">
                    <!-- Por Legajo -->
                    <div class="custom-control custom-switch custom-control-inline mt-2"
                        data-titlet="Agrupar por Legajo">
                        <input checked="" type="radio" class="custom-control-input" id="agrupLegajo" name="agrup"
                            value="1">
                        <label class="custom-control-label" for="agrupLegajo" style="padding-top: 3px;"><span
                                class="text-dark">Por Legajo</span></label>
                    </div>
                    <!-- Por Estructura -->
                    <div class="custom-control custom-switch custom-control-inline mt-2"
                        data-titlet="Agrupar por Estructura">
                        <input type="radio" class="custom-control-input" id="agrupEstruct" name="agrup" value="2">
                        <label class="custom-control-label" for="agrupEstruct" style="padding-top: 3px;"><span
                                class="text-dark">Por Estructura</span></label>
                    </div>
                    <div class="border"><button
                            class="btn btn-light btn-sm fontq px-3 btn-mobile h40 border-0 w150 text-dark" type="submit"
                            id="btnExportar">Exportar</button></div>
                </div>
            </div>
        </div>
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "/../../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    require __DIR__ . "/../../js/DateRanger.php";
    ?>
    <script src="../../js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="../../js/select2.min.js"></script>
    <script src="js/select-min.js?v=<?= vjs() ?>"></script>
    <script src="js/export-min.js?v=<?= vjs() ?>"></script>
</body>

</html>
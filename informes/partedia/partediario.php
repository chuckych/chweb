<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../../llamadas.php"; ?>
    <title><?= MODULOS['partedia'] ?></title>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '../../../nav.php'; ?>
        <!-- Encabezado -->
        <?php encabezado_mod('bg-custom', 'white', 'informes.png', MODULOS['partedia'], '') ?>
        <!-- Fin Encabezado -->
        <!-- <form action="" method="GET" name="fichadas" class="" onsubmit="ShowLoading()" id='range'> -->
        <?php
        $FechaMinMax = (fecha_min_max('FICHAS', 'FICHAS.FicFech'));
        $FirstDate = $FechaMinMax['min'];
        /** FirstDate */
        $FirstYear = Fech_Format_Var($FechaMinMax['min'], 'Y');
        /** FirstYear */
        $maxDate   = $FechaMinMax['max'];
        /** maxDate */
        $maxYear   = date('Y');
        /** maxYear */
        $FechaIni = $FechaMinMax['max'];
        $FechaFin = $FechaMinMax['max'];
        ?>
        <div class="row py-2">
            <div class="col-12 col-sm-8">
                <label for="Tipo"><span class="d-none d-sm-none d-md-none d-lg-block mb-1 fontq">Tipo de Personal: </span></label>
                <select class="selectjs_tipoper w150" id="Tipo" name="Tipo">
                </select>
                <label for="_resaltar"><span class="mx-2 d-none d-sm-none d-md-none d-lg-block">Resaltar: </span></label>
                <select id="_resaltar" class="select2Resaltar form-control w120">
                    <?php
                    foreach (RESALTAR as $key => $value) {
                        echo '<option value="' . $value . '">' . $key . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="col-12 col-sm-4">
                <div class="d-flex justify-content-sm-end justify-content-center mt-3 mt-sm-0">
                    <button type="button" class="btn btn-outline-custom border btn-sm fontq" data-toggle="collapse" data-target="#rowFiltros" aria-expanded="false" aria-controls="rowFiltros">
                        <span>Filtros</span>
                    </button>
                    <span id="trash_allIn" title="Limpiar Filtros" class="trash align-middle mt-2 fw5 ml-1"></span>
                    <input type="text" readonly class="mx-2 form-control text-center w150 ls2" name="_dr" id="_dr">
                </div>
            </div>
        </div>
        <div class="row collapse invisible" id="rowFiltros">
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
            <div class="col-12 pb-3">
                <!-- Legajo -->
                <label for="Per" class="mb-1 w100 fontq">Legajos</label>
                <div class="d-flex align-items-center">
                    <select class="form-control selectjs_personal" id="Per" name="Per">
                    </select>
                </div>
            </div>
        </div>
        <div class="row d-flex align-items-center mt-2 mt-sm-0">
            <div class="col-12">
                <!-- Tardes -->
                <div class="custom-control custom-switch custom-control-inline ml-1 w180">
                    <input type="checkbox" class="custom-control-input" id="FicNovT">
                    <label class="custom-control-label" for="FicNovT" style="padding-top: 3px;"><span class="text-dark">Llegadas Tarde</span></label>
                    <input type="hidden" name="" id="datoFicNovT">
                </div>
                <!-- Incumplimientos -->
                <div class="custom-control custom-switch custom-control-inline ml-1 w180">
                    <input type="checkbox" class="custom-control-input" id="FicNovI">
                    <label class="custom-control-label" for="FicNovI" style="padding-top: 3px;"><span class="text-dark">Incumplimientos</span></label>
                    <input type="hidden" name="" id="datoFicNovI">
                </div>
                <!-- Salidas anticipadas -->
                <div class="custom-control custom-switch custom-control-inline ml-1 w180">
                    <input type="checkbox" class="custom-control-input" id="FicNovS">
                    <label class="custom-control-label" for="FicNovS" style="padding-top: 3px;"><span class="text-dark">Salidas anticipadas</span></label>
                    <input type="hidden" name="" id="datoFicNovS">
                </div>
                <!-- Ausencias -->
                <div class="custom-control custom-switch custom-control-inline ml-1 w180">
                    <input type="checkbox" class="custom-control-input" id="FicNovA">
                    <label class="custom-control-label" for="FicNovA" style="padding-top: 3px;"><span class="text-dark">Ausencias</span></label>
                    <input type="hidden" name="" id="datoFicNovA">
                </div>
            </div>
        </div>
        <form action="reporte/index.php" method="POST" id="FormExportar" class="">
            <div class="row">
                <div class="col-12">
                    <div class="custom-control custom-switch custom-control-inline ml-1 w180">
                        <input type="checkbox" class="custom-control-input" id="PerSN" name="_PerSN">
                        <label class="custom-control-label" for="PerSN" style="padding-top: 3px;">
                            <span class="text-dark">Personal S/Novedades</span>
                        </label>
                    </div>
                </div>
                <div class="col-12">
                    <a class="btn btn-link text-decoration-none text-dark fontq px-0" data-toggle="collapse" href="#Permisos" role="button" aria-expanded="false" aria-controls="Permisos">
                        <span id="btnPermiso">Opciones del Reporte</span><span class="fontpp ml-2">
                            <svg class="bi mr-1" width="10" height="10" fill="currentColor">
                                <use xlink:href="../../img/bootstrap-icons.svg#chevron-down" />
                            </svg>
                        </span>
                    </a>
                </div>
                <div class="collapse pb-2 pb-0" id="Permisos">
                    <div class="col-12 form-inline">
                        <label class="" for="_format"><span class="d-none d-sm-block mr-2">Hoja: </span></label>
                        <select name="_format" id="_format" class="select2 form-control w80">
                            <?php
                            foreach (TIPO_HOJA as $key => $value) {
                                echo '<option value="' . $value . '">' . $key . '</option>';
                            }
                            ?>
                        </select>
                        <span class="ml-1"></span>
                        <select name="_orientation" id="_orientation" class="select2 form-control w120">
                            <?php
                            foreach (ORIENTACION as $key => $value) {
                                echo '<option value="' . $value . '">' . $key . '</option>';
                            }
                            ?>
                        </select>
                        <span class="ml-1 d-none d-sm-block"></span>
                        <span class="mt-2 mt-sm-0">
                            <select name="_destino" id="_destino" class="select2 form-control w200">
                                <?php
                                foreach (DESTINO as $key => $value) {
                                    echo '<option value="' . $value . '">' . $key . '</option>';
                                }
                                ?>
                            </select></span>
                    </div>
                    <div class="col-12 pt-2 d-none">
                        <span class="fontq">Bloquear:</span>
                        <div class="custom-control custom-switch custom-control-inline">
                            <input type="checkbox" class="custom-control-input" id="_print" name="_print" value="print">
                            <label class="custom-control-label" for="_print" style="padding-top: 3px;">
                                <span id="VerPor" data-toggle="tooltip" data-placement="top" data-html="true" title="" data-original-title="<b>Incluye valores en cero.</b>" aria-describedby="tooltip">Imprimir</span>
                            </label>
                        </div>
                        <div class="custom-control custom-switch custom-control-inline">
                            <input type="checkbox" class="custom-control-input" id="_annotforms" name="_annotforms" value="annot-forms">
                            <label class="custom-control-label" for="_annotforms" style="padding-top: 3px;">Comentarios</label>
                        </div>
                        <div class="custom-control custom-switch custom-control-inline">
                            <input type="checkbox" class="custom-control-input" id="_copy" name="_copy" value="copy">
                            <label class="custom-control-label" for="_copy" style="padding-top: 3px;">Copiar</label>
                        </div>
                    </div>
                    <div class="col-12 mt-2 form-inline d-none">
                        <label class="mr-2" for="_password">Contrase&ntilde;a de apertura: </label>
                        <input type="text" class="form-control w200" maxlength="10" name="_password">
                    </div>
                    <div class="col-12 mt-2 form-inline">
                        <label for="_watermark"><span class="mr-2 d-none d-sm-block">Marca de agua: </span></label>
                        <input type="text" class="form-control w200 h40" name="_watermark" placeholder="Marca de agua">
                    </div>
                </div>
            </div>
            <div class="row pb-2">
                <div class="col-12">
                    <button class="btn btn-custom btn-sm fontq px-3 float-right btn-mobile" type="submit" id="btnExportar">Generar PDF</button>
                </div>
            </div>
        </form>
        <div class="row d-none bg-white" id="IFrame">

        </div>
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "../../../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    require __DIR__ . "../../../js/DateRangerSingle.php";
    ?>
    <script src="../../js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="../../js/select2.min.js"></script>
    <script src="js/select.js?v=<?=vjs()?>"></script>
    <script src="js/export.js?v=<?=vjs()?>"></script>
</body>

</html>
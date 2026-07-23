<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "/../../llamadas.php"; ?>
    <title><?= MODULOS['informe_otras_novedades'] ?></title>
</head>

<body class="fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '/../../nav.php';
        $svg = iconEncabezados('informes');
        $titulo = "<span style='margin-top:2px'>" . MODULOS['informe_otras_novedades'] . "</span>";
        ?>
        <!-- Encabezado -->
        <?php encabezado_mod_svgIcon('bg-custom', 'white', $svg, $titulo, ''); ?>
        <?php
        $FechaMinMax = (fecha_min_max2('FICHAS2', 'FICHAS2.FicFech'));
        $FirstDate = $FechaMinMax['min'];
        /** FirstDate */
        $FirstYear = Fech_Format_Var($FechaMinMax['min'], 'Y');
        /** FirstYear */
        $maxDate = $FechaMinMax['max'];
        // $maxDate   = date('Y-m-d');
        /** maxDate */
        $maxYear = date('Y');
        /** maxYear */
        $FechaIni = $FechaMinMax['max'];
        $FechaFin = $FechaMinMax['max'];
        ?>
        <div class="row bg-white py-2">
            <div class="col-12 col-sm-6">
                <label for="Tipo" class="mb-1 font08">
                    <span class="mr-1 d-none d-sm-none d-md-none d-lg-block mb-1 font08">Tipo de Personal: </span>
                </label>
                <select class="selectjs_tipoper" id="Tipo" name="Tipo">
                </select>
            </div>
            <div class="col-12 col-sm-6">
                <div class="d-flex justify-content-sm-end justify-content-center mt-3 mt-sm-0">
                    <button type="button" class="btn btn-outline-custom border btn-sm font08" data-toggle="collapse"
                        data-target="#rowFiltros" aria-expanded="false" aria-controls="rowFiltros">
                        Filtros
                    </button>
                    <span id="trash_allIn" title="Limpiar Filtros" class="trash align-middle mt-2 fw5 ml-1"></span>
                    <input type="text" readonly class="mx-2 form-control text-center w250 ls2" name="_dr" id="_dr">
                </div>
            </div>
        </div>
        <div class="row bg-white collapse invisible" id="rowFiltros">
            <div class="col-12 col-sm-4">
                <!-- Empresa -->
                <label for="Emp" class="mb-1 font08"><?= $labelEmprPlu ?? '' ?></label>
                <select class="form-control selectjs_empresa" id="Emp" name="Emp"
                    data-label="<?= $labelEmprPlu ?? '' ?>">
                </select>
            </div>
            <div class="col-12 col-sm-4">
                <!-- Planta -->
                <label for="Plan" class="mb-1 w100 font08"><?= $labelPlanPlu ?? '' ?></label>
                <select class="form-control selectjs_plantas" id="Plan" name="Plan"
                    data-label="<?= $labelPlanPlu ?? '' ?>">
                </select>
            </div>
            <div class="col-12 col-sm-4">
                <!-- Sector -->
                <label for="Sect" class="mb-1 w100 font08"><?= $labelSectPlu ?? '' ?></label>
                <select class="form-control selectjs_sectores" id="Sect" name="Sect"
                    data-label="<?= $labelSectPlu ?? '' ?>">
                </select>
            </div>
            <div class="col-12 col-sm-4">
                <!-- Seccion -->
                <label for="Sec2" class="mb-1 w100 font08"><?= $labelSeccPlu ?? '' ?></label>
                <select disabled class="form-control select_seccion" id="Sec2" name="Sec2"
                    data-label="<?= $labelSeccPlu ?? '' ?>">
                </select>
            </div>
            <div class="col-12 col-sm-4">
                <!-- Grupos -->
                <label for="Grup" class="mb-1 w100 font08"><?= $labelGrupPlu ?? '' ?></label>
                <select class="form-control selectjs_grupos" id="Grup" name="Grup"
                    data-label="<?= $labelGrupPlu ?? '' ?>">
                </select>
            </div>
            <div class="col-12 col-sm-4">
                <!-- Sucursal -->
                <label for="Sucur" class="mb-1 w100 font08"><?= $labelSucuPlu ?? '' ?></label>
                <select class="form-control selectjs_sucursal" id="Sucur" name="Sucur"
                    data-label="<?= $labelSucuPlu ?? '' ?>">
                </select>
            </div>
            <div class="col-12 ">
                <!-- Legajo -->
                <label for="Per" class="mb-1 w100 font08">Legajos</label>
                <div class="d-flex align-items-center">
                    <select class="form-control selectjs_personal" id="Per" name="Per">
                    </select>
                </div>
            </div>
            <div class="col-12 pb-3">
                <!-- Novedad -->
                <label for="FicNove" class="mb-1 w100 font08">Novedad</label>
                <select class="form-control selectjs_FicNove" id="FicNove" name="FicNove">
                </select>
            </div>
        </div>
        <form action="reporte/" method="POST" id="FormExportar">
            <div class="row bg-white">
                <div class="pt-2 col-12">
                    <div class="custom-control custom-switch custom-control-inline ml-1 w180">
                        <input checked type="radio" class="custom-control-input" id="PorLegajo" name="_Por" value="Leg">
                        <label class="custom-control-label w180" for="PorLegajo" style="padding-top: 3px;"><span
                                class="text-dark">Por Legajo</span></label>
                    </div>
                    <div class="custom-control custom-switch custom-control-inline ml-1 w180">
                        <input type="radio" class="custom-control-input" id="PorNombre" name="_Por" value="ApNo">
                        <label class="custom-control-label w180" for="PorNombre" style="padding-top: 3px;"><span
                                class="text-dark">Por Nombre</span></label>
                    </div>
                    <div class="custom-control custom-switch custom-control-inline ml-1 w180">
                        <input type="radio" class="custom-control-input" id="PorFecha" name="_Por" value="Fech">
                        <label class="custom-control-label w180" for="PorFecha" style="padding-top: 3px;"><span
                                class="text-dark">Por Fecha</span></label>
                    </div>
                </div>
                <div class="col-12 pt-2">
                    <div class="custom-control custom-switch custom-control-inline ml-1 w180">
                        <input type="checkbox" class="custom-control-input" id="SaltoPag">
                        <label class="custom-control-label w180" for="SaltoPag" style="padding-top: 3px;"><span
                                class="text-dark">Salto de p&aacute;gina</span></label>
                        <input type="hidden" name="_SaltoPag" id="datoSaltoPag">
                    </div>
                </div>
                <div class="col-12">
                    <a class="btn btn-link text-decoration-none text-dark font08 px-0" data-toggle="collapse"
                        href="#Permisos" role="button" aria-expanded="false" aria-controls="Permisos">
                        <span id="btnPermiso">Opciones del Reporte</span><span class="fontpp ml-2">
                            <svg class="bi mr-1" width="10" height="10" fill="currentColor">
                                <use xlink:href="../../img/bootstrap-icons.svg#chevron-down" />
                            </svg>
                        </span>
                    </a>
                </div>
                <div class="collapse pb-2 pb-0" id="Permisos">
                    <div class="col-12 form-inline">
                        <label for="_format"><span class="d-none d-sm-block mr-2">Hoja: </span></label>
                        <select name="_format" id="_format" class="select2 form-control w80">
                            <?php
                            foreach (TIPO_HOJA as $key => $value) {
                                echo "<option value=\"$value\">$key</option>";
                            }
                            ?>
                        </select>
                        <span class="ml-1"></span>
                        <select name="_orientation" id="_orientation" class="select2 form-control w120">
                            <?php
                            foreach (ORIENTACION as $key => $value) {
                                echo "<option value=\"$value\">$key</option>";
                            }
                            ?>
                        </select>
                        <span class="ml-1 d-none d-sm-block"></span>
                        <span class="mt-2 mt-sm-0">
                            <select name="_destino" id="_destino" class="select2 form-control w200">
                                <?php
                                foreach (DESTINO as $key => $value) {
                                    echo "<option value=\"$value\">$key</option>";
                                }
                                ?>
                            </select>
                    </div>
                    <div class="col-12 mt-2 form-inline">
                        <label for="_watermark"><span class="mr-2 d-none d-sm-block">Marca de agua: </span></label>
                        <input type="text" class="form-control w200 h40" name="_watermark" placeholder="Marca de agua">
                    </div>
                </div>
            </div>
            <div class="row pb-2">
                <div class="pb-2 col-12">
                    <button class="btn btn-custom btn-sm font08 px-3 float-right btn-mobile" type="submit"
                        id="btnExportar">Generar PDF</button>
                </div>
            </div>
        </form>
        <div class="row d-none" id="IFrame">

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
    <script src="js/export.js?v=<?= version_file("/informes/inforonov/js/export.js") ?>"></script>
    <script src="js/select.js?v=<?= version_file("/informes/inforonov/js/select.js") ?>"></script>
</body>

</html>
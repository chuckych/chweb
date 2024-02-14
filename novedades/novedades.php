<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../llamadas.php"; ?>
    <title>
        <?= MODULOS['novedades'] ?>
    </title>
    <style>
        .dataTables_info {
            font-size: small;
            margin-top: 0px;
        }

        th {
            font-size: .9rem;
        }

        td {
            font-size: .9rem !important;
        }
    </style>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">

        <?php require __DIR__ . '../../nav.php'; ?>
        <!-- Encabezado -->
        <?php encabezado_mod('bg-custom', 'white', 'novedades.png', MODULOS['novedades'], '') ?>
        <!-- Fin Encabezado -->
        <div class="" id="divTablas">
            <div class="row radius pt-3 mb-0 pb-0">
                <div class="col-12 col-sm-6">
                    <button <?= AttrDisabled($_SESSION["ABM_ROL"]['aNov']) ?> type="button"
                        class="btn opa7 btn-custom btn-sm fontq" data-toggle="tooltip" data-placement="right"
                        data-html="true" title="" data-original-title="<span class='font1'>Agregar Novedades</span>"
                        id="addNov">
                        Agregar
                    </button>
                    <button type="button" class="btn btn-outline-custom border btn-sm fontq Filtros" data-toggle="modal"
                        data-target="#Filtros">
                        Filtros
                    </button>
                    <button type="button" class="ml-1 btn btn-light text-success fw5 border btn-sm fontq" id="btnExcel">
                        Excel
                    </button>
                    <span id="trash_all" title="Limpiar Filtros" class="invisible trash align-middle pb-0"></span>
                    <div class="custom-control custom-switch custom-control-inline w180 ml-1 pt-2 pt-lg-0">
                        <input type="checkbox" class="custom-control-input" id="Visualizar">
                        <label class="custom-control-label" for="Visualizar" style="padding-top: 3px;"><span
                                id="VerPor"></span></label>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="d-flex justify-content-sm-end justify-content-center mt-3 mt-sm-0">
                        <input type="text" readonly class="mx-2 form-control text-center w250 ls2" name="_dr" id="_dr">
                        <button title="Actualizar Grilla" type="button" id="Refresh" disabled
                            class="btn px-2 border-0 fontq float-right bg-custom text-white opa8">
                            <svg class="bi" width="20" height="20" fill="currentColor">
                                <use xlink:href="../img/bootstrap-icons.svg#arrow-repeat" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <?php
            $FechaMinMax = (fecha_min_max('FICHAS3', 'FICHAS3.FicFech'));
            $FechaMinMax2 = (fecha_min_max2('FICHAS3', 'FICHAS3.FicFech'));
            $FechaFinEnd = $FechaMinMax2['max'];
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
            // $FechaIni = date("Y-m-d", strtotime(hoy() . "- 1 month"));
            $AnioMax = date("Y", strtotime(hoy() . "+ 2 year"));
            ?>
            <input type="hidden" hidden value="<?= $FirstYear ?>" id="AnioMin">
            <input type="hidden" hidden value="<?= $AnioMax ?>" id="AnioMax">
            <div class="row pb-sm-3 invisible" id="pagLega">
                <div class="col-12 d-flex justify-content-sm-end align-items-center animate__animated animate__fadeIn">
                    <input type="text" data-mask="000000000" reverse="true" id="Per2"
                        class="form-control mr-2 w100 mt-n1 d-none text-center" style="height: 15px;">
                    <table class="p-3 table table-borderless text-nowrap w-auto table-sm table-responsive"
                        id="GetPersonal">

                    </table>
                </div>
            </div>
            <div class="row pb-sm-3" id="pagFech">
                <div class="col-12 d-flex justify-content-sm-end animate__animated animate__fadeIn ">
                    <table class="table-responsive p-3 table table-borderless text-nowrap w-auto table-sm"
                        id="GetFechas">

                    </table>
                </div>
            </div>
            <div class="row radius mt-sm-n5">
                <div class="col-12 animate__animated animate__fadeIn">
                    <div class="invisible" id="GetNovedadesTable">
                        <table class="table table-hover text-nowrap w-100" id="GetNovedades">
                        </table>
                    </div>
                </div>
                <div class="col-12 animate__animated animate__fadeIn">
                    <div class="table-responsive" id="GetNovedadesFechaTable">
                        <table class="table table-hover text-nowrap w-100" id="GetNovedadesFecha">
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($_SESSION["ABM_ROL"]['aNov'] == 1) { ?>
            <div class="p-2 d-none" id="divaddNov">
                <?php require 'addNov.php'; ?>
            </div>
        <?php } ?>
        <div id="modales"></div>
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "../../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    require __DIR__ . "../../js/DateRanger.php";
    /** INCLUIMOS LIBRERÍAS y script DATATABLES */
    require __DIR__ . "../../js/DataTable.php";
    require 'modal_Filtros.html';
    ?>
    <script src="../js/bootbox.min.js"></script>
    <script src="../js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="../vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="../js/select2.min.js"></script>
    <script src="../js/select2-es.js"></script>
    <script src="js/data.js?<?= version_file("/novedades/js/data.js") ?>"></script>
    <script src="js/select.js?<?= version_file("/novedades/js/select.js") ?>"></script>
    <script src="js/selectadd.js?<?= version_file("/novedades/js/selectadd.js") ?>"></script>
    <script src="js/trashSelect.js?<?= version_file("/novedades/js/trashSelect.js") ?>"></script>
    <script src="js/NovXLS.js?<?= version_file("/novedades/js/NovXLS.js") ?>"></script>

</body>

</html>
<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "/../llamadas.php"; ?>
    <title>
        <?= MODULOS['fichadas'] ?>
    </title>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '/../nav.php'; ?>
        <!-- Encabezado -->
        <?php encabezado_mod('bg-custom', 'white', 'fichadas3.png', MODULOS['fichadas'], '') ?>
        <!-- Fin Encabezado -->
        <!-- <form action="" method="GET" name="fichadas" class="" onsubmit="ShowLoading()" id='range'> -->
        <div class="row bg-white radius pt-3 mb-0 pb-0">
            <div class="col-12 col-sm-6">
                <button type="button" class="btn btn-outline-custom border btn-sm fontq Filtros" data-toggle="modal"
                    data-target="#Filtros">
                    Filtros
                </button>
                <button type="button" class="ml-1 btn btn-light text-success fw5 border btn-sm fontq" id="btnExcel">
                    Excel
                </button>
                <span id="trash_all" title="Limpiar Filtros" class="invisible trash align-middle pb-0"></span>
                <div class="custom-control custom-switch custom-control-inline w180 ml-1 mt-sm-0 mt-2">
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
        $FechaMinMax = (fecha_min_max('REGISTRO', 'REGISTRO.RegFeAs'));
        // print_r($FechaMinMax).exit;
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
        ?>
        <div id="tablas">
            <div class="row bg-white pb-sm-3" id="pagLega">
                <div
                    class="col-12 d-flex justify-content-sm-end align-items-center animate__animated animate__fadeIn table-responsive">
                    <table class="table table-borderless text-nowrap w-auto table-sm invisible" id="GetPersonal">
                    </table>
                </div>
            </div>
            <div class="row bg-white pb-sm-3" id="pagFech">
                <div class="col-12 d-flex justify-content-sm-end animate__animated animate__fadeIn table-responsive">
                    <table class="table table-borderless text-nowrap w-auto table-sm invisible" id="GetFechas"></table>
                </div>
            </div>
            <div class="row bg-white radius mt-sm-n5">
                <div class="col-12 animate__animated animate__fadeIn">
                    <div class="table-responsive" id="GetFichadasTable" style="display: none;">
                        <table class="table text-nowrap w-100 border shadow table-hover p-2" id="GetFichadas">
                        </table>
                    </div>
                </div>
                <div class="col-12 animate__animated animate__fadeIn">
                    <div class="table-responsive invisible" id="GetFichadasFechaTable">
                        <table class="table text-nowrap w-100 border shadow p-2 table-hover" id="GetFichadasFecha">
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "/../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    require __DIR__ . "/../js/DateRanger.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    require __DIR__ . "/../js/DataTable.php";
    require 'modal_Filtros.html';
    ?>
    <script src="../js/bootbox.min.js"></script>
    <script src="../js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="../vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="../js/select2.min.js"></script>
    <script src="js/data-min.js?<?= version_file("/fichadas/js/data-min.js") ?>"></script>
    <script src="js/toExcel-min.js?<?= version_file("/fichadas/js/toExcel-min.js") ?>"></script>

</body>

</html>
<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../llamadas.php"; ?>
    <title><?= MODULOS['ctacte'] ?>: Novedades</title>
    <style>
        .dataTables_info {
            font-size: small;
            margin-top: 0px;
        }
    </style>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '../../nav.php'; ?>
        <!-- Encabezado -->
        <?php encabezado_mod('bg-custom', 'white', 'ctacte.png', 'Cta Cte: Novedades', '') ?>
        <!-- Fin Encabezado -->
        <div class="row bg-white radius pt-3 mb-0 pb-0">
            <div class="col-12 col-sm-6">
                <button type="button" class="btn btn-outline-custom border btn-sm fontq Filtros" data-toggle="modal" data-target="#Filtros">
                    Filtros
                </button>
                <button type="button" class="ml-1 btn btn-light text-success fw5 border btn-sm fontq" id="btnExcel">
                    Excel
                </button>
                <span id="trash_all" title="Limpiar Filtros" class="invisible trash align-middle pb-0"></span>
                <div class="custom-control custom-switch custom-control-inline w250 ml-1">
                    <input type="checkbox" class="custom-control-input" id="Visualizar">
                    <label class="custom-control-label" for="Visualizar" style="padding-top: 3px;"><span id="VerPor"></span></label>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="d-flex justify-content-sm-end justify-content-center mt-3 mt-sm-0">
                    <input type="text" readonly class="d-none mx-2 form-control text-center w250 ls2" name="_dr" id="_dr">
                    <button type="button" id="Refresh" disabled class="btn btn-link text-decoration-none px-2 border-0 fontq float-right text-secondary">
                       Actualizar Grilla
                    </button>
                </div>
            </div>
        </div>
        <?php
        $FechaMinMax = (fecha_min_max('FICHAS3', 'FICHAS3.FicFech'));
        $FirstDate = $FechaMinMax['min'];
        /** FirstDate */
        $FirstYear = Fech_Format_Var($FechaMinMax['min'], 'Y');
        /** FirstYear */
        $maxDate   = $FechaMinMax['max'];
        /** maxDate */
        $maxYear   = date('Y');
        /** maxYear */
        $FechaIni = $FechaMinMax['min'];
        $FechaFin = $FechaMinMax['max'];
        // $FechaIni = date("Y-m-d", strtotime(hoy() . "- 1 month"));
        // $FechaFin = date("Y-m-d", strtotime(hoy() . "- 0 days"));
        ?>
        <div class="row bg-white pb-sm-3 d-none" id="pagLega">
            <div class="col-12 d-flex justify-content-sm-end align-items-center animate__animated animate__fadeIn">
                <input type="text" data-mask="000000000" reverse="true" id="Per2" class="form-control mr-2 w100 mt-n2 d-none text-center" style="height: 15px;">
                <table class="table table-borderless text-nowrap w-auto table-sm" id="GetPersonal">

                </table>
            </div>
        </div>
        <div class="row bg-white pb-sm-3" id="pagFech">
            <div class="col-12 d-flex justify-content-sm-end animate__animated animate__fadeIn">
                <table class="table table-borderless text-nowrap w-auto table-sm" id="GetFechas">

                </table>
            </div>
        </div>
        <div class="row bg-white radius mt-sm-n5">
            <div class="col-12 animate__animated animate__fadeIn">
                <div class="table-responsive d-none" id="GetNovedadesTable">
                    <table class="table table-hover text-nowrap w-100" id="GetNovedades">
                        <thead class="">
                            <tr>
                                <th class="text-center">AÑO</th>
                                <th class="text-center">COD</th>
                                <th>NOVEDAD</th>
                                <th class="text-center" title="DISPONIBLES">DISP.</th>
                                <th class="text-center" title="CONSUMIDOS">CONSUM..</th>
                                <th class="text-center" title="TOTAL">TOTAL</th>
                                <th class="text-center" title="CONTINGENTE PERIODO ACTUAL">CONTING.</th>
                                <th class="text-center" title="SALDO ANTERIOR">SALD. ANT.</th>
                                <th class="">PERIODO</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="col-12">
                <div class="table-responsive" id="GetNovedadesFechaTable">
                    <table class="table table-hover text-nowrap w-100" id="GetNovedadesFecha">
                        <thead class="">
                            <tr>
                                <th class="text-center">AÑO</th>
                                <th class="">LEGAJO</th>
                                <th>NOMBRE</th>
                                <th class="text-center" title="DISPONIBLES">DISP.</th>
                                <th class="text-center" title="CONSUMIDOS">CONSUM..</th>
                                <th class="text-center" title="TOTAL">TOTAL</th>
                                <th class="text-center" title="CONTINGENTE PERIODO ACTUAL">CONTING.</th>
                                <th class="text-center" title="SALDO ANTERIOR">SALD. ANT.</th>
                                <th class="">PERIODO</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "../../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    require __DIR__ . "../../js/DateRanger.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    require __DIR__ . "../../js/DataTable.php";
    require 'modal_Filtros.html';
    ?>
    <script src="../js/bootbox.min.js"></script>
    <script src="../js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="../vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="../js/select2.min.js"></script>
    <script src="js/data.js"></script>
    <script src="js/select.js"></script>
    <script src="js/trashSelect.js"></script>
    <script src="js/ctacteXLS.js"></script>
</body>

</html>
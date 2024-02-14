<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../llamadas.php"; ?>
    <title>
        <?= MODULOS['horas'] ?>
    </title>
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
        <?php encabezado_mod('bg-fich', 'white', 'reloj.png', MODULOS['horas'], '') ?>
        <!-- Fin Encabezado -->
        <!-- <form action="" method="GET" name="fichadas" class="" onsubmit="ShowLoading()" id='range'> -->
        <div class="row bg-white radius pt-3 mb-0 pb-0">
            <div class="col-12 col-sm-6">
                <button type="button" class="text-pop-up-top btn btn-outline-custom border btn-sm fontq Filtros"
                    data-toggle="modal" data-target="#Filtros">
                    Filtros
                </button>
                <button type="button" class="ml-1 btn btn-light text-success fw5 border btn-sm fontq" id="btnExcel">
                    Excel
                </button>
                <span id="trash_all" title="Limpiar Filtros" class="invisible trash align-middle pb-0"></span>
                <div class="custom-control custom-switch custom-control-inline w180 ml-1">
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
        $FechaMinMax = (fecha_min_max('FICHAS1', 'FICHAS1.FicFech'));
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
        <div class="row bg-white pb-sm-3 invisible" id="pagLega">
            <div class="col-12 d-flex justify-content-sm-end animate__animated animate__fadeIn">
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
                <div class="invisible" id="GetHorasTable">
                    <table class="table text-nowrap w-100 " id="GetHoras">
                        <thead class="font08">
                            <tr>
                                <th class="">Fecha</th>
                                <th class="">Día</th>
                                <th class="">Horario</th>
                                <th class="text-center">Hora</th>
                                <th class="">Descripción</th>
                                <th class="text-center" title="Horas Hechas">Hechas</th>
                                <th class="text-center" title="Horas Autorizadas">Autor.</th>
                                <th class="">Observ</th>
                                <th class="">Motivo</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="col-12 animate__animated animate__fadeIn">
                <div class="table-responsive invisible" id="GetHorasFechaTable">
                    <table class="table text-nowrap w-100" id="GetHorasFecha">
                        <thead class="font08">
                            <tr>
                                <th class="">Legajo</th>
                                <th class="ApNo">Nombre</th>
                                <th class="">Horario</th>
                                <th class="text-center">Hora</th>
                                <th class="">Descripción</th>
                                <th class="text-center">Hechas</th>
                                <th class="text-center">Autor.</th>
                                <th class="">Observ</th>
                                <th class="">Motivo</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="d-none mt-2 mb-4 shadow-sm border" id="div-horas-total">
            <div class="row">
                <div class="col-12">
                    <p class="p-3 pb-0 pt-3 m-0 font09 bg-light border-bottom">Totales:</p>
                    <table class="table w-100 table-responsive text-nowrap font09 p-2 mb-0 pb-0" id="tabla-horas-total">
                    </table>
                    <table class="d-none table w-100 table-responsive text-nowrap font09 p-2 mb-0 pb-0"
                        id="tabla-horas-total2">
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
    <script src="../vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="../js/select2.min.js"></script>
    <script src="js/proceso.js?<?= version_file("/horas/js/proceso.js") ?>"></script>
    <script src="js/select.js?<?= version_file("/horas/js/select.js") ?>"></script>
    <script src="js/trash-select.js?<?= version_file("/horas/js/trash-select.js") ?>"></script>
    <script src="js/HorXLS.js?<?= version_file("/horas/js/HorXLS.js") ?>"></script>
</body>

</html>
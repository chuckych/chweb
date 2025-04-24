<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "/../llamadas.php"; ?>
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
        <?php require __DIR__ . '/../nav.php'; ?>
        <!-- Encabezado -->
        <?php encabezado_mod('bg-fich', 'white', 'reloj.png', MODULOS['horas'], '') ?>
        <!-- Fin Encabezado -->
        <!-- <form action="" method="GET" name="fichadas" class="" onsubmit="ShowLoading()" id='range'> -->
        <div class="invisible loader-in" id="tablas">
            <div class="row bg-white radius pt-3 mb-0 pb-0">
                <div class="col-12 col-sm-6 d-inline-flex">
                    <button type="button" class="text-pop-up-top btn btn-outline-custom border btn-sm fontq Filtros"
                        data-toggle="modal" data-target="#Filtros">
                        Filtros
                    </button>
                    <button type="button"
                        class="ml-1 btn btn-light text-success fw4 border btn-sm fontq hint--top hint--rounded hint--no-arrow hint--default hint--no-shadow hint--no-animate"
                        id="btnExcel" aria-label="Exportar a Excel">
                        Excel
                    </button>
                    <span id="trash_all" title="Limpiar Filtros" class="invisible trash align-middle pb-0"></span>
                    <div class="d-flex align-items-center ml-2 border p-1 radius bg-light">
                        <div class="btn-group btn-group-toggle" data-toggle="buttons">
                            <label
                                class="btn btn-sm fontq btn-outline-custom border-0 w80 hint--top hint--rounded hint--no-arrow hint--default hint--no-shadow hint--no-animate"
                                aria-label="Visualizar por Legajo">
                                <input type="radio" name="VPor" id="VLegajo" value="0"> Legajo
                            </label>
                            <label
                                class="btn btn-sm fontq btn-outline-custom border-0 w80 hint--top hint--rounded hint--no-arrow hint--default hint--no-shadow hint--no-animate"
                                aria-label="Visualizar por Fechas">
                                <input type="radio" name="VPor" id="VFecha" value="1"> Fecha
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="input-group w-100 d-inline-flex justify-content-end">
                        <div class="d-flex justify-content-sm-end justify-content-center mt-3 mt-sm-0">
                            <input type="text" class="mx-2 form-control text-center w250" name="_dr" id="_dr">
                            <button title="Actualizar Grilla" type="button" id="Refresh"
                                class="btn px-2 border-0 fontq float-right bg-custom text-white opa8 hint--top hint--rounded hint--no-arrow hint--default hint--no-shadow hint--no-animate"
                                aria-label="Actualizar Grilla">
                                <svg class="bi" width="20" height="20" fill="currentColor">
                                    <use xlink:href="../img/bootstrap-icons.svg#arrow-repeat" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="tablas2">
                <div class="row bg-white pb-sm-3" id="pagLega" style="display:none">
                    <div class="col-12 d-flex justify-content-sm-end">
                        <table class="table table-borderless text-nowrap w-auto table-sm" id="GetPersonal">

                        </table>
                    </div>
                </div>
                <div class="row bg-white pb-sm-3" id="pagFech" style="display:none">
                    <div class="col-12 d-flex justify-content-sm-end">
                        <table class="table table-borderless text-nowrap w-auto table-sm" id="GetFechas">

                        </table>
                    </div>
                </div>
                <div class="row bg-white radius mt-sm-n5">
                    <div class="col-12">
                        <div class="" id="GetHorasTable" style="display:none">
                            <table class="table text-nowrap w-100 " id="GetHoras">
                                <thead class="font09">
                                </thead>
                            </table>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="table-responsive" id="GetHorasFechaTable" style="display:none">
                            <table class="table text-nowrap w-100" id="GetHorasFecha">
                                <thead class="font09">
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="d-none mt-2 mb-4 shadow-sm border radius animate__animated animate__fadeIn"
                    id="div-horas-total">
                    <div class="row">
                        <div class="col-12">
                            <p class="p-3 pb-0 pt-3 m-0 font09 bg-light border-bottom" id="totales-title">Totales:</p>
                            <table class="table w-100 table-responsive text-nowrap font09 p-2 mb-0 pb-0"
                                id="tabla-horas-total">
                            </table>
                            <table class="d-none table w-100 table-responsive text-nowrap font09 p-2 mb-0 pb-0"
                                id="tabla-horas-total2">
                            </table>
                        </div>
                    </div>
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
    require __DIR__ . "/../js/DateRangePicker.php";
    /** INCLUIMOS LIBRERÍAS y script DATATABLE */
    require __DIR__ . "/../js/DataTable.php";
    require 'modal_Filtros.html';
    ?>
    <script src="../vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="../js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="../js/select2.min.js"></script>
    <script src="../js/select2-es.js"></script>
    <script src="js/proceso.js?<?= version_file("/horas/js/proceso.js") ?>"></script>
    <script src="js/select.js?<?= version_file("/horas/js/select.js") ?>"></script>
    <script src="js/trash-select.js?<?= version_file("/horas/js/trash-select.js") ?>"></script>
    <script src="js/HorXLS.js?<?= version_file("/horas/js/HorXLS.js") ?>"></script>
</body>

</html>
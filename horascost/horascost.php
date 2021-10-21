<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../llamadas.php"; ?>
    <title><?= MODULOS['horascost'] ?></title>
    <style>
        .dataTables_info {
            font-size: small;
            margin-top: 0px;
        }

        .dtrg-level-0 td {
            padding-top: 5px !important;
            padding-bottom: 5px !important;
            border-top: 1px solid #cecece !important;
            border-bottom: 1px solid #cecece !important;
            font-weight: 500 !important;
        }

        .dtrg-level-1 td {
            padding-top: 5px !important;
            padding-bottom: 5px !important;
            /* border-bottom: 1px solid #cecece !important; */
            font-weight: 500 !important;
            background-color: #f8f9fa;
        }

        .table td,
        .table th {
            border-top: 0px solid #dee2e6;
        }
    </style>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '../../nav.php'; ?>
        <!-- Encabezado -->
        <?php encabezado_mod('bg-custom', 'white', 'horascost.png', MODULOS['horascost'], '') ?>
        <!-- Fin Encabezado -->
        <!-- <form action="" method="GET" name="fichadas" class="" onsubmit="ShowLoading()" id='range'> -->
        <?php
        $FechaMinMax = (fecha_min_max('FICHAS01', 'FICHAS01.FicFech'));
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
        $ocultar = '';
        if ($FechaMinMax['min'] == '') {
            echo '<div class="fontq py-4">No hay datos . . .</div>';
            $ocultar = 'd-none';
        }
        ?>
        <div class="<?= $ocultar ?>">
            <div class="row bg-white radius pt-3 mb-0 pb-0">
                <div class="col-12 col-sm-6">
                    <button type="button" class="btn btn-outline-custom border btn-sm fontq Filtros" data-toggle="modal" data-target="#Filtros">
                        Filtros
                    </button>
                    <button type="button" class="ml-1 btn btn-light text-success fw5 border btn-sm fontq" id="btnExcel">
                        Excel
                    </button>
                    <span id="trash_all" title="Limpiar Filtros" class="invisible trash align-middle pb-0"></span>
                    <div class="custom-control custom-switch custom-control-inline w180 ml-1">
                        <input type="checkbox" class="custom-control-input" id="Visualizar">
                        <label class="custom-control-label" for="Visualizar" style="padding-top: 3px;"><span id="VerPor"></span></label>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="d-flex justify-content-sm-end justify-content-center mt-3 mt-sm-0">
                        <input type="text" readonly class="mx-2 form-control text-center w250 ls2" name="_dr" id="_dr">
                        <button title="Actualizar Grilla" type="button" id="Refresh" disabled class="btn px-2 border-0 fontq float-right bg-custom text-white opa8">
                            <svg class="bi" width="20" height="20" fill="currentColor">
                                <use xlink:href="../img/bootstrap-icons.svg#arrow-repeat" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

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
            <div class="row bg-white radius mt-sm-n3">
                <div class="col-12 animate__animated animate__fadeIn">
                    <div class="table-responsive invisible" id="GetHorasTable">
                        <table class="table table-borderless text-nowrap w-100" id="GetHoras">
                            <thead class="border-bottom text-uppercase fontp">
                                <tr class="">
                                    <th class=""></th>
                                    <th class="">FECHA</th>
                                    <th class="" title="Horario y Desde">Horario</th>
                                    <th class="">Tipo Hora</th>
                                    <th class="text-center" title="Horas Hechas y Autorizadas">Horas</th>
                                    <th class="text-center" title="">Costo</th>
                                    <th class="" title="Tarea de producción">Tarea</th>
                                    <th class="" title="Planta">Planta</th>
                                    <th class="" title="Sucursal">Sucursal</th>
                                    <th class="" title="Grupo">Grupo</th>
                                    <th class="text-nowrap" title="Sector / Secci&oacute;n">Sector / Secci&oacute;n</th>
                                    <!-- <th class="" title="Secci&oacute;">Secci&oacute;n</th> -->
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <div class="table-responsive pb-3 invisible" id="GetHorasTotalesTable">
                        <p class="fontq mb-2 mt-2">TOTALES: </p>
                        <table class="table text-nowrap w-auto" id="GetHorasTotales">
                        </table>
                    </div>
                </div>
                <div class="col-12 animate__animated animate__fadeIn">
                    <div class="table-responsive invisible" id="GetHorasFechaTable">
                        <table class="table table-borderless text-nowrap w-100" id="GetHorasFecha">
                            <thead class="border-bottom text-uppercase fontp">
                                <tr class="">
                                    <th class=""></th>
                                    <th class="">LEGAJO</th>
                                    <th class="" title="Horario y Desde">Horario</th>
                                    <!-- <th class="">DESDE</th> -->
                                    <th class="">Tipo Hora</th>
                                    <!-- <th class=""></th> -->
                                    <th class="text-center" title="Horas Hechas y Autorizadas">Horas</th>
                                    <th class="d-none" title="">Tarea</th>
                                    <th class="d-none" title="">CodTar</th>
                                    <th class="text-center" title="">Costo</th>
                                    <!-- <th class="text-center" title="Horas Autorizadas">Autor.</th> -->
                                    <th class="" title="Tarea">Tarea Prod.</th>
                                    <th class="" title="Planta">Planta</th>
                                    <th class="" title="Sucursal">Sucursal</th>
                                    <th class="" title="Grupo">Grupo</th>
                                    <th class="text-nowrap" title="Sector / Secci&oacute;n">Sector / Secci&oacute;n</th>
                                    <!-- <th class="" title="Secci&oacute;">Secci&oacute;n</th> -->
                                </tr>
                            </thead>
                        </table>
                        <div class="table-responsive pb-5" id="GetHorasFechaTotalesTable">
                            <p class="fontq mb-2 mt-2">TOTALES: </p>
                            <table class="table text-nowrap w-auto" id="GetHorasFechaTotales">
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
    require __DIR__ . "../../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    require __DIR__ . "../../js/DateRanger.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    require __DIR__ . "../../js/DataTable.php";
    require 'modal_Filtros.html';
    ?>
    <script src="https://cdn.datatables.net/rowgroup/1.1.2/js/dataTables.rowGroup.min.js"></script>
    <script src="../vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="../js/select2.min.js"></script>
    <script src="../js/js-cookie/src/js.cookie.js"></script>
    <script src="js/data-min.js?v=<?= vjs() ?>"></script>
    <script src="js/select.js?v=<?= vjs() ?>"></script>
    <script src="js/trash-select.js?v=<?= vjs() ?>"></script>
    <script src="js/HorXLS.js?v=<?= vjs() ?>"></script>
</body>

</html>
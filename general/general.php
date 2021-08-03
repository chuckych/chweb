<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../llamadas.php"; ?>
    <title>Control <?= MODULOS['general'] ?></title>
    <style>
        .dataTables_info {
            font-size: small;
            margin-top: 0px;
        }

        .dt-button {
            display: none;
        }

        .well {
            margin-bottom: 0px !important;
        }

        @media all and (max-width:480px) {
            .custom-select {
                display: none !important;
            }

            .dataTables_filter {
                display: none !important;
            }
        }

        #GetGeneralFecha_filter label {
            margin: 0px !important;
        }
    </style>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '../../nav.php'; ?>
        <!-- Encabezado -->
        <?php encabezado_mod('bg-custom', 'white', 'general.png', 'Control ' . MODULOS['general'], '') ?>
        <!-- Fin Encabezado -->
        <!-- <form action="" method="GET" name="fichadas" class="" onsubmit="ShowLoading()" id='range'> -->
        <div class="row bg-white radius pt-3 mb-0 pb-0">
            <div class="col-12 col-sm-6 d-inline-flex d-flex align-items-center">
                <button type="button" class="btn btn-outline-custom border fontq Filtros" data-toggle="modal" data-target="#Filtros">
                Filtros
                </button>
                <button type="button" class="btn btn-outline-custom border fontq Exportar ml-1" data-toggle="modal" data-target="#Exportar">
                Reporte
                </button>
                <span id="trash_all" title="Limpiar Filtros" class="mx-2 invisible bi bi-trash fontq text-secondary pointer"></span>
                <div class="custom-control custom-switch custom-control-inline ml-1 d-flex align-items-center">
                    <input type="checkbox" class="custom-control-input" id="Visualizar">
                    <label class="custom-control-label" for="Visualizar" style="padding-top: 3px;">
                        <span id="VerPor"></span>
                        <span id="VerPorM"></span>
                    </label>
                </div>
                <div class="custom-control custom-switch custom-control-inline ml-1 d-flex align-items-center">
                    <input checked type="checkbox" class="custom-control-input" id="FicDiaL">
                    <label class="custom-control-label" for="FicDiaL" style="padding-top: 3px;" title="Al desactivar muestra días laborales, francos y feriados. Al estar activo solo días laborales">
                        <span class="text-dark d-none d-lg-block">D&iacute;a Laboral</span>
                        <span class="text-dark d-block d-lg-none">Laboral</span>
                    </label>
                    <input type="hidden" name="" id="datoFicDiaL">
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="d-flex justify-content-sm-end justify-content-center mt-3 mt-sm-0">
                    <input type="text" readonly class="mx-1 form-control text-center w250 ls2 h40" name="_dr" id="_dr">
                    <button title="Actualizar Grilla" type="button" id="Refresh" disabled class="btn border-0 float-right bg-custom text-white opa8">
                        <i class="bi bi-arrow-repeat"></i>
                    </button>
                </div>
            </div>
        </div>
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
        <div class="row bg-white pb-sm-3 invisible" id="pagLega">
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
                <div class="table-responsive invisible" id="GetGeneralTable">
                    <table class="table table-hover text-nowrap w-100" id="GetGeneral">
                        <thead class="">
                            <tr>
                                <th title="Detalle del registro" class="text-center"></th>
                                <th class="">FECHA</th>
                                <th class="text-nowrap">HORARIO</th>
                                <th class="text-center ls1" title="Primer y última Fichada">ENT-SAL</th>
                                <th>TIPO HORA</th>
                                <th class="text-center " title="Horas Autorizadas">HS AUTO</th>
                                <th class="text-center " title="Horas Calculadas">HS HECHAS</th>
                                <th title="Descripción de Novedad">NOVEDADES</th>
                                <th class="" title="Horas de la Novedad">NOV HS</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="col-12 animate__animated animate__fadeIn">
                <div class="table-responsive" id="GetGeneralFechaTable">
                    <table class="table text-nowrap w-100" id="GetGeneralFecha">
                        <thead class="">
                            <tr>
                                <th title="Detalle del registro" class="text-center"></th>
                                <th class="">LEGAJO</th>
                                <th class="text-nowrap">HORARIO</th>
                                <th class="text-center ls1" title="Primer y última Fichada">ENT-SAL</th>
                                <th>TIPO HORA</th>
                                <th class="text-center " title="Horas Autorizadas">HS AUTO</th>
                                <th class="text-center " title="Horas Calculadas">HS HECHAS</th>
                                <th title="Descripción de Novedad">NOVEDADES</th>
                                <th class="" title="Horas de la Novedad">NOV HS</th>
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
    require 'modal_Exportar.php';
    require __DIR__ . "../ModalGeneral.php";
    ?>
    <script src="../js/bootbox.min.js"></script>
    <script src="../js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="../vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script>
   
    </script>
    <script src="../js/select2.min.js"></script>
    <script src="js/data.js?v=<?=vjs()?>"></script>
    <script src="js/proceso-min.js?v=<?=vjs()?>"></script>
    <!-- <script src="js/proceso.js"></script> -->
    <script src="js/export.js?v=<?=vjs()?>"></script>
    <script src="js/select.js?v=<?=vjs()?>"></script>
    <script src="js/trash-select.js?v=<?=vjs()?>"></script>
    <!-- <script src="js/generalXLS.js"></script> -->
</body>

</html>
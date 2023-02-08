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

        .page-link {
            border: 0 px solid #cecece !important;
        }

        .divTipo .select2-container {
            width: 140px !important;
        }

        .bootbox.fade~.modal-backdrop.fade {
            opacity: 0 !important;
            filter: alpha(opacity=0) !important;
        }
        .bootbox {
            backdrop-filter: blur(1px) !important;
        }

        .bootbox-confirm {
            margin-top: 1.5rem !important;
        }

        .bootbox .modal-content {
            border: 0px solid #fafafa !important;
            box-shadow: -1px 5px 29px -7px rgba(0, 0, 0, 0.4);
            -webkit-box-shadow: -1px 5px 29px -7px rgba(0, 0, 0, 0.4);
            -moz-box-shadow: -1px 5px 29px -7px rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            border-radius: 0px !important;
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
                <span id="trash_all" data-titler="Limpiar Filtros" class="mx-2 invisible bi bi-trash fontq text-secondary pointer"></span>
                <div class="custom-control custom-switch custom-control-inline ml-1 d-flex align-items-center">
                    <input type="checkbox" class="custom-control-input" id="Visualizar">
                    <label class="custom-control-label" for="Visualizar" style="padding-top: 3px;">
                        <span id="VerPor"></span>
                        <span id="VerPorM"></span>
                    </label>
                </div>
                <div class="custom-control custom-switch custom-control-inline ml-1 d-flex align-items-center" data-titler='Activo : Solo días laborales; Inactivo: Todos'>
                    <input type="checkbox" class="custom-control-input" id="FicDiaL">
                    <label class="custom-control-label" for="FicDiaL" style="padding-top: 3px;">
                        <span class="text-dark d-none d-lg-block">D&iacute;a Laboral</span>
                        <span class="text-dark d-block d-lg-none">Laboral</span>
                    </label>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="d-flex justify-content-sm-end justify-content-center mt-3 mt-sm-0">
                    <span data-titlel="Filtrar Fechas"><input type="text" readonly class="mr-1 form-control text-center w250 ls1 h40" name="_dr" id="_dr"></span>
                    <button data-titlel="Actualizar Grilla" type="button" id="Refresh" disabled class="btn float-right btn-custom">
                        <i class="bi bi-arrow-repeat"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php
        $FechaMinMax = (fecha_min_max('FICHAS', 'FICHAS.FicFech'));
        $FechaMinMax2 = (fecha_min_max2('FICHAS3', 'FICHAS3.FicFech'));
        $FechaMinMax3 = (fecha_min_max2('FICHAS2', 'FICHAS2.FicFech'));
        $FechaFinEnd = $FechaMinMax2['max'];
        $FechaFinEnd = $FechaMinMax3['max'];
        $FechaFinEnd = FechaString($FechaMinMax3['max']) > FechaString($FechaMinMax2['max']) ? ($FechaMinMax3['max']) : ($FechaMinMax2['max']);
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
        <input type="hidden" value="<?= $FechaFin ?>" id="FechaFin">
        <div class="row bg-white pb-sm-3 invisible" id="pagLega">
            <div class="table-responsive">
                <div class="col-12 d-flex justify-content-sm-end align-items-center animate__animated animate__fadeIn">
                    <input type="text" data-mask="000000000" reverse="true" id="Per2" class="form-control mr-2 w100 mt-n2 d-none text-center" style="height: 15px;">
                    <table class="table table-borderless text-nowrap w-auto table-sm" id="GetPersonal">

                    </table>
                </div>
            </div>
        </div>
        <div class="row bg-white pb-sm-3" id="pagFech">
            <div class="table-responsive">
                <div class="col-12 d-flex justify-content-sm-end animate__animated animate__fadeIn">
                    <table class="table table-borderless text-nowrap w-auto table-sm" id="GetFechas">

                    </table>
                </div>
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
                                <th class="text-center" title="Horas Autorizadas">HS AUTO</th>
                                <th class="text-center" title="Horas Calculadas">HS HECHAS</th>
                                <th title="Descripción de Novedad">NOVEDADES</th>
                                <th class="" title="Horas de la Novedad">NOV HS</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            <div class="col-12 animate__animated animate__fadeIn">
                <div class="table-responsive invisible" id="GetGeneralFechaTable">
                    <table class="table text-nowrap w-100" id="GetGeneralFecha">
                        <thead>
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
    <script src="../js/select2.min.js"></script>
    <script src="js/data-min.js?<?=version_file("/general/js/data-min.js")?>"></script>
    <script src="js/proceso-min.js?<?=version_file("/general/js/proceso-min.js")?>"></script>
    <script src="js/select-min.js?<?=version_file("/general/js/select-min.js")?>"></script>
    <script src="js/trash-select-min.js?<?=version_file("/general/js/trash-select-min.js")?>"></script>
    <script src="js/export-min.js?<?=version_file("/general/js/export-min.js")?>"></script>
    <script>
        $(document).ready(function() {
            $.get('/<?= HOMEHOST ?>/status_ws.php', {
                status: 'ws',
            }).done(function(data) {
                $.notifyClose();
                notify(data.Mensaje, 'info', 2000, 'right')
            });
        });
    </script>
</body>

</html>
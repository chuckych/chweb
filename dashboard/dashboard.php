<!doctype html>
<html lang="es">

<head>
    <?php require __DIR__ . "../../llamadas.php"; ?>
    <title><?= MODULOS['Dashboard'] ?></title>
    <style>
        /* .highcharts-figure, .highcharts-data-table table {
  min-width: 310px; 
  max-width: 800px;
  margin: 1em auto;
} */

        #container {
            height: 400px;
        }

        .highcharts-credits{
            display: none;
        }
        .highcharts-figure,
        .highcharts-data-table table {
            min-width: 320px;
            max-width: 660px;
            margin: 1em auto;
        }

        .highcharts-data-table table {
            font-family: Verdana, sans-serif;
            border-collapse: collapse;
            border: 1px solid #EBEBEB;
            margin: 10px auto;
            text-align: center;
            width: 100%;
            max-width: 500px;
        }

        .highcharts-data-table caption {
            padding: 1em 0;
            font-size: 1.2em;
            color: #555;
        }

        .highcharts-data-table th {
            font-weight: 600;
            padding: 0.5em;
        }

        .highcharts-data-table td,
        .highcharts-data-table th,
        .highcharts-data-table caption {
            padding: 0.5em;
        }

        .highcharts-data-table thead tr,
        .highcharts-data-table tr:nth-child(even) {
            background: #f8f8f8;
        }

        .highcharts-data-table tr:hover {
            background: #f1f7ff;
        }
    </style>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

</head>

<body class="animate__animated animate__fadeIn">
    <div class="container shadow pb-2">
        <?php require __DIR__ . '../../nav.php'; ?>
        <?=
        encabezado_mod2('bg-custom', 'white', 'graph-up',  MODULOS['Dashboard'], '25', 'text-white mr-2');
        ?>
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
        // $FechaIni = date("Y-m-d", strtotime(hoy() . "- 1 month"));
        $AnioMax = date("Y", strtotime(hoy() . "+ 1 year"));
        ?>
        <input type="hidden" hidden value="<?= $FirstYear ?>" id="AnioMin">
        <input type="hidden" hidden value="<?= $AnioMax ?>" id="AnioMax">
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-sm-end justify-content-center mt-3">
                    <div class="input-group w350">
                        <div class="input-group-prepend">
                            <span class="input-group-text border-0">
                                <svg class="bi mr-1" width="18" height="18" fill="currentColor">
                                    <use xlink:href="../img/bootstrap-icons.svg#calendar-range" />
                                </svg>
                            </span>
                        </div>
                        <input type="text" readonly class="form-control text-center border-0 radius-0 ls2 h40" name="_dr" id="_dr">
                        <button title="Actualizar" type="button" id="Refresh" class="btn fontq border-0 btn-default" style=" border-top-left-radius: 0em 0em; border-bottom-left-radius: 0em 0em;background-color: #E9ECEF">
                            <svg class="bi mr-1 border-0 arrow-repeat" width="18" height="18" fill="currentColor">
                                <use xlink:href="../img/bootstrap-icons.svg#arrow-repeat" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-12 mt-3" id="chartNove">
                <div class="shadow-sm p-3 ChartsDiv">
                    <p class="fontq text-dark fw5">Novedades</p>
                    <canvas id="myChart3" class="" height="100"></canvas>
                </div>
            </div>
            <div class="col-12 col-sm-6 mt-3" id="charNoveT">
                <div class="shadow-sm p-3 ChartsDiv">
                    <span class="fontq text-dark fw5">Novedades por Tipo</span>
                    <figure class="highcharts-figure">
                        <div id="ChartTipoNove"></div>
                    </figure>
                </div>
            </div>
            <div class="col-12 col-sm-6 mt-3" id="chartNoveH">
                <div class="shadow-sm p-3 ChartsDiv">
                    <span class="fontq text-dark fw5">Total Horas</span>
                    <figure class="highcharts-figure">
                        <div id="ChartTotalHoras"></div>
                    </figure>
                </div>
            </div>
            <!-- <div class="col-12 col-sm-6 mt-3" id="chartNoveH">
                <div class="shadow-sm p-3 ChartsDiv">
                    <span class="fontq text-dark fw5">Total Horas</span>
                    <canvas id="myChart2" class="" height="200"></canvas>
                </div>
            </div> -->
        </div>
    </div>
    <?php
    require __DIR__ . "../../js/jquery.php";
    ?>
    <!-- moment.min.js -->
    <script type="text/javascript" src="../js/dateranger/moment.min.js"></script>
    <!-- daterangepicker.min.js -->
    <script type="text/javascript" src="../js/dateranger/daterangepicker.min.js"></script>
    <!-- daterangepicker.css -->
    <link rel="stylesheet" type="text/css" href="../js/dateranger/daterangepicker.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.18.1/moment.min.js"></script>
    <script type="text/javascript" src="../js/chartjs/Chart.js"></script>
    <script type="text/javascript" src="data.js?v=<?= vjs() ?>"></script>
</body>

</html>
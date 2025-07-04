<?php
$Periodo = peri_min_max();
$nove = nov_cta_cte();
$getPeri = $_GET['peri'] ?? '';
$Periodo = $getPeri ? $getPeri : $Periodo['max'];
$noves = super_unique($nove, 'cod');
$Novedad = isset($_GET['nove']) ? $_GET['nove'] : $noves[0]['cod'];
$no_cta_cte = (!$nove[0]['peri']) ? 'd-none' : '';

?>
<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "/../../llamadas.php"; ?>
    <title><?= MODULOS['ctactehoras'] ?></title>
</head>

<body class="animate__animated animate__fadeIn">
    <div class="container shadow pb-2">
        <?php require __DIR__ . '/../../nav.php'; ?>
        <?= encabezado_mod('bg-custom', 'white', 'ctacte_hora.png', MODULOS['ctactehoras'], ''); ?>
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
                <div class="custom-control custom-switch custom-control-inline w250 ml-1">
                    <input type="checkbox" class="custom-control-input" id="Visualizar">
                    <label class="custom-control-label" for="Visualizar" style="padding-top: 3px;"><span id="VerPor"
                            data-toggle="tooltip" data-placement="top" data-html="true"
                            title="<b>Incluye valores en cero.</b>">Mostrar Todo</span></label>
                    <input type="hidden" id="datos">
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="d-flex justify-content-sm-end justify-content-center mt-3 mt-sm-0">
                    <input type="text" readonly class="mx-2 form-control text-center w250 ls2" name="_dr" id="_dr">
                    <button title="Actualizar Grilla" type="button" id="Refresh" disabled
                        class="btn px-2 border-0 fontq float-right bg-custom text-white opa8">
                        <svg class="bi" width="20" height="20" fill="currentColor">
                            <use xlink:href="../../img/bootstrap-icons.svg#arrow-repeat" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        <?php // if ($nove[0]['peri']) { ?>
        <div class="row radius py-2">
            <?php
            $FechaMinMax = fecha_min_max('FICHAS3', 'FICHAS3.FicFech');
            $FirstDate = $FechaMinMax['min'];
            /** FirstDate */
            $FirstYear = Fech_Format_Var($FechaMinMax['min'], 'Y');
            /** FirstYear */
            $maxDate = $FechaMinMax['max'];
            /** maxDate */
            $maxYear = date('Y');
            /** maxYear */
            // if (!isset($_GET['_dr']) or (empty($_GET['_dr']))) {
            $FechaIni = date("Y-m-d", strtotime(hoy() . "- 1 month"));
            $FechaFin = $FechaMinMax['max'];
            if ($FechaIni > $FechaFin) {
                $FechaIni = $FechaMinMax['min'];
            }
            ?>
            <div class="col-12 col-sm-6">
                <a title="Cta Cte Novedades" href="../"
                    class="btn btn-outline-custom border-0 btn-sm fontq">Novedades</a>
            </div>
            <div class="col-12 col-sm-6 d-flex justify-content-end">
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="todo" name="cta" class="custom-control-input" value="3">
                    <label class="custom-control-label" for="todo" style="padding-top: 3px;">Todo</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="debe" name="cta" class="custom-control-input" value="1">
                    <label class="custom-control-label" for="debe" style="padding-top: 3px;">Debe</label>
                </div>
                <div class="custom-control custom-radio custom-control-inline">
                    <input type="radio" id="haber" name="cta" class="custom-control-input" value="2">
                    <label class="custom-control-label" for="haber" style="padding-top: 3px;">Haber</label>
                </div>
                <input type="hidden" id="cta">
            </div>
        </div>
        <div class="row bg-white">
            <div class="col-12 animate__animated animate__fadeIn pb-3 table-responsive">
                <table class="table table-hover text-wrap w-100" id="table-cte_hor">
                    <thead class="text-uppercase">
                        <th class="text-center"></th>
                        <th class="">LEG.</th>
                        <th class="">NOMBRE</th>
                        <th class="text-center" title="">
                            <span data-toggle="tooltip" data-placement="top" data-html="true"
                                title="<b>RESULTADO DE CTA CTE</b>">CTA CTE</span>
                        </th>
                        <th class="text-center">
                            <span data-toggle="tooltip" data-placement="top" data-html="true"
                                title="<b>HORAS HECHAS</b>">HECHAS</span>
                        </th>
                        <th class="text-center">
                            <span data-toggle="tooltip" data-placement="top" data-html="true"
                                title="<b>JORNADA REDUCIDA<br> RESTA EN CTA CTE</b>">JOR R. -</span>
                        </th>
                        <th class="text-center">
                            <span data-toggle="tooltip" data-placement="top" data-html="true"
                                title="<b>JORNADA REDUCIDA<br> SUMA EN CTA CTE</b>">JOR R. +</span>
                        </th>
                        <th class="text-center">
                            <span data-toggle="tooltip" data-placement="top" data-html="true"
                                title="<b>FRANCOS<br/>RESTA EN CTA CTE</b>">FRANCOS -</span>
                        </th>
                        <th class="text-center">
                            <span data-toggle="tooltip" data-placement="top" data-html="true"
                                title="<b>FRANCOS<br/>SUMA EN CTA CTE</b>">FRANCOS +</span>
                        </th>
                        <!-- <th class="text-center" title="">SUMA</th> -->
                    </thead>
                </table>
            </div>
        </div>
        <?php //} else {
        // echo '<div class="w-100 text-white p-3 fw4 mt-3 radius opa7 bg-custom">No hay Información en cuenta corriente.</div>';
        //}
        ?>
    </div>
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "/../../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    require __DIR__ . "/../../js/DateRanger.php";
    /** INCLUIMOS LIBRERÍAS y script DATATABLE */
    require __DIR__ . "/../../js/DataTable.php";
    $array_cta = 'array_cte_hora';
    require 'modal_Filtros.html';
    require 'modal_Detalle.html';
    ?>

    <script>
        function ActualizaTablas() {
            $('#table-cte_hor').DataTable().ajax.reload();
        };

        $("#Refresh").on("click", function () {
            $('#table-cte_hor').DataTable().ajax.reload();
        });

        $("#todo").prop('checked', 'true')
        $("#cta").val('3');

        $("#todo").change(function () {
            $("#cta").val($("#todo").val());
            $('#table-cte_hor').DataTable().ajax.reload();
        });
        $("#debe").change(function () {
            $("#cta").val($("#debe").val());
            $('#table-cte_hor').DataTable().ajax.reload();
        });
        $("#haber").change(function () {
            $("#cta").val($("#haber").val());
            $('#table-cte_hor').DataTable().ajax.reload();
        });
    </script>
    <script src="../../js/select2.min.js"></script>
    <script src="js/select.js?v=<?= vjs() ?>"></script>
    <script src="js/trash-select.js?v=<?= vjs() ?>"></script>
    <script src="js/detalle.js?v=<?= vjs() ?>"></script>
    <script src="js/cteHorasXLS.js?v=<?= vjs() ?>"></script>
    <script>
        $('#datos').val('2');
        $(document).ready(function () {
            $("#Visualizar").change(function () {
                if ($("#Visualizar").is(":checked")) {
                    $('#datos').val('1')
                    $('#table-cte_hor').DataTable().ajax.reload();
                } else {
                    $('#datos').val('2')
                    $('#table-cte_hor').DataTable().ajax.reload();
                }
            });

            $('#table-cte_hor').DataTable({
                "initComplete": function (settings, json) {
                    $('#trash_all').removeClass('invisible');
                    $("#Refresh").prop('disabled', false);
                    $("#_dr").change(function () {
                        $('#table-cte_hor').DataTable().ajax.reload();
                    });
                },
                bProcessing: true,
                serverSide: true,
                deferRender: true,
                searchDelay: 1000,
                // stateSave:true,
                "ajax": {
                    url: "/" + $("#_homehost").val() + "/ctacte/horas/GetCtaCteHoras.php",
                    type: "POST",
                    "data": function (data) {
                        data.Per = $("#Per").val();
                        data.Tipo = $("#Tipo").val();
                        data.Emp = $("#Emp").val();
                        data.Plan = $("#Plan").val();
                        data.Sect = $("#Sect").val();
                        data.Sec2 = $("#Sec2").val();
                        data.Grup = $("#Grup").val();
                        data.Sucur = $("#Sucur").val();
                        data._dr = $("#_dr").val();
                        data.cta = $("#cta").val();
                        data.Visualizar = $("#datos").val();
                    },
                },
                createdRow: function (row, data, dataIndex) {
                    $(row).addClass('animate__animated animate__fadeIn');
                },
                columns: [{
                    "class": "align-middle py-0 px-1 text-center",
                    "data": "modal"
                },
                {
                    "class": "align-middle",
                    "data": "Legajo"
                },
                {
                    "class": "align-middle",
                    "data": "Nombre"
                }, {
                    "class": "text-center align-middle bg-light",
                    "data": "ctacte"
                }, {
                    "class": "text-center align-middle",
                    "data": "HorasEx"
                },
                {
                    "class": "text-center align-middle",
                    "data": "JorRedu1"
                },
                {
                    "class": "text-center align-middle",
                    "data": "JorRedu2"
                },
                {
                    "class": "text-center align-middle",
                    "data": "Franco1"
                },
                {
                    "class": "text-center align-middle",
                    "data": "Franco2"
                },
                ],
                scrollX: true,
                scrollCollapse: true,
                scrollY: '25vmax',
                paging: 1,
                searching: 1,
                info: 1,
                ordering: 0,
                language: {
                    "url": "/<?= HOMEHOST ?>/js/DataTableSpanishShort2.json?" + vjs()
                },
            });
        });
    </script>
</body>

</html>
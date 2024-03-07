<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../../llamadas.php"; ?>
    <!-- daterangepicker.css -->
    <link rel="stylesheet" type="text/css" href="/<?= HOMEHOST ?>/js/dateranger/daterangepicker.css" />
    <title>
        <?= MODULOS['reporte'] ?>
    </title>
</head>

<body class="bg-secondary">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '../../../nav.php';
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" class="bi icon-tabler-chart-line" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 19l16 0" /><path d="M4 15l4 -6l4 2l4 -5l4 4" /></svg>';
        ?>
        <!-- Encabezado -->
        <?= encabezado_mod_svgIcon('bg-custom', 'white', $svg, MODULOS['reporte'], ''); ?>
        <!-- Fin Encabezado -->
        <div class="row pt-3">
            <div class="col-12 col-sm-6 mb-sm-0 mb-3">
                <div class="btn-group btn-group-toggle bg-light border radius p-1" data-toggle="buttons">
                    <label
                        class="btn btn-sm font08 btn-outline-custom border-0 radius w100 hint--top hint--rounded hint--no-arrow hint--default hint--no-shadow"
                        aria-label="Visualizar Horas y Novedades">
                        <input type="radio" name="VPor" id="VTodo" value="todo"> Todo
                    </label>
                    <label
                        class="btn btn-sm font08 btn-outline-custom border-0 radius w100 hint--top hint--rounded hint--no-arrow hint--default hint--no-shadow"
                        aria-label="Visualizar Novedades">
                        <input type="radio" name="VPor" id="VNovedades" value="novedades"> Novedades
                    </label>
                    <label
                        class="btn btn-sm font08 btn-outline-custom border-0 radius w100 hint--top hint--rounded hint--no-arrow hint--default hint--no-shadow"
                        aria-label="Visualizar Horas">
                        <input type="radio" name="VPor" id="VHoras" value="horas"> Horas
                    </label>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="input-group w-100 d-inline-flex justify-content-end">
                    <div class="hint--left hint--rounded hint--no-arrow hint--default hint--no-shadow"
                        aria-label="Seleccionar fechas">
                        <div class="d-inline-flex radius border">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0 bg-white" id="Refresh">
                                    <svg class="bi mr-1" width="18" height="18" fill="currentColor">
                                        <use xlink:href="../../img/bootstrap-icons.svg#calendar-range"></use>
                                    </svg>
                                </span>
                            </div>
                            <div>
                                <input type="text" class="form-control text-center border-0 ls1 h40 w250 loader-in"
                                    name="_dr" id="_dr">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php require 'filtros.html'; ?>
        <section id="section_tablas" class="mb-3" style="display:none">
            <div id="div_tabla">
                <p class="bg-custom opa8 mb-2 p-2 text-white text-center">Horas</p>
                <div id="card_totales"></div>
                <table id="tabla" class="table w-100 text-nowrap">
                </table>
                <table id="tabla_totales" class="table w-100 text-nowrap mb-2">
                </table>
            </div>
            <div class="mt-2" id="div_tabla_novedades">
                <p class="bg-custom opa8 mb-2 p-2 text-white text-center">Novedades</p>
                <div id="card_totales_nove"></div>
                <table id="tabla_novedades" class="table w-100 text-nowrap">
                </table>
            </div>
        </section>
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "../../../js/jquery.php";
    require __DIR__ . "../../../js/DataTable.php";

    ?>
    <!-- moment.min.js -->
    <script type="text/javascript" src="/<?= HOMEHOST ?>/js/dateranger/moment.min.js"></script>
    <!-- daterangepicker.min.js -->
    <script type="text/javascript" src="/<?= HOMEHOST ?>/js/dateranger/daterangepicker.min.js"></script>
    <!-- notify -->
    <script src="/<?= HOMEHOST ?>/js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <!-- select2 -->
    <script src="/<?= HOMEHOST ?>/js/select2.min.js"></script>
    <script src="/<?= HOMEHOST ?>/js/select2-es.js"></script>
    <!-- reporte.js -->
    <script
        src="/<?= HOMEHOST ?>/informes/reporte/js/reporte.js?<?= version_file("/informes/reporte/js/reporte.js") ?>"></script>
</body>

</html>
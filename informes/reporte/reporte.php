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

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '../../../nav.php'; ?>
        <!-- Encabezado -->
        <?= encabezado_mod('bg-custom', 'white', 'informes.png', MODULOS['reporte'], ''); ?>
        <!-- Fin Encabezado -->
        <!-- <form action="" method="GET" name="fichadas" class="" onsubmit="ShowLoading()" id='range'> -->
        <div class="row bg-white pt-3">
            <div class="col-12 col-sm-6 mb-sm-0 mb-3">
                <div class="btn-group btn-group-toggle bg-light border radius p-1" data-toggle="buttons">
                    <label
                        class="btn btn-sm fontq btn-outline-custom border-0 w100 hint--top hint--rounded hint--no-arrow hint--default hint--no-shadow hint--no-animate"
                        aria-label="Visualizar Horas y Novedades">
                        <input type="radio" name="VPor" id="VTodo" value="todo"> Todo
                    </label>
                    <label
                        class="btn btn-sm fontq btn-outline-custom border-0 w100 hint--top hint--rounded hint--no-arrow hint--default hint--no-shadow hint--no-animate"
                        aria-label="Visualizar Novedades">
                        <input type="radio" name="VPor" id="VNovedades" value="novedades"> Novedades
                    </label>
                    <label
                        class="btn btn-sm fontq btn-outline-custom border-0 w100 hint--top hint--rounded hint--no-arrow hint--default hint--no-shadow hint--no-animate"
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
                <table id="tabla" class="table w-100 text-nowrap">
                </table>
            </div>
            <div class="mt-2" id="div_tabla_novedades">
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
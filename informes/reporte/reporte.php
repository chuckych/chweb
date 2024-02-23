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
        <div class="row bg-white radius pt-3 mb-0 pb-0">
            <div class="col-12 col-sm-6">
                <button type="button" class="btn btn-outline-custom border btn-sm fontq Filtros d-print-none"
                    data-toggle="modal" data-target="#Filtros">
                    Filtros
                </button>
                <button type="button" class="ml-1 btn btn-outline-custom border btn-sm fontq d-print-none" disabled
                    id="btnExcel">
                </button>
                <span id="trash_all" title="Limpiar Filtros" class="trash align-middle pb-0 d-print-none"></span>
            </div>
            <div class="col-12 col-sm-6">
                <div class="input-group w-100 d-inline-flex justify-content-end">
                    <div class="hint--left hint--rounded hint--no-arrow hint--default hint--no-shadow"
                        aria-label="Seleccionar fechas">
                        <div class="shadow-sm d-inline-flex border">
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
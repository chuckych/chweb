<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "/../../llamadas.php"; ?>
    <!-- daterangepicker.css -->
    <link rel="stylesheet" type="text/css" href="/<?= HOMEHOST ?>/js/dateranger/daterangepicker.css" />
    <title>
        <?= MODULOS['proyectar'] ?>
    </title>
    <style>
        /* .select2-results__option[aria-selected=true] {
            display: block;
        } */

        .dataTables_paginate {
            /* margin-bottom: 10px !important; */
            margin-top: 0px !important;
            /* border-radius: var(--main-radius-0); */
        }
    </style>
</head>

<body class="bg-secondary">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '/../../nav.php';
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-graph-up-arrow" viewBox="0 0 16 16">
        <path fill-rule="evenodd" d="M0 0h1v15h15v1H0zm10 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-1 0V4.9l-3.613 4.417a.5.5 0 0 1-.74.037L7.06 6.767l-3.656 5.027a.5.5 0 0 1-.808-.588l4-5.5a.5.5 0 0 1 .758-.06l2.609 2.61L13.445 4H10.5a.5.5 0 0 1-.5-.5"/>
        </svg>';
        ?>
        <!-- Encabezado -->
        <?= encabezado_mod_svgIcon('bg-custom', 'white', $svg, MODULOS['proyectar'], ''); ?>
        <!-- Fin Encabezado -->
        <?php require 'filtros.html'; ?>
        <div class="form-row mb-3">
            <div class="col-12">
                <a href="javascript:void(0)" id="btnHoras" class="float-right btn btn-link font08 mt-1">
                    Actualizar Tablas
                </a>
            </div>
            <div class="col-12 col-lg-5">
                <table id="tabla" class="table text-nowrap fadeIn">

                </table>
            </div>
            <div class="col-12 col-lg-7 mt-3 mt-sm-0">
                <div class="" id="divTablaHoras">
                    <table id="tabla_horas" class="table text-nowrap fadeIn">
                        <thead></thead>
                        <tfoot>
                            <tr></tr>
                        </tfoot>
                    </table>
                </div>
                <a href="javascript:void(0)" class="btn btn-link font08 float-right hint--top" id="btnExportar"
                    aria-label="Exportar horas calculadas" style="display: none;">
                    Exportar TXT
                </a>
                <a href="javascript:void(0)" class="btn btn-link font08 float-right hint--left" id="btnExportarPDF"
                    aria-label="Exportar horas calculadas" style="display: none;">
                    Exportar PDF
                </a>
            </div>
        </div>
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "/../../js/jquery.php";
    require __DIR__ . "/../../js/DataTable.php";

    ?>
    <script type="text/javascript" src="/<?= HOMEHOST ?>/js/dateranger/moment.min.js"></script>
    <!-- daterangepicker.min.js -->
    <script type="text/javascript" src="/<?= HOMEHOST ?>/js/dateranger/daterangepicker.min.js"></script>
    <!-- notify -->
    <script src="/<?= HOMEHOST ?>/js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <!-- select2 -->
    <script src="/<?= HOMEHOST ?>/js/select2.min.js"></script>
    <script src="/<?= HOMEHOST ?>/js/select2-es.js"></script>
    <script
        src="/<?= HOMEHOST ?>/procesar/proyectar/js/select.js?<?= version_file("/procesar/proyectar/js/select.js") ?>"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script> -->
    <script type="text/javascript" src="/<?= HOMEHOST ?>/js/jspdf.umd.min.js"></script>
</body>

</html>
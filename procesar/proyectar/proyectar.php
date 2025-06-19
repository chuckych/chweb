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
        .select2-results__option[aria-selected=true] {
            display: block;
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
        <table id="tabla" class="table text-nowrap">

        </table>
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
</body>

</html>
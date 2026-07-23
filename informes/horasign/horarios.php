<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "/../../llamadas.php"; ?>
    <!-- daterangepicker.css -->
    <link rel="stylesheet" type="text/css" href="/<?= HOMEHOST ?>/js/dateranger/daterangepicker.css" />
    <title>
        <?= MODULOS['horarios_asignados'] ?>
    </title>
    <style>
        .dataTables_paginate {
            margin-top: 0px !important;
        }

        .dataTables_info {
            font-size: 0.7rem;
        }

        #tablaPersonal_paginate {
            margin-top: 16px !important;
        }

        #tablaPersonal_info {
            margin-top: -5px !important;
            padding: 0px !important;
        }

        .infoTabla>div {
            margin-top: -10px;
        }
    </style>
</head>

<body>
    <!-- inicio container -->
    <div class="container pb-2">
        <?php require __DIR__ . '/../../nav.php';
        $svg = iconEncabezados('informes');
        $titulo = "<span style='margin-top:2px'>" . MODULOS['horarios_asignados'] . "</span>";
        ?>
        <!-- Encabezado -->
        <?php encabezado_mod_svgIcon('bg-custom', 'white', $svg, $titulo, ''); ?>
        <?php require 'body.php'; ?>
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
        src="/<?= HOMEHOST ?>/informes/horasign/js/horarios.js?<?= version_file("/informes/horasign/js/horarios.js") ?>"></script>
</body>

</html>
<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../../llamadas.php"; ?>
    <title><?= MODULOS['datos'] ?></title>
    <style>
        .dataTables_paginate {
            margin-bottom: 0px !important;
            margin-top: 0px !important;
        }

        .dataTables_length {
            font-size: small;
            margin-top: 10px;
        }

        #tableNacion thead {
            display: none;
        }

        /* input[type="search"]::-webkit-search-cancel-button {
            -webkit-appearance: searchfield-cancel-button;
        } */
        /* input[type="search"]::-webkit-search-cancel-button {
            display: none;
        } */
    </style>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '../../../nav.php'; ?>
        <!-- Encabezado -->
        <?php //encabezado_mod('bg-custom', 'white', 'informes.png', MODULOS['datos'], '') 
        ?>
        <?php encabezado_mod2('bg-custom', 'white', 'gear-fill',  MODULOS['datos'], '25', 'text-white mr-2'); ?>
        <!-- Fin Encabezado -->
        <div class="row bg-white py-2">
            <div class="col-12 p-3">
                <nav class="fontq">
                    <div class="nav nav-tabs bg-light" id="nav-tab" role="tablist">
                        <a class="px-3 nav-item nav-link active text-dark" id="nacion-tab" data-toggle="tab" href="#nacion" role="tab" aria-controls="nacion" aria-selected="true"><span class="text-tab">Nacionalidades</span></a>
                        <a class="px-3 nav-item nav-link text-dark" id="provincia-tab" data-toggle="tab" href="#provincia" role="tab" aria-controls="provincia" aria-selected="true"><span class="text-tab">Provincias</span></a>
                        <a class="px-3 nav-item nav-link text-dark" id="localidad-tab" data-toggle="tab" href="#localidad" role="tab" aria-controls="localidad" aria-selected="true"><span class="text-tab">Localidades</span></a>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <?php require 'tab-nacion.php' ?>
                    <?php require 'tab-provincia.php' ?>
                    <?php require 'tab-localidad.php' ?>
                </div>
            </div>
        </div>
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "../../../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATATABLE */
    require __DIR__ . "../../../js/DataTable.php";
    ?>
    <script src="https://cdn.datatables.net/scroller/2.0.3/js/dataTables.scroller.min.js"></script>
    <script src="js/data.js?v=<?= vjs() ?>"></script>
    <script src="../../js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
</body>

</html>
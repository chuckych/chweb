<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "/../../llamadas.php"; ?>
    <title><?= MODULOS['estruct'] ?></title>
    <style>
        .dataTables_paginate {
            margin-bottom: 0px !important;
            margin-top: 0px !important;
        }

        .dataTables_length {
            font-size: small;
            margin-top: 10px;
        }

        thead {
            display: none;
        }
    </style>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '/../../nav.php'; ?>
        <!-- Encabezado -->
        <?php encabezado_mod2('bg-custom', 'white', 'gear-fill', MODULOS['estruct'], '25', 'text-white mr-2'); ?>
        <!-- Fin Encabezado -->
        <div class="row bg-white py-2">
            <div class="col-12 p-3">
                <nav class="fontq">
                    <div class="nav nav-tabs bg-light" id="nav-tab" role="tablist">
                        <a class="px-3 nav-item nav-link active text-dark" id="empresas-tab" data-toggle="tab"
                            href="#empresas" role="tab" aria-controls="empresas" aria-selected="true"><span
                                class="text-tab d-sm-none d-block" data-titler="Empresas">Emp</span><span
                                class="text-tab d-none d-sm-block">Empresas</span></a>
                        <a class="px-3 nav-item nav-link text-dark" id="plantas-tab" data-toggle="tab" href="#plantas"
                            role="tab" aria-controls="plantas" aria-selected="true"><span
                                class="text-tab d-sm-none d-block" data-titlel="Plantas">Plan</span><span
                                class="text-tab d-none d-sm-block">Plantas</span></a>
                        <a class="px-3 nav-item nav-link text-dark" id="sucur-tab" data-toggle="tab" href="#sucur"
                            role="tab" aria-controls="sucur" aria-selected="true"><span
                                class="text-tab d-sm-none d-block" data-titlel="Sucursales">Suc</span><span
                                class="text-tab d-none d-sm-block">Sucursales</span></a>
                        <a class="px-3 nav-item nav-link text-dark" id="grupos-tab" data-toggle="tab" href="#grupos"
                            role="tab" aria-controls="grupos" aria-selected="true"><span
                                class="text-tab d-sm-none d-block" data-titlel="Grupos">Grup</span><span
                                class="text-tab d-none d-sm-block">Grupos</span></a>
                        <a class="px-3 nav-item nav-link text-dark" id="sector-tab" data-toggle="tab" href="#sector"
                            role="tab" aria-controls="sector" aria-selected="true"><span
                                class="text-tab d-sm-none d-block" data-titlel="Sectores">Sect</span><span
                                class="text-tab d-none d-sm-block">Sectores</span></a>
                        <a class="px-3 nav-item nav-link text-dark" id="tareas-tab" data-toggle="tab" href="#tareas"
                            role="tab" aria-controls="tareas" aria-selected="true"><span
                                class="text-tab d-sm-none d-block" data-titlel="Tareas de Producción">Tar</span><span
                                data-titlel="Tareas de Producción" class="text-tab d-none d-sm-block">Tareas</span></a>
                    </div>
                </nav>
                <div class="tab-content" id="nav-tabContent">
                    <?php require 'tab-empresas.php' ?>
                    <?php require 'tab-plantas.php' ?>
                    <?php require 'tab-sucur.php' ?>
                    <?php require 'tab-grupos.php' ?>
                    <?php require 'tab-sector.php' ?>
                    <?php require 'tab-tareas.php' ?>
                </div>
            </div>
        </div>
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "/../../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATATABLE */
    require __DIR__ . "/../../js/DataTable.php";
    ?>
    <script src="js/data-min.js?v=<?= vjs() ?>"></script>
    <script src="../../js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="/<?= HOMEHOST ?>/js/select2.min.js"></script>
</body>

</html>
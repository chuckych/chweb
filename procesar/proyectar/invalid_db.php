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

        <!-- version de sistema no compatible-->
        <div class="alert alert-dark text-center mt-3 shadow-sm" role="alert">
            <h4 class="alert-heading"><i class="bi bi-exclamation-triangle mr-2"></i>ERROR</h4>
            <p>Este módulo no se puede utilizar porque la base de datos no es compatible con esta versión del sistema.
            </p>
            <strong>Requiere Versión 71_20250528 o superior</strong>
            <hr>
            <p class="mb-0">Por favor, actualice su base de datos o contacte al administrador del sistema.</p>
        </div>

    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "/../../js/jquery.php";
    ?>
</body>

</html>
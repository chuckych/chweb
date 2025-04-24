<?php
ExisteModRol('1');
secure_auth_ch();
?>
<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "/../../llamadas.php"; ?>
    <title>Auditoria</title>
    <style>
        .select2-container {
            width: 100% !important;
        }

        #tableAuditoria tr {
            cursor: pointer;
        }
    </style>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2" style="animation-fill-mode: unset">
        <?php require __DIR__ . '/../../nav.php'; ?>
        <!-- Encabezado -->
        <?= encabezado_mod2('bg-custom', 'white', 'journal-check', 'Auditoría', '25', 'text-white mr-2'); ?>
        <!-- Fin Encabezado -->
        <div class="row bg-white" id="divTableAud" style="display: none;">
            <div class="col-12">
                <table id="tableAuditoria" class="table table-hover w-100 text-nowrap p-2">
                    <thead class="fontq"></thead>
                </table>
            </div>
        </div>
        <div id="modalAuditoria"></div>
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "/../../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATATABLE */
    require __DIR__ . "/../../js/DataTable.php";
    ?>
    <!-- <script src="../../js/moment.min.js"></script> -->
    <!-- moment.min.js -->
    <script type="text/javascript" src="/<?= HOMEHOST ?>/js/dateranger/moment.min.js"></script>
    <!-- daterangepicker.min.js -->
    <script type="text/javascript" src="/<?= HOMEHOST ?>/js/dateranger/daterangepicker.min.js"></script>
    <!-- daterangepicker.css -->
    <link rel="stylesheet" type="text/css" href="/<?= HOMEHOST ?>/js/dateranger/daterangepicker.css" />
    <script src="/<?= HOMEHOST ?>/vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="/<?= HOMEHOST ?>/js/select2.min.js"></script>
    <script src="main-min.js?<?= microtime(true) ?>"></script>
</body>

</html>
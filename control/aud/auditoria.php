<?php
ExisteModRol('1');
secure_auth_ch();
?>
<!doctype html>
<html lang="es">

<head>
    <?php require __DIR__ . "../../../llamadas.php"; ?>
    <title>Auditoria</title>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2" style="animation-fill-mode: unset">
        <?php require __DIR__ . '../../../nav.php'; ?>
        <!-- Encabezado -->
        <?= encabezado_mod2('bg-custom', 'white', 'journal-check',  'Auditoría', '25', 'text-white mr-2'); ?>

        <!-- Fin Encabezado -->
        <div class="row bg-white mt-2 py-3" id="divTableAud" style="display: none;">
            <div class="col-12 table-responsive">
                <table id="tableAuditoria" class="table table-hover text-nowrap">
                </table>

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
    <script src="../../js/moment.min.js"></script>
    <script src="main.js?<?= vjs() ?>"></script>
</body>

</html>
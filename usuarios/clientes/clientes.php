<!doctype html>
<html lang="es">

<head>
    <?php require __DIR__ . "../../../llamadas.php"; ?>
    <title><?= MODULOS['cuentas'] ?></title>
    <style>
    .dtrg-level-0 td{
        border-top: 1px solid #DEE2E6 !important;
    }
    thead{
        display: none;
    }
    </style>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow">
        <?php require __DIR__ . '../../../nav.php'; ?>
        <!-- Encabezado -->
        <?=
            encabezado_mod2('bg-custom', 'white', 'diagram-3-fill',  MODULOS['cuentas'], '25', 'text-white mr-2');
        ?>
            <div class="row mt-3">
                <div class="col-12 table-responsive invisible">
                    <table class="table text-nowrap w-100" id="GetClientes">
                        
                    </table>
                </div>
            </div>
            <?php require 'modalFormCuenta.html';?>
        <!-- fin container -->
        <?php
        /** INCLUIMOS LIBRERÍAS JQUERY */
        require __DIR__ . "../../../js/jquery.php";
        require __DIR__ . "../../../js/DataTable.php";
        ?>
        <script src="../../js/datatable/dataTables.rowGroup.min.js"></script>
        <script src="../../js/bootbox.min.js"></script>
        <script src="data.js?v=<?=vjs()?>"></script>
</body>

</html>
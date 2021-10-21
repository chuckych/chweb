<!doctype html>
<html lang="es">

<head>
    <!-- <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" /> -->
    <?php require __DIR__ . "../../llamadas.php"; ?>
    <!-- <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script> -->
    <title><?=MODULOS['auditoria']?></title>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '../../nav.php'; ?>
        <!-- Encabezado -->
        <?= encabezado_mod('bg-custom', 'white', 'rueda2.png', MODULOS['auditoria'], '') ?>
        <!-- Fin Encabezado -->

        <div class="row bg-white mt-2 py-3 radius">
            <div class="col-12">
                <button class="btn btn-sm btn-link text-decoration-none fontq text-secondary p-0 pb-1 m-0 float-right" id="Refresh">Actualizar Grilla</button>
            </div>
            <div class="col-12 animate__animated animate__fadeIn table-responsive p-3">
                <table class="table table-striped text-nowrap w-100" id="table-auditoria">
                    <thead class="text-uppercase border-top-0">
                        <tr>
                            <th>FECHA</th>
                            <th>HORA</th>
                            <th>USUARIO</th>
                            <th>TERMINAL</th>
                            <th>MODULO</th>
                            <th>TIPO</th>
                            <th>DATOS</th>
                            <!-- <th class="w-100"></th> -->
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "../../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATATABLE */
    require __DIR__ . "../../js/DataTable.php";
    ?>
    <!-- <link rel="stylesheet" href="../js/datatable/fixedHeader.bootstrap4.min.css"> -->
    <!-- <script src="../js/datatable/dataTables.fixedHeader.min.js"></script> -->
    <script src="data-min.js?v=<?=vjs()?>"></script>
</body>

</html>
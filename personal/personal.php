<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../llamadas.php"; ?>
    <!-- <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script> -->
    <title><?= MODULOS['personal'] ?></title>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '../../nav.php'; ?>
        <!-- Encabezado -->
        <?= encabezado_mod('bg-fich', 'white', 'usuarios3.png', MODULOS['personal'], '') ?>
        <!-- Fin Encabezado -->
        <div class="row bg-white py-2">
            <div class="col-12">
                <!-- <a href="/<?= HOMEHOST ?>/personal/legajo/index.php" class="btn btn-sm fontq text-white px-3 <?= $bgcolor ?>">Nuevo</a> -->
                <button type="button" class="mt-2 border btn btn-sm fontq px-3 btn-outline-custom" data-toggle="modal" data-target="#altaNuevoLeg">
                    <span data-toggle="tooltip" data-placement="top" data-html="true" data-original-title="<b>Nuevo Legajo</b>">Nuevo</span>
                </button>
                <div class="custom-control custom-switch float-right pt-1">
                    <input type="checkbox" class="custom-control-input" name="_eg" id="_eg">
                    <label class="custom-control-label" for="_eg" style="padding-top: 3px;" data-toggle="tooltip" data-placement="top" data-html="true" title="" data-original-title="<b>Filtrar Legajos Inactivos</b>">De Baja</label>
                </div>
                <?php require __DIR__ . '/modalNuevoLeg.php' ?>
            </div>
        </div>
        <div class="row bg-white py-3 radius invisible" id="PersonalTable">
            <div class="col-12 table-responsive">
                <table class="table table-hover text-nowrap w-100 table-sm" id="table-personal">
                    <thead class="text-uppercase border-top-0">
                        <tr>
                            <th class="text-center"></th>
                            <th>LEGAJO</th>
                            <th>NOMBRE</th>
                            <th>DNI</th>
                            <th>ESTADO</th>
                            <th>EMPRESA</th>
                            <th>PLANTA</th>
                            <th>CONVENIO</th>
                            <th>SECTOR</th>
                            <th>SECCION</th>
                            <th>GRUPO</th>
                            <th>SUCURSAL</th>
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
    <script src="altaLeg.js?v=<?= vjs() ?>"></script>
    <script src="data.js?v=<?= vjs() ?>"></script>
    <script src="../vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="legajo/js/mascaras.js?v=<?= vjs() ?>"></script>
    <script src="../js/select2.min.js"></script>
    <script src="../js/select2Filtros-min.js"></script>
</body>

</html>
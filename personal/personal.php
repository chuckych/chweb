<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../llamadas.php"; ?>
    <!-- <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script> -->
    <title><?= MODULOS['personal'] ?></title>
    <style>
        /* #table-personal .tooltip-inner {
            background-color: #fafafa;
            color: #333;
            font-size: 0.8rem;
            z-index: 9999 !important;
            text-align: left !important;
            width: 100% !important;
        } */
    </style>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '../../nav.php'; ?>
        <!-- Encabezado -->
        <?= encabezado_mod('bg-fich', 'white', 'usuarios3.png', MODULOS['personal'], '') ?>
        <!-- Fin Encabezado -->
        <div class="row bg-white pt-3">
            <div class="col-12">
                <!-- <a href="/<?= HOMEHOST ?>/personal/legajo/index.php" class="btn btn-sm fontq text-white px-3 <?= $bgcolor ?>">Nuevo</a> -->
                <!-- <div class="d-flex align-items-center"> -->
                    <button type="button" class="border btn btn-sm fontq px-3 btn-outline-custom" data-toggle="modal" data-target="#altaNuevoLeg">
                        <span data-toggle="tooltip" data-placement="top" data-html="true" data-original-title="<b>Nuevo Legajo</b>">Nuevo</span>
                    </button>
                    <button type="button" class="btn btn-outline-custom border btn-sm fontq Filtros" data-toggle="modal" data-target="#Filtros">
                        Filtros
                    </button>
                    <span id="trash_all" data-toggle="tooltip" data-placement="top" data-html="true" title="" data-original-title="<b>Limpiar Filtros</b>" class="fontq text-secondary mx-1 pointer"><i class="bi bi-trash"></i></span>
                    <button type="button" class="btn btn-light text-success fw5 border btn-sm fontq" id="btnExcel" data-toggle="tooltip" data-placement="top" data-html="true" title="" data-original-title="<b>Exportar todos los legajos. Incluyendo De Baja</b>">
                        Excel
                    </button>
                <!-- </div> -->
                <div class="custom-control custom-switch float-right pt-1">
                    <input type="checkbox" class="custom-control-input" name="_eg" id="_eg">
                    <label class="custom-control-label" for="_eg" style="padding-top: 3px;" data-toggle="tooltip" data-placement="top" data-html="true" title="" data-original-title="<b>Filtrar Legajos Inactivos</b>">De Baja</label>
                </div>
                <div class="custom-control custom-switch float-right pt-1 mr-2">
                    <input type="checkbox" class="custom-control-input" name="_porApNo" id="_porApNo">
                    <label class="custom-control-label" for="_porApNo" style="padding-top: 3px;" data-toggle="tooltip" data-placement="top" data-html="true" title="" data-original-title="<b>Ordenar por Nombre</b>">Por Nombre</label>
                </div>
            </div>
        </div>
        <div class="row bg-white py-3 radius invisible" id="PersonalTable">
            <div class="col-12 table-responsive">
                <table class="table table-hover text-nowrap w-100" id="table-personal">
                    <thead class="text-uppercase border-top-0">
                        <tr>
                            <th class="text-center p-2"><i class="bi bi-pencil-square"></i></th>
                            <th><span data-titler="Legajo">LEG.</span></th>
                            <th>NOMBRE y APELLIDO</th>
                            <th class="text-center p-2">TIPO</th>
                            <!-- <th>ESTADO</th> -->
                            <th>EMPRESA</th>
                            <th>PLANTA</th>
                            <th>CONVENIO</th>
                            <th>SECTOR / SECCION</th>
                            <!-- <th>SECCION</th> -->
                            <th>GRUPO / SUCURSAL</th>
                            <!-- <th>SUCURSAL</th> -->
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
    require __DIR__ . '/modal_Filtros.html';
    require __DIR__ . '/modalNuevoLeg.php';
    ?>
    <script src="altaLeg.js?v=<?= vjs() ?>"></script>
    <script src="data.js?v=<?= vjs() ?>"></script>
    <script src="perExcel.js?v=<?= vjs() ?>"></script>
    <script src="../vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="legajo/js/mascaras.js?v=<?= vjs() ?>"></script>
    <script src="../js/select2.min.js"></script>
    <!-- <script src="../js/select2Filtros-min.js"></script> -->
    <script src="getSelect/select.js?v=<?= vjs() ?>"></script>
</body>

</html>
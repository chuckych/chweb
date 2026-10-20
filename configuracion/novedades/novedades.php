<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "/../../llamadas.php"; ?>
    <title><?= MODULOS['novedades_config'] ?></title>
    <style>
        <?php require __DIR__ . "/style.php"; ?>
    </style>
</head>

<body class="fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '/../../nav.php'; ?>
        <!-- Encabezado -->
        <?php
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="icon icon-tabler icons-tabler-outline icon-tabler-article" viewBox="0 0 24 24"><path fill="none" stroke="none" d="M0 0h24v24H0z"/><path d="M3 6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2zm4 2h10M7 12h10M7 16h10"/></svg>';
        ?>
        <!-- Encabezado -->
        <?php encabezado_mod_svgIcon('bg-custom', 'white', $svg, MODULOS['novedades_config'], ''); ?>
        <!-- Fin Encabezado -->
        <div class="row py-3">
            <div class="col-12" id="table-opt">
                <div class="opt-tbl mb-4 d-flex justify-content-between align-items-center gap5">
                    <div id="div-nueva-novedad" class="d-inline-flex gap5">
                        <button class="btn h40 px-3 btn-sm btn-custom border-0 ml-1" id="btn-nueva-novedad">
                            <span class="d-none d-sm-block">
                                <i class="bi bi-plus-lg"></i> Nueva Novedad
                            </span>
                            <span class="d-block d-sm-none">
                                Nuevo
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <table id="tblNovedades" class="shadow-sm table w-100 p-3 border radius loader-in"
                    style="display:none;">
                </table>
            </div>
        </div>
        <?php require __DIR__ . "/modal.php"; ?>
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "/../../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATATABLE */
    require __DIR__ . "/../../js/DataTable.php";
    ?>
    <script src="/<?= HOMEHOST ?>/js/jscolor/jscolor.min.js"></script>
    <script src="/<?= HOMEHOST ?>/js/datatable/dataTables.rowGroup.min.js"></script>
    <script src="/<?= HOMEHOST ?>/vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="novedades.js?v=<?= version_file("/configuracion/novedades/novedades.js") ?>"></script>
    <script src="/<?= HOMEHOST ?>/js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="/<?= HOMEHOST ?>/js/select2.min.js"></script>
</body>

</html>
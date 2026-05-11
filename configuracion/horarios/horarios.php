<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "/../../llamadas.php"; ?>
    <title><?= MODULOS['horarios'] ?></title>
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
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="mb-1 mr-2" viewBox="0 0 16 16">
        <path d="M14 0H2a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2M1 3.857C1 3.384 1.448 3 2 3h12c.552 0 1 .384 1 .857v10.286c0 .473-.448.857-1 .857H2c-.552 0-1-.384-1-.857z"/>
        <path d="M7 10a1 1 0 0 0 0-2H1v2zm2-3h6V5H9a1 1 0 0 0 0 2"/>
        </svg>';
        ?>
        <!-- Encabezado -->
        <?php encabezado_mod_svgIcon('bg-custom', 'white', $svg, '<span>Configuración de Horarios</span>', ''); ?>
        <!-- Fin Encabezado -->
        <div class="row bg-white py-3">
            <div class="col-12" id="table-responsive-horarios"></div>
            <div class="col-12">
                <div class="table-responsive" style="display:none;">
                    <table id="tblHorarios" class="shadow-sm table table-borderless w-100 p-3 border radius loader-in"></table>
                </div>
            </div>
        </div>
        <?php require __DIR__ . "/modal_horario.php"; ?>
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
    <script src="horarios.js?v=<?= version_file("/configuracion/horarios/horarios.js") ?>"></script>
    <script src="/<?= HOMEHOST ?>/js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="/<?= HOMEHOST ?>/js/select2.min.js"></script>
</body>

</html>
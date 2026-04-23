<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "/../../llamadas.php"; ?>
    <title><?= MODULOS['liquidar'] ?></title>
</head>

<body class="fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '/../../nav.php'; ?>
        <!-- Encabezado -->
        <?= encabezado_mod('bg-custom', 'white', 'descargar.png', MODULOS['liquidar_custom'], '') ?>
        <!-- Fin Encabezado -->
        <div class="row">
            <div class="col-12 col-sm-6 mt-2">
                <button class="mt-2 mt-sm-4 pt-1 btn btn-outline-secondary border font09 h40" type="button" data-toggle="collapse"
                    data-target="#definirCampos" aria-expanded="false" aria-controls="definirCampos">
                    <span class="d-inline-flex gap5">
                        <i class="bi bi-gear-fill"></i>
                        <span class="d-block d-sm-none">Campos</span>
                        <span class="d-none d-sm-block">Definir Campos</span>
                    </span>
                </button>
            </div>
            <div class="col-12 col-sm-6 mt-2">
                <div class="d-flex align-items-end justify-content-end gap10">
                    <div>
                        <label for="date-picker" class="">Fecha desde / hasta:</label>
                        <input id="date-picker" type="text" class="date-picker form-control h40 text-center">
                    </div>
                    <div>
                        <button type="button" class="btn btn-custom font09 w150 h40" id="btn-exportar">Exportar
                            TXT</button>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="collapse" id="definirCampos">
                    <?php require __DIR__ . '/form_campos.php'; ?>
                </div>
            </div>
            <div class="col-12 mt-3" id="resultado-exportacion">
                <div class="card border radius">
                    <div class="card-header py-2 d-flex align-items-center justify-content-between bg-white">
                        <span class="font09">Resultados generados</span>
                        <small class="text-muted d-none d-sm-block" id="resultado-exportacion-archivo"></small>
                    </div>
                    <div class="card-body p-0">
                        <pre id="resultado-exportacion-contenido" class="mb-0 p-3"
                            style="max-height: 550px; overflow: auto; white-space: pre;">Sin resultados cargados.</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- fin container -->
    <?php
    require __DIR__ . "/../../js/jquery.php";
    require __DIR__ . "/../../js/DateRanger.php";
    require __DIR__ . "/../../js/DataTable.php";
    ?>
    <script src="../../vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="../../js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="../../js/moment.min.js"></script>
    <script src="../../js/select2.min.js"></script>
    <script src="../../js/Sortable.min.js"></script>
    <script src="liquidar_custom.js?v=<?= version_file("/liquidar/custom/liquidar_custom.js") ?>"></script>
</body>

</html>
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
        <div class="border radius p-3 mt-3 shadow-sm">
            <div class="row">
                <div class="col-12 col-sm-4 mb-2">
                    <label for="campo-plantilla">Plantilla</label>
                    <select id="campo-plantilla" class="form-control" style="width:200px">
                    </select>
                </div>
                <div class="col-12 col-sm-8 mb-2">
                    <div class="d-flex align-items-end justify-content-end gap10">
                        <div>
                            <label for="date-picker" class="">Fecha desde / hasta:</label>
                            <input id="date-picker" type="text" class="date-picker form-control h40 text-center">
                        </div>
                        <div>
                            <button type="button" class="btn btn-custom font09 w150 h40" id="btn-exportar">
                                Exportar TXT
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="d-flex align-items-end justify-content-between">
                        <div>
                            <button class="mt-2 mt-sm-4 pt-1 btn btn-outline-secondary border font09 h40" type="button"
                                data-toggle="collapse" data-target="#definirCampos" aria-expanded="false"
                                aria-controls="definirCampos">
                                <span class="d-inline-flex gap5">
                                    <i class="bi bi-gear-fill"></i>
                                    <span class="d-block d-sm-none">Campos</span>
                                    <span class="d-none d-sm-block">Definir Campos</span>
                                </span>
                            </button>
                        </div>
                        <div class="d-flex align-items-center" style="gap:8px;">
                            <button type="button" class="btn btn-outline-secondary font09 border"
                                id="btn-crear-plantilla" title="Crear Plantilla">
                                <span class="d-none d-sm-block">Crear Plantilla</span>
                                <div class="d-flex flex-column gap5 d-block d-sm-none">
                                    <span>
                                        <i class="bi bi-plus-lg"></i>
                                    </span>
                                </div>
                            </button>
                            <button type="button" class="hint--top btn btn-outline-danger font09 border"
                                id="btn-eliminar-plantilla" aria-label="Eliminar Plantilla" title="Eliminar Plantilla">
                                <span class="d-none d-sm-block"><i class="bi bi-trash"></i></span>
                                <div class="d-flex flex-column gap5 d-block d-sm-none">
                                    <span>
                                        <i class="bi bi-trash"></i>
                                    </span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="collapse" id="definirCampos">
                    <?php require __DIR__ . '/form_campos.php'; ?>
                </div>
            </div>
            <div class="col-12 mt-3" id="resultado-exportacion">
                <div class="p-2 border radius shadow-sm">
                    <div class="card-header d-flex align-items-center justify-content-between bg-white">
                        <span class="font09">Resultados generados</span>
                        <small class="text-muted d-none d-sm-block fadeInDown"
                            id="resultado-exportacion-archivo"></small>
                    </div>
                    <div class="card-body">
                        <pre id="resultado-exportacion-contenido" class="mb-0"
                            style="height: 500px; overflow: auto; white-space: pre;">Sin resultados cargados.</pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fadeIn" id="modal-crear-plantilla" tabindex="-1" role="dialog"
            aria-labelledby="modal-crear-plantilla-title" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h6 class="modal-title" id="modal-crear-plantilla-title">Crear Plantilla</h6>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-0">
                            <label for="nombre-plantilla">Nombre de plantilla</label>
                            <input type="text" class="form-control h40" id="nombre-plantilla" maxlength="50">
                            <small id="nombre-plantilla-error" class="text-danger d-none"></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary border-0 font09"
                            data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-custom font09" id="btn-guardar-plantilla">Guardar</button>
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
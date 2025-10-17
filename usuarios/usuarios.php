<?php
$Cliente = ExisteCliente($_GET['_c']);
$_GET['_rol'] = $_GET['_rol'] ?? '';
?>
<!doctype html>
<html lang="es">

<head>
    <link href="../js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "/../llamadas.php"; ?>
    <title>Usuarios</title>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow">
        <?php require __DIR__ . '/../nav.php'; ?>
        <?=
            encabezado_mod2('bg-custom', 'white', 'people-fill', 'Usuarios: ' . $Cliente, '25', 'text-white mr-2');
        ?>
        <input type="hidden" id="recid_c" value="<?= $_GET['_c'] ?>">
        <input type="hidden" id="_rol" value="<?= $_GET['_rol'] ?>">
        <div class="row mt-3">
            <div class="col-12 col-sm-6">
                <a href="personal/?_c=<?= $_GET['_c'] ?>" class="fw4 btn fontq btn-outline-custom border hint hint--right"
                    id="btnImportar" aria-label="Importar usuarios desde Control Horario">
                    <span class="mr-1 d-none d-sm-inline fw5">IMPORTAR PERSONAL CH</span>
                    <span class="mr-1 d-inline d-sm-none">IMPORTAR DE CH</span>
                    <i class="bi-download font1"></i>
                </a>
            </div>
            <div class="col-12 col-sm-6 mb-2">
                <?php if (modulo_cuentas() == '1') { ?>
                    <a href="clientes/" class="btn fontq float-right m-0 opa7 btn-custom"><i
                            class="bi bi-diagram-3-fill mr-2"></i>Cuentas</a>
                <?php } ?>
                <a href="roles/?_c=<?= $_GET['_c'] ?>" class="mr-1 btn fontq float-right m-0 opa7 btn-custom"><i
                        class="bi bi-sliders mr-2"></i>Roles</a>
            </div>
            <div class="col-12 mt-1" id="div_import_ad" style="display:none;">
                <a href="javascript:void(0);" class="fw4 btn fontq btn-outline-custom border hint hint--right"
                    id="btnImportarAD" aria-label="Importar usuarios desde Active Directory">
                    <span class="mr-1 fw5">IMPORTAR USUARIOS AD</span>
                </a>
            </div>
        </div>
        <div class="mt-2">
            <div class="row">
                <div class="col-12 table-responsive invisible">
                    <table class="table w-100 text-wrap table-sm" id="GetUsuarios">
                        <thead class="text-uppercase">
                            <tr>
                                <th class="border-bottom">uid</th>
                                <th class="border-bottom">recid</th>
                                <th class="border-bottom">nombre</th>
                                <th class="border-bottom"></th>
                                <th class="border-bottom">legajo</th>
                                <th class="border-bottom">rol</th>
                                <th class="border-bottom">estado</th>
                                <th class="border-bottom">estado</th>
                                <th class="border-bottom">id_cliente</th>
                                <th class="border-bottom">recid_cliente</th>
                                <th class="border-bottom">cuenta</th>
                                <th class="border-bottom">rol</th>
                                <th class="border-bottom">ultimo acceso</th>
                                <th class="border-bottom">fecha alta</th>
                                <th class="border-bottom">fecha mod</th>
                                <th class="border-bottom">Acciones</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal animate__animated animate__fadeIn" id="modalListas" data-backdrop="static"
            data-keyboard="true" tabindex="-1" aria-labelledby="modalListasLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-xl">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <div class="modal-title" id="modalListasLabel"></div>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" class="bi bi-x"></span>
                        </button>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-outline-custom border btn-sm fontq px-3"
                            data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
        <?php require "modalAddUser.html"; ?>
        <?php require "modalEditUser.html"; ?>
        <?php require "modalUserAD.html"; ?>
    </div>
    <!-- fin container -->
    <?php
    require __DIR__ . "/../js/jquery.php";
    require __DIR__ . "/../js/DataTable.php";
    ?>
    <script src="/<?= HOMEHOST ?>/js/datatable/dataTables.rowGroup.min.js"></script>
    <script src="/<?= HOMEHOST ?>/js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="/<?= HOMEHOST ?>/js/select2.min.js"></script>
    <script src="/<?= HOMEHOST ?>/js/bootbox.min.js"></script>
    <script src="usuarios.js?<?= version_file("/usuarios/usuarios.js") ?>"></script>
</body>

</html>
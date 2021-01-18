<?php
$Cliente = ExisteCliente($_GET['_c'])
?>
<!doctype html>
<html lang="es">

<head>
    <link href="../js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../llamadas.php"; ?>
    <title>Usuarios</title>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow">
        <?php require __DIR__ . '../../nav.php'; ?>
        <?=
            encabezado_mod2('bg-custom', 'white', 'people-fill',  'Usuarios: ' . $Cliente, '25', 'text-white mr-2');
        ?>
        <input type="hidden" id="recid_c" value="<?= $_GET['_c'] ?>">
        <input type="hidden" id="_rol" value="<?= $_GET['_rol'] ?>">
        <div class="row mt-3">
            <div class="col-12 col-sm-6">
                <a href="personal/?_c=<?= $_GET['_c'] ?>" class="fw4 btn fontq btn-outline-custom border">
                    <span class="mr-1 d-none d-sm-inline">IMPORTAR PERSONAL</span>
                    <span class="mr-1 d-inline d-sm-none">IMPORTAR</span>
                    <i class="bi-download font1"></i>
                </a>
                <span id="respuestaResetClave"></span>
            </div>
            <div class="col-12 col-sm-6 mb-2">
                <?php if (modulo_cuentas() == '1') { ?>
                    <a href="clientes/" class="btn fontq float-right m-0 opa7 btn-custom"><i class="bi bi-diagram-3-fill mr-2"></i>Cuentas</a>
                <?php } ?>
                <a href="roles/?_c=<?= $_GET['_c'] ?>" class="mr-1 btn fontq float-right m-0 opa7 btn-custom"><i class="bi bi-sliders mr-2"></i>Roles</a>
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
        <?php require "modalAddUser.html"; ?>
        <?php require "modalEditUser.html"; ?>
    </div>
    <!-- fin container -->
    <?php
    require __DIR__ . "../../js/jquery.php";
    require __DIR__ . "../../js/DataTable.php";
    ?>
    <script src="../js/datatable/dataTables.rowGroup.min.js"></script>
    <script src="../js/select2.min.js"></script>
    <script src="../js/bootbox.min.js"></script>
    <script src="usuarios-min.js?v=<?=vjs()?>"></script>
</body>

</html>
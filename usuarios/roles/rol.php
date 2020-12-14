<?php
$modulo = '2';
$Cliente = ExisteCliente($_GET['_c']);
?>
<!doctype html>
<html lang="es">

<head>
    <?php require __DIR__ . "../../../llamadas.php"; ?>
    <title>Roles</title>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow">
        <?php require __DIR__ . '../../../nav.php'; ?>
        <!-- Encabezado -->
        <?=
            encabezado_mod2('bg-custom', 'white', 'sliders',  'Roles: ' . $Cliente, '25', 'text-white mr-2');
        ?>
        <span id="respuesta"></span>
        <input type="hidden" id="recid_cRol" value="<?= $_GET['_c'] ?>">
        <!-- Fin Encabezado -->
            <div class="row mt-3">
                <div class="col-12">
                    <?php if (modulo_cuentas() == '1') { ?>
                        <a href="../clientes/" class="btn fontq float-right m-0 opa7 btn-custom"><i class="bi bi-diagram-3-fill mr-2"></i>Cuentas</a>
                    <?php } ?>
                    <a href="../?_c=<?= $_GET['_c'] ?>" class="btn mr-1 fontq float-right m-0 opa7 btn-custom"><i class="bi bi-people-fill mr-2"></i>Usuarios</a>
                </div>
            </div>
        <div class="row mt-2">
            <div class="col-12 mt-2 table-responsive" style="display: none;">
                <table class="table w-100 text-nowrap" id="GetRoles">
                    <thead class="text-uppercase">
                        <tr>
                            <th>id</th>
                            <th>recid</th>
                            <th>recid_cliente</th>
                            <th>nombre</th>
                            <th>id_cliente</th>
                            <th>cuenta</th>
                            <th>usuarios</th>
                            <th>modulos</th>
                            <th>abm</th>
                            <th>empresas</th>
                            <th>plantas</th>
                            <th>convenios</th>
                            <th>sectores</th>
                            <th>grupos</th>
                            <th>sucursal</th>
                            <th>fecha_alta</th>
                            <th>fecha_mod</th>
                            <th></th>
                        </tr>
                    </thead>
                    <!-- <tbody> -->
                        <?php
                        // if (modulo_cuentas() != '1') {
                        //     $data_rol = array_filter($data_rol, function ($e) {
                        //         return $e['nombre'] != 'SISTEMA';
                        //     });
                        // }
                        ?>
                    <!-- </tbody> -->
                </table>
            </div>
        </div>
    </div>
    </div>
    <!-- fin container -->
    <?php
    require "modal_abm.html";
    require __DIR__ . "../../../js/jquery.php";
    require __DIR__ . "../../../js/DataTable.php";
    ?>
    <script src="../../js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="../../js/bootbox.min.js"></script>
    <script src="modal.js"></script>
    <script src="datarol-min.js"></script>

</body>

</html>
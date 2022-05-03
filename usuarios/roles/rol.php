<?php
$modulo = '2';
$Cliente = ExisteCliente($_GET['_c']);
?>
<!doctype html>
<html lang="es">

<head>
    <?php require __DIR__ . "../../../llamadas.php"; ?>
    <title>Roles</title>
    <style>
        .dtrg-level-0 {
            background-color: #ffffff;
            /* border-radius: 5px; */
        }
        .dtrg-level-0 td{
            padding-bottom: 0px;
            /* font-family: 'Montserrat-SemiBold'; */
            /* font-weight: 300 !important; */
            border-top: 1px solid #e3e3e3 !important;
        }
        td{
            border: 0px solid #e3e3e3 !important;
        }
    </style>
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
        <div class="row my-3">
            <div class="col-12">
                <?php if (modulo_cuentas() == '1') { ?>
                    <a href="../clientes/" class="btn fontq float-right m-0 opa7 btn-custom"><i class="bi bi-diagram-3-fill mr-2"></i>Cuentas</a>
                <?php } ?>
                <a href="../?_c=<?= $_GET['_c'] ?>" class="btn mr-1 fontq float-right m-0 opa7 btn-custom"><i class="bi bi-people-fill mr-2"></i>Usuarios</a>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
            <?php $classSKF = ($_SESSION["RECID_CLIENTE"] <> 'kxo7w2q-') ? 'w-auto text-nowrap':'w-100 text-wrap';?>
                <table class="table border p-2 <?=$classSKF?>" id="GetRoles">
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
                            <th>Listas</th>
                            <th>abm</th>
                            <?php $ocultar = ($_SESSION["RECID_CLIENTE"] <> 'kxo7w2q-') ? 'style="display: none;"':'';?>
                                <th><span <?=$ocultar ?> >empresas</span></th>
                                <th><span <?=$ocultar ?> >plantas</span></th>
                                <th><span <?=$ocultar ?> >convenios</span></th>
                                <th><span <?=$ocultar ?> >sectores</span></th>
                                <th><span <?=$ocultar ?> >grupos</span></th>
                                <th><span <?=$ocultar ?> >sucursal</span></th>
                            <th>fecha_alta</th>
                            <th>fecha_mod</th>
                            <th></th>
                        </tr>
                    </thead>
                    <?php
                    // if (modulo_cuentas() != '1') {
                    //     $data_rol = array_filter($data_rol, function ($e) {
                    //         return $e['nombre'] != 'SISTEMA';
                    //     });
                    // }
                    ?>
                </table>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal animate__animated animate__fadeIn" id="modalListas" data-backdrop="static" data-keyboard="true" tabindex="-1" aria-labelledby="modalListasLabel" aria-hidden="true">
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
                        <button type="button" class="btn btn-outline-custom border btn-sm fontq px-3" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- fin container -->
    <?php
    require "modal_abm.html";
    require __DIR__ .  '../../../js/jquery.php';
    require __DIR__ . "../../../js/DataTable.php";
    ?>
    <script>
        fetch('../clientes/testConnect.php?_c=<?= $_GET['_c'] ?>')
            .then(response => response.json())
            .then(data => {
                if (data.status == "Error") {
                    notify('No hay conexión con Control Horario<br>Para la cuenta <strong><?= $Cliente ?></strong>', 'warning', 0, 'right')
                }
            });
    </script>
    <script src="/<?= HOMEHOST ?>/js/datatable/dataTables.rowGroup.min.js"></script>
    <script src="/<?= HOMEHOST ?>/js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="/<?= HOMEHOST ?>/js/bootbox.min.js"></script>
    <script src="/<?= HOMEHOST ?>/usuarios/roles/modal-min.js?v=<?= vjs() ?>"></script>
    <script src="/<?= HOMEHOST ?>/usuarios/roles/datarol-min.js?v=<?= vjs() ?>"></script>
</body>

</html>
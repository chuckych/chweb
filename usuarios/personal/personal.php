<?php
function notif_error_var($get, $texto)
{
    $ok = "";
    if (isset($_GET[$get])) {
        $ok = '<div class="col-12">
        <div class="animate__animated animate__fadeInDown mt-1 p-3 radius-0 fw4 fontq alert alert-danger text-uppercase alert-dismissible fade show" role="alert">
			' . $texto . '
			<button type="button" class="close" data-dismiss="alert" aria-label="Close">
			  <span aria-hidden="true">&times;</span>
			</button>
          </div>
          </div>';
    }
    return $ok;
}
$Cliente_c = simple_pdoQuery("SELECT clientes.recid as 'recid', clientes.ident as 'ident', clientes.id as 'id', clientes.nombre as 'nombre' FROM clientes WHERE clientes.id > 0 AND clientes.recid='$_GET[_c]' LIMIT 1");
($Cliente_c['id']) ? '' : header("Location: /" . HOMEHOST . "/usuarios/clientes/");
?>
<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "/../../llamadas.php"; ?>
    <title><?= MODULOS['cuentas'] ?> » Personal CH</title>
    <style>
        .dataTables_info {
            margin-top: 0px !Important;
        }

        .dataTables_paginate {
            margin-top: 0px !Important;
        }

        .pre-carga div {
            background-color: #efefef !important;
            color: #efefef !important;
        }

        .pre-carga .custom-control {
            display: none;
        }
    </style>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow">
        <?php require __DIR__ . '/../../nav.php'; ?>
        <?=
            encabezado_mod2('bg-custom', 'white', 'people-fill', 'Importar Personal: ' . $Cliente_c['nombre'], '25', 'text-white mr-2');
        ?>
        <div class="mt-1">
            <div class="row mt-3">
                <?= notif_error_var('error', '<span class="fw5">Los campos legajo y Rol son obligatorios</span>') ?>
            </div>
            <form action="<?= htmlspecialchars('crud.php?_c=' . $_GET['_c']) ?>" name="f1" id="f1" method="POST"
                class="w-100">
                <!-- <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?<?= $_SERVER['QUERY_STRING'] ?>" name="f1" id="f1" method="POST" class="w-100" onsubmit="ShowLoading()"> -->
                <div class="row">
                    <div class="col-12 col-sm-9">
                        <div class="form-inline <?= dnone($rowcount) ?> mb-2">
                            <!-- <p class="p-1 m-0 fontq">Seleccionar Rol</p> -->
                            <select name="rol" id="rol" class="h40 form-control custom-select w250 SelecRol">
                            </select>
                            <input type="hidden" hidden id="_crecid" value="<?= ($_GET['_c']) ?>">
                            <div class="mt-2 mt-sm-0 custom-control custom-switch custom-control-inline ml-2"
                                id="DivLegaPass">
                                <input id="LegaPass" class="custom-control-input" type="checkbox" name="LegaPass">
                                <label for="LegaPass" class="custom-control-label" style="padding-top: 3px;">
                                    <span data-toggle="tooltip" data-placement="top" data-html="true"
                                        data-original-title="<span class='w150 fw5 text-dark'>Legajo como Usuario<br>DNI como Contraseña.<br>Si el DNI esta vacío, no se importará.</span>">Importar
                                        Legajo y DNI</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                        <a href="/<?= HOMEHOST ?>/usuarios/?_c=<?= $_GET['_c'] ?>"
                            class="d-flex align-items-center btn fontq float-right opa7 btn-custom">
                            <span class=""><i class="bi bi-people-fill mr-2"></i>Usuarios</span>
                        </a>
                    </div>
                </div>
                <div class="row pb-4">
                    <div class="col-12">
                        <input type="hidden" name="_c" value="<?= $_GET['_c'] ?>">
                        <input type="hidden" name="id_c" value="<?= $Cliente_c['id'] ?>">
                        <input type="hidden" name="ident" value="<?= $Cliente_c['ident'] ?>">
                        <table class="table table-hover w-100 text-wrap table-sm" id="table-personal">
                            <thead class="text-uppercase">
                                <tr>
                                    <th class="">Legajo</th>
                                    <th class="">DNI</th>
                                    <th>Nombre</th> <!-- Nombre -->
                                    <th>Empresa</th> <!-- Empresa -->
                                    <th>Planta</th> <!-- Planta -->
                                    <th>Sector</th> <!-- Sector -->
                                    <th>Grupo</th> <!-- Grupo -->
                                    <th>Sucursal</th> <!-- Sucursal -->
                                </tr>
                            </thead>
                        </table>
                        <div class="">
                            <button type="submit" name="submit"
                                class="px-4 btn btn-custom border fontq mt-2 <?= dnone($rowcount) ?>  btn-mobile"
                                value="Importar" id="submit"><i class="bi-download font1 mr-2"></i>IMPORTAR</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- fin container -->
    <?php
    require __DIR__ . "/../../js/jquery.php";
    require __DIR__ . "/../../js/DataTable.php";
    /** INCLUIMOS LIBRERÍAS JQUERY */
    ?>
    <script src="/<?= HOMEHOST ?>/js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="/<?= HOMEHOST ?>/js/select2.min.js"></script>
    <script src="data-min.js?v=<?= vjs() ?>"></script>
</body>

</html>
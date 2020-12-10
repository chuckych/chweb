<?php
require __DIR__ . '/crud.php';
// UnsetGet('id');
ExisteCliente($_GET['_c']);
list($id_c, $ident, $nombre_c) = Cliente_c($_GET['_c']);
define("id_c", $id_c);
define("nombre_c", $nombre_c);
define("ident", $ident);
?>
<!doctype html>
<html lang="es">

<head>
    <?php require __DIR__ . "../../../llamadas.php"; ?>
    <title><?= MODULOS['cuentas'] ?> » Personal CH</title>
    <style>
        .dataTables_info {
            margin-top: 0px !Important;
        }
        .dataTables_paginate  {
            margin-top: 0px !Important;
        }
    </style>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow">
        <?php require __DIR__ . '../../../nav.php'; ?>
        <?=
            encabezado_mod2('bg-custom', 'white', 'people-fill',  'Importar Personal: '.nombre_c, '25', 'text-white mr-2');
        ?>
        <div class="mt-1">
            <div class="row mt-3">
                <?= notif_error_var('error', '<span class="fw5">Los campos legajo y Rol son obligatorios</span>') ?>
            </div>
            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?<?= $_SERVER['QUERY_STRING'] ?>" name="f1" method="POST" class="w-100" onsubmit="ShowLoading()">
                <div class="row">
                    <div class="col-12 col-sm-9">
                        <div class="form-inline <?= dnone($rowcount) ?> mb-2">
                            <!-- <p class="p-1 m-0 fontq">Seleccionar Rol</p> -->
                            <select required name="rol" id="rol" class="h40 form-control custom-select w250 mr-3">
                                <option selected value="" disabled>Seleccionar Rol</option>
                                <?= ListaRoles($_GET['_c']) ?>
                            </select>
                            <input type="hidden" hidden id="_crecid" value="<?= ($_GET['_c']) ?>">
                            <div class="mt-2 mt-sm-0 custom-control custom-switch custom-control-inline" id="DivLegaPass">
                                <input id="LegaPass" class="custom-control-input" type="checkbox" name="LegaPass">
                                <label for="LegaPass" class="custom-control-label" style="padding-top: 3px;">
                                    <span data-toggle="tooltip" data-placement="top" data-html="true" data-original-title="<span class='w150 fw5 text-dark'>Legajo como Usuario<br>DNI como Contraseña.<br>Si el DNI esta vacío, no se importará.</span>">Importar Legajo y DNI</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-3">
                    <a href="/<?= HOMEHOST ?>/usuarios/?_c=<?= $_GET['_c'] ?>" class="d-flex align-items-center btn fontq float-right opa7 btn-custom">
                        <span class="">Usuarios</span>
                    </a>
                    </div>
                    <div class="col-12 pb-2 <?= dnone($rowcount) ?>">
                        <div class="">
                            <button type="button" class="p-0 fontq btn btn-link text-secondary" id="marcar">Marcar</button> |
                            <button type="button" class="p-0 fontq btn btn-link text-secondary" id="desmarcar">Desmarcar</button>
                        </div>
                    </div>
                </div>
                <!-- <div class="col-12 col-sm-6 d-flex justify-content-end">
                        
                    </div> -->
                <div class="row pb-4">
                    <div class="col-12">
                        <input type="hidden" name="_c" value="<?= $_GET['_c'] ?>">
                        <input type="hidden" name="id_c" value="<?= $id_c ?>">
                        <input type="hidden" name="ident" value="<?= $ident ?>">
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
                            <button type="submit" name="submit" class="d-none d-sm-block fw4 btn border w130 fontq float-right mt-2 <?= dnone($rowcount) ?> btn-custom" value="Importar">IMPORTAR</button>
                            <button type="submit" name="submit" class="d-block d-sm-none h50 btn-block fw4 btn border fontq float-right mt-2 <?= dnone($rowcount) ?> btn-custom" value="Importar">IMPORTAR</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- fin container -->
    <?php
    require __DIR__ . "../../../js/jquery.php";
    require __DIR__ . "../../../js/DataTable.php";
    /** INCLUIMOS LIBRERÍAS JQUERY */
    ?>
    <script src="data-min.js"></script>
</body>

</html>
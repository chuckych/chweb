<?php
$modulo = '2';
// (!isset($_GET['_c'])) ? header("Location: /" . HOMEHOST . "/usuarios/clientes/") : '';
ExisteCliente($_GET['_c']);
require __DIR__ . '/crud.php';
UnsetGet('id');
$url          = host() . "/" . HOMEHOST . "/data/GetRoles.php?tk=" . token() . "&recid_c=" . $_GET['_c'];
// echo $url;
$json         = file_get_contents($url);
$array        = json_decode($json, TRUE);
if (is_array($array)) :
    $rowcount     = (count($array[0]['roles']));
endif;
$data_rol         = $array[0]['roles'];
$tituloform   = (isset($_GET['mod'])) ? 'Editar' : 'Alta Rol';
$ValueSubmit  = (isset($_GET['mod'])) ? 'editar' : 'alta';
$NombreSubmit = (isset($_GET['mod'])) ? 'Guardar' : 'Aceptar';
$ShadowEdit   = (isset($_GET['mod'])) ? 'border' : '';
$VerClave     = (isset($_GET['mod'])) ? '0' : '1';
$varEditForm  = (isset($_GET['mod'])) ? '?id=' . $_GET['id'] . '&mod' : '';
$collapse     = (isset($_GET['mod'])) ? '0' : 'collapse';
/** Recorremos array para editar usuario filtrando por el recid de la table mediante la variable GET id */
if (isset($_GET['mod'])) :
    $r = array_filter($data_rol, function ($e) {
        return $e['recid'] == $_GET['id'];
    });
    foreach ($r as $value) :
        if ($value['recid'] == $_GET['id']) {
            $nombre  = $value['nombre'];
            $recid   = $value['recid'];
        }
    endforeach;
endif;
list($id_c, $ident, $nombre_c, $recid_c) = Cliente_c($_GET['_c']);
define("id_c", $id_c);
define("nombre_c", $nombre_c);
define("recid_c", $recid_c);

$url   = host() . "/" . HOMEHOST . "/data/GetModulos.php?tk=" . token();
$json  = file_get_contents($url);
$array = json_decode($json, TRUE);
if (is_array($array)) :
    $rowcount_mod = (count($array[0]['modulos']));
endif;

$chEmpresas   = count_estructura($_GET['_c'], 'empresas');
$chPlantas    = count_estructura($_GET['_c'], 'plantas');
$chConvenios  = count_estructura($_GET['_c'], 'convenios');
$chSectores   = count_estructura($_GET['_c'], 'sectores');
$chGrupos     = count_estructura($_GET['_c'], 'grupos');
$chSucursales = count_estructura($_GET['_c'], 'sucursales');

?>
<!doctype html>
<html lang="es">

<head>
    <?php require __DIR__ . "../../../llamadas.php"; ?>
    <title>Roles</title>
    <style>
        td {
            border: 0px solid #cecece;
        }
    </style>
    <!-- <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<meta http-equiv="X-UA-Compatible" content="IE=11"> -->
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow">
        <?php require __DIR__ . '../../../nav.php'; ?>
        <!-- Encabezado -->
        <?=
            encabezado_mod2('bg-custom', 'white', 'sliders',  'Roles: ' . nombre_c, '25', 'text-white mr-2');
        ?>
        <!-- Fin Encabezado -->
        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?<?= $_SERVER['QUERY_STRING'] ?>" method="post" class="" onsubmit="ShowLoading()">
            <div class="row mt-3">
                <div class="col-4">
                    <a class="fontq btn px-3 btn-custom opa8" data-toggle="collapse" href="#collapse_rol" role="button" aria-expanded="false" aria-controls="collapse_rol">
                        <?= $tituloform ?>
                    </a>
                    <p class="m-0 fw4 fontq mt-2"><?= $nombre ?></p>
                    <?= $duplicado ?>
                </div>
                <div class="col-8">
                    <?php if (modulo_cuentas() == '1') { ?>
                        <a href="/<?= HOMEHOST ?>/usuarios/clientes/" class="w80 btn fontq float-right m-0 opa7 btn-custom">Cuentas</a>
                    <?php } ?>
                    <a href="/<?= HOMEHOST ?>/usuarios/?_c=<?= $_GET['_c'] ?>" class="w80 btn mr-1 fontq float-right m-0 opa7 btn-custom">Usuarios</a>
                </div>
                <div class="col-12 <?= $collapse ?>" id="collapse_rol">
                    <div class="form-inline mt-2">
                        <input type="text" required name="nombre" id="nombre" placeholder="Nombre del Rol" class="h40 form-control mr-sm-2 <?= $ErrNombre ?>" value="<?= $nombre ?>">
                        <input type="hidden" name="nombre2" id="nombre2" placeholder="rol" class="mr-sm-2 " value="<?= $nombre ?>">
                        <input type="hidden" name="cliente" id="cliente" placeholder="" class="form-control mr-sm-2" value="<?= id_c ?>">
                        <input type="hidden" name="recid_c" id="recid_c" placeholder="" class="form-control mr-sm-2" value="<?= recid_c ?>">
                        <?php if (!$VerClave) : ?>
                            <input type="hidden" name="recid" id="recid" placeholder="" class="" value="<?= $_GET['id'] ?>">
                        <?php endif ?>
                        <button type="submit" name="submit" id="" class="d-none d-sm-block h40 w100 btn fontq float-right m-0 opa9 btn-custom" value="<?= $ValueSubmit ?>"><?= $NombreSubmit ?></button>
                        <button type="submit" name="submit" id="" class="d-block d-sm-none  mt-2 h50 btn-block btn fontq float-right m-0 opa9 btn-custom" value="<?= $ValueSubmit ?>"><?= $NombreSubmit ?></button>
                    </div>
                </div>
            </div>
        </form>
        <div class="row mt-2">
            <?= notif_error_var('err', 'Error al borrar el Rol. Existe información en la base de datos') ?>
            <div class="col-12 mt-2">
                <table class="table w-100 text-nowrap" id="table-roles">
                    <thead class="text-uppercase">
                        <tr>
                            <th class="">Nombre</th>
                            <th class="text-center">Usuarios</th>
                            <th class="text-center">Módulos</th>
                            <th class="text-center">ABM</th>
                            <th class="text-center">Empresas</th>
                            <th class="text-center">Plantas</th>
                            <th class="text-center">Convenios</th>
                            <th class="text-center">Sectores</th>
                            <th class="text-center">Grupo</th>
                            <th class="text-center">Sucursal</th>
                            <th class="text-center text-secondary">
                                <svg class="bi" width="12" height="12" fill="currentColor">
                                    <use xlink:href="../../img/bootstrap-icons.svg#pencil" />
                                </svg>
                            </th>
                            <th class="text-center text-secondary">
                                <svg class="bi" width="12" height="12" fill="currentColor">
                                    <use xlink:href="../../img/bootstrap-icons.svg#trash" />
                                </svg>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (modulo_cuentas() != '1') {
                            $data_rol = array_filter($data_rol, function ($e) {
                                return $e['nombre'] != 'SISTEMA';
                            });
                        }
                        /** Filtramos el usuario SISTEMA para que solo sea visible por el rol que tenga acceso a Cuentas */
                        if (is_array($data_rol)) :
                            foreach ($data_rol as $value) :
                                $id             = $value['id'];
                                $nombre         = $value['nombre'];
                                $recid          = $value['recid'];
                                $recid_cliente  = $value['recid_cliente'];
                                $cant_roles     = $value['cant_roles'];
                                $cant_modulos   = $value['cant_modulos'];
                                $cant_sectores  = $value['cant_sectores'];
                                $cant_grupos    = $value['cant_grupos'];
                                $cant_plantas   = $value['cant_plantas'];
                                $cant_sucur     = $value['cant_sucur'];
                                $cant_empresas  = $value['cant_empresas'];
                                $cant_convenios = $value['cant_convenios'];
                                $cliente        = $value['cliente'];
                                $fecha_alta     = FechaFormatH($value['fecha_alta']);
                                $fecha_mod      = FechaFormatH($value['fecha_mod']);
                                $sum_cant = array(
                                    $cant_roles,
                                    $cant_modulos,
                                    $cant_sectores,
                                    $cant_grupos,
                                    $cant_plantas,
                                    $cant_sucur,
                                    $cant_empresas,
                                    $cant_convenios
                                );
                        ?>
                                <tr>
                                    <?php /** NOMBRE */ ?>
                                    <td class=""><?= $nombre ?></td>
                                    <?php /** USUARIOS */ ?>
                                    <td class="text-center">
                                        <a title="Usuarios del rol <?= $nombre ?>" href="/<?= HOMEHOST ?>/usuarios/?_c=<?= $_GET['_c'] ?>&_rol=<?= $nombre ?>" class="w70 fw5 opa7 btn btn-outline-custom btn-sm fontp">
                                            <?= $cant_roles ?>
                                        </a>
                                    </td>
                                    <?php /** MÓDULOS */ ?>
                                    <td class="text-center">
                                        <a title="Módulos del rol <?= $nombre ?>" href="/<?= HOMEHOST ?>/usuarios/modulos/?_r=<?= $recid ?>&id=<?= $id ?>&_c=<?= $recid_cliente ?>" class="w70 fw5 opa7 btn btn-outline-custom btn-sm fontp">
                                            <?= $cant_modulos ?> / <?= $rowcount_mod ?>
                                        </a>
                                    </td>
                                    <?php /** ABM ROL */ ?>
                                    <td class="text-center">
                                        <button title="Altas, bajas y modificaciones del rol <?= ucwords(strtolower($nombre)) ?>" type="button" class="w70 fw5 opa7 btn btn-outline-custom btn-sm fontp" data-toggle="modal" data-target="#ModalABM" data="<?= $nombre ?>" data1="<?= $recid ?>" data2="<?= $id ?>" data3="<?= $cliente ?>" id="open-modal">
                                            ABM
                                        </button>
                                    </td>
                                    <?php /** EMPRESAS */ ?>
                                    <td class="text-center">
                                        <a title="Empresas del rol <?= $nombre ?>" href="/<?= HOMEHOST ?>/usuarios/estructura/?_r=<?= $recid ?>&id=<?= $id ?>&_c=<?= $_GET['_c'] ?>&e=empresas" class="w70 fw5 opa7 btn btn-outline-custom btn-sm fontp">
                                            <?= $cant_empresas ?> / <?php echo $chEmpresas ?>
                                        </a>
                                    </td>
                                    <?php /** PLANTAS */ ?>
                                    <td class="text-center">
                                        <a title="Plantas del rol <?= $nombre ?>" href="/<?= HOMEHOST ?>/usuarios/estructura/?_r=<?= $recid ?>&id=<?= $id ?>&_c=<?= $_GET['_c'] ?>&e=plantas" class="w70 fw5 opa7 btn btn-outline-custom btn-sm fontp">
                                            <?= $cant_plantas ?> / <?php echo $chPlantas ?>
                                        </a>
                                    </td>
                                    <?php /** CONVENIOS */ ?>
                                    <td class="text-center">
                                        <a title="Convenios del rol <?= $nombre ?>" href="/<?= HOMEHOST ?>/usuarios/estructura/?_r=<?= $recid ?>&id=<?= $id ?>&_c=<?= $_GET['_c'] ?>&e=convenios" class="w70 fw5 opa7 btn btn-outline-custom btn-sm fontp">
                                            <?= $cant_convenios ?> / <?php echo $chConvenios ?>
                                        </a>
                                    </td>
                                    <?php /** SECTORES */ ?>
                                    <td class="text-center">
                                        <a title="Sectores del rol <?= $nombre ?>" href="/<?= HOMEHOST ?>/usuarios/estructura/?_r=<?= $recid ?>&id=<?= $id ?>&_c=<?= $_GET['_c'] ?>&e=sectores" class="w70 fw5 opa7 btn btn-outline-custom btn-sm fontp">
                                            <?= $cant_sectores ?> / <?php echo $chSectores ?>
                                        </a>
                                    </td>
                                    <?php /** GRUPOS */ ?>
                                    <td class="text-center">
                                        <a title="Grupos del rol <?= $nombre ?>" href="/<?= HOMEHOST ?>/usuarios/estructura/?_r=<?= $recid ?>&id=<?= $id ?>&_c=<?= $_GET['_c'] ?>&e=grupos" class="w70 fw5 opa7 btn btn-outline-custom btn-sm fontp">
                                            <?= $cant_grupos ?> / <?php echo $chGrupos ?>
                                        </a>
                                    </td>
                                    <?php /** SUCURSALES */ ?>
                                    <td class="text-center">
                                        <a title="Sucursales del rol <?= $nombre ?>" href="/<?= HOMEHOST ?>/usuarios/estructura/?_r=<?= $recid ?>&id=<?= $id ?>&_c=<?= $_GET['_c'] ?>&e=sucursales" class="w70 fw5 opa7 btn btn-outline-custom btn-sm fontp">
                                            <?= $cant_sucur ?> / <?php echo $chSucursales ?>
                                        </a>
                                    </td>
                                    <?php /** EDITAR ROL */ ?>
                                    <td class="text-center">
                                        <a onclick="ShowLoading()" title="Editar rol" href="index.php?id=<?= $recid ?>&mod&_c=<?= $_GET['_c'] ?>" class="btn btn-sm fontp btn-outline-custom border">
                                            <svg class="bi" width="15" height="15" fill="currentColor">
                                                <use xlink:href="../../img/bootstrap-icons.svg#pencil" />
                                            </svg>
                                        </a>
                                    </td>
                                    <?php /** ELIMINAR ROL */ ?>
                                    <td class="">
                                        <?php if (array_sum($sum_cant) <= 0) : ?>
                                            <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?<?= $_SERVER['QUERY_STRING'] ?>" method="post" onsubmit="ShowLoading()">
                                                <input type="hidden" name="recid" id="recid" value="<?= $recid ?>">
                                                <input type="hidden" name="recid_c" id="recid_c" placeholder="" class="form-control mr-sm-2" value="<?= recid_c ?>">
                                                <button name='submit' value="trash" type="submit" title="Eliminar" class="btn btn-sm fontp btn-outline-custom border">
                                                    <svg class="bi" width="15" height="15" fill="currentColor">
                                                        <use xlink:href="../../img/bootstrap-icons.svg#trash" />
                                                    </svg>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                        <?php
                            endforeach;
                        endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php $nada = '0';
        if ($nada) :
            if (is_array($data_rol)) :
                foreach ($data_rol as $value) :
                    $nombre     = $value['nombre'];
                    $cliente    = $value['cliente'];
                    $cant_roles = $value['cant_roles'];
                    $id         = $value['id'];
                    if ($cant_roles) {
        ?>
                        <div class="mt-2" id="<?= $value['nombre'] ?>">
                            <div class="row bg-white shadow-sm">
                                <p class="bg-light p-3 w-100 fontq fw4">Rol: <?= $value['nombre'] ?></p>
                                <div class="col-12 mb-3">
                                    <table class="table w-100" id="table-<?= $value['nombre'] ?>">
                                        <thead>
                                            <tr>
                                                <th>Legajo</th>
                                                <th>Nombre</th>
                                                <th>Usuario</th>
                                                <th>Cliente</th>
                                                <th>Estado</th>
                                                <th>Alta</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php }
                    if ($cant_roles) { ?>
                        <div class="mt-2" id="<?= $value['nombre'] ?>">
                            <div class="row bg-white shadow-sm">
                                <p class="bg-light p-3 w-100 fontq fw4">Rol: <?= $value['nombre'] ?></p>
                                <div class="col-12 mb-3">
                                    <table class="table w-100" id="table-<?= $value['nombre'] ?>">
                                        <thead>
                                            <tr>
                                                <th>Legajo</th>
                                                <th>Nombre</th>
                                                <th>Usuario</th>
                                                <th>Cliente</th>
                                                <th>Estado</th>
                                                <th>Alta</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>

            <?php }
                endforeach;
            endif; ?>
        <?php endif; ?>
    </div>
    </div>
    <!-- fin container -->
    <?php
    require "modal_abm.html";
    require __DIR__ . "../../../js/jquery.php";
    require __DIR__ . "../../../js/DataTable.php";
    if ($nada) :
        $urlr          = host() . "/" . HOMEHOST . "/data/GetRoles.php?tk=" . token() . "&recid_c=" . $_GET['_c'];
        $jsonr = file_get_contents($urlr);
        $arrayr = json_decode($jsonr, TRUE);
        $data_rolr = $arrayr[0]['roles'];
        foreach ($data_rolr as $value) :
            $nombre = $value['nombre'];
            $id     = $value['id'];
    ?>

            <script>
                $.fn.DataTable.ext.pager.numbers_length = 5;
                alert = function() {};
                $(document).ready(function() {
                    $('#table-<?= $nombre ?>').DataTable({
                        ajax: {
                            url: "<?= host() ?>/<?= HOMEHOST ?>/data/GetUser.php?tk=<?= token() ?>&rol_id=<?= $id ?>",
                            dataSrc: "users"
                        },
                        columns: [{
                            "data": "legajo"
                        }, {
                            "data": "nombre"
                        }, {
                            "data": "usuario"
                        }, {
                            "data": "cliente"
                        }, {
                            "data": "estado_n"
                        }, {
                            "data": "fecha_alta"
                        }],
                        deferRender: true,
                        scrollX: true,
                        scrollCollapse: true,
                        paging: 1,
                        searching: 1,
                        scrollCollapse: true,
                        info: 1,
                        ordering: 1,
                    });
                });
            </script>
    <?php
        endforeach;
    endif;
    ?>
    <script>
        $(document).ready(function() {
            $('#table-roles').DataTable({
                scrollY: '50vh',
                scrollX: true,
                scrollCollapse: true,
                paging: 1,
                searching: 1,
                scrollCollapse: true,
                info: 1,
                language: {
                    "url": "/<?= HOMEHOST ?>/js/DataTableSpanish.json"
                },
                ordering: false
            });
        });
    </script>
    <script src="../../js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="modal.js"></script>
</body>

</html>
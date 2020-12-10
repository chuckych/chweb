<?php

// (!isset($_GET['alta']) or !isset($_GET['id'])) ? header("Location: /" . HOMEHOST . "/usuarios/clientes/") : '';
require __DIR__ . '/crud.php';
// UnsetGet('true');
UnsetGet('true');
UnsetGet('truem');
UnsetGet('trued');
UnsetGet('truee');
UnsetGet('e');
UnsetGet('id');
UnsetGet('v');
UnsetGet('ct');
UnsetGet('_rol');
// UnsetGet('dur');
ExisteCliente($_GET['_c']);

$url   = host() . "/" . HOMEHOST . "/data/GetUser.php?tk=" . token() . "&recid_c=" . $_GET['_c'];
$json  = file_get_contents($url);
$array = json_decode($json, TRUE);
if (is_array($array)) :
    if (!$array['error']) {
        $rowcount_u = (count($array['users']));
    }
endif;
// echo $url;
$data         = $array['users'];
$tituloform   = (isset($_GET['mod'])) ? 'Editar usuario' : 'Alta de usuario';
$ValueSubmit  = (isset($_GET['mod'])) ? 'editar' : 'alta';
$NombreSubmit = (isset($_GET['mod'])) ? 'Guardar' : 'Aceptar';
$ShadowEdit   = (isset($_GET['mod'])) ? 'shadow-lg border' : '';
$VerClave     = (isset($_GET['mod'])) ? '0' : '1';
$varEditForm  = (isset($_GET['mod'])) ? '?id=' . $_GET['id'] . '&mod&_c=' . $_GET['_c'] : '';
$varEditForm2 = (isset($_GET['_c'])) ? '?_c=' . $_GET['_c'] . '&alta' : '';
$collapse     = (isset($_GET['mod'])) ? '0' : 'collapse';
/** Recooremos array para editar usuario filtrando por el recid de la table mediante la varieble GET id */
if (isset($_GET['mod'])) :
    $r = array_filter($data, function ($e) {
        return $e['recid'] == $_GET['id'];
    });
    foreach ($r as $value) :
        if ($value['recid'] == $_GET['id']) {
            $nombre  = $value['nombre'];
            $usuario = $value['usuario'];
            $rol     = $value['rol'];
            $recid   = $value['recid'];
            $uid   = $value['uid'];
        }
    endforeach;
endif;

function ListaRoles2()
{
    $url   = host() . "/" . HOMEHOST . "/data/GetRoles.php?tk=" . token();
    $json  = file_get_contents($url);
    $array = json_decode($json, TRUE);
    $data  = $array['roles'];
    if (is_array($array)) :
        $r = array_filter($data, function ($e) {
            return $e['id'] == $_POST['rol'];
        });
        foreach ($r as $value) :
            $nombre  = $value['nombre'];
            $id  = $value['id'];
            echo '<option selected value="' . $id . '">' . $nombre . '</option>';
        endforeach;
    endif;
}
function ListaRoles3($recid)
{
    $url          = host() . "/" . HOMEHOST . "/data/GetUser.php?tk=" . token() . "&recid=" . $recid;
    $json         = file_get_contents($url);
    $array        = json_decode($json, TRUE);
    $data         = $array['users'];
    if (is_array($array)) :
        $r = array_filter($data, function ($e) {
            return $e['recid'] == $_GET['id'];
        });
        foreach ($r as $value) :
            $nombre = $value['rol_n'];
            $id     = $value['rol'];
            echo '<option selected value="' . $id . '">' . $nombre . '</option>';
        endforeach;
    endif;
}
list($id_c, $ident, $nombre_c, $recid_c, $host_c) = Cliente_c($_GET['_c']);
define("id_c", $id_c);
define("nombre_c", $nombre_c);
define("ident", $ident);
define("host_c", $host_c);
// define("recid_c", $recid_c);
?>
<!doctype html>
<html lang="es">

<head>
    <?php require __DIR__ . "../../llamadas.php"; ?>
    <title>Usuarios</title>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow">
        <?php require __DIR__ . '../../nav.php'; ?>
        <?=
            encabezado_mod2('bg-custom', 'white', 'people-fill',  'Usuarios: ' . nombre_c, '25', 'text-white mr-2');
        ?>
        <form name="alta" id="alta" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?<?= $_SERVER['QUERY_STRING'] ?>" method="post" class="" autocomplete="off" onsubmit="ShowLoading()">
            <div class="">
                <div class="row mt-3">
                    <div class="col-6">
                        <a class="fontq btn px-3 text-white opa8 <?= $bgcolor ?>" data-toggle="collapse" href="#collapse_usuarios" role="button" aria-expanded="false" aria-controls="collapse_usuarios">
                            <?= $tituloform ?>
                        </a>
                        <p class="m-0 mt-2 fw4 fontq"><?= $nombre ?></p>
                        <?= $duplicado ?>
                    </div>
                    <div class="col-6">
                        <?php if (modulo_cuentas() == '1') { ?>
                            <a href="/<?= HOMEHOST ?>/usuarios/clientes/" class="w80 btn fontq float-right m-0 opa7 btn-custom">Cuentas</a>
                        <?php } ?>
                        <a href="/<?= HOMEHOST ?>/usuarios/roles/?_c=<?= $_GET['_c'] ?>" class="w80 mr-1 btn fontq float-right m-0 opa7 btn-custom">Roles</a>
                    </div>
                    <?= notif_ok_var('dur', 'Se importaron ' . $_GET['ct'] . ' usuarios desde Control Horario.<br />Duración de la importación: <span>' . $_GET['v'] . ' Segundos</span>') ?>
                    <?= notif_ok_var('true', 'Se restableció la contraseña de <span class="fw5">' . $_GET['true'] . '</span>') ?>
                    <?= notif_ok_var('truem', 'Se modificó usuario <span class="fw5">' . $_GET['truem'] . '</span>') ?>
                    <?= notif_ok_var('trued', 'Se eliminó el usuario <span class="fw5">' . $_GET['trued'] . '</span>') ?>
                    <?php
                    $msje = ($_GET['e'] === "0") ? 'Se <span class="fw5 text-success">habilito</span> el usuario ' : 'Se <span class="fw5 text-danger">deshabilito</span> el usuario ';
                    ?>
                    <?= notif_ok_var('truee', $msje . '<span class="fw5">' . $_GET['truee'] . '</span>') ?>

                    <div class="<?= $collapse ?>" id="collapse_usuarios">
                        <div class="col-12 form-inline mt-2">
                            <input type="text" required name="nombre" id="nombre" placeholder="Nombre y Apellido (*)" class=" h40 form-control mr-sm-2 <?= $ErrNombre ?>" value="<?= $nombre ?>">
                            <!-- <input type="text" name="usuario" id="" placeholder="" class="" value=""> -->
                            <input type="text" name="usuario" id="usuario" placeholder="Usuario Login" title="Este campo puede quedar vació. Se generara un usuario automáticamente." class="h40 form-control mt-2 mt-sm-0 mr-sm-2 <?= $ErrUsuario ?> usuario" value="<?= $usuario ?>">
                            <select required name="rol" id="rol" placeholder="Rol" class="h40 form-control mt-2 mt-sm-0 custom-select mr-sm-2 <?= $ErrRol ?>">
                                <option value="" disabled selected hidden>Rol (*)</option>
                                <?= ListaRoles($_GET['_c']) ?>
                                <?= ListaRoles3($_GET['id']) ?>
                            </select>
                            <input type="hidden" name="cliente" id="cliente" placeholder="cliente" class="mr-sm-2" value="<?= id_c ?>">
                            <input type="hidden" name="ident" id="ident" placeholder="ident" class="mr-sm-2" value="<?= ident ?>">
                            <input type="hidden" name="recid" id="recid" class="" value="<?= $_GET['id'] ?>">
                            <?php if ($VerClave) : ?>
                                <!-- <input type="password" name="contraseña" id="contraseña" placeholder="contraseña" class="form-control mr-sm-2 <?= $ErrContraseña ?>" value="<?= $contraseña ?>"> -->
                            <?php endif ?>
                            <button type="submit" name="submit" id="" class="d-none d-sm-block h40 w100 btn fontq float-right m-0 opa9 btn-custom" value="<?= $ValueSubmit ?>"><?= $NombreSubmit ?></button>
                            <button type="submit" name="submit" id="" class="d-block d-sm-none  mt-2 h50 w-100 btn float-right m-0 opa9 btn-custom" value="<?= $ValueSubmit ?>"><?= $NombreSubmit ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="mt-1">
            <div class="row">
                <div class="col-12 mb-2">
                    <a href="personal/?_c=<?= $_GET['_c'] ?>" class="fw4 btn float-right fontq btn-custom">IMPORTAR PERSONAL</a>
                </div>
                <div class="col-12">
                    <table class="table table-hover w-100 dt-responsive nowrap" id="table-user">
                        <thead class="text-uppercase">
                            <tr>
                                <th>Nombre</th>
                                <th>Legajo</th>
                                <th>Usuario</th>
                                <th>Rol</th>
                                <th class="text-center px-1"></th>
                                <th class="text-center px-1"></th>
                                <th class="text-center px-1"></th>
                                <th class="text-center px-1"></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- fin container -->
    <?php
    require __DIR__ . "../../js/jquery.php";
    require __DIR__ . "../../js/DataTable.php";
    ?>
    <script>
        $(document).ready(function() {
            $('#table-user').DataTable({
                search: {
                    search: ("<?= $_GET['_rol'] ?>"),
                    regex: true,
                },
                deferRender: true,
                ajax: {
                    url: "?p=array_user.php&_c=<?= $_GET['_c'] ?>",
                    dataSrc: "users"
                },
                columns: [{
                    "class": "align-middle",
                    "data": "nombre"
                }, {
                    "class": "align-middle",
                    "data": "legajo"
                }, {
                    "class": "align-middle",
                    "data": "usuario"
                }, {
                    "class": "align-middle",
                    "data": "rol"
                }, {
                    "class": "text-center px-1",
                    "data": "reset_c"
                }, {
                    "class": "text-center px-1",
                    "data": "editar"
                }, {
                    "class": "text-center px-1",
                    "data": "dar_baja"
                }, {
                    "class": "text-center px-1",
                    "data": "eliminar"
                }],
                scrollX: true,
                scrollCollapse: true,
                paging: 1,
                searching: 1,
                scrollCollapse: true,
                info: 1,
                ordering: 0,
                responsive: false,
                language: {
                    "url": "/<?= HOMEHOST ?>/js/DataTableSpanishShort2.json",
                },
            });
        });
    </script>
</body>

</html>
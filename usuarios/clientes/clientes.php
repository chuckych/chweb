<?php
require __DIR__ . '/crud.php';
UnsetGet('id');
$url          = host() . "/" . HOMEHOST . "/data/GetClientes.php?tk=" . token();
$json         = file_get_contents($url);
$array        = json_decode($json, TRUE);
if (is_array($array)) :
    $rowcount     = (count($array[0]['clientes']));
endif;
$data         = $array[0]['clientes'];
$tituloform   = (isset($_GET['mod'])) ? 'Editar Cuenta' : 'Agregar Cuenta';
$ValueSubmit  = (isset($_GET['mod'])) ? 'editar' : 'alta';
$NombreSubmit = (isset($_GET['mod'])) ? 'Guardar' : 'Aceptar';
$ShadowEdit   = (isset($_GET['mod'])) ? 'shadow-lg border' : '';
$VerClave     = (isset($_GET['mod'])) ? '0' : '1';
$collapse     = (isset($_GET['mod'])) ? '0' : 'collapse';
$varEditForm  = (isset($_GET['mod'])) ? '?id=' . $_GET['id'] . '&mod' : '';

/** Recorremos array para editar usuario filtrando por el recid de la table mediante la variable GET id */
if (isset($_GET['mod'])) :
    $r = array_filter($data, function ($e) {
        return $e['recid'] == $_GET['id'];
    });
    foreach ($r as $value) :
        if ($value['recid'] == $_GET['id']) {
            $nombre     = $value['nombre'];
            $host       = $value['host'];
            $db         = $value['db'];
            $user       = $value['user'];
            $pass       = $value['pass'];
            $auth       = $value['auth_windows'];
            $auth       = ($auth == 1) ? 'checked' : '';
            $identauto  = $value['ident'];
            $tkmobile   = $value['tkmobile'];
            $WebService = $value['WebService'];
            // $rol = $value['rol'];
            // $recid = $value['recid'];
        }
    endforeach;
endif;
?>
<!doctype html>
<html lang="es">

<head>
    <?php require __DIR__ . "../../../llamadas.php"; ?>
    <title><?= MODULOS['cuentas'] ?></title>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow">
        <?php require __DIR__ . '../../../nav.php'; ?>
        <!-- Encabezado -->
        <?=
            encabezado_mod2('bg-custom', 'white', 'diagram-3-fill',  MODULOS['cuentas'], '25', 'text-white mr-2');
        ?>
        <!-- Fin Encabezado -->
        <form action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>?<?= $_SERVER['QUERY_STRING'] ?>" method="post" class="">
            <div class="">
                <div class="row mt-3">
                    <div class="col-12 mb-3">
                        <a class="fontq btn opa8 btn-custom" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">
                            <?= $tituloform ?>
                        </a>
                        <?= $duplicado ?>
                    </div>
                    <div class="<?= $collapse ?>" id="collapseExample">
                        <div class="col-12 form-inline">
                            <input type="text" required title="campo obligatorio" name="nombre" id="nombre" placeholder="Nombre de Cuenta" class="h40 form-control mr-sm-2 <?= $ErrNombre ?>" value="<?= $nombre ?>">
                            <?php if ($VerClave) : ?>
                                <input title="El identificador puede quedar en blanco. El mismo se genera automáticamente" type="text" maxlength="3" name="ident" id="ident" placeholder="Identificador de Cuenta" class="mt-2 mt-sm-0 h40 form-control mr-sm-2" value="<?= $identauto ?>">
                            <?PHP endif; ?>
                        </div>
                        <div class="col-12 mt-2">
                            <p class="fontq m-0 mb-1 fw4">Datos de conexion SQL:</p>
                        </div>
                        <div class="col-12 form-inline">
                            <input type="text" name="host" id="host" placeholder="Host" class="h40 form-control mr-sm-2" value="<?= $host ?>">
                            <input type="text" name="db" id="db" placeholder="Base de Datos" class="h40 form-control mr-sm-2 mt-2 mt-sm-0" value="<?= $db ?>">
                        </div>
                        <div class="col-12 form-inline mt-0 mt-sm-2">
                            <input type="text" name="user" id="user" placeholder="Usuario" class="h40 form-control mr-sm-2 mt-2 mt-sm-0" value="<?= $user ?>">
                            <input type="text" name="pass" id="pass" placeholder="Password" class="h40 form-control mr-sm-2 mt-2 mt-sm-0" value="<?= $pass ?>">
                            <?php if (!$VerClave) : ?>
                                <input type="hidden" name="recid" id="recid" placeholder="recid" class="" value="<?= $_GET['id'] ?>">
                            <?php endif ?>
                        </div>
                        <div class="col-12 form-inline mt-2">
                            <div class="custom-control custom-switch ml-3">
                                <input type="checkbox" <?= $auth ?> name="auth" class="custom-control-input" id="auth" value="1">
                                <label class="custom-control-label fw4" for="auth">
                                    <p class="mt-1 text-secondary">Autenticación de Windows</p>
                                </label>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <p class="fontq m-0 mb-1 fw4">Mobile:</p>
                        </div>
                        <div class="col-12">
                            <input type="text" title="" name="tkmobile" id="tkmobile" placeholder="Token Mobile" class="h40 form-control mr-sm-2" value="<?= $tkmobile ?>">
                        </div>
                        <div class="col-12 mt-2">
                            <p class="fontq m-0 mb-1 fw4">Ruta WebService:</p>
                        </div>
                        <div class="col-12">
                            <input type="text" title="WebService" name="WebService" id="WebService" placeholder="Ejemplo: http://192.168.1.202:6400" class="h40 form-control mr-sm-2" value="<?= $WebService ?>">
                        </div>
                        <div class="col-12 mt-sm-2 mt-0">
                            <button type="submit" name="submit" id="submit" class="d-none d-sm-block mt-2 mt-sm-0 h40 w100 btn fontq float-right m-0 opa9 btn-custom" value="<?= $ValueSubmit ?>"><?= $NombreSubmit ?></button>
                            <button type="submit" name="submit" id="submit" class="d-block d-sm-none my-3 btn-block mt-2 mt-sm-0 h50 btn fontq float-right m-0 opa9 btn-custom" value="<?= $ValueSubmit ?>"><?= $NombreSubmit ?></button> <!-- button mobile -->
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="mt-1">
            <div class="row">
                <div class="col-12 mb-2">
                    Lista de Cuentas
                </div>
                <div class="col-12 table-responsive">
                    <table class="table table-hover text-nowrap w-100" id="GetClientes">
                        <thead class="text-uppercase ">
                            <tr>
                                <th class="text-center"><span data-icon="&#xe042;"></th>
                                <th>CUENTA</th>
                                <th>USUARIOS</th>
                                <th>ROLES</th>
                                <th>IDENT</th>
                                <th>HOST / DB</th>
                                <!-- <th>BASE DE DATOS</th> -->
                                <th>USUARIO / PASS</th>
                                <!-- <th>PASSWORD</th> -->
                                <th>AUT</th>
                                <th>TKMOBILE</th>
                                <th>WEBSERVICE</th>
                                <!-- <th>EDITAR</th> -->
                                <!-- <th></th> -->
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <!-- fin container -->
        <?php
        /** INCLUIMOS LIBRERÍAS JQUERY */
        require __DIR__ . "../../../js/jquery.php";
        require __DIR__ . "../../../js/DataTable.php";
        ?>
        <script src="data.js"></script>
</body>

</html>
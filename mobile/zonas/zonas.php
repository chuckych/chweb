<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../../llamadas.php"; ?>
    <title><?= MODULOS['mobilezonas'] ?></title>
    <style type="text/css" media="screen">
        td {
            vertical-align: middle !important
        }

        /* .map_canvas { float: left; }
        form { width: 300px; float: left; } */

        #maps {
            height: 500px;
        }

        /* #geocomplete { width: 200px} */

        .map_canvas {
            width: 100%;
            height: 350px;
        }

        #examples a {
            text-decoration: underline;
        }

        #multiple li {
            cursor: pointer;
            text-decoration: underline;
        }
    </style>
    <script>
    </script>

</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2" style="animation-fill-mode: unset">
        <?php require __DIR__ . '../../../nav.php'; ?>
        <!-- Encabezado -->
        <?= encabezado_mod('bg-mob', 'white', 'markermap.png', MODULOS['mobilezonas'], '') ?>
        <!-- Fin Encabezado -->
        <?php if (token_exist($_SESSION['RECID_CLIENTE'])) {
            /** Check de token */ ?>
            <input type="hidden" id="latitud">
            <input type="hidden" id="longitud">
            <input type="hidden" id="zona">
            <input type="hidden" id="map_size">
            <div class="row bg-white py-2 radius" id="VerZonas">
                <div class="col-12 m-0 pb-2">
                    <button class="btn btn-outline-custom border-danger fontq float-left opa8" id="Zona"><i class="bi bi-plus-lg mr-2"></i>Nueva Zona</button>
                    <a class="btn btn-outline-custom border-danger ml-1 fontq opa8 text-decoration-none fontq" href="../"><i class="bi bi-clipboard-data mr-2"></i>Fichadas</a>
                    <a class="btn btn-outline-custom border-danger ml-1 fontq opa8 text-decoration-none fontq float-left" href="../usuarios/"><i class="bi bi-people-fill mr-2"></i>Usuarios</a>
                    <button class="btn btn-sm btn-link text-decoration-none fontq text-secondary p-0 pb-1 m-0 float-right" id="Refresh">Actualizar Grilla</button>
                </div>
                <div class="col-12 table-responsive" id="divtable">
                    <table class="table table-hover table-sm text-nowrap w-100" id="table-zonas">
                        <thead class="text-uppercase border-top-0">
                            <tr>
                                <th class="w30"></th>
                                <th class="w200">Zona</th>
                                <th class="w40">Radio</th>
                                <th class="w40"></th>
                                <th class=""></th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="col-12" id="divmap">
                    <div class="divmap d-none">
                        <div class="d-flex inline d-flex justify-content-between">
                            <div class="fontq" id="NombreZona"></div>&nbsp;
                            <div class="fontq" id="RadioZona"></div>
                        </div>
                        <div id="maps" class="shadow-sm"></div>
                    </div>
                </div>
                <?php
                if (modulo_cuentas()):
                ?>
                <div class="col-12 m-0">
                    <form action="../RefreshToken.php" method="POST" id="RefreshToken">
                        <select class="selectjs_cuentaToken w200" id="tk" name="tk">
                        </select>
                    </form>
                </div>
                <?php
                endif;
                ?>
            </div>
            

            <div class="d-none p-2 mt-2" id="rowNuevaZona">
                <div class="row mb-2">
                    <div class="col-12">
                        <span class="text-dark pr-3">Nueva Zona</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <form>
                            <div class="form-group">
                                <input id="geocomplete" type="text" class="form-control h40" placeholder="Ingrese un lugar o dirección" value="" />
                            </div>
                            <div class="pb-4">
                                <input type="reset" value="Reset" class="float-right btn btn-outline-custom border btn-sm fontq">
                            </div>

                        </form>
                        <form action="insert-zone.php" method="POST" id="CrearZona">
                            <div class="form-inline">
                                <label for="nombre" class="text-nowrap fontq w80">Nombre</label>
                                <input type="text" class="form-control h40 w300" id="nombre" required name="nombre" placeholder="Nombre de la zona" pattern="[a-zA-Z0-9- _ñ]+">
                            </div>
                            <div class="form-inline mt-2">
                                <label for="metros" class="text-nowrap fontq w80">Radio</label>
                                <select name="metros" id="metros" class="select2 form-control w300 h40">
                                    <?php
                                    foreach (RADIOS as $key => $value) {
                                        echo '<option value="' . $value . '">' . $key . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group row">
                                <div class="col-12">
                                    <button type="submit" class="float-right btn btn-custom btn-sm px-3 opa8 fontq" name="submit" value="true" id="btnSubmitZone">Crear Zona</button>
                                    <button type="button" class="float-right btn btn-link text-decoration-none text-secondary btn-sm px-3 fontq" id="cancelZone">Cancelar</button>
                                </div>
                            </div>
                            <div class="d-inline-flex m-0">
                                <div name="formatted_address" value="" class="text-dark fontq border-0" readonly></div>
                                <input name="lat" type="hidden">
                                <input name="lng" type="hidden">
                                <input name="alta_zona" type="hidden" value="true" class="">
                                <input type="hidden" name="u_nombre" id="u_nombre">
                            </div>

                            <div class="map_canvas" id="map_canvas"></div>
                        </form>
                        <a id="reset" href="#" style="display:none;" class="my-2 btn btn-outline-custom border btn-sm">Resetar Marcador</a>
                    </div>
                </div>
            </div>


        <?php } else {
            echo '<div class="alert alert-light mt-3">La Cuenta no tiene Token Mobile Asociado</div>';
        }
        /** Fin de check de token*/ ?>
        <?php //require 'modalCreaZona.html'; 
        ?>
        <?php require 'modalEliminaZona.html'; ?>
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "../../../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATATABLE */
    require __DIR__ . "../../../js/DataTable.php";
    ?>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?=API_KEY_MAPS()?>&sensor=false&amp;libraries=places" defer></script>
    <script src="../../js/lib/geocomplete/jquery.geocomplete.js"></script>
    <script src="../../js/select2.min.js"></script>
    <script src="script-min.js?v=<?=vjs()?>"></script>

</body>

</html>
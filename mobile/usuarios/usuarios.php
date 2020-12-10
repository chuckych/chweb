<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../../llamadas.php"; ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.1.2/css/rowGroup.dataTables.min.css">
    <title><?= MODULOS['mobileuser'] ?></title>
    <style type="text/css" media="screen">
        img[src="https://server.xenio.uy/img/white_logo.png"] {
            display: none !important
        }

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
        .dtrg-level-1{
            font-size: .8rem !important;
        }
        table.dataTable tr.dtrg-group.dtrg-level-0 td {
        font-weight: normal;
        font-size: .8rem !important;
        }
        table.dataTable tr.dtrg-group.dtrg-level-1 td {
        font-weight: normal;
        font-size: .8rem !important;
        }
        table.dataTable tr.dtrg-group td {
            background-color: #fafafa;
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
        <?= encabezado_mod('bg-custom', 'white', 'usuarios.png', MODULOS['mobileuser'], '') ?>
        <!-- Fin Encabezado -->
        <?php if (token_exist($_SESSION['RECID_CLIENTE'])) {
            /** Check de token */ ?>
            <div class="row bg-white py-2 radius" id="VerUsuarios">
                <div class="col-12 m-0 pb-2">
                    <button class="btn btn-sm btn-custom border fontq float-left opa8" id="NuevoUsuario">Nuevo Usuario</button>
                    <a class="btn btn-link border-0 fontq opa8 text-decoration-none fontq text-secondary" href="../">Fichadas</a>
                    <a class="btn btn-link border-0 fontq opa8 text-decoration-none fontq text-secondary float-left" href="../zonas/">Zonas</a>
                    <button class="btn btn-sm btn-link text-decoration-none fontq text-secondary p-0 pb-1 m-0 float-right" id="Refresh">Actualizar Grilla</button>
                </div>
                <div class="col-12 table-responsive" id="divtable">
                    <table class="table text-wrap w-100" id="table-usuarios">
                        <thead class="text-uppercase border-top-0">
                            <th>Usuario</th>
                            <th class="d-none">Rostro</th>
                            <th class="d-none"></th>
                            <th>Estado</th>
                            <th>Fecha Alta</th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th class=""></th>
                        </thead>
                    </table>
                </div>
                <?php
                if (modulo_cuentas()) :
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
            <div class="row d-none" id="divEntrenar">
                
            </div>
            <div class="d-none p-3 mt-2 shadow-sm " id="rowNuevoUsuario">
                <div class="row mb-2">
                    <div class="col-12">
                        <span class="text-dark pr-3" id="Titulo">Nuevo</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <form action="insert-usuarios.php" method="POST" id="CrearUsuario" class="w320">
                            <div class="form-group">
                                <label for="_id" class="text-nowrap fontq w80">ID<span class="fontp ml-1">(*)</span></label>
                                <input type="text" data-mask='00000000' reverse="true" class="form-control h40 w300" id="_id" required name="_id" placeholder="D.N.I.">
                                <input type="hidden" name="alta" value="true">
                            </div>
                            <div class="form-group">
                                <label for="_name" class="text-nowrap fontq w80">Nombre<span class="fontp ml-1">(*)</span></label>
                                <input type="text" class="form-control h40 w300" maxlength="30" placeholder="Nombre y Apellido" id="_name" required name="_name">
                            </div>
                            <div class="form-group">
                                <label for="_email" class="text-nowrap fontq w80">Email</label>
                                <input type="email" class="form-control h40 w300" maxlength="50" placeholder="Correo electr&oacute;nico" id="_email" name="_email">
                            </div>
                            <div class="form-group">
                                <label for="_enable" class="text-nowrap fontq w80">Estado</label>
                                <div class="custom-control custom-switch custom-control-inline ml-1 d-flex align-items-center">
                                    <input type="checkbox" name="_enable" class="custom-control-input" id="_enable">
                                    <label class="custom-control-label" for="_enable" style="padding-top: 3px;">Activo / Inactivo</label>
                                </div>
                            </div>
                            <div class="form-group d-flex justify-content-end mr-3">
                                <button type="button" class="float-right btn btn-link text-decoration-none text-secondary btn-sm px-3 fontq" id="cancelUsuario">Cancelar</button>
                                <button type="submit" class="float-right btn btn-custom btn-sm px-3 opa8 fontq" name="submit" value="true" id="btnSubmitUser">Crear</button>
                            </div>
                            <div class="mt-2 d-none" id="divRespuesta">

                            </div>
                        </form>
                    </div>
                </div>
            </div>


        <?php } else {
            echo '<div class="alert alert-light mt-3">La Cuenta no tiene Token Mobile Asociado</div>';
        }
        /** Fin de check de token*/ ?>
        <?php //require 'modalCreaZona.html'; 
        ?>
        <?php require 'modalEliminaUsuario.html'; ?>
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "../../../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATATABLE */
    require __DIR__ . "../../../js/DataTable.php";
    ?>
    <script src="https://cdn.datatables.net/rowgroup/1.1.2/js/dataTables.rowGroup.min.js"></script>
    <script src="../../vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= API_KEY_MAPS() ?>&sensor=false&amp;libraries=places" defer></script>
    <script src="../../js/lib/geocomplete/jquery.geocomplete.js"></script>
    <script src="../../js/select2.min.js"></script>
    <script src="script-min.js"></script>
</body>

</html>
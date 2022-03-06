<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../../llamadas.php"; ?>
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.1.2/css/rowGroup.dataTables.min.css"> -->
    <title>Fichadas Mobile HR</title>
    <style type="text/css" media="screen">
        .datos {
            margin-left: 5px;
            margin-top: 0px;
            padding-top: 0px;
        }

        .datos div {
            font-weight: bold;
        }

        .datos p {
            margin: 0px;
            padding: 0px;
        }

        .datos label {
            margin: 0px;
            padding: 0px;
        }

        #mapzone {
            width: 100%;
            height: 250px;
        }

        table.dataTable td {
            vertical-align: middle;
        }

        table.dataTable {
            margin-top: 0px !important;
        }

        .navbar-toggler {
            z-index: 1;
            box-shadow: none !important;
            outline: 0 !important;
        }

        .addDevice,
        .linkMapa {
            border-radius: 2px !important;
        }
    </style>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2" style="animation-fill-mode: unset" id="container">

        <?php require __DIR__ . '../../../nav.php'; ?>
        <!-- Encabezado -->
        <?= encabezado_mod3('bg-mob', 'white', '../../img/mobile-hrp-2.svg', 'Fichadas ' . MODULOS['mobile'] . ' HR', 'color: #fff; width:30px', 'mr-2'); ?>
        <!-- Fin Encabezado -->
        <?php
        $FirstDate = "2019/01/01";
        $FirstYear = '2019';
        $maxDate   = date('Y-m-d');
        $maxYear   = date('Y');
        /** Para dateRangePicker */
        // $arrayFech = (fecha_min_max_mysql('reg_', 'fechaHora'));
        $query = "SELECT MIN(fechaHora) AS 'min', MAX(fechaHora) AS 'max' FROM reg_ WHERE id_company = '$_SESSION[ID_CLIENTE]'";
        $arrayFech = simple_pdoQuery($query);

        $min = !empty($arrayFech['min']) ? FechaFormatVar($arrayFech['min'], 'd-m-Y') : date('d-m-Y');
        $max = !empty($arrayFech['max']) ? FechaFormatVar($arrayFech['max'], 'd-m-Y') : date('d-m-Y');
        $aniomin = !empty($arrayFech['min']) ? FechaFormatVar($arrayFech['min'], 'Y') : date('Y');
        $aniomax = !empty($arrayFech['max']) ? FechaFormatVar($arrayFech['max'], 'Y') : date('Y');
        echo '<input type="hidden" id="min" value="' . $min . '">';
        echo '<input type="hidden" id="max" value="' . $max . '">';
        echo '<input type="hidden" id="aniomin" value="' . $aniomin . '">';
        echo '<input type="hidden" id="aniomax" value="' . $aniomax . '">';

        ?>
        <input type="hidden" id="_drMob2">

        <div class="row mt-2">
            <div class="col-12 px-1">
                <nav class="navbar navbar-expand-lg p-0 py-1 m-0">
                    <div class="p-0">
                        <button class="navbar-toggler btn btn-outline-custom border-ddd ml-3" type="button"
                            data-toggle="collapse" data-target="#navbarBtnMenu" aria-controls="navbarBtnMenu"
                            aria-expanded="false" aria-label="Toggle navigation">
                            <i class="bi bi-list"></i>
                        </button>
                    </div>
                    <div class="collapse navbar-collapse w-100 float-left ml-2 ml-sm-1" id="navbarBtnMenu">
                        <ul class="navbar-nav mr-auto px-2 w-100" id="btnMenu">
                            <div class="d-sm-inline-flex">
                                <li class="nav-item mt-2 mt-sm-0">
                                    <button readonly data-titlet="Fichadas" type="button"
                                        class="h35 mr-1 btn btn-custom border btn-sm px-3 showChecks fontq">
                                        <span class="d-block d-sm-none w100">
                                            <div class="d-flex alig-items-center justify-content-between">
                                                <div class="">Fichadas</div>
                                                <div class=""><i class="ml-3 bi bi-clipboard-data-fill"></i></div>
                                            </div>
                                        </span>
                                        <span class="d-none d-sm-block"><i class="bi bi-clipboard-data-fill"></i></span>
                                    </button>
                                </li>
                                <li class="nav-item mt-2 mt-sm-0">
                                    <button data-titlet="Gestión de usuarios" type="button"
                                        class="h35 mr-1 btn btn-outline-custom border-ddd btn-sm px-3 showUsers fontq">
                                        <span class="d-none d-sm-block"><i class="bi bi-people-fill"></i></span>
                                        <span class="d-block d-sm-none w100">
                                            <div class="d-flex alig-items-center justify-content-between">
                                                <div class="">Usuarios</div>
                                                <div class=""><i class="ml-3 bi bi-people-fill"></i></div>
                                            </div>
                                        </span>
                                    </button>
                                </li>
                                <li class="nav-item mt-2 mt-sm-0">
                                    <button data-titlet="Dispositivos"
                                        class="h35 mr-1 btn btn-sm btn-outline-custom border-ddd fontq h35 px-3 showDevices fontq">
                                        <span class="d-block d-sm-none w100">
                                            <div class="d-flex alig-items-center justify-content-between">
                                                <div class="">Dispositivos</div>
                                                <div class=""><i class="ml-3 bi bi-tablet-fill"></i></div>
                                            </div>
                                        </span>
                                        <span class="d-none d-sm-block"><i class="bi bi-tablet-fill"></i></span>
                                    </button>
                                </li>
                            </div>
                            <div class="w-100">
                                <li class="nav-item mt-2 mt-sm-0">
                                    <button data-titlel="Descargar registros"
                                        class="h35 mr-1 btn btn-sm btn-outline-custom border-ddd fontq h35 px-3 actualizar fontq float-right d-none d-sm-block">
                                        <span class="d-block d-sm-none w100">
                                            <div class="d-flex alig-items-center justify-content-between">
                                                <div class="">Actualizar</div>
                                                <div class=""><i class="ml-3 bi bi-cloud-download-fill"></i></div>
                                            </div>
                                        </span>
                                        <span class="d-none d-sm-block"><i class="bi bi-cloud-download-fill"></i></span>
                                    </button>
                                </li>
                                <button id="expandContainer" type="button" class="h35 float-right d-none d-sm-block btn btn-sm btn-outline-custom border-0 w35 fontq mr-1 text-secondary linkMapa"><i class="bi bi-arrows-angle-expand "></i></button>
                            </div>
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
        <div class="row bg-white pb-3 radius invisible" id="RowTableMobile">
            <div class="col-12 table-responsive" id="divTableMobile">
                <table class="table text-nowrap w-100" id="table-mobile">
                    <thead class="fontq shadow-sm">
                    </thead>
                </table>
            </div>
            <?php
            if (modulo_cuentas()) :
            ?>
            <div class="col-12 m-0 mt-2">
                <form action="changeCompanyApi.php" method="POST" id="RefreshToken">
                    <select class="selectjs_cuentaToken w200" id="recid" name="recid">
                    </select>
                </form>
            </div>
            <?php
            endif;
            ?>
        </div>
        <div class="bg-white pb-3 radius" id="RowTableUsers">
            <?php require_once __DIR__ . "/users.html"; ?>
        </div>
        <div class="bg-white pb-3 radius" id="RowTableDevices">
            <?php require_once __DIR__ . "/devices.html"; ?>
        </div>

    </div>
    <div id="modales"></div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require 'modal.php';
    require __DIR__ . "../../../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    require __DIR__ . "../../../js/DateRanger.php";
    /** INCLUIMOS LIBRERÍAS y script DATATABLE */
    require __DIR__ . "../../../js/DataTable.php";

    ?>
    <!-- <script src="https://cdn.datatables.net/rowgroup/1.1.2/js/dataTables.rowGroup.min.js"></script> -->
    <!-- <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script> -->
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= API_KEY_MAPS() ?>&sensor=false&amp;libraries=places"
        defer></script>
    <!-- <script src="../js/lib/geocomplete/jquery.geocomplete.js"></script> -->
    <script src="../../js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="../../vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="../../js/clipboardjs/dist/clipboard.min.js"></script>
    <script src="../../js/select2.min.js"></script>
    <script src="js/script.js?v=<?= time() ?>"></script>
    <script src="js/script_users.js?v=<?= time() ?>"></script>
    <script src="js/script_devices.js?v=<?= time() ?>"></script>
    <!-- <script src="FicMobExcel.js?v=<?= time() ?>"></script> -->

</body>

</html>
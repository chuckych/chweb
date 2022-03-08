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
            /* height: 50px; */
        }

        #table-mobile.dataTable td {
            vertical-align: middle;
            height: 50px;
        }

        .smtd {
            display: flex;
            align-items: center;
            height: 100%;
        }
        .smtdcol {
            display: flex;
            align-items: left;
            flex-direction: column;
            /* height: 100%; */
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

        .radisu2 {
            border-radius: 2px !important;
        }

        #btnMenu button {
            line-height: 1.1rem;
            /* border: 1px solid #cecece !important; */
        }

        @media all and (max-width:480px) {
            #btnMenu {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
                padding-bottom: 5px;
            }
            .lengthFilterTable{
                display: block;
                overflow-x: auto;
                white-space: nowrap;
                padding-bottom: 5px;
            }
        }
    </style>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2" style="animation-fill-mode: unset" id="container">

        <?php require __DIR__ . '../../../nav.php'; ?>
        <!-- Encabezado -->
        <div id="encabezado" class="sticky-top">
            <?= encabezado_mod3('bg-mob', 'white', '../../img/mobile-hrp-3.svg', 'Fichadas ' . MODULOS['mobile'], 'color: #fff; width:30px', 'mr-2'); ?>
        </div>
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

        <?php require __DIR__ . '../menuBtn.html' ?>

        <div class="wrapper">
            <div class="loading show">
                <div class="spin"></div>
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
            <div class="bg-white pb-3 radius invisible" id="RowTableUsers">
                <div class="row">
                    <div class="col-12 col-lg-8">
                        <table class="table text-nowrap w-100" id="tableUsuarios">
                            <thead class="fontq"></thead>
                        </table>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div id="divformUsuario">
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white pb-3 invisible" id="RowTableDevices">
                <div class="row">
                    <div class="col-12 col-sm-6 w-100">
                        <table class="table text-nowrap w-auto" id="tableDevices">
                            <thead class="fontq"></thead>
                        </table>
                    </div>
                </div>
            </div>
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
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= API_KEY_MAPS() ?>&sensor=false&amp;libraries=places" defer></script>
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
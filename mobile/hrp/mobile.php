<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../../llamadas.php"; ?>
    <link rel="stylesheet" href="css/styleMobile.css?=<?= time() ?>">
    <title>Fichadas Mobile HR</title>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2" style="animation-fill-mode: unset" id="container">
        <?php require __DIR__ . '../../../nav.php'; ?>
        <!-- Encabezado -->
        <div id="encabezado" class="sticky-top">
            <?php encabezado_mod3('bg-mob', 'white', '../../img/mobile-hrp-3.svg', 'Fichadas ' . MODULOS['mobile'], 'color: #fff; width:30px', 'mr-2'); ?>
        </div>
        <!-- Fin Encabezado -->
        <?php
        $FirstDate = "2019/01/01";
        $FirstYear = '2019';
        $maxDate   = date('Y-m-d');
        $maxYear   = date('Y');
        /** Para dateRangePicker */
        $api = "api/v1/checks/dates.php?key=$_SESSION[RECID_CLIENTE]";
        $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = getRemoteFile($url, $timeout = 10);
        // print_r($url);
        $api = json_decode($api, true);
        $arrayFech = $api['RESPONSE_DATA'];
        $min = !empty($arrayFech['min']) ? FechaFormatVar($arrayFech['min'], 'd-m-Y') : date('d-m-Y');
        $max = !empty($arrayFech['max']) ? FechaFormatVar($arrayFech['max'], 'd-m-Y') : date('d-m-Y');
        $aniomin = !empty($arrayFech['min']) ? FechaFormatVar($arrayFech['min'], 'Y') : date('Y');
        $aniomax = !empty($arrayFech['max']) ? FechaFormatVar($arrayFech['max'], 'Y') : date('Y');
        echo '<input type="hidden"  id="min" value="' . $min . '">';
        echo '<input type="hidden"  id="max" value="' . $max . '">';
        echo '<input type="hidden"  id="aniomin" value="' . $aniomin . '">';
        echo '<input type="hidden"  id="aniomax" value="' . $aniomax . '">';
        ?>
        <input type="hidden" id="_drMob2">
        <input type="hidden" id="actMobile" value="<?= $_GET['act'] ?? 0 ?>">
        <!-- <input type="hidden" id="apiMobile" value="<?= $_SESSION["APIMOBILEHRP"] ?? 0 ?>"> -->
        <?php
        if ($_SERVER['SERVER_NAME'] == 'localhost') { // Si es localhost

            echo '<input type="hidden" id="apiMobile" value="' . $_SESSION["APIMOBILEHRP"] . '">';
        } else {
            echo '<input type="hidden" id="apiMobile" value="http://chweb.ar">';
        }
        ?>
        <?php require __DIR__ . '../menuBtn.html' ?>
        <div class="wrapper">
            <div class="row bg-white invisible mt-2" id="RowTableMobile">
                <div class="col-12" id="divTableMobile">
                    <table class="table text-nowrap w-100 border shadow p-2" id="table-mobile">
                        <thead class="fontq">
                        </thead>
                    </table>
                </div>
                <?php
                if (modulo_cuentas()) :
                ?>
                    <div class="col-12 m-0 mt-2">
                        <form action="changeCompanyApi.php" method="POST" id="RefreshToken">
                            <select class="selectjs_cuentaToken w200" id="recid" name="recid" style="display:none">
                            </select>
                        </form>
                    </div>
                    <div class="col-12">
                        <div class="fontq mt-2 p-2">
                            <div>ID: <span id="dataIdCompany" class="ml-1"></span></div>
                            <div>AppCode: <span id="dataRecidCompany" class="ml-1"></span></div>
                        </div>
                    </div>
                <?php
                endif;
                ?>
            </div>
            <div class="bg-white invisible mt-2" id="RowTableUsers">
                <div class="row">
                    <div class="col-12 col-sm-10">
                        <table class="table text-nowrap w-100 border shadow p-2" id="tableUsuarios">
                            <thead class="fontq"></thead>
                        </table>
                    </div>
                    <div class="col-12 col-sm-4">
                    </div>
                </div>
            </div>
            <div class="bg-white invisible mt-2" id="RowTableDevices">
                <div class="row">
                    <div class="col-12 col-sm-10">
                        <table class="table text-nowrap w-100 border shadow p-2" id="tableDevices">
                            <thead class="fontq"></thead>
                        </table>
                    </div>
                    <div class="col-12 col-sm-4">
                    </div>
                </div>
            </div>
            <div class="bg-white invisible mt-2" id="RowTableZones">
                <div class="row">
                    <div class="col-12 col-lg-8">
                        <table class="table text-nowrap w-100 border shadow p-2" id="tableZones">
                            <thead class="fontq"></thead>
                        </table>
                    </div>
                    <div class="col-12 col-lg-4"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <span id="mapTitle"></span>
                </div>
                <div class="col-12 mb-4 pb-4">
                    <div id="map" class="p-3" style="border:4px solid #ddd; display:none"></div>
                    <div id="positionMap"></div>
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
    /** INCLUIMOS LIBRERÍAS y script DATATABLE */
    require __DIR__ . "../../../js/DataTable.php";
    ?>
    <script type="text/javascript" src="/<?= HOMEHOST ?>/js/dateranger/moment.min.js"></script>
    <script type="text/javascript" src="/<?= HOMEHOST ?>/js/dateranger/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/<?= HOMEHOST ?>/js/dateranger/daterangepicker.css" />
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= API_KEY_MAPS() ?>&amp;libraries=places" defer></script>
    <script src="/<?= HOMEHOST ?>/js/lib/geocomplete/jquery.geocomplete.js"></script>
    <script src="/<?= HOMEHOST ?>/js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="/<?= HOMEHOST ?>/vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="/<?= HOMEHOST ?>/js/select2.min.js"></script>
    <script src="js/script.js?v=<?= vjs() ?>"></script>
    <script src="js/script_users.js?v=<?= vjs() ?>"></script>
    <script src="js/script_devices.js?v=<?= vjs() ?>"></script>
    <script src="js/script_zones.js?v=<?= vjs() ?>"></script>
    <script src="js/script_mapa.js?v=<?= vjs() ?>"></script>
    <script>
        sessionStorage.setItem($('#_homehost').val() + '_api_mobile', ('<?php echo $_SESSION["APIMOBILEHRP"] ?>'));
    </script>
    <?php
    if (modulo_cuentas()) :
    ?>
        <script>
            new Promise((resolve) => {
                fetch('getCuentasApi.php', {
                        method: 'get',
                    }).then(response => response.json())
                    .then(data => {
                        resolve(data);
                        $(".selectjs_cuentaToken").select2({
                            data: data,
                            multiple: false,
                            language: "es",
                            placeholder: "Cambiar de Cuenta",
                            minimumInputLength: "0",
                            minimumResultsForSearch: 10,
                            maximumInputLength: "10",
                            selectOnClose: false,
                            searching: false,
                            closeOnSelect: true,
                        });
                    }).catch(err => console.log(err));
            });
            $(".selectjs_cuentaToken").on("select2:select", function(e) {
                CheckSesion();
                $("#RefreshToken").submit();
            });
            $("#RefreshToken").on("submit", function(e) {
                e.preventDefault();
                $.ajax({
                    type: $(this).attr("method"),
                    url: $(this).attr("action"),
                    data: $(this).serialize(),
                    beforeSend: function(data) {
                        loadingTable('#table-mobile');
                        loadingTableUser('#tableUsuarios');
                        loadingTableDevices('#tableDevices');
                    },
                    success: function(data) {
                        if (data.status == "ok") {
                            sessionStorage.setItem($('#_homehost').val() + '_api_mobile', (data.api));
                            $('#dataIdCompany').html(data.idCompany)
                            $('#dataRecidCompany').html(data.recidCompany)
                            minmaxDate()
                            tablemobile.columns.adjust().draw()
                        }
                    },
                    error: function() {}
                });
            });
            $('#dataIdCompany').html('<?= $_SESSION['ID_CLIENTE'] ?>')
            $('#dataRecidCompany').html('<?= $_SESSION['RECID_CLIENTE'] ?>')
            Select2Value('<?= $_SESSION['ID_CLIENTE'] ?>', '<?= $_SESSION['CLIENTE'] ?>', ".selectjs_cuentaToken")
        </script>
    <?php
    endif;
    ?>
</body>

</html>
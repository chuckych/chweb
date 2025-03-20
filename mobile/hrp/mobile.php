<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../../llamadas.php"; ?>
    <link rel="stylesheet" href="css/styleMobile.css?=<?= version_file("/mobile/hrp/css/styleMobile.css") ?>">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.2/dist/leaflet.css"
        integrity="sha256-sA+zWATbFveLLNqWO2gtiw3HL/lh1giY/Inf1BJ0z14=" crossorigin="" />
    <link href='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/leaflet.fullscreen.css'
        rel='stylesheet' />
    <title>Mobile HR</title>
</head>

<body class="fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2 bg" style="animation-fill-mode: unset" id="container">
        <?php require __DIR__ . '../../../nav.php'; ?>
        <!-- Encabezado -->
        <div id="encabezado" class="sticky-top">
            <?php encabezado_mod3('bg-mob', 'white', '../../img/mobile-hrp-3.svg', '' . MODULOS['mobile'] . ' HRP', 'color: #fff; width:30px', 'mr-2'); ?>
        </div>
        <!-- Fin Encabezado -->
        <?php
        $FirstDate = "2019/01/01";
        $FirstYear = '2019';
        $maxDate = date('Y-m-d');
        $maxYear = date('Y');
        /** Para dateRangePicker */
        $api = "api/v1/checks/dates.php?key=$_SESSION[RECID_CLIENTE]";
        $url = $_SESSION["APIMOBILEHRP"] . "/" . HOMEHOST . "/mobile/hrp/" . $api;
        $api = getRemoteFile($url, $timeout = 10);
        // print_r($url);
        $api = json_decode($api, true);
        $arrayFech = $api['RESPONSE_DATA'] ?? '';
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
        } else if ($_SERVER['SERVER_NAME'] == '192.168.1.220') { // Si es localhost
            echo '<input type="hidden" id="apiMobile" value="' . $_SESSION["APIMOBILEHRP"] . '">';
        } else {
            echo '<input type="hidden" id="apiMobile" value="' . $_SESSION["APIMOBILEHRP"] . '">';
        }
        ?>
        <?php require __DIR__ . '../menuBtn.html' ?>
        <div class="wrapper bg">
            <div class="row invisible" id="RowTableMobile">
                <div class="col-12">
                    <div class="collapse border-0 radius bg-white fadeIn" id="collapseFilterChecks">
                        <div class="pt-2">
                            <div class="form-row p-3">
                                <div class="col-12 col-sm-4 d-flex flex-column">
                                    <label>Usuarios
                                        <div class="mt-2"></div>
                                        <select name="FilterUser" id="FilterUser"
                                            class="form-control w-100 FilterUser invisible h40"></select>
                                    </label>
                                </div>
                                <div class="col-12 col-sm-4  d-flex flex-column mt-2 mt-sm-0">
                                    <label>Zonas
                                        <div class="mt-2"></div>
                                        <select name="FilterZones" id="FilterZones"
                                            class="form-control w-100 FilterZones invisible h40"></select>
                                    </label>
                                </div>
                                <div class="col-12 col-sm-4 d-flex flex-column mt-2 mt-sm-0">
                                    <label>Dispositivos
                                        <div class="mt-2"></div>
                                        <select name="FilterDevice" id="FilterDevice"
                                            class="form-control w-100 FilterDevice invisible h40"></select>
                                    </label>
                                </div>
                                <div class="col-12 col-sm-6 mt-3">
                                </div>
                                <div class="col-12 col-sm-6 mt-3 d-flex align-items-center justify-content-end">
                                    <div class="btn-group btn-group-toggle border-0 p-1 bg mr-1">
                                        <button
                                            class="btn border-0 hint--rounded hint--no-arrow hint--secondary hint--no-shadow hint--left"
                                            aria-label="Borrar Filtros" id="ClearFilter">
                                            <i class="text-secondary bi bi-eraser-fill"></i>
                                        </button>
                                    </div>
                                    <div class="btn-group btn-group-toggle border-0 p-1 bg" data-toggle="buttons">
                                        <label
                                            class="btn btn-outline-success border-0 hint--rounded hint--no-arrow hint--secondary hint--no-shadow hint--top"
                                            aria-label="Identificado">
                                            <input type="radio" name="FilterIdentified" id="FilterIdentified1"
                                                value="1"> <i class="bi bi-person-bounding-box"></i>
                                        </label>
                                        <label
                                            class="btn btn-outline-danger border-0 hint--rounded hint--no-arrow hint--secondary hint--no-shadow hint--bottom"
                                            aria-label="No Identificado">
                                            <input type="radio" name="FilterIdentified" id="FilterIdentified2"
                                                value="2"> <i class="bi bi-person-bounding-box"></i>
                                        </label>
                                        <label
                                            class="btn btn-outline-secondary border-0 hint--rounded hint--no-arrow hint--secondary hint--no-shadow hint--top"
                                            aria-label="Todos">
                                            <input type="radio" name="FilterIdentified" id="FilterIdentified3" value=""
                                                checked> <i class="bi bi-person-bounding-box"></i>
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="pb-2"></div>
                </div>
                <div class="col-12" id="divTableMobile">
                    <table class="table text-nowrap w-100 border-0 radius p-2 bg-white" id="table-mobile">
                        <thead class="font08">
                        </thead>
                    </table>
                </div>
                <?php
                if (modulo_cuentas()):
                    ?>
                    <div class="col-12 m-0 mt-2">
                        <form action="changeCompanyApi.php" method="POST" id="RefreshToken">
                            <select class="selectjs_cuentaToken" id="recid" name="recid" style="display:none">
                            </select>
                        </form>
                    </div>
                    <?php
                endif;
                ?>
                <div class="col-12 mt-3">
                    <div id="mapid" class="bg-white"></div>
                    <hr>
                    <div id="dataT" class="mt-2"></div>
                </div>
            </div>
            <div class="invisible mt-2" id="RowTableUsers">
                <div class="row">
                    <div class="col-12 col-sm-8 mb-3 d-flex justify-content-end">
                        <button class="btn btn-sm btn-outline-custom h40 px-3 w200" id="importUser">
                            Importar Usuarios
                        </button>
                    </div>
                    <div class="col-12 col-sm-8">
                        <table class="table text-nowrap w-100 border-0 radius p-2 bg-white" id="tableUsuarios">
                            <thead class="font08"></thead>
                        </table>
                    </div>
                    <div class="col-12 col-sm-4">
                    </div>
                </div>
            </div>
            <div class="invisible mt-2" id="RowTableDevices">
                <div class="row">
                    <div class="col-12">
                        <table class="table text-nowrap w-100 border-0 radius p-2 bg-white" id="tableDevices">
                            <thead class="font08"></thead>
                        </table>
                    </div>
                    <div class="col-12 col-sm-4">
                    </div>
                </div>
            </div>
            <div class="invisible mt-2" id="RowTableZones">
                <div class="row">
                    <div class="col-12 col-lg-8">
                        <table class="table text-nowrap w-100 border-0 radius shadow p-2 bg-white" id="tableZones">
                            <thead class="font08"></thead>
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
    <div id="modalRegistro"></div>
    <div id="modalImport"></div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    // require 'modal.php';
    require __DIR__ . "../../../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATATABLE */
    require __DIR__ . "../../../js/DataTable.php";
    ?>
    <script src="https://unpkg.com/leaflet@1.9.2/dist/leaflet.js"
        integrity="sha256-o9N1jGDZrf5tS+Ft4gbIK7mYMipq9lqpVJ91xHSyKhg=" crossorigin=""></script>
    <script src='https://api.mapbox.com/mapbox.js/plugins/leaflet-fullscreen/v1.0.1/Leaflet.fullscreen.min.js'></script>
    <script type="text/javascript" src="/<?= HOMEHOST ?>/js/dateranger/moment.min.js"></script>
    <script type="text/javascript" src="/<?= HOMEHOST ?>/js/dateranger/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="/<?= HOMEHOST ?>/js/dateranger/daterangepicker.css" />
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= API_KEY_MAPS() ?>&libraries=places&callback=initMap"
        defer></script>
    <script src="/<?= HOMEHOST ?>/js/lib/geocomplete/jquery.geocomplete.js"></script>
    <script src="/<?= HOMEHOST ?>/js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="/<?= HOMEHOST ?>/vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="/<?= HOMEHOST ?>/js/select2.min.js"></script>
    <script src="js/fn.js?v=<?= version_file("/mobile/hrp/js/fn.js") ?>"></script>
    <script src="js/script.js?v=<?= version_file("/mobile/hrp/js/script.js") ?>"></script>
    <script src="js/script_users.js?v=<?= version_file("/mobile/hrp/js/script_users.js") ?>"></script>
    <script src="js/script_devices.js?v=<?= version_file("/mobile/hrp/js/script_devices.js") ?>"></script>
    <script src="js/script_zones.js?v=<?= version_file("/mobile/hrp/js/script_zones.js") ?>"></script>
    <script src="js/script_mapa.js?v=<?= version_file("/mobile/hrp/js/script_mapa.js") ?>"></script>
    <script>
        sessionStorage.setItem($('#_homehost').val() + '_api_mobile', ('<?php echo $_SESSION["APIMOBILEHRP"] ?>'));
    </script>
    <?php
    if (modulo_cuentas()):
        ?>
        <script>
            const getCuentas = async () => {
                const url = 'getCuentasApi.php';
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                const json = await response.json();
                json.forEach(element => {
                    $('.selectjs_cuentaToken').append(`<option value="${element.id}">${element.id} - ${element.text}</option>`);
                });

                $(".selectjs_cuentaToken").select2({
                    placeholder: 'Cambiar de Cuenta',
                    minimumResultsForSearch: 10,
                    allowClear: false,
                    selectOnClose: false,
                    width: '250px'
                })
                return json;
            }

            getCuentas();

            $(".selectjs_cuentaToken").on("select2:select", function (e) {
                CheckSesion();
                $("#RefreshToken").submit();
            });

            $("#RefreshToken").on("submit", function (e) {
                e.preventDefault();
                ClearFilterMobile();
                $("#collapseFilterChecks").collapse('hide');
                $.ajax({
                    type: $(this).attr("method"),
                    url: $(this).attr("action"),
                    data: $(this).serialize(),
                    beforeSend: function (data) {
                        loadingTable('#table-mobile')
                    },
                    success: function (data) {
                        if (data.status == "ok") {
                            sessionStorage.setItem($('#_homehost').val() + '_api_mobile', (data.api));
                            minmaxDate()
                            getToken()
                        }
                    },
                    error: function () { }
                });
            });
            Select2Value('<?= $_SESSION['ID_CLIENTE'] ?>', '<?= $_SESSION['CLIENTE'] ?>', ".selectjs_cuentaToken")
        </script>
        <?php
    endif;
    ?>
</body>

</html>
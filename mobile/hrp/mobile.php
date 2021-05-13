<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../../llamadas.php"; ?>
    <!-- <link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.1.2/css/rowGroup.dataTables.min.css"> -->
    <title><?= MODULOS['mobile'] ?></title>
    <style type="text/css" media="screen">
        .datos {
            margin-left: 5px;
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

        .dtrg-level-1 {
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
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2" style="animation-fill-mode: unset">
    
        <?php require __DIR__ . '../../../nav.php'; ?>
        <!-- Encabezado -->
        <?= encabezado_mod('bg-mob', 'white', 'mobile.png', 'Fichadas ' . MODULOS['mobile'].' HR', '') ?>
        <!-- Fin Encabezado -->
            <!-- <div class="row bg-white radius py-2">
                <?php
                $FirstDate = "2019/01/01";
                /** FisrsDate */
                $FirstYear = '2019';
                /** FirstYear */
                $maxDate   = date('Y-m-d');
                /** maxDate */
                $maxYear   = date('Y');
                /** maxYear */
                ?>
                <div class="col-12 col-sm-9">
                    <div class="d-flex justify-content-sm-end justify-content-center">
                        <input type="text" readonly class="mx-2 form-control text-center w250 ls1" name="_dr" id="_drMob">
                        <button title="Actualizar Grilla" type="button" id="Refresh" class="btn px-2 border-0 fontq float-right bg-custom text-white opa8">
                            <svg class="bi" width="20" height="20" fill="currentColor">
                                <use xlink:href="../img/bootstrap-icons.svg#arrow-repeat" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div> -->
            <!-- </form> -->

            <div class="row bg-white py-3 radius" id="RowTableMobile">
                
                <div class="col-12 table-responsive">
                    <table class="table text-wrap w-100" id="table-mobile">
                        <thead class="text-uppercase border-top-0">
                            <tr>
                                <th class="">FOTO</th>
                                <th class="">NOMBRE</th>
                                <th class="">FECHA</th>
                                <th class="">DIA</th>
                                <th class="">HORA</th>
                                <th class="text-center">MAPA</th>
                                <th class="">EVENTO</th>
                                <th class="">PHONE ID</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require 'modal.php';
    require __DIR__ . "../../../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    require __DIR__ . "../../../js/DateRanger.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    require __DIR__ . "../../../js/DataTable.php";
    ?>
    <!-- <script src="https://cdn.datatables.net/rowgroup/1.1.2/js/dataTables.rowGroup.min.js"></script> -->
    <!-- <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script> -->
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= API_KEY_MAPS() ?>&sensor=false&amp;libraries=places" defer></script>
    <!-- <script src="../js/lib/geocomplete/jquery.geocomplete.js"></script> -->
    <script src="../../js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="../../js/select2.min.js"></script>
    <!-- <script src="script-min.js"></script> -->
    <script src="script.js?v=<?=vjs()?>"></script>
    <script src="FicMobExcel.js?v=<?=vjs()?>"></script>

</body>

</html>
<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../llamadas.php"; ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/rowgroup/1.1.2/css/rowGroup.dataTables.min.css">
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
        <?php require __DIR__ . '../../nav.php'; ?>
        <!-- Encabezado -->
        <?= encabezado_mod('bg-mob', 'white', 'mobile.png', 'Fichadas' . MODULOS['mobile'], '') ?>
        <!-- Fin Encabezado -->
        <?php if (token_exist($_SESSION['RECID_CLIENTE'])) {
            /** Check de token */ ?>
            <div class="row bg-white radius py-2">
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
                <div class="col-12 col-sm-3 mb-2 mb-sm-0">
                    <a class="btn btn-outline-custom border-0 fontq opa8 float-left btn-sm" href="zonas/">Zonas</a>
                    <a class="btn btn-outline-custom border-0 fontq opa8 float-left btn-sm" href="usuarios/">Usuarios</a>
                    <button type="button" class="ml-2 btn btn-light text-success fw5 border btn-sm fontq" title="Exportar Excel" id="btnExcel"> </button>
                </div>
                <div class="col-12 col-sm-9">
                    <div class="d-flex justify-content-sm-end justify-content-center">
                        <input type="text" readonly class="mx-2 form-control text-center w250 ls1" name="_dr" id="_dr" placeholder="<?= fechformat($FechaIni) . ' - ' . fechformat($FechaFin) ?>">
                        <button title="Actualizar Grilla" type="button" id="Refresh" class="btn px-2 border-0 fontq float-right bg-custom text-white opa8">
                            <svg class="bi" width="20" height="20" fill="currentColor">
                                <use xlink:href="../img/bootstrap-icons.svg#arrow-repeat" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            <!-- </form> -->

            <div class="row bg-white pb-3 radius">
                <!-- <div class="col-12 m-0">

                    <button class="btn btn-sm btn-link text-decoration-none fontq text-secondary p-0 pb-1 m-0 float-right" id="Refresh">Actualizar Grilla</button>
                </div> -->
                <div class="col-12 table-responsive">
                    <table class="table text-wrap w-100" id="table-mobile">
                        <thead class="text-uppercase border-top-0">
                            <tr>
                                <!-- <th class=""></th> -->
                                <th class="">FOTO</th>
                                <th class="">ID</th>
                                <th class="">NOMBRE</th>
                                <th class="">FECHA</th>
                                <th class="">DIA</th>
                                <th class="">HORA</th>
                                <th class="text-center">MAPA</th>
                                <th class="text-center">FACE</th>
                                <th class="">ZONA</th>
                                <th class="">MODO</th>
                                <th class="">TIPO</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <?php
                if (modulo_cuentas()) :
                ?>
                    <div class="col-12 m-0 mt-2">
                        <form action="RefreshToken.php" method="POST" id="RefreshToken">
                            <select class="selectjs_cuentaToken w200" id="tk" name="tk">
                            </select>
                        </form>
                    </div>
                <?php
                endif;
                ?>
            </div>
        <?php } else {
            echo '<div class="alert alert-light mt-3">La Cuenta no tiene Token Mobile Asociado</div>';
        }
        /** Fin de check de token*/ ?>
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require 'modal.php';
    require __DIR__ . "../../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    require __DIR__ . "../../js/DateRanger.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    require __DIR__ . "../../js/DataTable.php";
    ?>
    <!-- <script src="https://cdn.datatables.net/rowgroup/1.1.2/js/dataTables.rowGroup.min.js"></script> -->
    <!-- <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script> -->
    <script src="https://maps.googleapis.com/maps/api/js?key=<?= API_KEY_MAPS() ?>&sensor=false&amp;libraries=places" defer></script>
    <!-- <script src="../js/lib/geocomplete/jquery.geocomplete.js"></script> -->
    <script src="../js/select2.min.js"></script>
    <!-- <script src="script-min.js"></script> -->
    <script src="script-min.js"></script>
    <script src="FicMobExcel.js"></script>

</body>

</html>
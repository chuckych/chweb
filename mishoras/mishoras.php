<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "/../llamadas.php"; ?>
    <title><?= MODULOS['mishoras'] ?></title>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php
        $countModRol = (count($_SESSION['MODS_ROL']));
        if ($countModRol != '1') {
            echo '<div class="">';
        } else {
            echo '<div class="d-none">';
        }
        require __DIR__ . '/../nav.php';
        echo '</div>';
        ?>
        <!-- Encabezado -->
        <div class="sticky-top">
            <?=
                encabezado_mod2('bg-custom', 'white', 'clock-history', '<span class="d-none d-sm-inline">' . MODULOS['mishoras'] . '&nbsp;</span><span class="nombre"></span>', '25', 'text-white mr-2');
            ?>
            <!-- </div> -->
            <!-- Fin Encabezado -->
            <div class="row pt-3 pb-3 pb-sm-2 bg-white">
                <div class="col-12 col-sm-6 d-flex justify-content-end justify-content-sm-start">
                    <button type="button" disabled
                        class="btn-lg d-flex align-items-center btn fontq btn-outline-custom px-3 totales"
                        data-toggle="modal" data-target="#Total_General">
                        <?= $icon_bar_chart_fill ?>
                        <span>Totales</span>
                    </button>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="d-flex justify-content-sm-end justify-content-center mt-3 mt-sm-0">
                        <div class="input-group w350">
                            <div class="input-group-prepend">
                                <span class="input-group-text border-0">
                                    <?= $icon_calendar_range ?>
                                </span>
                            </div>
                            <input type="text" readonly class="form-control text-center border-0 radius ls2 h40"
                                name="_dr" id="_dr">
                            <button title="Actualizar Grilla" type="button" id="Refresh"
                                class="d-none btn fontq border-0 opa7 btn-custom"
                                style=" border-top-left-radius: 0em 0em; border-bottom-left-radius: 0em 0em;">
                                <?= $icon_arrow_repeat ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row pb-3 radius">
            <div class="col-12 m-0 table-responsive">
                <table class="table table-hover w-100 text-nowrap bg-white" id="Tabla_General">
                    <thead class="">
                        <th class="text-center">VER</th>
                        <th class="">NOMBRE / LEG.</th>
                        <th class="">FECHA</th>
                        <th class="">HORARIO</th>
                        <th class="text-center ls1" title="Primer y última Fichada">ENTRA</th>
                        <th class="text-center ls1" title="Primer y última Fichada">SALE</th>
                        <th class="">TIPO HORA</th>
                        <th class="text-center " title="Horas Autorizadas">HS PAGAS</th>
                        <th class="text-center " title="Horas Calculadas">HS HECHAS</th>
                        <th class="" title="Descripción de Novedad">NOVEDADES</th>
                        <th class="" title="Horas de la Novedad">NOV HS</th>
                    </thead>
                </table>
            </div>
        </div>
        <?php require __DIR__ . "/ModalMisHoras.php"; ?>
        <?php require __DIR__ . "/ModalTotales.php"; ?>
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "/../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    require __DIR__ . "/../js/DateRanger.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    require __DIR__ . "/../js/DataTable.php";
    ?>
    <script src="proceso-min.js?v=<?= vjs() ?>"></script>

</body>

</html>
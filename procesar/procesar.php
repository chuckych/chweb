<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "/../llamadas.php"; ?>
    <title><?= MODULOS['procesar'] ?></title>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '/../nav.php'; ?>
        <!-- Encabezado -->
        <?= encabezado_mod('bg-custom', 'white', 'rueda2.png', MODULOS['procesar'], '') ?>
        <!-- Fin Encabezado -->
        <form action="procesando.php" method="post" class="procesando">
            <div class="row p-2">
                <div class="col-sm-6">
                    <div class="row" id="content">
                        <div class="col-6">
                            <div class=" mt-2">
                                <!-- Tipo personal -->
                                <input type="hidden" name="procesando" id="procesando">
                                <label for="ProcTipo" class="mr-2">Tipo de Personal</label>
                                <select class="select2 form-control" id="ProcTipo" name="ProcTipo">
                                    <?php
                                    foreach (TIPO_PER as $key => $value) {
                                        echo '<option value="' . $value . '">' . $key . '</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-6 d-flex align-items-center justify-content-end">
                            <div
                                class="custom-control custom-switch custom-control-inline d-flex align-items-center justify-content-end mt-4">
                                <input disabled checked type="checkbox" class="custom-control-input" id="Legajos"
                                    name="CheckLegajos">
                                <label class="custom-control-label" for="Legajos" style="padding-top: 3px;">
                                    <span>Todos los Legajos</span>
                                </label>
                            </div>
                        </div>
                        <!-- <div class="col-12 pt-2"> -->
                        <!-- <label for="ProcLegaIni" class="">Legajo desde / hasta:</label> -->

                        <!-- </div> -->

                        <!-- <div class="col-6">
                            <div class="d-inline-flex align-items-center w-100">
                                <input type="number" name="ProcLegaIni" id="ProcLegaIni" class="form-control text-center h40" value="1" max="999999999">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="d-inline-flex align-items-center w-100 pt-1 pt-sm-0">
                                <input type="number" name="ProcLegaFin" id="ProcLegaFin" class="form-control text-center h40" value="999999999" max="999999999">
                            </div>
                        </div> -->
                        <div class="col-12 pt-2">
                            <label for="ProcLegaIni" class="">Fecha desde / hasta:</label>
                        </div>
                        <div class="col-12">
                            <input type="text" class="form-control w-100 h40 ls1 text-center" name="_dr">
                        </div>
                        <div class="col-12 col-sm-6 pt-2">
                            <label for="ProcEmp">Empresa</label><br>
                            <div class="d-inline-flex align-items-center w-100">
                                <!-- Empresa -->
                                <select class="form-control selectjs_empresa w-100" id="ProcEmp" name="ProcEmp">
                                </select>
                                <!-- <span id="trash_emp" class="btn btn-sm btn-link opa1""><?= imgIcon('trash3', 'Limpiar Selección ', 'w15'); ?></span> -->
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 pt-2">
                            <label for="ProcPlan">Planta</label><br>
                            <div class="d-inline-flex align-items-center w-100">
                                <!-- Planta -->
                                <select class="form-control selectjs_plantas w-100" id="ProcPlan" name="ProcPlan">
                                </select>
                                <!-- <span id="trash_plan" class="btn btn-sm btn-link opa1"><?= imgIcon('trash3', 'Limpiar Selección ', 'w15'); ?></span> -->
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 pt-2">
                            <label for="ProcSect">Sector</label>
                            <div class="d-inline-flex align-items-center w-100"><br>
                                <!-- Sector -->
                                <select class="form-control selectjs_sectores w-100" id="ProcSect" name="ProcSect">
                                </select>
                                <!-- <span id="trash_sect" class="btn btn-sm btn-link opa1"><?= imgIcon('trash3', 'Limpiar Selección ', 'w15'); ?></span> -->
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 pt-2">
                            <label for="ProcSec2">Sección</label>
                            <div class="d-inline-flex align-items-center w-100" id="select_seccion"><br>
                                <!-- Seccion -->
                                <select class="form-control select_seccion w-100" id="ProcSec2"
                                    name="ProcSec2"></select>
                                <!-- <span id="trash_secc" class="btn btn-sm btn-link opa1"><?= imgIcon('trash3', 'Limpiar Selección ', 'w15'); ?></span> -->
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 pt-2">
                            <label for="ProcGrup">Grupos</label>
                            <div class="d-inline-flex align-items-center w-100" id="select_seccion"><br>
                                <!-- Grupos -->
                                <select class="form-control selectjs_grupos w-100" id="ProcGrup"
                                    name="ProcGrup"></select>
                                <!-- <span id="trash_grup" class="btn btn-sm btn-link opa1"><?= imgIcon('trash3', 'Limpiar Selección ', 'w15'); ?></span> -->
                            </div>
                        </div>
                        <div class="col-12 col-sm-6 pt-2">
                            <label for="ProcSucur">Sucursal</label>
                            <div class="d-inline-flex align-items-center w-100" id="select_seccion"><br>
                                <!-- Grupos -->
                                <select class="form-control selectjs_sucursal w-100" id="ProcSucur"
                                    name="ProcSucur"></select>
                                <!-- <span id="trash_sucur" class="btn btn-sm btn-link opa1"><?= imgIcon('trash3', 'Limpiar Selección ', 'w15'); ?></span> -->
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 pt-4 d-flex justify-content-end" id="footer">
                            <button type="submit" class="btn btn-custom btn-sm text-white fontq h40 btn-mobile px-5"
                                id="submit"></button>
                        </div>
                    </div>
                    <input type="hidden" name="SelEmpresa" id="SelEmpresa">
                    <input type="hidden" name="SelPlanta" id="SelPlanta">
                    <input type="hidden" name="SelSector" id="SelSector">
                    <input type="hidden" name="SelSeccion" id="SelSeccion">
                    <input type="hidden" name="SelGrupo" id="SelGrupo">
                    <input type="hidden" name="SelSucursal" id="SelSucursal">
                    <div class="col-12 mt-4">
                        <div id="respuesta" class="alert d-none fonth">
                            <div id="respuetatext"></div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 pt-2">
                    <table class="table table-hover text-nowrap w-100 table-sm table-borderless" id="GetPersonal">
                        <thead class="">
                            <tr>
                                <th>
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" name="select_all" value="1"
                                            id="Personal-select-all" type="checkbox">
                                        <label class="custom-control-label" for="Personal-select-all"></label>
                                    </div>
                                </th>
                                <th>LEGAJO</th>
                                <th>NOMBRE</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </form>
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "/../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    require __DIR__ . "/../js/DateRanger.php";
    /** INCLUIMOS LIBRERÍAS y script DATATABLE */
    require __DIR__ . "/../js/DataTable.php";
    ?>
    <script src="../vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="../js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="../js/select2.min.js"></script>
    <script src="js/procesar.js?v=<?= vjs() ?>"></script>
    <script src="js/select.js?v=<?= vjs() ?>"></script>
    <script src="js/trash-select.js?v=<?= vjs() ?>"></script>

</body>

</html>
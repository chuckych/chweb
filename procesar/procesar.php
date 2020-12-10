<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "../../llamadas.php"; ?>
    <title><?= MODULOS['procesar'] ?></title>
</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '../../nav.php'; ?>
        <!-- Encabezado -->
        <?= encabezado_mod('bg-fich', 'white', 'rueda2.png', MODULOS['procesar'], '') ?>
        <!-- Fin Encabezado -->
        <form action="procesando.php" method="post" class="procesando">
            <div class="row bg-white mt-2 p-3 radius">
                <div class="w500">
                    <div class="col-12 m-0">
                        <div class="form-inline mt-2">
                            <!-- Tipo personal -->
                            <input type="hidden" name="procesando" id="procesando">
                            <label for="ProcTipo" class="mr-2 w120">Tipo de Personal</label>
                            <select class="select2 form-control w150" id="ProcTipo" name="ProcTipo">
                                <?php
                                foreach (TIPO_PER as $key => $value) {
                                    echo '<option value="' . $value . '">' . $key . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-inline mt-2">
                            <label for="ProcLegaIni" class="mr-2 w120">Del Legajo:</label>
                            <input type="number" name="ProcLegaIni" id="ProcLegaIni" class="form-control w150 text-center h40" value="1" max="999999999">
                            <label for="ProcLegaFin" class="mx-1">Al:</label>
                            <input type="number" name="ProcLegaFin" id="ProcLegaFin" class="form-control w150 text-center h40" value="999999999" max="999999999">
                        </div>
                    </div>
                    <div class="col-12 mt-2">
                        <div class="form-inline mt-2">
                            <label for="ProcFechaIni" class="mr-2 w120">Fecha Desde:</label>
                            <input type="date" class="form-control w150 h40" name="ProcFechaIni" id="ProcFechaIni" value="<?= date('Y-m-d') ?>">
                            <label for="ProcFechaFin" class="mx-1">Al:</label>
                            <input type="date" class="form-control w150 h40" name="ProcFechaFin" id="ProcFechaFin" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <div class="form-inline mt-2">
                            <!-- Empresa -->
                            <label for="ProcEmp" class="mr-2 w120">Empresa</label>
                            <select class="form-control selectjs_empresa w300" id="ProcEmp" name="ProcEmp">
                            </select>
                            <span id="trash_emp" class="btn btn-sm btn-link opa1""><?= imgIcon('trash3', 'Limpiar Selección ', 'w15'); ?></span>
                        </div>
                        <div class=" form-inline mt-2">
                                <!-- Planta -->
                                <label for="ProcPlan" class="mr-2 w120">Planta</label>
                                <select class="form-control selectjs_plantas w300" id="ProcPlan" name="ProcPlan">
                                </select>
                                <span id="trash_plan" class="btn btn-sm btn-link opa1"><?= imgIcon('trash3', 'Limpiar Selección ', 'w15'); ?></span>
                        </div>
                        <div class="form-inline mt-2">
                            <!-- Sector -->
                            <label for="ProcSect" class="mr-2 w120">Sector</label>
                            <select class="form-control selectjs_sectores w300" id="ProcSect" name="ProcSect">
                            </select>
                            <span id="trash_sect" class="btn btn-sm btn-link opa1"><?= imgIcon('trash3', 'Limpiar Selección ', 'w15'); ?></span>
                        </div>
                        <div class="form-inline mt-2 d-none" id="select_seccion">
                            <!-- Seccion -->
                            <label for="ProcSec2" class="mr-2 w120">Sección</label>
                            <select class="form-control select_seccion w300" id="ProcSec2" name="ProcSec2">
                            </select>
                            <span id="trash_secc" class="btn btn-sm btn-link opa1"><?= imgIcon('trash3', 'Limpiar Selección ', 'w15'); ?></span>
                        </div>
                        <div class="form-inline mt-2">
                            <!-- Grupos -->
                            <label for="ProcGrup" class="mr-2 w120">Grupos</label>
                            <select class="form-control selectjs_grupos w300" id="ProcGrup" name="ProcGrup">
                            </select>
                            <span id="trash_grup" class="btn btn-sm btn-link opa1"><?= imgIcon('trash3', 'Limpiar Selección ', 'w15'); ?></span>
                        </div>
                        <div class="form-inline mt-2">
                            <!-- Sucursal -->
                            <label for="ProcSucur" class="mr-2 w120">Sucursal</label>
                            <select class="form-control selectjs_sucursal w300" id="ProcSucur" name="ProcSucur">
                            </select>
                            <span id="trash_sucur" class="btn btn-sm btn-link opa1"><?= imgIcon('trash3', 'Limpiar Selección ', 'w15'); ?></span>
                        </div>
                    </div>
                    <div class="col-12 mt-2 d-flex justify-content-end">
                        <div class="form-inline mt-2">
                            <button type="submit" class="btn bg-custom btn-sm text-white fontq w150" id="submit"></button>
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
            </div>
        </form>
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "../../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    // require __DIR__ . "../../js/DateRanger.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    // require __DIR__ . "../../js/DataTable.php";
    ?>
    <script src="../vendor/igorescobar/jquery-mask-plugin/dist/jquery.mask.min.js"></script>
    <script src="../js/select2.min.js"></script>
    <script src="js/procesar.js"></script>
    <script src="js/select.js"></script>
    <script src="js/trash-select.js"></script>

</body>

</html>
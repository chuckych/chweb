<!doctype html>
<html lang="es">

<head>
<link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
<?php require __DIR__ . "../../llamadas.php"; ?>
<title>Ingreso de Fichadas</title>

</head>

<body class="animate__animated animate__fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '../../nav.php'; ?>
        <!-- Encabezado -->
        <?= encabezado_mod('bg-fich', 'white', 'fichadas.png', MODULOS['fichar'], '') ?>
        <!-- Fin Encabezado -->
        <form action="FicharHorario.php" method="post" class="FicharHorario">
        <div class="row bg-white mt-2 p-3 radius">
        <div class="w500">
            <div class="col-12 m-0">
                <div class="form-inline mt-2">
                    <!-- Tipo personal -->
                    <input type="hidden" name="FicharHorario" id="FicharHorario">
                    <label for="FichTipo" class="mr-2 w120">Tipo de Personal</label>
                    <select class="select2 form-control w150" id="FichTipo" name="FichTipo">
                        <?php
                        foreach (TIPO_PER as $key => $value) {
                            echo '<option value="' . $value . '">' . $key . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-inline mt-2">
                    <label for="FichLegaIni" class="mr-2 w120">Del Legajo:</label>
                    <input type="number" name="FichLegaIni" id="FichLegaIni" class="form-control w150 text-center h40" value="1" max="999999999">
                    <label for="FichLegaFin" class="mx-1">Al:</label>
                    <input type="number" name="FichLegaFin" id="FichLegaFin" class="form-control w150 text-center h40" value="999999999" max="999999999">
                </div>
            </div>
            <div class="col-12 mt-2">
                <div class="form-inline mt-2">
                    <label for="FichFechaIni" class="mr-2 w120">Fecha Desde:</label>
                    <input type="date" class="form-control w150 h40" name="FichFechaIni" id="FichFechaIni" value="<?= date('Y-m-d') ?>">
                    <label for="FichFechaFin" class="mx-1">Al:</label>
                    <input type="date" class="form-control w150 h40" name="FichFechaFin" id="FichFechaFin" value="<?= date('Y-m-d') ?>">
                </div>
            </div>
            <div class="col-12 mt-3">
                <div class="form-inline mt-2">
                    <!-- Empresa -->
                    <label for="FichEmp" class="mr-2 w120">Empresa</label>
                    <select class="form-control selectjs_empresa w300" id="FichEmp" name="FichEmp">
                    </select>
                    <span id="trash_emp" class="btn btn-sm btn-link opa1""><?= imgIcon('trash3', 'Limpiar Selección ', 'w15'); ?></span>
                </div>
                <div class=" form-inline mt-2">
                <!-- Planta -->
                    <label for="FichPlan" class="mr-2 w120">Planta</label>
                    <select class="form-control selectjs_plantas w300" id="FichPlan" name="FichPlan">
                    </select>
                    <span id="trash_plan" class="btn btn-sm btn-link opa1"><?= imgIcon('trash3', 'Limpiar Selección ', 'w15'); ?></span>
                </div>
                <div class="form-inline mt-2">
                <!-- Sector -->
                    <label for="FichSect" class="mr-2 w120">Sector</label>
                    <select class="form-control selectjs_sectores w300" id="FichSect" name="FichSect">
                    </select>
                    <span id="trash_sect" class="btn btn-sm btn-link opa1"><?= imgIcon('trash3', 'Limpiar Selección ', 'w15'); ?></span>
                </div>
                <div class="form-inline mt-2 d-none" id="select_seccion">
                <!-- Seccion -->
                    <label for="FichSec2" class="mr-2 w120">Sección</label>
                    <select class="form-control select_seccion w300" id="FichSec2" name="FichSec2">
                    </select>
                    <span id="trash_secc" class="btn btn-sm btn-link opa1"><?= imgIcon('trash3', 'Limpiar Selección ', 'w15'); ?></span>
                </div><small id="SectorHelpBlock2" class="form-text text-muted"></small>
                <div class="form-inline mt-2">
                    <!-- Grupos -->
                    <label for="FichGrup" class="mr-2 w120">Grupos</label>
                    <select class="form-control selectjs_grupos w300" id="FichGrup" name="FichGrup">
                    </select>
                    <span id="trash_grup" class="btn btn-sm btn-link opa1"><?= imgIcon('trash3', 'Limpiar Selección ', 'w15'); ?></span>
                </div>
                <div class="form-inline mt-2">
                    <!-- Sucursal -->
                    <label for="FichSucur" class="mr-2 w120">Sucursal</label>
                    <select class="form-control selectjs_sucursal w300" id="FichSucur" name="FichSucur">
                    </select>
                    <span id="trash_sucur" class="btn btn-sm btn-link opa1"><?= imgIcon('trash3', 'Limpiar Selección ', 'w15'); ?></span>
                </div>
            </div>
            <div class="col-12 mt-3">
                <div class="form-inline mt-2">
                    <label for="FichIngresar" class="mr-2 w120">Ingresar</label>
                    <select class="select2 form-control w300" id="FichIngresar" name="FichIngresar">
                        <?php
                        foreach (INGRESAR as $key => $value) {
                            echo '<option value="' . $value . '">' . $key . '</option>';
                        }
                        ?>
                    </select>
                </div>
                <div class="form-inline mt-2">
                    <label for="FichLaboral" class="mr-2 w120">Laboral</label>
                    <select class="select2 form-control w300" id="FichLaboral" name="FichLaboral">
                        <?php
                        foreach (LABORAL as $key => $value) {
                            echo '<option value="' . $value . '">' . $key . '</option>';
                        }
                        ?>
                    </select>
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
    <script src="js/procesar.js?v=<?=vjs()?>"></script>
    <script src="js/select.js?v=<?=vjs()?>"></script>
    <script src="js/trash-select.js?v=<?=vjs()?>"></script>

</body>

</html>
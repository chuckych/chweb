<?php
$labelEmpr ??= 'Empresas';
$labelPlan ??= 'Plantas';
$labelSect ??= 'Sectores';
$labelSecc ??= 'Secciones';
$labelGrup ??= 'Grupos';
$labelSucu ??= 'Sucursales';
$mappingLabels = [
    'Empresas' => $labelEmpr,
    'Plantas' => $labelPlan,
    'Sectores' => $labelSect,
    'Secciones' => $labelSecc,
    'Grupos' => $labelGrup,
    'Sucursales' => $labelSucu,
];
?>
<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "/../llamadas.php"; ?>
    <title>
        <?= MODULOS['cierres'] ?>
    </title>
</head>

<body class="fadeIn">
    <!-- inicio container -->
    <div class="container shadow pb-2">
        <?php require __DIR__ . '/../nav.php';
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="#e3e3e3" viewBox="0 -960 960 960"><path d="M232-216 96-352l51-51 84 85 170-170 52 51zm0-312L96-664l51-51 85 85 169-170 52 51zm296 240v-72h336v72zm0-312v-72h336v72z"/></svg>';
        $titulo = "<span style='margin-top:1px'>" . MODULOS['cierres'] . "</span>";
        ?>
        <!-- Encabezado -->
        <?php encabezado_mod_svgIcon('bg-custom', 'white', $svg, $titulo, ''); ?>
        <?php
        $FechaMinMax = (fecha_min_max('FICHAS', 'FICHAS.FicFech'));
        $FechaMinMax2 = (fecha_min_max2('FICHAS', 'FICHAS.FicFech'));
        $FechaFinEnd = $FechaMinMax2['max'];
        $FirstDate = $FechaMinMax['min'];
        /** FirstDate */
        $FirstYear = Fech_Format_Var($FechaMinMax['min'], 'Y');
        /** FirstYear */
        $maxDate = $FechaMinMax['max'];
        /** maxDate */
        $maxYear = date('Y');
        /** maxYear */
        $FechaIni = $FechaMinMax['max'];
        $FechaFin = $FechaMinMax['max'];
        // $FechaIni = date("Y-m-d", strtotime(hoy() . "- 1 month"));
        $AnioMax = date("Y", strtotime(hoy()));
        ?>
        <input type="hidden" value="<?= $FirstYear ?>" id="anioMin">
        <input type="hidden" value="<?= $AnioMax ?>" id="anioMax">
        <input type="hidden" value='<?= json_encode($mappingLabels) ?>' id="mappingLabels">
        <!-- Fin Encabezado -->
        <form id="form-procesar">
            <div class="mt-3 px-2">
                <div class="row">
                    <div class="col-12 col-sm-6">
                        <div class="form-row">
                            <div class="col-12">
                                <div class="d-inline-flex justify-content-end w-100">
                                    <div class="d-none">
                                        <span class="font08 py-2 mr-2">Procesar Por:</span>
                                        <div class="d-flex align-items-center pt-2">
                                            <div class="btn-group btn-group-toggle p-1 border radius"
                                                data-toggle="buttons" style="gap: 5px;">
                                                <label
                                                    class="hint--top btn font08 btn btn-outline-custom border-0 w100 radius"
                                                    id="TipoIngresoFiltros" data-toggle="tooltip"
                                                    data-placement="bottom" data-html="true" title=""
                                                    aria-label="Procesar por Filtros">
                                                    <input type="radio" name="procesar_por" value="1"> Filtros
                                                </label>
                                                <label
                                                    class="hint--top btn font08 btn btn-outline-custom border-0 w100 radius"
                                                    id="TipoIngresoFiltrosLegajos" data-toggle="tooltip"
                                                    data-placement="bottom" data-html="true" title=""
                                                    aria-label="Procesar por Legajo">
                                                    <input checked type="radio" name="procesar_por" value="2"> Legajos
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <button type="button"
                                            class="float-right font08 btn btn-link text-decoration-none text-secondary"
                                            id="trash_allFilter">
                                            Limpiar Filtro
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 col-sm-6 mt-2">
                                <div class="d-inline-flex mt-1 w-100">
                                    <!-- Tipo personal -->
                                    <select class="select2Tipo form-control" id="aTipo" name="TipoDePersonal"
                                        style="width:100%;">
                                        <?php
                                        foreach (TIPO_PER as $key => $value) {
                                            echo "<option value=\"$value\">$key</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-auto"></div>
                            <div class="col-12 col-sm-6 mt-2">
                                <!-- Empresa -->
                                <select class="form-control sel_empresa" id="aEmp" name="aEmp">
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 mt-2">
                                <!-- PLanta -->
                                <select class="form-control sel_plantas" id="aPlan" name="aPlan">
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 mt-2">
                                <!-- Sector -->
                                <select class="form-control sel_sectores w200" id="aSect" name="aSect">
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 mt-2">
                                <!-- Seccion -->
                                <select class="form-control sel_seccion w200" id="aSec2" name="aSec2">
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 mt-2">
                                <!-- Grupo -->
                                <select class="form-control sel_grupos w200" id="aGrup" name="aGrup">
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 mt-2">
                                <!-- Sucursal -->
                                <select class="form-control sel_sucursal w200" id="aSucur" name="aSucur">
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 mt-2">
                                <div class="d-flex flex-column mt-1 w-100">
                                    <!-- Rango de Fecha -->
                                    <label style="width:fit-content">Fecha de Cierre</label>
                                    <div style="width:100%;">
                                        <input type="text" class="bg-white h40 form-control text-center" name="Fecha"
                                            id="cierre">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 mt-3 d-flex justify-content-end">
                                <button type="submit" class="ml-1 btn bg-custom text-white font08" id="submit"
                                    style="width:150px"></button>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="col-12 mt-3 d-flex justify-content-end">
                                <div class="custom-control custom-switch hint--left hint-switch-label" aria-label="Se quitarán cierres de legajos seleccionados" id="switch">
                                    <input type="checkbox" class="custom-control-input" id="EliminaCierre"
                                        name="Eliminar">
                                    <label class="custom-control-label" style="padding-top: 2px;" for="EliminaCierre">
                                        Quitar Cierres
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 table-responsive pt-2" id="divTablePers">
                        <table class="table table-hover text-nowrap w-100 table-sm" id="table">
                            <thead class="">
                                <tr class="bg-light">
                                    <th class="border-0">
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input" name="select_all" value="1"
                                                id="Personal-select-all" type="checkbox">
                                            <label class="custom-control-label" for="Personal-select-all"
                                                style="padding-top:4px;"></label>
                                        </div>
                                    </th>
                                    <th class="border-0"><span id="countMarcados" class="font08"></span></th>
                                    <th class="border-0"></th>
                                    <th class="border-0 font08">Fecha de cierre</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </form>
        <div id="modales"></div>
    </div>
    <!-- fin container -->
    <?php
    /** INCLUIMOS LIBRERÍAS JQUERY */
    require __DIR__ . "/../js/jquery.php";
    /** INCLUIMOS LIBRERÍAS y script DATERANGER */
    require __DIR__ . "/../js/DateRanger.php";
    /** INCLUIMOS LIBRERÍAS y script DATATABLES */
    require __DIR__ . "/../js/DataTable.php";
    ?>
    <script src="../js/bootstrap-notify-master/bootstrap-notify.min.js"></script>
    <script src="../js/select2.min.js"></script>
    <script src="../js/select2-es.js"></script>
    <script src="js/main.js?<?= version_file("/cierres/js/main.js") ?>"></script>

</body>

</html>
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
$iframe = $_GET['iframe'] ?? false;
?>
<!doctype html>
<html lang="es">

<head>
    <link href="/<?= HOMEHOST ?>/js/select2.min.css" rel="stylesheet" />
    <?php require __DIR__ . "/../llamadas.php"; ?>
    <title>
        <?= MODULOS['fichar'] ?>
    </title>
</head>

<body class="fadeIn">
    <!-- inicio container -->
    <div class="container shadow py-2">
        <?php require __DIR__ . '/../nav.php';
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="#e3e3e3" viewBox="0 -960 960 960"><path d="M481-781q106 0 200 45.5T838-604q7 9 4.5 16t-8.5 12-14 4.5-14-8.5q-55-78-141.5-119.5T481-741t-182 41.5T158-580q-6 9-14 10t-14-4q-7-5-8.5-12.5T126-602q62-85 155.5-132T481-781m0 94q135 0 232 90t97 223q0 50-35.5 83.5T688-257t-87.5-33.5T564-374q0-33-24.5-55.5T481-452t-58.5 22.5T398-374q0 97 57.5 162T604-121q9 3 12 10t1 15q-2 7-8 12t-15 3q-104-26-170-103.5T358-374q0-50 36-84t87-34 87 34 36 84q0 33 25 55.5t59 22.5 58-22.5 24-55.5q0-116-85-195t-203-79-203 79-85 194q0 24 4.5 60t21.5 84q3 9-.5 16T208-205t-15.5-.5T182-217q-15-39-21.5-77.5T154-374q0-133 96.5-223T481-687m0-192q64 0 125 15.5T724-819q9 5 10.5 12t-1.5 14-10 11-17-1q-53-27-109.5-41.5T481-839q-58 0-114 13.5T260-783q-8 5-16 2.5T232-791t-2-14.5 10-11.5q56-30 117-46t124-16m0 289q93 0 160 62.5T708-374q0 9-5.5 14.5T688-354q-8 0-14-5.5t-6-14.5q0-75-55.5-125.5T481-550t-130.5 50.5T296-374q0 81 28 137.5T406-123q6 6 6 14t-6 14-14 6-14-6q-59-62-90.5-126.5T256-374q0-91 66-153.5T481-590m-1 196q9 0 14.5 6t5.5 14q0 75 54 123t126 48q6 0 17-1t23-3q9-2 15.5 2.5T744-191q2 8-3 14t-13 8q-18 5-31.5 5.5t-16.5.5q-89 0-154.5-60T460-374q0-8 5.5-14t14.5-6"/></svg>';
        $titulo = "<span style='margin-top:1px'>" . MODULOS['fichar'] . "</span>";
        ?>
        <!-- Encabezado -->
        <?php $iframe ? '' : encabezado_mod_svgIcon('bg-custom', 'white', $svg, $titulo, ''); ?>
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
                                <div class="d-inline-flex justify-content-between w-100">
                                    <div>
                                        <span class="font08 py-2 mr-2">Ingresar Por:</span>
                                        <div class="d-flex align-items-center pt-2">
                                            <div class="btn-group btn-group-toggle p-1 border radius"
                                                data-toggle="buttons" style="gap: 5px;">
                                                <label
                                                    class="hint--top btn font08 btn btn-outline-custom border-0 w100 radius"
                                                    id="TipoIngresoFiltros" data-toggle="tooltip"
                                                    data-placement="bottom" data-html="true" title=""
                                                    aria-label="Ingresar por Filtros">
                                                    <input type="radio" name="procesar_por" value="1"> Filtros
                                                </label>
                                                <label
                                                    class="hint--top btn font08 btn btn-outline-custom border-0 w100 radius"
                                                    id="TipoIngresoFiltrosLegajos" data-toggle="tooltip"
                                                    data-placement="bottom" data-html="true" title=""
                                                    aria-label="Ingresar por Legajo">
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
                            <div class="col-12 mt-2">
                                <div class="d-inline-flex mt-1 w-100">
                                    <!-- Rango de Fecha -->
                                    <div style="width:100%;">
                                        <input type="text" class="bg-white h40 form-control text-center" name="_drProc"
                                            id="_drProc">
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
                        <div class="row">
                            <div class="col-12 mt-2">
                                <label for="FichIngresar" class="">Ingresar</label>
                                <select class="form-control" id="FichIngresar" name="TipoDeFichada">
                                    <?php
                                    foreach (INGRESAR as $key => $value) {
                                        echo "<option value=\"$value\">$key</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-12 mt-2">
                                <label for="FichLaboral" class="">Laboral</label>
                                <select class="form-control" id="FichLaboral" name="Laboral">
                                    <?php
                                    foreach (LABORAL as $key => $value) {
                                        echo "<option value=\"$value\">$key</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-12 mt-3 d-flex justify-content-end">
                                <button type="submit" class="ml-1 btn bg-custom text-white font08 w100"
                                    id="submit"></button>
                            </div>
                            <div class="col-12 mt-4 d-flex justify-content-start">
                                <span class="font08 radius w-100">
                                    <div><span class="requeridos">(*)</span> Ingresar:</div>
                                    <ul>
                                        <li>Por Filtros, al menos un filtro es obligatorio.</li>
                                        <li>Por Legajos, al menos un legajo es obligatorio.</li>
                                    </ul>
                                </span>
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
    <script src="js/main.js?<?= version_file("/fichar/js/main.js") ?>"></script>

</body>

</html>
<?php
UnsetGet('_leg');
function selected_estruct_filtros($Col_Cod, $Col_Des, $From, $get)
{
    require __DIR__ . '../../config/conect_mssql.php';
    $query = "SELECT $From.$Col_Cod, $From.$Col_Des FROM $From WHERE $From.$Col_Cod IN (" . implode(',', $get) . ")";
    $rs    = sqlsrv_query($link, $query);
    while ($fila = sqlsrv_fetch_array($rs)) :
        echo '<option selected value="' . $fila[$Col_Cod] . '">' . $fila[$Col_Des] . '</option>';
    endwhile;
    sqlsrv_free_stmt($rs);
    sqlsrv_close($link);
}
?>
<style>
    .select2-results__option[aria-selected=true] { display: none; }
</style>
<div class="col-12 mb-2 mb-sm-0">
    <?php if (($_datos != 'mishoras')){ ?>
    <button type="button" class="btn btn btn-outline-custom fontq w100" data-toggle="modal" data-target="#Filtros_Estruct">
        Filtros
    </button>
    <?php }  ?>
    <?php if ($_datos == 'cta_nov') { ?>
        <?php if ($_GET['_leg'] == '1') { ?>
            <a class="d-none d-sm-block btn btn-link fontq text-secondary float-right" href="index.php"> Visualizar por Novedad</a>
        <?php } else { ?>
            <a class="d-none d-sm-block btn btn-link fontq text-secondary float-right" href="?_leg=1"> Visualizar por Legajo</a>
        <?php }  ?>
    <?php } ?>
    <?php if (($_datos == 'general') ||  ($_datos == 'mishoras')){ 
        $check_dl = (($_GET['_dl'] ?? '') ===  'on') ? 'checked' : '';
        ?>
        <div class="custom-control custom-switch float-right py-1">
            <input <?= $check_dl ?> type="checkbox" class="custom-control-input" name="_dl" id="_dl">
            <label class="custom-control-label" for="_dl" style="padding-top: 3px;">Solo Laboral</label>
        </div>
    <?php } ?>
    <?php if ($_datos == 'personal') { 
        UnsetGet('_eg');
        $check_eg = ($_GET['_eg'] ===  'on') ? 'checked' : '';
    ?>
        <div class="custom-control custom-switch float-right pt-1">
            <input <?php //echo $check_eg;?> type="checkbox" class="custom-control-input" name="_eg" id="_eg">
            <!-- <input <?=$check_eg?> type="checkbox" class="custom-control-input" name="_eg" id="_eg" onchange="this.form.submit()"> -->
            <label class="custom-control-label" for="_eg" style="padding-top: 3px;">De Baja</label>
        </div>
    <?php } ?>
</div>
<div class="modal" id="Filtros_Estruct" tabindex="-1" role="dialog" aria-labelledby="Filtros_EstructLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg animate__animated animate__fadeIn" role="document">
        <div class="modal-content" id="filtros">
            <div class="modal-header border-bottom-0 pb-0">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row form-group mb-4">
                    <div class="col-12 col-sm-4 mt-2 mt-sm-0"><label for="" class="d-none d-sm-block">Empresa</label><select multiple name="_emp[]" class="selectjs_empresa form-control"><?php (isset($_GET['_emp'])) ? (selected_estruct_filtros('EmpCodi', 'EmpRazon', 'EMPRESAS', $_GET['_emp'])) : ''; ?></select></div>
                    <div class="col-12 col-sm-4 mt-2 mt-sm-0"><label for="" class="d-none d-sm-block">Planta</label><select multiple name="_pla[]" class="selectjs_plantas form-control"><?php (isset($_GET['_pla'])) ? (selected_estruct_filtros('PlaCodi', 'PlaDesc', 'PLANTAS', $_GET['_pla'])) : ''; ?></select></div>
                    <div class="col-12 col-sm-4 mt-2 mt-sm-0"><label for="" class="d-none d-sm-block">Convenio</label><select multiple name="_con[]" class="selectjs_convenios form-control"><?php (isset($_GET['_con'])) ? (selected_estruct_filtros('ConCodi', 'ConDesc', 'CONVENIO', $_GET['_con'])) : ''; ?></select></div>
                    <div class="col-12 col-sm-4 mt-2"><label for="" class="d-none d-sm-block">Sector</label><select multiple name="_sec[]" class="selectjs_sector form-control"><?php (isset($_GET['_sec'])) ? (selected_estruct_filtros('SecCodi', 'SecDesc', 'SECTORES', $_GET['_sec'])) : ''; ?></select></div>
                    <div class="col-12 col-sm-4 mt-2"><label for="" class="d-none d-sm-block">Grupo</label><select multiple name="_gru[]" class="selectjs_grupos form-control"><?php (isset($_GET['_gru'])) ? (selected_estruct_filtros('GruCodi', 'GruDesc', 'GRUPOS', $_GET['_gru'])) : ''; ?></select></div>
                    <div class="col-12 col-sm-4 mt-2"><label for="" class="d-none d-sm-block">Sucursal</label><select multiple name="_suc[]" class="selectjs_sucursal form-control"><?php (isset($_GET['_suc'])) ? (selected_estruct_filtros('SucCodi', 'SucDesc', 'SUCURSALES', $_GET['_suc'])) : ''; ?></select></div>
                    <?php if (($_GET['_leg'] != '1') && ($_datos != 'cta_horas')) { ?><div class="col-12 mt-2"><label for="" class="d-none d-sm-block">Personal</label><select multiple name="_per[]" class="selectjs_personal form-control"><?php (isset($_GET['_per'])) ? (selected_estruct_filtros('LegNume', 'LegApNo', 'PERSONAL', $_GET['_per'])) : ''; ?></select></div>
                    <?php } ?>
                </div>
            </div>
            <button type="submit" class="btn text-white opa8 h50 fontq fw4 <?= $bgcolor ?>">Aplicar</button>
        </div>
    </div>
</div>
<div class="tab-pane fade" id="horarios" role="tabpanel" aria-labelledby="horarios-tab">
    <div class="row m-0 border border-top-0 overflow-auto" style="max-height: 450px;min-height: 450px;">
        <div class="col-12 pt-3">
            <div class="form-inline mt-2">
                <label for="LegHoAl" class="mr-2 w120">Tipo de asignación</label>
                <select name="LegHoAl" id="LegHoAl" class="select2 form-control w250">
                    <?php
                    foreach (TIPO_ASIGN as $key => $value) {
                        echo '<option value="' . $value . '">' . $key . '</option>';
                    }
                    ?>
                </select>
                <div class="ml-2 custom-control custom-checkbox mt-1">
                    <input type="checkbox" class="custom-control-input" <?= $persLegHLPlani ?> id="LegHLPlani" name="LegHLPlani">
                    <label class="custom-control-label" for="LegHLPlani" style="padding-top: 3px;">Usar Planificaci&oacute;n</label>
                </div>
            </div>
            <label for="" class="mt-3">Asignación de horarios:</label>
            <div class="w450 ">
                <label class="m-0 fontq mr-1 p-2 w100 fw4">Por Legajo:</label>
                <div class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox" class="custom-control-input" id="LegHLDe" name="LegHLDe" <?= $persLegHLDe ?>>
                    <label class="custom-control-label" for="LegHLDe" style="padding-top: 3px;">Desde</label>
                </div>
                <div class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox" class="custom-control-input" id="LegHLDH" name="LegHLDH" <?= $persLegHLDH ?>>
                    <label class="custom-control-label" for="LegHLDH" style="padding-top: 3px;">Desde - Hasta</label>
                </div>
                <div class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox" class="custom-control-input" id="LegHLRo" name="LegHLRo" <?= $persLegHLRo ?>>
                    <label class="custom-control-label" for="LegHLRo" style="padding-top: 3px;">Rotación</label>
                </div>
            </div>
            <div class="w450 ">
                <label class="m-0 fontq mr-1 p-2 w100 fw4">Por Sector:</label>
                <div class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox" class="custom-control-input" id="LegHSDe" name="LegHSDe" <?= $persLegHSDe ?>>
                    <label class="custom-control-label" for="LegHSDe" style="padding-top: 3px;">Desde</label>
                </div>
                <div class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox" class="custom-control-input" id="LegHSDH" name="LegHSDH" <?= $persLegHSDH ?>>
                    <label class="custom-control-label" for="LegHSDH" style="padding-top: 3px;">Desde - Hasta</label>
                </div>
                <div class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox" class="custom-control-input" id="LegHSRo" name="LegHSRo" <?= $persLegHSRo ?>>
                    <label class="custom-control-label" for="LegHSRo" style="padding-top: 3px;">Rotación</label>
                </div>
            </div>
            <div class="w450 ">
                <label class="m-0 fontq mr-1 p-2 w100 fw4">Por Grupo:</label>
                <div class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox" class="custom-control-input" id="LegHGDe" name="LegHGDe" <?= $persLegHGDe ?>>
                    <label class="custom-control-label" for="LegHGDe" style="padding-top: 3px;">Desde</label>
                </div>
                <div class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox" class="custom-control-input" id="LegHGDH" name="LegHGDH" <?= $persLegHGDH ?>>
                    <label class="custom-control-label" for="LegHGDH" style="padding-top: 3px;">Desde - Hasta</label>
                </div>
                <div class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox" class="custom-control-input" id="LegHGRo" name="LegHGRo" <?= $persLegHGRo ?>>
                    <label class="custom-control-label" for="LegHGRo" style="padding-top: 3px;">Rotación</label>
                </div>
            </div>
            <div class="mt-3 form-inline">
                <label for="LegHoLi" class="mr-2">Tiempo límite antes de horarios para detección de horario alternativo</label>
                <input class="form-control w80" type="text" value="<?= $pers['LegHoLi'] ?>" id="LegHoLi" name="LegHoLi">
            </div>
        </div>
        <div class="col-12 py-3">
            <div class="shadow-sm">
                <div class="form-inline mt-2 <?= $bgcolor ?> p-2">
                    <label for="" class="mx-2 fw4 text-white p-2">Horario alternativo según fichadas:</label>
                    <button title="Asignar Horario" type="button" class="px-2 btn btn-sm btn-light fontq text-secondary" data-toggle="modal" data-target="#altahorarioal">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>

                <div class="mt-2 p-2">
                    <table class="table text-nowrap table-sm w-auto" id="PerHoAlt">
                        <thead class="border-top-0">
                            <tr>
                                <th class="text-center">Cód</th>
                                <th class="">Descripción</th>
                                <th class=""></th>
                                <th class="w-100"></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
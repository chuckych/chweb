<div class="tab-pane fade" id="control" role="tabpanel" aria-labelledby="control-tab">
    <div class="row m-0 border border-top-0" style="max-height: 450px;min-height: 450px;">
        <div class="col-12 col-sm-7 pt-3">
            <div class="form-inline mt-2">
                <div class="input-group">
                    <div class="form-check">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="LegEsta" name="LegEsta" <?= $persLegEsta ?>>
                            <label class="custom-control-label" for="LegEsta" style="padding-top: 3px;">No Controlar Horario</label>
                        </div>
                    </div>
                </div>
                <?php
                if ($persCierreFech) {
                    $persCierreFech = Fech_Format_Var($persCierreFech, 'd/m/Y');
                } else {
                    $persCierreFech = '';
                }
                ?>
                <div class="ml-5 input-group d-flex justify-content-end">
                    <label for="CierreFech" class="mr-2">Fecha de Cierre</label>
                    <input type="text" class="form-control" value="<?= $persCierreFech ?>" id="CierreFech" name="CierreFech" placeholder="dd/mm/yyyy">
                    <!-- <span id="trash_CierreFech" class="ml-1 trash"></span> -->
                    <span id="trash_CierreFech" class="btn btn-sm btn-link opa1"><?= imgIcon('trash3', 'Borrar ', 'w15'); ?></span>
                </div>
            </div>
            <div class="form-inline mt-3">
                <label for="LegToTa" class="mr-2 w120">Tolerancia Tarde</label>
                <input class="form-control w60" type="number" value="<?= $pers['LegToTa'] ?>" id="LegToTa" name="LegToTa" max="99" maxlength="2">
                <label for="LegReTa" class="mx-2">Recorte</label>
                <input class="form-control w60" type="number" value="<?= $pers['LegReTa'] ?>" id="LegReTa" name="LegReTa" max="99" maxlength="2" value="1">
            </div>
            <div class="form-inline mt-2">
                <label for="LegToIn" class="mr-2 w120">Tolerancia Inc.</label>
                <input class="form-control w60" type="number" value="<?= $pers['LegToIn'] ?>" id="LegToIn" name="LegToIn" max="99" maxlength="2">
                <label for="LegReIn" class="mx-2">Recorte</label>
                <input class="form-control w60" type="number" value="<?= $pers['LegReIn'] ?>" id="LegReIn" name="LegReIn" max="99" maxlength="2" value="1">
            </div>
            <div class="form-inline mt-2">
                <label for="LegToSa" class="mr-2 w120">Tolerancia Salida</label>
                <input class="form-control w60" type="number" value="<?= $pers['LegToSa'] ?>" id="LegToSa" name="LegToSa" max="99" maxlength="2">
                <label for="LegReSa" class="mx-2">Recorte</label>
                <input class="form-control w60" type="number" value="<?= $pers['LegReSa'] ?>" id="LegReSa" name="LegReSa" max="99" maxlength="2" value="1">
            </div>
            <div class="form-inline mt-2">
                <label for="LegIncTi" class="mr-2 w120">Incuplimiento</label>
                <select name="LegIncTi" id="LegIncTi" class="select2 form-control w450">
                    <?php
                    foreach (INCUMPLIMIENTO as $key => $value) {
                        echo '<option value="' . $value . '">' . $key . '</option>';
                    }
                    ?>
                </select>
            </div>
            <div class="form-inline mt-2">
                <label for="LegRegCH" class="mr-2 w120">Regla de control</label>
                <select name="LegRegCH" id="LegRegCH" class="selectjs_regla form-control w250">

                </select>
                <span id="trash_LegRegCH" class="trash"></span>

            </div>
            <div class="form-inline">
                <div class="custom-control custom-checkbox mt-3">
                    <input type="checkbox" class="custom-control-input" <?= $persLegNo24 ?> id="LegNo24" name="LegNo24">
                    <label class="custom-control-label" for="LegNo24" style="padding-top: 3px;">No partir novedades adicionales a las 24Hs del día.</label>
                </div>
            </div>
            <div class="form-inline">
                <div class="custom-control custom-checkbox mt-1">
                    <input type="checkbox" class="custom-control-input" <?= $persLegPrCosteo ?> id="LegPrCosteo" name="LegPrCosteo">
                    <label class="custom-control-label" for="LegPrCosteo" style="padding-top: 3px;">Calcular Horas Costeadas</label>
                </div>
            </div>
            <div class="form-inline mt-3">
                <label for="LegValHora" class="mr-2 w120">Valor Hora</label>
                <input class="form-control w100" type="text" value="<?= $LegValHora ?>" id="LegValHora" name="LegValHora" placeholder="0,00">
            </div>
        </div>
        <div class="col-12 col-sm-5 pt-3">
            <label for="" class="">Proceso de Horas:</label><br />
            <div class="form-group">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="LegPrRe" name="LegPrRe" <?= $persLegPrRe ?>>
                    <label class="custom-control-label mx-2" for="LegPrRe" style="padding-top: 3px;">Por Regla de Control</label>
                </div>
                <div class="custom-control custom-checkbox mt-1">
                    <input type="checkbox" class="custom-control-input" id="LegPrCo" name="LegPrCo" <?= $persLegPrCo ?>>
                    <label class="custom-control-label mx-2" for="LegPrCo" style="padding-top: 3px;">Por Convenio</label>
                </div>
                <div class="custom-control custom-checkbox mt-1">
                    <input type="checkbox" class="custom-control-input" id="LegPrPl" name="LegPrPl" <?= $persLegPrPl ?>>
                    <label class="custom-control-label mx-2" for="LegPrPl" style="padding-top: 3px;">Por Planta</label>
                </div>
                <div class="custom-control custom-checkbox mt-1">
                    <input type="checkbox" class="custom-control-input" id="LegPrSe" name="LegPrSe" <?= $persLegPrSe ?>>
                    <label class="custom-control-label mx-2" for="LegPrSe" style="padding-top: 3px;">Por Sector</label>
                </div>
                <div class="custom-control custom-checkbox mt-1">
                    <input type="checkbox" class="custom-control-input" id="LegPrGr" name="LegPrGr" <?= $persLegPrGr ?>>
                    <label class="custom-control-label mx-2" for="LegPrGr" style="padding-top: 3px;">Por Grupo</label>
                </div>
                <div class="custom-control custom-checkbox mt-1">
                    <input type="checkbox" class="custom-control-input" id="LegPrHo" name="LegPrHo" <?= $persLegPrHo ?>>
                    <label class="custom-control-label mx-2" for="LegPrHo" style="padding-top: 3px;">Por Horario</label>
                </div>
            </div>
        </div>
    </div>
</div>
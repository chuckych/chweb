<div class="tab-pane fade show active" id="datos" role="tabpanel" aria-labelledby="home-tab">
    <div class="row m-0 border border-top-0 overflow-auto vh-100" style="max-height: 450px;min-height: 450px;">
        <div class="col-12 py-3">
            <div class="form-inline">
                <label for="LegApNo" class="mr-2">Apellido y Nombres</label>
                <input type="text" class="form-control w400" id="LegApNo" value="<?= $pers['LegApNo'] ?>" name="LegApNo" maxlength="40">
            </div>
            <div class="form-inline mt-3" id="lista_leg">
                <!-- documento -->
                <label for="LegTDoc" class="mr-2 w80">Documento</label>
                <select class="select2 form-control data_leg" id="LegTDoc" name="LegTDoc">
                    <?php
                    foreach (TIPO_DOC as $key => $value) {
                        echo '<option value="' . $value . '">' . $key . '</option>';
                    }
                    ?>
                </select>
                <input type="hidden" class="ml-2 form-control data_leg" value="<?= $pers['LegNume'] ?>" id="LegNume" name="LegNume">
                <input type="number" class="ml-2 form-control data_leg" value="<?= $pers['LegDocu'] ?>" id="LegDocu" name="LegDocu">
                <!-- Cuil -->
                <label for="LegCUIT" class="mx-2">CUIL</label>
                <input type="text" class="form-control data_leg" placeholder="00-00000000-0" value="<?= $pers['LegCUIT'] ?>" id="LegCUIT" name="LegCUIT">
                <!-- Nacionalidad -->
                <label for="LegNaci" class="mx-2">Nacionalidad</label>
                <select class="form-control selectjs_naciones data_leg" style="min-width:200px;" value="<?= $pers['LegNaci'] ?>" id="LegNaci" name="LegNaci">
                </select>
                <!-- Button modal altaNacion-->
                <span data-toggle="modal" data-target="#altaNacion" class="pointer">
                    <button id="btn__Nacion" type="button" class="ml-1 btn btn-sm btn-light text-secondary" data-toggle="tooltip" data-placement="right" title="CREAR">
                        +
                    </button>
                </span>
                <!-- <span id="trash_nacion" class="btn btn-sm btn-link opa1"><?= imgIcon('trash3', 'Limpiar Selección ', 'w15'); ?></span> -->
                <span id="trash_nacion" class="trash"></span>
            </div>
            <div class="form-inline mt-2">
                <!-- Estado Civil -->
                <label for="LegEsCi" class="mr-2 w80">Estado Civil</label>
                <select class="select2 form-control" value="<?= $pers['LegEsCi'] ?>" id="LegEsCi" name="LegEsCi">
                    <?php
                    foreach (ESTADO_CIVIL as $key => $value) {
                        echo '<option value="' . $value . '">' . $key . '</option>';
                    }
                    ?>
                </select>
                <!-- Fin Estado Civil -->
                <!-- Género -->
                <label for="LegSexo" class="mx-2">Género</label>
                <select class="select2 form-control" value="<?= $pers['LegSexo'] ?>" id="LegSexo" name="LegSexo">
                    <?php
                    foreach (SEXO as $key => $value) {
                        echo '<option value="' . $value . '">' . $key . '</option>';
                    }
                    ?>
                </select>
                <!-- Fin Género -->
                <!-- Nacimiento -->
                <?php

                if ($persLegFeNa) {
                    $persLegFeNa = Fech_Format_Var($persLegFeNa, 'd/m/Y');
                } else {
                    $persLegFeNa = '';
                }
                ?>
                <label for="LegFeNa" class="mx-2">Nacimiento</label>
                <input type="text" class="form-control text-center" value="<?= $persLegFeNa ?>" id="LegFeNa" name="LegFeNa" onkeyup="javascript:calcularEdad();" placeholder="dd/mm/yyyy">
                <span id="trash_LegFeNa" class="ml-1 trash"></span>
                <div id="result" class="fontq ml-2"></div><!-- div donde mostraremos la edad -->
                <!-- Fin Nacimiento -->
            </div>
            <!-- Domicilio -->
            <div class="">
                <p class="fontq mt-3 mb-2 fw5">Domicilio:</p>
                <div class="form-inline">
                    <!-- Calle -->
                    <label for="LegDomi" class="mr-2 w80">Calle</label>
                    <input type="text" name="LegDomi" class="form-control" id="LegDomi" value="<?= $pers['LegDomi'] ?>">
                    <!-- Numero -->
                    <label for="LegDoNu" class="mx-2">N°</label>
                    <input type="number" name="LegDoNu" class="form-control w80" id="LegDoNu" value="<?= $pers['LegDoNu'] ?>">
                    <!-- Piso -->
                    <label for="LegDoPi" class="mx-2">Piso</label>
                    <input type="number" name="LegDoPi" class="form-control w70" id="LegDoPi" value="<?= $pers['LegDoPi'] ?>">
                    <!-- Depto -->
                    <label for="LegDoDP" class="mx-2">Depto</label>
                    <input type="text" name="LegDoDP" class="form-control w60" id="LegDoDP" value="<?= $pers['LegDoDP'] ?>" maxlength="2">
                    <!-- Cp -->
                    <label for="LegCOPO" class="mx-2">CP</label>
                    <input type="text" name="LegCOPO" class="form-control w100" id="LegCOPO" value="<?= $pers['LegCOPO'] ?>">
                </div>
                <div class="form-inline mt-2">
                    <!-- Provincia -->
                    <label for="LegProv" class="mr-2 w80">Provincia</label>
                    <select class="form-control selectjs_provincias" style="min-width:250px;" id="LegProv" name="LegProv">
                    </select>
                    <!-- Button modal altaProvincia-->
                    <span data-toggle="modal" data-target="#altaProvincia" class="pointer">
                        <button type="button" class="ml-1 btn btn-sm btn-light" data-toggle="tooltip" data-placement="right" title="CREAR">
                            +
                        </button>
                    </span>
                    <span id="trash_prov" class="trash"></span>
                </div>
                <div class="form-inline mt-2">
                    <!-- Localidad -->
                    <label for="LegLoca" class="mr-2 w80">Localidad</label>
                    <select class="form-control selectjs_localidad" style="min-width:250px;" id="LegLoca" name="LegLoca">
                    </select>
                    <!-- Button modal altaLocalidad-->
                    <span data-toggle="modal" data-target="#altaLocalidad" class="pointer">
                        <button type="button" class="ml-1 btn btn-sm btn-light" data-toggle="tooltip" data-placement="right" title="CREAR">
                            +
                        </button>
                    </span>
                    <span id="trash_loca" class="trash"></span>
                </div>
                <div class="form-inline mt-2">
                    <!-- Observación -->
                    <label class="">
                        <span class="mr-2">Observación</span>
                        <input type="text" name="LegDoOb" class="form-control w300" placeholder="Observaciones" id="LegDoOb" value="<?= $pers['LegDoOb'] ?>" maxlength="40">
                    </label>
                </div>
            </div>
            <!-- Fin Domicilio -->
            <!-- Teléfonos -->
            <p class="fontq mt-3 mb-2 fw5">Teléfonos:</p>
            <div class="form-inline">
                <label for="LegTel1" class="mr-2 w80">Teléfono</label>
                <input type="tel" class="form-control" value="<?= $pers['LegTel1'] ?>" id="LegTel1" name="LegTel1" maxlength="15">
                <input type="text" class="mx-2 form-control" placeholder="Observaciones" value="<?= $pers['LegTeO1'] ?>" id="LegTeO1" name="LegTeO1" maxlength="20">
            </div>
            <div class="form-inline mt-2">
                <label for="LegTel2" class="mr-2 w80">Teléfono</label>
                <input type="tel" class="form-control" value="<?= $pers['LegTel2'] ?>" id="LegTel2" name="LegTel2" maxlength="15">
                <input type="text" class="mx-2 form-control" placeholder="Observaciones" value="<?= $pers['LegTeO2'] ?>" id="LegTeO2" name="LegTeO2" maxlength="20">
            </div>
            <!-- Fin Telefonos -->
        </div>
    </div>
</div>
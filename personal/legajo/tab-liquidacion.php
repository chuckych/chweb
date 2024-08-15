<div class="tab-pane fade" id="liquid" role="tabpanel" aria-labelledby="liquid-tab">
    <div class="row m-0 border border-top-0 overflow-auto">
        <div class="col-12 py-2">
            <div class="form-inline mt-2">
                <!-- Tipo personal -->
                <label for="LegTipo" class="mx-2">Tipo de Personal</label>
                <select class="select2 form-control w120" id="LegTipo" name="LegTipo">
                    <?php
                    foreach (TIPO_PER as $key => $value) {
                        echo '<option value="' . $value . '">' . $key . '</option>';
                    }
                    ?>
                </select>
                <!-- Tipo personal -->

                <?php

                if ($persLegFeEg) {
                    $persLegFeEg = Fech_Format_Var($persLegFeEg, 'd/m/Y');
                } else {
                    $persLegFeEg = '';
                }
                if ($persLegFeIn) {
                    $persLegFeIn = Fech_Format_Var($persLegFeIn, 'd/m/Y');
                } else {
                    $persLegFeIn = '';
                }

                ?>

                <label for="LegFeIn" class="mx-2">Ingreso</label>
                <input class="form-control text-center ls1" type="text" id="LegFeIn" name="LegFeIn" value="<?= ($persLegFeIn) ?>">
                <!-- <input class="form-control text-center ls1" type="text" value="<?= $persLegFeIn ?>" id="LegFeIn" name="LegFeIn"> -->
                <span id="trash_LegFeIn" class="btn btn-sm btn-link opa1"><?= imgIcon('trash3', 'Borrar ', 'w15'); ?></span>
                <label for="LegFeEg" class="mx-2">Egreso</label>
                <input class="form-control text-center ls1" type="text" value="<?= ($persLegFeEg) ?>" id="LegFeEg" name="LegFeEg">
                <!-- <input class="form-control" type="date" value="<?= ($persLegFeEg) ?>" id="LegFeEg" name="LegFeEg" max="<?= date('Y-m-d') ?>"> -->
                <span id="trash_LegFeEg" class="btn btn-sm btn-link opa1"><?= imgIcon('trash3', 'Borrar ', 'w15'); ?></span>
            </div>
        </div>
        <div class="col-sm-10 col-12 py-2">
            <div class="mt-2 shadow-sm p-0">
                <div class="form-inline mt-2 <?= $bgcolor ?> p-2">
                    <label class="mx-2 fw4 text-white p-2">Historial:</label>
                    <button data-titlet="Nuevo Ingreso y Egreso" type="button" class="px-2 btn btn-sm btn-light fontq text-secondary" data-toggle="modal" data-target="#altahistorial">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>

                <div class="p-2">
                    <table class="table text-nowrap w-100" id="Perineg">
                        <thead class="border-top-0">
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-12 py-2">
            <div class="w-100 mt-2 shadow-sm p-0">
                <div class="form-inline mt-2 <?= $bgcolor ?> p-2">
                    <label for="" class="mx-2 fw4 text-white p-2">Premios:</label>
                    <button data-titlet="Asignar Premio" type="button" class="px-2 btn btn-sm btn-light fontq text-secondary" data-toggle="modal" data-target="#altapremios">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>

                <div class="p-2">
                    <table class="table w-100 text-nowrap table-sm" id="Perpremio">
                        <thead class="border-top-0">
                            <tr>
                                <th class="text-center">Premio</th>
                                <th class="">Descripción</th>
                                <th class=""></th>
                                <th class="w-100"></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-12 py-2">
            <div class="w-100 mt-2 shadow-sm p-0">
                <div class="form-inline mt-2 <?= $bgcolor ?> p-2">
                    <label for="" class="mx-2 fw4 text-white p-2">Conceptos:</label>
                    <button data-titlet="Asignar Concepto" type="button" class="px-2 btn btn-sm btn-light fontq text-secondary" data-toggle="modal" data-target="#altaconceptos">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>

                <div class="p-2">
                    <table class="table w-100 text-nowrap table-sm" id="OtrosConLeg">
                        <thead class="border-top-0">
                            <tr>
                                <th class="text-center">Código</th>
                                <th class="">Descripción</th>
                                <th class="text-center">Valor</th>
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
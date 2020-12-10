<!-- Modal -->
<div class="modal fade" id="altaconvenio" data-backdrop="static" data-keyboard="true" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content" style="width: 850px;">
            <div class="modal-header border-bottom-0">
                <h6 class="modal-title" id="staticBackdropLabel">Convenio</h6>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body pb-0">
                <!-- <div class="row"> -->
                <form action="alta_opciones.php" method="post" class="form-convenio row">
                    <div id="" class="col-12 mt-2">
                        <div class="form-inline">
                            <input autofocus id="desc_convenio" class="form-control h40 w300" placeholder="Descripción" type="text" name="desc_convenio">
                            <input type="hidden" name="dato_conv" value="alta_convenio">
                            <input type="hidden" name="codConv">
                        </div>
                        <small class="form-text text-muted mr-2 mt-2">
                            Si la antiguedad es menor a 6 meses, informar 1 día por cada:
                        </small>
                        <div class="form-inline mt-2">
                            <input id="ConDias" class="form-control w80 mr-2" placeholder="" type="number" min="0" name="ConDias" value="20">
                            <select name="ConTDias" id="ConTDias" class="select2 form-control w140">
                                <?php
                                foreach (ConTDias as $key => $value) {
                                    echo '<option value="' . $value . '">' . $key . '</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-12 my-3">
                        <div class="form-group d-flex justify-content-end">
                            <button type="submit" class="btn btn-sm text-white fontq <?= $bgcolor ?>" id="btnConv">Aceptar</button>
                        </div>
                    </div>
                </form>
                <div class="row d-none" id="rowConvVac">
                    <!-- oculto -->
                    <div id="tabs" class="col-12 pb-3">
                        <div class="nav nav-tabs fontq" id="nav-tab" role="tablist">
                            <!-- Pestaña Convenio Dias Vaca -->
                            <a class="nav-item nav-link active text-secondary" id="DiasVac-tab" data-toggle="tab" href="#DiasVac" role="tab" aria-controls="DiasVac" aria-selected="true">Días de vacaciones según antiguedad</a>
                            <!-- Pestaña Convenio Feriados -->
                            <a class="nav-item nav-link text-secondary" id="FeriConv-tab" data-toggle="tab" href="#FeriConv" role="tab" aria-controls="FeriConv" aria-selected="false">Feriados</a>
                        </div>
                        <div class="tab-content" id="nav-tabContent">
                            <!-- TAB DIAS DE VACACIONES CONVENIOS -->
                            <div class="tab-pane fade show active" id="DiasVac" role="tabpanel" aria-labelledby="DiasVac-tab">
                                <div class="row m-0 border border-top-0">
                                    <div class="col-12 mb-2" id="diasvac">
                                        <div class="">
                                            <form action="alta_opciones.php" method="POST" class="form-diasvac" name="form-diasvac">
                                                <div class="form-inline my-2">
                                                    <label class="align-middle fontq mr-2" for="anios">Hasta</label>
                                                    <input class="form-control w60" type="number" name="anios" id="anios" value="0" min="0">
                                                    <label class="align-middle fontq mx-2" for="meses">Años y</label>
                                                    <input class="form-control w60" type="number" name="meses" id="meses" value="6" min="0" max="12">
                                                    <label class="align-middle fontq mx-2">Meses.</label>
                                                    <input class="form-control w70" type="number" name="diasvac" id="diasvac" value="14" min="0">
                                                    <label class="align-middle fontq mx-2" for="diasvac">Días.</label>
                                                    <input type="hidden" name="alta-diasvac" value="alta-diasvac" class="w60">
                                                    <input type="hidden" name="cod-diasvac" class="w60">
                                                    <!-- <input type="submit" class="ml-2 btn btn-sm text-white fontq <?= $bgcolor ?>" name="" value="+"> -->
                                                    <span class="pointer">
                                                        <button type="submit" class="ml-1 btn btn-sm text-white fontq <?= $bgcolor ?>" data-toggle="tooltip" data-placement="right" title="Agregar">
                                                            +
                                                        </button>
                                                    </span>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    <div id="ConvVacaTabla" class="col-12 table-responsive mb-2 d-none">
                                        <table class="table" id="ConvVaca">
                                            <thead class="border-top-0">
                                                <tr>
                                                    <th class="text-center">Años</th>
                                                    <th class="text-center">Meses</th>
                                                    <th class="text-center">Días</th>
                                                    <th class="text-center"></th>
                                                    <th class="w-100"></th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <!-- FIN TAB DIAS DE VACACIONES CONVENIOS -->
                            <!-- TAB FERIADOS CONVENIOS -->
                            <div class="tab-pane fade" id="FeriConv" role="tabpanel" aria-labelledby="FeriConv-tab">
                                <div class="row m-0 border border-top-0">
                                    <div class="col-12 my-2" id="fericonv">
                                        <form action="alta_opciones.php" method="POST" class="form-fericonv" name="form-fericonv">
                                            <input type="hidden" name="alta-feriConv" value="alta-feriConv" class="w60">
                                            <input type="hidden" name="CFConv" class="w60">
                                            <div class="form-inline my-2">
                                                <label class="align-middle fontq mr-2" for="CFFech">Fecha</label>
                                                <input class="form-control" type="date" name="CFFech" id="CFFech">
                                                <label class="align-middle fontq mx-2" for="CFDesc">Descripción</label>
                                                <input class="form-control" type="text" name="CFDesc" id="CFDesc">
                                            </div>
                                            <div class="form-inline mt-2">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="CFInFeTR" name="CFInFeTR">
                                                    <label class="custom-control-label" for="CFInFeTR" style="padding-top:3px;">No informar si se trabajo y se generaron horas por feriado trabajado (InFeTR)</label>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted mr-2 mt-3">
                                                Códigos de liquidación:
                                            </small>
                                            <div class="form-inline mt-2">
                                                <label class="align-middle fontq mr-2 w80" for=""></label>
                                                <label class="align-middle fontq mr-2 w100" for="">Feriado</label>
                                                <label class="align-middle fontq mr-2 w100" for=""></label>
                                                <label class="align-middle fontq mr-2 " for="">Feriado Trab.</label>
                                            </div>
                                            <div class="form-inline mt-2">
                                                <label class="align-middle fontq mr-2 w80" for="CFCodM">Mensuales</label>
                                                <input maxlength="10" class="form-control w100" type="text" name="CFCodM" id="CFCodM">
                                                <input maxlength="10" class="form-control w100 ml-1" type="text" name="CFCodM3" id="CFCodM3">
                                                <input maxlength="10" class="form-control w100 ml-2" type="text" name="CFCodM2" id="CFCodM2">
                                                <div class="custom-control custom-switch mx-2">
                                                    <input type="checkbox" class="custom-control-input" id="CFInfM" name="CFInfM">
                                                    <label class="custom-control-label" for="CFInfM" style="padding-top:3px;">Informar en horas</label>
                                                </div>
                                                <select name="CFInMeNL" id="CFInMeNL" class="select2 form-control w150">
                                                    <?php
                                                    foreach (INFOR_EN_HORAS as $key => $value) {
                                                        echo '<option value="' . $value . '">' . $key . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-inline mt-2">
                                                <label class="align-middle fontq mr-2 w80" for="CFCodJ">Jornales</label>
                                                <input maxlength="10" class="form-control w100" type="text" name="CFCodJ" id="CFCodJ">
                                                <input maxlength="10" class="form-control w100 ml-1" type="text" name="CFCodJ3" id="CFCodJ3">
                                                <input maxlength="10" class="form-control w100 ml-2" type="text" name="CFCodJ2" id="CFCodJ2">
                                                <div class="custom-control custom-switch mx-2">
                                                    <input type="checkbox" class="custom-control-input" id="CFInfJ" name="CFInfJ">
                                                    <label class="custom-control-label" for="CFInfJ" style="padding-top:3px;">Informar en horas</label>
                                                </div>
                                                <select name="CFInJoNL" id="CFInJoNL" class="select2 form-control w150">
                                                    <?php
                                                    foreach (INFOR_EN_HORAS as $key => $value) {
                                                        echo '<option value="' . $value . '">' . $key . '</option>';
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group d-flex justify-content-end mt-3">
                                                <input type="submit" class="btn btn-sm text-white fontq <?= $bgcolor ?>" value="Agregar">
                                            </div>
                                        </form>
                                    </div>
                                    <div id="ConvFeriTabla" class="col-12 table-responsive mb-2 d-none">
                                        <table class="table text-nowrap" id="ConvFeri">
                                            <thead class="border-top-0">
                                                <tr>
                                                    <th class="">Fecha</th>
                                                    <th class="">Descripción</th>
                                                    <th class="text-center">InFeTR</th>
                                                    <th class="">CodMenF</th>
                                                    <th class="">CodMenJ</th>
                                                    <th class="text-center">InfM</th>
                                                    <th class="text-center">InfJ</th>
                                                    <th class="text-center"></th>
                                                    <th class="w-100"></th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- FIN TAB FERIADOS CONVENIOS -->
                    </div>
                </div>
            </div>
            <div class="modal-footer m-0 p-0 border-top-0">
                <div id="espera"></div>
                <div id="alerta_convenio" class="radius fontq alert m-0 d-none w-100" role="alert">
                    <strong class="respuesta_convenio fw5"></strong>
                    <span class="mensaje_convenio fw4"></span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="tab-pane fade" id="dispositivo" role="tabpanel" aria-labelledby="dispositivo-tab">
    <div class="row m-0 border border-top-0 overflow-auto" style="max-height: 450px;min-height: 450px;">
        <div class="col-12 col-sm-12 mt-3">
            <form action="alta_opciones.php" method="post" class="form-grupocapt">
                <label for="LegGrHa2" class="mr-2">Grupo de Dispositivos</label>
                <select class="form-control selectjs_grupocapt w200" name="LegGrHa2" id="LegGrHa2">
                </select>
                <input type="hidden" name="GrupoHabi" value="true" id="GrupoHabi">
                <input type="hidden" name="LegajoGrHa" value="<?= $_GET['_leg'] ?>" id="LegajoGrHa">
                <button class="d-none btn btn-sm btn-light text-secondary fontq" id="grupocapt">Seleccionar</button>
            </form>
        </div>
        <div class="col-12 mt-2">
            <div class="shadow-sm p-0">
                <div class="form-inline bg-custom p-2">
                    <label for="" class="mx-2 fw4 p-2 text-white">Dispositivos habilitados:</label>
                </div>
                <input type="hidden" value="<?= $pers['LegGrHa'] ?>" id="LegGrHa" name="LegGrHa">
                <div class="px-2 table-responsive">
                    <table class="table w-auto text-nowrap" id="GrupoCapt">
                        <thead class="border-top-0">
                            <tr>
                                <!-- <th class="">Grupo</th> -->
                                <!-- <th class="">Reloj</th> -->
                                <th class="">Serie</th>
                                <th class="">Dispositivo</th>
                                <th class="">Marca</th>
                                <th class="w-100"></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-12 mt-2">
            <div class="shadow-sm p-0">
                <div class="form-inline bg-custom p-2">
                    <label for="" class="mx-2 fw4 p-2 text-white">Dispositivos habilitados con vencimiento:</label>
                    <span data-toggle="modal" data-target="#altaPerRelo" class="pointer">
                    <button id="btn__PerRelo" type="button" class="ml-1 btn btn-sm btn-light text-secondary" data-toggle="tooltip" data-placement="right" title="Agregar Dispositivo">
                        &#x27A5;
                    </button>
                    </span>
                </div>
                <div class="px-2 table-responsive">
                    <table class="table w-auto text-nowrap" id="TablePerRelo">
                        <thead class="border-top-0">
                            <tr>
                                <th class="">Serie</th>
                                <th class="">Dispositivo</th>
                                <th class="">Marca</th>
                                <th class="">Desde</th>
                                <th class="">Vencimiento</th>
                                <th class="text-center"></th>
                                <th class="w-100"></th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <?php require __DIR__ . '/modalPerRelo.php' ?>
            </div>
        </div>
    </div>
</div>